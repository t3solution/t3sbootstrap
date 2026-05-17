<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;

class AssetHelper implements SingletonInterface
{
	
	public function __construct(
		private readonly AssetCollector $assetCollector,
		
	) {}
	
	
	public function addCSS(array $cssfiles): void
	{
		$end = '';
		foreach ($cssfiles as $cssfile) {
			$basePath = $cssfile->getStorage()->getConfiguration()['basePath'];
			$end = substr($basePath, -1);
			if ($end === '/') {
				$basePath = substr($basePath, 0, -1);
			}
			$identifier = $cssfile->getIdentifier();
			// @extensionScannerIgnoreLine
			$this->assetCollector->addStyleSheet($cssfile->getName(), $basePath.$identifier);
		}
	}


	/**
	 * addJavaScript
	 */
	public function addJS(array $jsfiles, int $priority=0): void
	{
		$end = '';
		foreach ($jsfiles as $jsfile) {
			$basePath = $jsfile->getStorage()->getConfiguration()['basePath'];
			$end = substr($basePath, -1);
			if ($end === '/') {
				$basePath = substr($basePath, 0, -1);
			}
			$identifier = $jsfile->getIdentifier();
			if ( !empty($priority) ) {
				$this->assetCollector->addJavaScript($jsfile->getName(), $basePath.$identifier, [], $options = ['priority' => true]);
			} else {
				$this->assetCollector->addJavaScript($jsfile->getName(), $basePath.$identifier);
			}
		}
	}

}
