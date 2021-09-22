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
// content consent w/ typoscript_rendering and jQuery
$contentConsentScript ="
	$('.ajaxSubmit').on('click', function(event) {
		var submit = $(this),
			uri = submit.data('ajaxuri'),
			currentRecord = submit.val();
		if ($('#preloader-'+currentRecord).length > 0) {
			$('#c'+currentRecord).css('position','relative');
			$('#preloader-'+currentRecord).css('display','block');
		}
		$.ajax(
			uri,
			{
				'type': 'post',
				'data': {currentRecord: currentRecord}
			}
		).done(function(result) {
			$('#ajax-result-'+currentRecord).removeClass('px-3');
			$('#ajax-result-'+currentRecord).html(result);

			if ($('#preloader-'+currentRecord).length > 0) {
				$('#preloader-'+currentRecord).css('display','none');
				$('#c'+currentRecord).removeAttr('style');
			}
			if ( lazyload > 0 ) {
				new LazyLoad({
					elements_selector: '.lazy',
					threshold: 0
				});
			}
		});
	});
";

			$assetJsInline = $event->getAssetCollector()->getInlineJavaScripts();
			$contentconsent ='';
			$addheight ='';
			$video ='';
			$jquery ='';
			$js = '';

			foreach ($assetJsInline as $library => $source) {

				if (substr($library, 0, 7) == 'vanilla' ) {
					$js .= $source['source'] .PHP_EOL;
					$event->getAssetCollector()->removeInlineJavaScript($library);
				} elseif ( GeneralUtility::isFirstPartOfStr($library, 'background-video-') ) {
					$video .= $source['source'] .PHP_EOL;
					$event->getAssetCollector()->removeInlineJavaScript($library);
				} elseif ( GeneralUtility::isFirstPartOfStr($library, 'addheight-') ) {
					$addheight .= $source['source'];
					$event->getAssetCollector()->removeInlineJavaScript($library);
				} elseif ( $library == 'contentconsent' ) {
					$contentconsent .= $source['source'];
					$event->getAssetCollector()->removeInlineJavaScript($library);
				} elseif ( GeneralUtility::isFirstPartOfStr($library, 'contentconsentthumbnailautosize-') ) {
					$contentconsentthumbnailautosize .= $source['source'];
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

			if ($contentconsent) {
				$contentconsent = $contentconsent == 'true' ? 1 : 0;
				$contentconsent = '	var lazyload = '.$contentconsent.';';
				$contentconsent = $contentconsent.PHP_EOL.$contentConsentScript.PHP_EOL;
				if ($contentconsentthumbnailautosize) {
					$contentconsent = $contentconsent.PHP_EOL.$contentconsentthumbnailautosize.PHP_EOL;
				}
			}

			$vanillaOnly = FALSE;
			if ( strlen((string)$jquery) + strlen((string)$contentconsent) < 1 ) {
				$vanillaOnly = TRUE;
			}

			if ($vanillaOnly) {
				$event->getAssetCollector()->addInlineJavaScript("t3sbInlineJS",
				 $video.PHP_EOL.PHP_EOL."function ready(fn) {".PHP_EOL."	if (document.readyState != 'loading'){".PHP_EOL."		fn();".PHP_EOL."	} else {".PHP_EOL."		document.addEventListener('DOMContentLoaded', fn);".PHP_EOL."	}".PHP_EOL."}".PHP_EOL."ready(() => {".$addheight.$js."});".PHP_EOL);
			} else {
				$event->getAssetCollector()->addInlineJavaScript("t3sbInlineJS",
				 $video.PHP_EOL.PHP_EOL."function ready(fn) {".PHP_EOL."	if (document.readyState != 'loading'){".PHP_EOL."		fn();".PHP_EOL."	} else {".PHP_EOL."		document.addEventListener('DOMContentLoaded', fn);".PHP_EOL."	}".PHP_EOL."}".PHP_EOL."ready(() => {".$addheight.$js."});".PHP_EOL.PHP_EOL."(function($){'use strict';".PHP_EOL. $jquery.$contentconsent .PHP_EOL."})(jQuery);".PHP_EOL);
			}
		}
	}
}