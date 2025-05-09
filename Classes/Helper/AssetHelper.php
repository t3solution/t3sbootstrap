<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;


/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class AssetHelper implements SingletonInterface
{
	/**
	 * addStyleSheet
	 */
	public function addCSS(array $cssfiles): void
	{
		$assetCollector = GeneralUtility::makeInstance(AssetCollector::class);
		$end = '';
		foreach ($cssfiles as $cssfile) {
			$basePath = $cssfile->getStorage()->getConfiguration()['basePath'];
			$end = substr($basePath, -1);
			if ($end === '/') {
				$basePath = substr($basePath, 0, -1);
			}
			$identifier = $cssfile->getIdentifier();
			$assetCollector->addStyleSheet($cssfile->getName(), $basePath.$identifier);
		}
	}


	/**
	 * addJavaScript
	 */
	public function addJS(array $jsfiles, int $priority=0): void
	{
		$assetCollector = GeneralUtility::makeInstance(AssetCollector::class);
		$end = '';
		foreach ($jsfiles as $jsfile) {
			$basePath = $jsfile->getStorage()->getConfiguration()['basePath'];
			$end = substr($basePath, -1);
			if ($end === '/') {
				$basePath = substr($basePath, 0, -1);
			}
			$identifier = $jsfile->getIdentifier();
			if ( !empty($priority) ) {
				$assetCollector->addJavaScript($jsfile->getName(), $basePath.$identifier, [], $options = ['priority' => true]);
			} else {
				$assetCollector->addJavaScript($jsfile->getName(), $basePath.$identifier);
			}
		}
	}

}
