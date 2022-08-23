<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Resource\FileRepository;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class CardWrapper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{
		$processedData['visibleCards'] = !empty($flexconf['visibleCards']) ? (int)$flexconf['visibleCards'] : 4;
		$processedData['gutter'] = !empty($flexconf['gutter']) ? (int)$flexconf['gutter'] : 0;

		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$queryBuilder->setRestrictions(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));
		$children = $queryBuilder
			->select('*')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($processedData['data']['uid'], \PDO::PARAM_INT))
			)
			->orderBy('sorting')
			->execute()
			->fetchAll();

		$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);

		$processedData['cropMaxCharacters'] = $flexconf['cropMaxCharacters'];

		if (count($children)) {

			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);

			foreach ( $children as $key=>$child ) {
				$fileObjects = $fileRepository->findByRelation('tt_content', 'assets', $child['uid']);
				$children[$key] = $flexFormService->convertFlexFormContentToArray($child['pi_flexform']);
				$children[$key]['imgwidth'] = $child['imagewidth'] ?: 576;
				if (!empty($fileObjects)) {
					if ($flexconf['card_wrapper'] == 'flipper'){
						$children[$key]['hFa'] = $child['tx_t3sbootstrap_header_fontawesome']
						 ? '<i class="'.$child['tx_t3sbootstrap_header_fontawesome'].' me-1"></i> ' : '';
						$children[$key]['file'] = $fileObjects;
						$children[$key]['backheader'] = $children[$key]['header']['text'];
						$children[$key]['header'] = $child['header'];
					} else {
						$children[$key]['file'] = $fileObjects[0];
						$children[$key]['header'] = $child['header'];
					}
				}
				$children[$key]['ratio'] = $child['tx_t3sbootstrap_image_ratio'] ?: '0';
				$children[$key]['uid'] = $child['uid'];

				$children[$key]['subheader'] = $child['subheader'];
				$children[$key]['header_link'] = $child['header_link'];
				$children[$key]['header_position'] = $child['header_position'] ? ' text-'.$child['header_position'] :'';
				$children[$key]['tx_t3sbootstrap_header_display'] = $child['tx_t3sbootstrap_header_display'];
				$children[$key]['tx_t3sbootstrap_header_class'] = $child['tx_t3sbootstrap_header_class'];
				$children[$key]['tx_t3sbootstrap_header_fontawesome'] = $child['tx_t3sbootstrap_header_fontawesome'];
				$children[$key]['celink'] = $child['tx_t3sbootstrap_header_celink'];

				$children[$key]['settings'] = $flexFormService->convertFlexFormContentToArray($child['tx_t3sbootstrap_flexform']);
			}
			$processedData['cards'] = $children;

			if ($flexconf['card_wrapper'] == 'flipper') {

				switch ( count($children) ) {
					 case 1:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-12 col-md-12';
					break;
					 case 2:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-6';
					break;
					 case 3:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-4';
					break;
					 case 4:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-3';
					break;
					 case 6:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-2';
					break;
					 default:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-4';
				}
			}
			// swiperjs
			if ($flexconf['card_wrapper'] == 'slider') {
				$processedData['visibleCards'] = (int)$flexconf['visibleCards'] ?: 3;
				$processedData['cols'] = floor(12 / $processedData['visibleCards']);
				$processedData['width'] = $flexconf['width'];
				$processedData['ratio'] = $flexconf['ratio'];
				$processedData['slidesPerView'] = (int)$flexconf['slidesPerView'] ?: 4;
				$processedData['breakpoints576'] = (int)$flexconf['breakpoints576'] ?: 2;
				$processedData['breakpoints768'] = (int)$flexconf['breakpoints768'] ?: 3;
				$processedData['breakpoints992'] = (int)$flexconf['breakpoints992'] ?: 4;
				$processedData['slidesPerGroup'] = (int)$flexconf['slidesPerGroup'];
				$processedData['spaceBetween'] = (int)$flexconf['spaceBetween'];
				$processedData['loop'] = (int)$flexconf['loop'];
				$processedData['navigation'] = (int)$flexconf['navigation'];
				$processedData['pagination'] = (int)$flexconf['pagination'];
				$processedData['autoplay'] = (int)$flexconf['autoplay'];
				$processedData['delay'] = $flexconf['autoplay'] ? (int)$flexconf['delay'] : 99999999;
			}

			if ($flexconf['card_wrapper'] == 'deck' && $flexconf['equalHeight']) {
				$processedData['equalHeight'] = ' d-flex align-items-stretch';
			}
		}

		$processedData['card_wrapper_layout'] = $flexconf['card_wrapper'] ?: '';

		return $processedData;
	}

}
