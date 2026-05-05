<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;

class SwiperContainer implements SingletonInterface
{
    public function __construct(
        private readonly ConnectionPool               $connectionPool,
        private readonly FileRepository               $fileRepository,
        private readonly FrontendRestrictionContainer $frontendRestrictions,
    ) {}

    public function getProcessedData(array $processedData, array $flexconf): array
    {
        $processedData['swiperCss']        = $flexconf['swiperCss']       ?? '';
        $processedData['swiperJs']         = $flexconf['swiperJs']        ?? '';
        $processedData['customSwiperJs']   = $flexconf['customSwiperJs']  ?? '';
        $processedData['useCustomSwiperJs']= $flexconf['useCustomSwiperJs'] ?? false;
        $processedData['sliderStyle']      = $flexconf['sliderStyle']     ?? 'Default';
        $processedData['width']            = $flexconf['width']           ?? 1440;
        $processedData['ratio']            = $flexconf['ratio']           ?? '';
        $processedData['slidesPerView']    = (int)($flexconf['slidesPerView']  ?? 0);
        $processedData['breakpoints10']    = (int)($flexconf['breakpoints10']  ?? 1) ?: 1;
        $processedData['breakpoints576']   = (int)($flexconf['breakpoints576'] ?? 2) ?: 2;
        $processedData['breakpoints768']   = (int)($flexconf['breakpoints768'] ?? 3) ?: 3;
        $processedData['breakpoints992']   = (int)($flexconf['breakpoints992'] ?? 4) ?: 4;
        $processedData['slidesPerGroup']   = (int)($flexconf['slidesPerGroup'] ?? 1) ?: 1;
        $processedData['spaceBetween']     = (int)($flexconf['spaceBetween']   ?? 0);
        $processedData['loop']             = (int)!empty($flexconf['loop']);
        $processedData['zoom']             = (int)!empty($flexconf['zoom']);
        $processedData['navigation']       = (int)!empty($flexconf['navigation']);
        $processedData['pagination']       = (int)!empty($flexconf['pagination']);
        $processedData['autoplay']         = (int)!empty($flexconf['autoplay']);
        $processedData['origImage']        = (int)!empty($flexconf['origImage']);

        // delay nur sinnvoll wenn autoplay aktiv
        $processedData['delay'] = !empty($flexconf['autoplay']) && !empty($flexconf['delay'])
            ? (int)$flexconf['delay']
            : 0;

        $processedData['swiperSlides'] = $this->fetchSlideFiles(
            (int)$processedData['data']['uid']
        );

        return $processedData;
    }

    private function fetchSlideFiles(int $parentUid): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $qb->setRestrictions($this->frontendRestrictions);

        $rows = $qb
            ->select('uid')
            ->from('tt_content')
            ->where(
                $qb->expr()->eq(
                    'tx_container_parent',
                    $qb->createNamedParameter($parentUid, Connection::PARAM_INT)
                )
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchAllAssociative();

        $files = [];
        foreach ($rows as $row) {
            $files[$row['uid']] = $this->fileRepository
                ->findByRelation('tt_content', 'assets', $row['uid']);
        }

        return $files;
    }
}
