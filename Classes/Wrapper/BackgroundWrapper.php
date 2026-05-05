<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\FileInterface;
use T3SBS\T3sbootstrap\Helper\StyleHelper;
use T3SBS\T3sbootstrap\Utility\VideoRenderer;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;

class BackgroundWrapper implements SingletonInterface
{
    private const FILE_TYPE_IMAGE = 2;
    private const FILE_TYPE_VIDEO = 4;

    public function __construct(
        private readonly FileRepository         $fileRepository,
        private readonly VideoRenderer          $videoRenderer,
        private readonly BackgroundImageUtility $backgroundImageUtility,
        private readonly StyleHelper            $styleHelper,
        private readonly ConnectionPool         $connectionPool,
    ) {}

    public function getProcessedData(
        array $processedData,
        array $flexconf,
        array $settings
    ): array {
        $processedData['style']          = $processedData['style'] ?? '';
        $processedData['enableAutoheight'] = !empty($flexconf['enableAutoheight']);
        $processedData['addHeight']        = !empty($flexconf['addHeight']) ? (int)$flexconf['addHeight'] : 0;

        $bgMediaQueries = $settings['bgMediaQueries'] ?? '2560,1920,1200,992,768,576';
        $fileExtension  = $settings['media.']['fileExtension'] ?? '';

        $files = $this->fileRepository->findByRelation(
            'tt_content', 'assets', (int)$processedData['data']['uid']
        );
        $file = $files[0] ?? null;

        if ($file instanceof FileInterface) {
            $processedData = match ($file->getType()) {
                self::FILE_TYPE_VIDEO => $this->processVideo($processedData, $flexconf, $file),
                self::FILE_TYPE_IMAGE => $this->processImage($processedData, $flexconf, $file, $bgMediaQueries, $fileExtension),
                default               => $processedData, // audio – nichts tun
            };
        } else {
            // Kein Medium – nur Hintergrundfarbe + Padding
            if (!empty($flexconf['noMediaPaddingTopBottom'])) {
                $processedData['style'] .= ' padding: ' . $flexconf['noMediaPaddingTopBottom'] . 'rem 0;';
            }
        }

        $processedData = $this->applyVideoParams($processedData, $flexconf);

        return $processedData;
    }

    // ─── Video ───────────────────────────────────────────────────────────────

    private function processVideo(array $processedData, array $flexconf, FileInterface $file): array
    {
        $mimeType  = $file->getMimeType();
        $extension = $file->getExtension();

        if ($mimeType === 'video/youtube' || $extension === 'youtube') {
            return $this->processStreamingVideo($processedData, $flexconf, $file, 'youtube');
        }

        if ($mimeType === 'video/vimeo' || $extension === 'vimeo') {
            return $this->processStreamingVideo($processedData, $flexconf, $file, 'vimeo');
        }

        return $this->processLocalVideo($processedData, $flexconf, $file);
    }

    private function processStreamingVideo(
        array $processedData,
        array $flexconf,
        FileInterface $file,
        string $platform
    ): array {
        $processedData['youtube']         = $platform === 'youtube';
        $processedData['vimeo']           = $platform === 'vimeo';
        $processedData['isVideo']         = true;
        $processedData['contentPosition'] = $flexconf['contentPosition'] ?? 'align-self-center';
        $processedData['ytVideo']         = [
            'bgHeight' => $flexconf['bgHeight'] ?? '',
            'ytshift'  => $flexconf['ytshift']  ?? '',
        ];
        $processedData['videoAutoPlay'] = $file->getProperties()['autoplay'];
        $processedData['videoId']       = $this->videoRenderer->render($file);

        return $processedData;
    }

    private function processLocalVideo(array $processedData, array $flexconf, FileInterface $file): array
    {
        $uid       = (int)$processedData['data']['uid'];
        $autoplay  = $file->getProperties()['autoplay'];
        $loop      = $flexconf['loop'];
        $mute      = $autoplay ? true : $flexconf['mute'];

        $mobileHeight = $flexconf['mobileHeight'] !== 'none' ? (int)trim($flexconf['mobileHeight']) : '';
        $mobileWidth  = $flexconf['mobileWidth']  !== 'none' ? (int)trim($flexconf['mobileWidth'])  : '';
        $hShift       = (int)($flexconf['horizontalShift'] ?? 0);

        $processedData['file']            = $file;
        $processedData['horizontalShift'] = $hShift;
        $processedData['shift']           = $flexconf['shift'];
        $processedData['alignItem']       = $flexconf['alignVideoItem'] !== 'none'
            ? ' ' . $flexconf['alignVideoItem'] : '';

        $processedData['localVideo']['inlineCSS'] =
            '@media (max-width:768px){#s-' . $uid .
            ' figure.video{width:' . $mobileWidth . '%; max-height:' . $mobileHeight .
            'px; margin-left:' . $hShift . '%}}';

        [$ratio, $ratioClass] = $this->resolveAspectRatio($flexconf['aspectRatio'] ?? '16:9');
        $ratioArr = explode('x', $ratio);
        $processedData['ratioCalcCss']        = '.ratio-' . $ratio . '{--bs-aspect-ratio:calc(' . $ratioArr[1] . ' / ' . $ratioArr[0] . ' * 100%);}';
        $processedData['localVideo']['class'] = ' ratio ratio-' . $ratio;

        $processedData['localVideo']['overlayChild'] = $this->countOverlayChildren(
            $uid,
            (int)$processedData['data']['sys_language_uid']
        );
        $processedData['localVideo']['autoplay']  = $autoplay;
        $processedData['localVideo']['loop']      = $loop;
        $processedData['localVideo']['mute']      = $mute;
        $processedData['localVideo']['controls']  = $flexconf['localControls'] ?: 0;

        return $processedData;
    }

