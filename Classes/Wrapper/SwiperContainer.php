<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;

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
		$processedData['swiperJs'] = $flexconf['swiperJs'] ?? '';
		$processedData['sliderStyle'] = $flexconf['sliderStyle'];
		$processedData['width'] = $flexconf['width'];
		$processedData['ratio'] = $flexconf['ratio'];
		$processedData['slidesPerView'] = (int)$flexconf['slidesPerView'] ?: 4;
		$processedData['breakpoints576'] = (int)$flexconf['breakpoints576'] ?: 2;
		$processedData['breakpoints768'] = (int)$flexconf['breakpoints768'] ?: 3;
		$processedData['breakpoints992'] = (int)$flexconf['breakpoints992'] ?: 4;
		$processedData['slidesPerGroup'] = (int)$flexconf['slidesPerGroup'];
		$processedData['spaceBetween'] = (int)$flexconf['spaceBetween'];
		$processedData['loop'] = (int)$flexconf['loop'];
		$processedData['zoom'] = (int)$flexconf['zoom'];
		$processedData['navigation'] = (int)$flexconf['navigation'];
		$processedData['pagination'] = (int)$flexconf['pagination'];
		$processedData['autoplay'] = (int)$flexconf['autoplay'];
		$processedData['delay'] = !empty($flexconf['autoplay']) ? (int)$flexconf['delay'] : 99999999;


$processedData['origImage'] = $flexconf['origImage'] ?? '';


		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$statement = $queryBuilder
			->select('uid')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($processedData['data']['uid'], \PDO::PARAM_INT))
			)
			->execute()
			->fetchAll();
		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
		foreach($statement as $element) {
			$filesFromRepository[$element['uid']] = $fileRepository->findByRelation('tt_content', 'assets', $element['uid']);
		}
		$processedData['swiperSlides'] = $filesFromRepository;

		return $processedData;
	}

}
