<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class SwiperContainer implements SingletonInterface
{
    /**
     * Returns the $processedData
     */
    public function getProcessedData(array $processedData, array $flexconf): array
    {
        $filesFromRepository = [];

        $processedData['swiperCss'] = !empty($flexconf['swiperCss']) ? $flexconf['swiperCss'] : '';
        $processedData['swiperJs'] = !empty($flexconf['swiperJs']) ? $flexconf['swiperJs'] : '';
        $processedData['customSwiperJs'] = !empty($flexconf['customSwiperJs']) ? $flexconf['customSwiperJs'] : '';
        $processedData['useCustomSwiperJs'] = !empty($flexconf['useCustomSwiperJs']) ? $flexconf['useCustomSwiperJs'] : false;
        $processedData['sliderStyle'] = $flexconf['sliderStyle'];
        $processedData['width'] = $flexconf['width'];
        $processedData['ratio'] = $flexconf['ratio'];
        $processedData['slidesPerView'] = !empty($flexconf['slidesPerView']) ? (int)$flexconf['slidesPerView'] : 0;
        $processedData['breakpoints10'] = !empty($flexconf['breakpoints10']) ? (int)$flexconf['breakpoints10'] : 1;
        $processedData['breakpoints576'] = (int)$flexconf['breakpoints576'] ?: 2;
        $processedData['breakpoints768'] = (int)$flexconf['breakpoints768'] ?: 3;
        $processedData['breakpoints992'] = (int)$flexconf['breakpoints992'] ?: 4;
        $processedData['slidesPerGroup'] = (int)$flexconf['slidesPerGroup'] ?: 1;
        $processedData['spaceBetween'] = (int)$flexconf['spaceBetween'];
        $processedData['loop'] = (int)$flexconf['loop'];
        $processedData['zoom'] = !empty($flexconf['zoom']) ? (int)$flexconf['zoom'] : 0;
        $processedData['navigation'] = (int)$flexconf['navigation'];
        $processedData['pagination'] = (int)$flexconf['pagination'];
        $processedData['autoplay'] = (int)$flexconf['autoplay'];
        $processedData['origImage'] = !empty($flexconf['origImage']) ? $flexconf['origImage'] : '';
        $processedData['delay'] = 0;
        if (!empty($flexconf['delay'])) {
            $processedData['delay'] = !empty($flexconf['autoplay']) ? (int)$flexconf['delay'] : 99999999;
        }

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
        $statement = $queryBuilder
            ->select('uid')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($processedData['data']['uid'], Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        foreach ($statement as $element) {
            $filesFromRepository[$element['uid']] = $fileRepository->findByRelation('tt_content', 'assets', $element['uid']);
        }
        $processedData['swiperSlides'] = $filesFromRepository;

        return $processedData;
    }
}
