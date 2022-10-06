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
class CarouselContainer implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{
		$processedData['maxWidth'] = $flexconf['width'] ? $flexconf['width'].'px' : '1440px';
		$processedData['interval'] = $flexconf['interval'];
		$processedData['darkVariant'] = $flexconf['darkVariant'];
		$processedData['carouselFade'] = !empty($flexconf['carouselFade']) ? ' carousel-fade': '';
		$processedData['carouselFade'] .= !empty($flexconf['darkVariant']) ? ' carousel-dark': '';
		$processedData['thumbnails'] = !empty($flexconf['thumbnails']) ? true : false;
		$processedData['mobileIndicators'] = !empty($flexconf['mobileIndicators']) ? '' : ' d-none d-md-block';

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
		$carouselSlides = [];
		foreach($statement as $element) {
			$file = $fileRepository->findByRelation('tt_content', 'assets', $element['uid']);
			if ( !empty($file) ) {
				if ($file[0]->getMimeType() == 'video/mp4' || $file[0]->getMimeType() == 'video/webm' || $file[0]->getMimeType() == 'video/wav'
				 || $file[0]->getMimeType() == 'video/ogg' || $file[0]->getMimeType() == 'video/flac' || $file[0]->getMimeType() == 'video/opus') {
					$processedData['containsVideo'] = TRUE;
				}
			}
			if (!empty($file[0])) {
				$carouselSlides[$element['uid']] = $file[0];
			} else {
				$carouselSlides[$element['uid']] = '';				
			}
		}

		$processedData['carouselSlides'] = !empty($carouselSlides) ? $carouselSlides : '';

		return $processedData;
	}

}
