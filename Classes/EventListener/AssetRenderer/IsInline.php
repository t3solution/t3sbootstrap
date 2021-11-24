<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\EventListener\AssetRenderer;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Page\Event\BeforeJavaScriptsRenderingEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Http\ApplicationType;


class IsInline
{

	public function __invoke(BeforeJavaScriptsRenderingEvent $event): void
	{
		if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
			&& ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
			return;
		}

		if (defined('TYPO3') && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
			return;
		}

		if ( $event->isInline() == FALSE ) {
			return;
		}
		# CSS in header
		if ( $event->isPriority() == TRUE ) {
			$assetCssInline = $event->getAssetCollector()->getInlineStyleSheets();
			$css = '';
			foreach ($assetCssInline as $library => $source) {
				$css .= $source['source']. ' ';
				$event->getAssetCollector()->removeInlineStyleSheet($library);
			}
			if ( $css )
			$event->getAssetCollector()->addInlineStyleSheet('t3sbInlineCSS', $css, [], ['priority' => true]);

			return;
		} else {

			$assetJsInline = $event->getAssetCollector()->getInlineJavaScripts();
			$addheight ='';
			$video ='';
			$jquery ='';
			$js = '';

			foreach ($assetJsInline as $library => $source) {

				if (substr($library, 0, 7) == 'vanilla' ) {
					$js .= $source['source'] .PHP_EOL;
					$event->getAssetCollector()->removeInlineJavaScript($library);
				} elseif ( str_starts_with($library, 'addheight-') ) {
					$addheight .= $source['source'];
					$event->getAssetCollector()->removeInlineJavaScript($library);
				} else {
					$jquery .= $source['source'] .PHP_EOL;
					$event->getAssetCollector()->removeInlineJavaScript($library);
				}
			}

			if ($addheight) {
				$addheight = "
	// Autoheight for background images
	var TYPO3 = TYPO3 || {};
	TYPO3.settings = {'ADDHEIGHT':{".rtrim(trim($addheight),",")."}};" .PHP_EOL;
			}


	$vanillaOnly = FALSE;
	if ( strlen((string)$jquery) < 1 ) {
		$vanillaOnly = TRUE;
	}

			if ($vanillaOnly) {
				$event->getAssetCollector()->addInlineJavaScript("t3sbInlineJS",
				 $video.PHP_EOL.PHP_EOL."function ready(fn) {".PHP_EOL."	if (document.readyState != 'loading'){".PHP_EOL."		fn();".PHP_EOL."	} else {".PHP_EOL."		document.addEventListener('DOMContentLoaded', fn);".PHP_EOL."	}".PHP_EOL."}".PHP_EOL."ready(() => {".$addheight.$js."});".PHP_EOL);
			} else {
				$event->getAssetCollector()->addInlineJavaScript("t3sbInlineJS",
				 $video.PHP_EOL.PHP_EOL."function ready(fn) {".PHP_EOL."	if (document.readyState != 'loading'){".PHP_EOL."		fn();".PHP_EOL."	} else {".PHP_EOL."		document.addEventListener('DOMContentLoaded', fn);".PHP_EOL."	}".PHP_EOL."}".PHP_EOL."ready(() => {".$addheight.$js."});".PHP_EOL.PHP_EOL."(function($){'use strict';".PHP_EOL. $jquery .PHP_EOL."})(jQuery);".PHP_EOL);
			}
		}
	}
}