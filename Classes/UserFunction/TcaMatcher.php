<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\UserFunction;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;

/**
 * ConfigController
 */
class TcaMatcher
{

	/**
	 * autoLayoutParent
	 *
	 * @return bool
	 */
	public function autoLayoutParent($arguments): bool
	{
		$parent = false;
		if ( $arguments['record']['tx_container_parent'][0] ) {
			$uid = (int)$arguments['record']['tx_container_parent'][0];
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
			$result = $queryBuilder
				  ->select('*')
				  ->from('tt_content')
				  ->where(
					 $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
				  )
				  ->execute();
			$parent_rec = $result->fetch();

			if ( $parent_rec['CType'] == 'autoLayout_row' ) {
				$parent = true;
			}
		}

		return $parent;
	}

	/**
	 * toastContainerParent
	 *
	 * @return bool
	 */
	public function toastContainerParent($arguments): bool
	{
		$parent = true;
		if ( $arguments['record']['tx_container_parent'][0] ) {
			$uid = (int)$arguments['record']['tx_container_parent'][0];
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
			$result = $queryBuilder
				  ->select('*')
				  ->from('tt_content')
				  ->where(
					 $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
				  )
				  ->execute();
			$parent_rec = $result->fetch();
			if ( $parent_rec['CType'] == 'toast_container' ) {
				$parent = false;
			}
		}

		return $parent;
	}


	/**
	 * container_1 ($_EXTCONF['container'] in tt_content.php)
	 *
	 * @return bool
	 */
	public function container_1($arguments): bool
	{
		return true;
	}

	/**
	 * container_0 ($_EXTCONF['container'] in tt_content.php)
	 *
	 * @return bool
	 */
	public function container_0($arguments): bool
	{
		return false;
	}

	/**
	 * spacing_1 ($_EXTCONF['spacing'] in tt_content.php)
	 *
	 * @return bool
	 */
	public function spacing_1($arguments): bool
	{
		return true;
	}

	/**
	 * ratio ($_EXTCONF['ratio'] in tt_content.php)
	 *
	 * @return bool
	 */
	public function ratio_0($arguments): bool
	{
		return false;
	}


	/**
	 * ratio ($_EXTCONF['ratio'] in tt_content.php)
	 *
	 * @return bool
	 */
	public function ratio_1($arguments): bool
	{
		return true;
	}


	/**
	 * spacing_0 ($_EXTCONF['spacing'] in tt_content.php)
	 *
	 * @return bool
	 */
	public function spacing_0($arguments): bool
	{
		return false;
	}

	/**
	 * color_1 ($_EXTCONF['color'] in tt_content.php)
	 *
	 * @return bool
	 */
	public function color_1($arguments): bool
	{
		if ( $arguments['record']['CType'][0] == 'parallax_wrapper' ) {
				return false;
		} else {
				return true;
		}
	}

	/**
	 * color_0 ($_EXTCONF['color'] in tt_content.php)
	 *
	 * @return bool
	 */
	public function color_0($arguments): bool
	{
		return false;
	}

	/**
	 * is child of flex-container
	 *
	 * @return bool
	 */
	public function flexContainerParent($arguments): bool
	{
		$parent = false;

		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);

		if ( $arguments['record']['tx_container_parent'][0] ) {
			$uid = (int)$arguments['record']['tx_container_parent'][0];
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
			$result = $queryBuilder
				  ->select('*')
				  ->from('tt_content')
				  ->where(
					 $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
				  )
				  ->execute();
			$parent_rec = $result->fetch();
			$parent_flexconf = $flexformService->convertFlexFormContentToArray($parent_rec['tx_t3sbootstrap_flexform']);

			if ( $parent_rec['CType'] == 'container' && $parent_flexconf['flexContainer'] ) {
				$parent = true;
			}
		}

