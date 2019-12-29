<?php
namespace T3SBS\T3sbootstrap\UserFunction;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;

use TYPO3\CMS\Core\Resource\FileRepository;


/**
 * ConfigController
 */
class TcaMatcher
{

	/**
	 * autoLayoutParent
	 *
	 * @return void
	 */
	public function autoLayoutParent($arguments)
	{

		$parent = false;
		if ( $arguments['record']['tx_gridelements_container'][0] ) {
			$uid = (int)$arguments['record']['tx_gridelements_container'][0];
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
			$result = $queryBuilder
				  ->select('*')
				  ->from('tt_content')
				  ->where(
					 $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
				  )
				  ->execute();
			$parent_rec = $result->fetchAll();
			if ( $parent_rec[0]['tx_gridelements_backend_layout'] == 'autoLayout_row' ) {
				$parent = true;
			}
		}

		return $parent;
	}

	/**
	 * container_1 ($_EXTCONF['container'] in tt_content.php)
	 *
	 * @return void
	 */
	public function container_1($arguments)
	{
		return true;
	}

	/**
	 * container_0 ($_EXTCONF['container'] in tt_content.php)
	 *
	 * @return void
	 */
	public function container_0($arguments)
	{
		if ($arguments['record']['tx_gridelements_backend_layout'][0] == 'container') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * spacing_1 ($_EXTCONF['spacing'] in tt_content.php)
	 *
	 * @return void
	 */
	public function spacing_1($arguments)
	{
		return true;
	}

	/**
	 * ratio ($_EXTCONF['ratio'] in tt_content.php)
	 *
	 * @return void
	 */
	public function ratio_0($arguments)
	{
		return false;
	}


	/**
	 * ratio ($_EXTCONF['ratio'] in tt_content.php)
	 *
	 * @return void
	 */
	public function ratio_1($arguments)
	{
		return true;
	}


	/**
	 * spacing_0 ($_EXTCONF['spacing'] in tt_content.php)
	 *
	 * @return void
	 */
	public function spacing_0($arguments)
	{
		return false;
	}

	/**
	 * color_1 ($_EXTCONF['color'] in tt_content.php)
	 *
	 * @return void
	 */
	public function color_1($arguments)
	{

		if ( $arguments['record']['tx_gridelements_backend_layout'][0] == 'parallax_wrapper' ) {

				return false;
		} else {

				return true;
		}

	}

	/**
	 * color_0 ($_EXTCONF['color'] in tt_content.php)
	 *
	 * @return void
	 */
	public function color_0($arguments)
	{
		return false;
	}

	/**
	 * is child of flex-container
	 *
	 * @return void
	 */
	public function flexContainerParent($arguments)
	{
		$parent = false;

		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
		if ( $arguments['record']['tx_gridelements_container'][0] ) {
			$uid = (int)$arguments['record']['tx_gridelements_container'][0];
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
			$result = $queryBuilder
				  ->select('*')
				  ->from('tt_content')
				  ->where(
					 $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
				  )
				  ->execute();
			$parent_rec = $result->fetchAll();
			$flexconf = $flexformService->convertFlexFormContentToArray($parent_rec[0]['tx_t3sbootstrap_flexform']);
			if ( $parent_rec[0]['tx_gridelements_backend_layout'] == 'container' && $flexconf['flexContainer'] ) {
				$parent = true;
			}
		}

		return $parent;
	}


	/**
	 * is carouselRatioParent
	 *
	 * @return void
	 */
	public function carouselRatioParent($arguments)
	{
		$parent = false;
		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);

		if ( $arguments['record']['tx_gridelements_container'][0] ) {
			$uid = (int)$arguments['record']['tx_gridelements_container'][0];
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
			$result = $queryBuilder
				  ->select('*')
				  ->from('tt_content')
				  ->where(
					 $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
				  )
				  ->execute();
			$parent_rec = $result->fetchAll();

			$flexconf = $flexformService->convertFlexFormContentToArray($parent_rec[0]['tx_t3sbootstrap_flexform']);

			$parent = $flexconf['ratio'] ? true : false;
		}

		return $parent;
	}


	/**
	 * isButton
	 *
	 * @return void
	 */
	public function isButton($arguments)
	{
		$button = false;
		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
		if ( $arguments['record']['tx_gridelements_container'][0] ) {
			$uid = (int)$arguments['record']['tx_gridelements_container'][0];
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
			$result = $queryBuilder
				  ->select('*')
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
	 * @return void
	 */
	public function isMenu($arguments)
	{
		$menu = false;
		if ( substr($arguments['record']['CType'][0], 0, 4) == 'menu' ) {
			$menu = true;
		}

		return $menu;
	}

	/**
	 * animateCss
	 *
	 * @return void
	 */
	public function animateCss($arguments)
	{
		$animateCss = false;

		$extconf = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('t3sbootstrap');

		if ( $extconf['animateCss'] ) {

			$animateCss = true;

		}

		return $animateCss;
	}

	/**
	 * isYoutube
	 *
	 * @return void
	 */
	public function isYoutube($arguments)
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
	 * @return void
	 */
	public function isLocalVideo($arguments)
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
	 * @return void
	 */
	public function isNoMedia($arguments)
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
	 * @return void
	 */
	public function isImage($arguments)
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


}
