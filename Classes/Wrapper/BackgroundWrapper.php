<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
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
        // Header inside the section instead of above it: on top of the image,
        // together with the content. Only the header of the wrapper itself -
        // the headers of the elements inside it are untouched.
        $processedData['headerInside']     = !empty($flexconf['headerInside']);

        // The header is printed above the <section>, and the <section> is what
        // carries the class - so a contentMarginTop would land between the two.
        // Same condition as in the template; DefaultHelper moves the class over.
        $processedData['marginTopOnHeader'] = !$processedData['headerInside']
            && (
                !empty($processedData['data']['header'])
                || (!empty($settings['supraheader']) && !empty($processedData['data']['tx_t3sbootstrap_supraheader']))
            );
        $processedData['addHeight']        = !empty($flexconf['addHeight']) ? (int)$flexconf['addHeight'] : 0;

        // Text and (semi-transparent) background colour belong to the wrapper, not to
        // the medium. They used to be set only in processImage(), so with a local video
        // the overlay stayed colourless and the wrapper was visually not there at all.
        $processedData['overlayClass']   = !empty($processedData['data']['tx_t3sbootstrap_textcolor'])
            ? ' text-' . $processedData['data']['tx_t3sbootstrap_textcolor'] : '';
        $processedData['bgColorOverlay'] = $this->styleHelper->getBgColor($processedData['data'], false);

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

        // The "localvideo" sheet sits behind displayCond isLocalVideo(), which reads the already
        // stored assets relation - on the very first save every key below can be missing. Cast,
        // not just ??: an empty string reached the inline JS as a missing argument and broke it.
        $loop      = (int)($flexconf['loop'] ?? 0);
        $mute      = $autoplay ? true : (int)($flexconf['mute'] ?? 1);

        $rawMobileHeight = (string)($flexconf['mobileHeight'] ?? '200');
        $rawMobileWidth  = (string)($flexconf['mobileWidth']  ?? '100');
        $alignVideoItem  = (string)($flexconf['alignVideoItem'] ?? 'align-self-center');

        // 'none' used to yield an empty value, so "max-height:px" - invalid, dropped
        // by the browser.
        $mobileHeight = $rawMobileHeight !== 'none' ? (int)trim($rawMobileHeight) : 0;
        $mobileWidth  = $rawMobileWidth  !== 'none' ? (int)trim($rawMobileWidth)  : 0;
        $hShift       = (int)($flexconf['horizontalShift'] ?? 0);

        $processedData['file']            = $file;
        $processedData['horizontalShift'] = $hShift;
        $processedData['shift']           = (int)($flexconf['shift'] ?? 0);
        $processedData['alignItem']       = $alignVideoItem !== 'none' && $alignVideoItem !== ''
            ? ' ' . $alignVideoItem : '';

        $processedData['localVideo']['inlineCSS'] = $this->buildMobileCss($uid, $mobileWidth, $mobileHeight, $hShift);

        // resolveAspectRatio() normalises every notation to "WxH", so the CSS is
        // built the same way as in ConfigProcessor and BootstrapProcessor - the
        // generated .ratio-* rules of one page can no longer contradict each other.
        [$ratio] = $this->resolveAspectRatio((string)($flexconf['aspectRatio'] ?? '9/37'));
        $ratioArr = explode('x', $ratio);
        $width  = (float)($ratioArr[0] ?? 0);
        $height = (float)($ratioArr[1] ?? 0);
        if ($width <= 0.0 || $height <= 0.0) {
            [$width, $height] = [16.0, 9.0];
            $ratio = '16x9';
        }
        $processedData['ratioCalcCss']        = '.ratio-' . $ratio . '{--bs-aspect-ratio:calc(' . $height . ' / ' . $width . ' * 100%);}';
        $processedData['localVideo']['class'] = ' ratio ratio-' . $ratio;

        $processedData['localVideo']['overlayChild'] = $this->countOverlayChildren(
            $uid,
            (int)$processedData['data']['sys_language_uid']
        );
        $processedData['localVideo']['autoplay']  = $autoplay;
        $processedData['localVideo']['loop']      = $loop;
        $processedData['localVideo']['mute']      = $mute;
        $processedData['localVideo']['controls']  = (int)($flexconf['localControls'] ?? 0);

        return $processedData;
    }

    /**
     * CSS for the local video on mobile. "max-height" alone cannot grow anything - the height
     * comes from the inner ratio div (56.25% of the width) - so height is set and the video is
     * cropped with object-fit:cover. Breakpoint 767.98px: at 768px both branches applied at once.
     */
    private function buildMobileCss(int $uid, int $mobileWidth, int $mobileHeight, int $hShift): string
    {
        $figure = [];

        if ($mobileWidth > 0) {
            $figure[] = 'width:' . $mobileWidth . '%';
        }
        if ($mobileHeight > 0) {
            // min-height must be set too, otherwise the 200px floor from t3sbootstrap.css
            // wins - min-height beats height regardless of specificity, so a configured
            // value below 200 had no effect.
            $figure[] = 'height:' . $mobileHeight . 'px';
            $figure[] = 'min-height:' . $mobileHeight . 'px';
            $figure[] = 'max-height:' . $mobileHeight . 'px';
        }
        $figure[] = 'margin-left:' . $hShift . '%';

        $selector = '#s-' . $uid . ' figure.video';

        $css = '@media (max-width:767.98px){'
            . $selector . '{' . implode(';', $figure) . '}';

        if ($mobileHeight > 0) {
            $css .= $selector . '>.ratio{height:100%}'
                 .  $selector . '>.ratio::before{padding-top:0}'
                 .  $selector . ' video{object-fit:cover}';
        }

        return $css . '}';
    }

    /**
     * Parses an aspect ratio into "WxH" format - width first, as everywhere else in the
     * extension. The notations differ: this FlexForm's value picker stores height first
     * ("37by9" has the value "9/37") while the field description offers "37:9" as an
     * equivalent entry - unnormalised, the generated rule states the ratio upside down.
     *
     * Accepts "9/37" (H/W) as well as "37:9", "37by9" and "37x9" (W/H). Fallback: "16x9".
     *
     * @return array{0: string, 1: string}  [ratio-key, css-class-suffix]
     */
    private function resolveAspectRatio(string $raw): array
    {
        if (str_contains($raw, '/')) {
            [$h, $w] = explode('/', $raw, 2);
            // The picker writes height first ("9/37"), but "16/9" is what someone
            // types by hand. Every ratio offered here is landscape, so the larger
            // number is the width - that settles both spellings.
            if ((float)$h > (float)$w) {
                [$w, $h] = [$h, $w];
            }
        } elseif (str_contains($raw, ':')) {
            [$w, $h] = explode(':', $raw, 2);
        } elseif (str_contains($raw, 'by')) {
            [$w, $h] = explode('by', $raw, 2);
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
        $qb->setRestrictions(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));
        return (int) $qb
            ->count('uid')
            ->from('tt_content')
            ->where(
                $qb->expr()->eq('sys_language_uid', $qb->createNamedParameter($languageUid, Connection::PARAM_INT)),
                $qb->expr()->eq('tx_container_parent', $qb->createNamedParameter($parentUid, Connection::PARAM_INT))
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

        // overlayClass and bgColorOverlay moved to getProcessedData() so they apply
        // to every medium.
        $processedData['style']      .= $this->buildFilterStyle($flexconf);

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