    /**
     * Parst Seitenverhältnis in "WxH"-Format.
     * Unterstützt "16:9", "16/9", "16x9".
     * Fallback: "16x9".
     *
     * @return array{0: string, 1: string}  [ratio-key, css-class-suffix]
     */
    private function resolveAspectRatio(string $raw): array
    {
        if (str_contains($raw, ':')) {
            [$w, $h] = explode(':', $raw, 2);
        } elseif (str_contains($raw, '/')) {
            [$h, $w] = explode('/', $raw, 2);
        } elseif (str_contains($raw, 'x')) {
            [$w, $h] = explode('x', $raw, 2);
        } else {
            [$w, $h] = ['16', '9'];
        }

        $ratio = trim($w) . 'x' . trim($h);
        return [$ratio, $ratio];
    }

    private function countOverlayChildren(int $parentUid, int $languageUid): int
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tt_content');
        return (int) $qb
            ->count('uid')
            ->from('tt_content')
            ->where(
                $qb->expr()->eq('sys_language_uid', $qb->createNamedParameter($languageUid, Connection::PARAM_INT)),
                $qb->expr()->eq('tx_container_parent', $qb->createNamedParameter($parentUid, Connection::PARAM_INT)),
                $qb->expr()->eq('deleted', 0)
            )
            ->executeQuery()
            ->fetchOne();
    }

    // ─── Image ───────────────────────────────────────────────────────────────

    private function processImage(
        array $processedData,
        array $flexconf,
        FileInterface $file,
        string $bgMediaQueries,
        string $fileExtension
    ): array {
        if (!empty($flexconf['origImage'])) {
            $processedData['file']     = $file;
            $processedData['imgWidth'] = (int)($flexconf['width'] ?? 1296);
        } else {
            $this->backgroundImageUtility->getBgWrapperImage(
                $processedData['data']['uid'], $file, $flexconf, $bgMediaQueries, $fileExtension
            );
            $processedData['bgImage'] = $file;

            if (!empty($flexconf['paddingTopBottom'])) {
                $processedData['style'] .= ' padding: ' . $flexconf['paddingTopBottom'] . 'rem 0;';
            }
        }

        $processedData['alignItem']   = !empty($flexconf['alignItem']) ? ' ' . $flexconf['alignItem'] : '';
        $processedData['imageRaster'] = !empty($flexconf['imageRaster']) ? 'multiple-' : '';

        if (!empty($processedData['data']['tx_t3sbootstrap_textcolor'])) {
            $processedData['overlayClass'] = ' text-' . $processedData['data']['tx_t3sbootstrap_textcolor'];
        }

        $processedData['bgColorOverlay'] = $this->styleHelper->getBgColor($processedData['data'], false);
        $processedData['style']         .= $this->buildFilterStyle($flexconf);

        return $processedData;
    }

    private function buildFilterStyle(array $flexconf): string
    {
        $filter = '';
        if (!empty($flexconf['imgGrayscale'])) {
            $filter .= ' grayscale(' . $flexconf['imgGrayscale'] . '%)';
        }
        if (!empty($flexconf['imgSepia'])) {
            $filter .= ' sepia(' . $flexconf['imgSepia'] . '%)';
        }
        if (!empty($flexconf['imgOpacity']) && $flexconf['imgOpacity'] != 100) {
            $filter .= ' opacity(' . $flexconf['imgOpacity'] . '%)';
        }

        return $filter ? 'filter: ' . trim($filter) . ';' : '';
    }

    // ─── Video-Parameter (YouTube / Vimeo) ───────────────────────────────────

    private function applyVideoParams(array $processedData, array $flexconf): array
    {
        $vMute = !empty($flexconf['videoMute']) ? $flexconf['videoMute'] : 0;
        $mute  = !empty($processedData['videoAutoPlay']) ? 1 : $vMute;

        if (!empty($flexconf['videoControls']) || empty($processedData['videoAutoPlay'])) {
            $processedData['controlStyle'] = '';
        } else {
            $processedData['controlStyle'] = ' pointer-events:none;';
        }

        $videoId = $processedData['videoId'] ?? null;

        if ($videoId && !empty($processedData['youtube'])) {
            $processedData['youtubeParams'] =
                '?autoplay=' . ($processedData['videoAutoPlay'] ?? 0) .
                '&loop='     . ($flexconf['videoLoop'] ?? 0) .
                '&playlist=' . $videoId .
                '&mute='     . $mute .
                '&rel=0&showinfo=0' .
                '&controls=' . ($flexconf['videoControls'] ?? 0) .
                '&modestbranding=' . ($flexconf['videoControls'] ?? 0);
        }

        if ($videoId && !empty($processedData['vimeo'])) {
            $autoplay = $processedData['videoAutoPlay'] ?? 0;
            $processedData['vimeoParams'] =
                ($autoplay ? '&background=1' : '') .
                '&autoplay=' . $autoplay .
                '&loop='     . ($flexconf['videoLoop'] ?? 0) .
                '&mute='     . $mute;
            $processedData['startButton'] = $autoplay ? 0 : 1;
        }

        return $processedData;
    }
}