		return $parent;
	}

	/**
	 * is child of carousel-container and not owl
	 *
	 * @return bool
	 */
	public function flexCarouselParentNotOwl($arguments): bool
	{
		$parent = true;

		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);

		if ( $arguments['record']['tx_container_parent'][0] ) {
			$uid = (int)$arguments['record']['tx_container_parent'][0];
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
			$result = $queryBuilder
				  ->select('*')
				  ->from('tt_content')
				  ->where(
					 $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
				  )
				  ->execute();
			$parent_rec = $result->fetch();
			$parent_flexconf = $flexformService->convertFlexFormContentToArray($parent_rec['tx_t3sbootstrap_flexform']);

			if ( $parent_rec['CType'] == 'carousel_container' && ((int) $parent_flexconf['owlCarousel'] == 1 || (int) $parent_flexconf['multislider'] == 1) ) {
				$parent = false;
			}
		}

		return $parent;
	}

	/**
	 * is child of carousel-container and is owl style 1
	 *
	 * @return bool
	 */
	public function flexCarouselParentIsOwl($arguments): bool
	{
		$parent = false;

		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);

		if ( $arguments['record']['tx_container_parent'][0] ) {
			$uid = (int)$arguments['record']['tx_container_parent'][0];
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
			$result = $queryBuilder
				  ->select('*')
				  ->from('tt_content')
				  ->where(
					 $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
				  )
				  ->execute();
			$parent_rec = $result->fetch();
			$parent_flexconf = $flexformService->convertFlexFormContentToArray($parent_rec['tx_t3sbootstrap_flexform']);

			if ( $parent_rec['CType'] == 'carousel_container' && (int) $parent_flexconf['owlCarousel'] == 1 ) {
				$parent = true;
			}
		}

		return $parent;
	}

	/**
	 * isButton
	 *
	 * @return bool
	 */
	public function isButton($arguments): bool
	{
		$button = false;
		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);

		if ( $arguments['record']['tx_container_parent'][0] ) {
			$uid = (int)$arguments['record']['tx_container_parent'][0];
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
			$result = $queryBuilder
				  ->select('tx_t3sbootstrap_flexform')
				  ->from('tt_content')
				  ->where(
					 $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
				  )
				  ->execute();
			$parent_rec = $result->fetchAll();
			$flexconf = $flexformService->convertFlexFormContentToArray($parent_rec[0]['tx_t3sbootstrap_flexform']);
			if ( $flexconf['appearance'] == 'button' ) {
				$button = true;
			}
		}

		return $button;
	}

	/**
	 * isMenu
	 *
	 * @return bool
	 */
	public function isMenu($arguments): bool
	{
		$menu = false;
		if ($arguments['record']['CType'][0]) {
			if ( substr($arguments['record']['CType'][0], 0, 4) == 'menu' ) {
				$menu = true;
			}
		}

		return $menu;
	}

	/**
	 * animateCss
	 *
	 * @return bool
	 */
	public function animateCss($arguments): bool
	{
		$animateCss = false;
		$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
		if ( $extconf['animateCss'] ) {
			$animateCss = true;
		}

		return $animateCss;
	}

	/**
	 * isYoutube
	 *
	 * @return bool
	 */
	public function isYoutube($arguments): bool
	{
		$youtube = false;

		if (is_int($arguments['record']['uid'])) {

			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$fileObjects = $fileRepository->findByRelation('tt_content', 'assets', $arguments['record']['uid']);
			$file = $fileObjects[0] ? $fileObjects[0] : FALSE;

			if ($file) {
				if ( $file->getType() === 4 && ( $file->getMimeType() === 'video/youtube' || $file->getExtension() === 'youtube' ) ) {
					$youtube = true;
				}
			}
		}

		return $youtube;
	}


	/**
	 * isLocalVideo
	 *
	 * @return bool
	 */
	public function isLocalVideo($arguments): bool
	{
		$video = false;

		if (is_int($arguments['record']['uid'])) {

			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$fileObjects = $fileRepository->findByRelation('tt_content', 'assets', $arguments['record']['uid']);
			$file = $fileObjects[0] ? $fileObjects[0] : FALSE;

			if ($file) {
				if ( $file->getType() === 4 ) {
					if ( ( $file->getMimeType() === 'video/youtube' || $file->getExtension() === 'youtube' )
					 || $file->getMimeType() === 'video/vimeo' || $file->getExtension() === 'vimeo' ) {
						$video = false;
					} else {
						$video = true;
					}
				}
			}
		}

		return $video;
	}

	/**
	 * isNoMedia
	 *
	 * @return bool
	 */
	public function isNoMedia($arguments): bool
	{
		$media = false;

		if (is_int($arguments['record']['uid'])) {

			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$fileObjects = $fileRepository->findByRelation('tt_content', 'assets', $arguments['record']['uid']);
			$file = $fileObjects[0] ? $fileObjects[0] : FALSE;

			if (!$file) {
				$media = true;
			} else {
				if ( $file->getProperties()['hidden'] ) {
					$media = true;
				}
			}
		}

		return $media;
	}

	/**
	 * isImage
	 *
	 * @return bool
	 */
	public function isImage($arguments): bool
	{
		$image = false;

		if (is_int($arguments['record']['uid'])) {

			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$fileObjects = $fileRepository->findByRelation('tt_content', 'assets', $arguments['record']['uid']);
			$file = $fileObjects[0] ? $fileObjects[0] : FALSE;
			if ($file) {
				if ( $file->getType() === 2 && !$file->getProperties()['hidden'] ) {
					$image = true;
				}
			}
		}

		return $image;
	}


	/**
	 * isDropdownMenu
	 *
	 * @return bool
	 */
	public function isDropdownMenu($arguments): bool
	{
		$level = false;
		$pageRepository = GeneralUtility::makeInstance(PageRepository::class);
		$parentPage = $pageRepository->getPage($arguments['record']['pid']);
		if ($parentPage['is_siteroot']) {
			$level = true;
		}

		return $level;
	}


}
