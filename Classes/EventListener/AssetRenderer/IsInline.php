<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\EventListener\AssetRenderer;

use TYPO3\CMS\Core\Page\Event\BeforeJavaScriptsRenderingEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Http\ApplicationType;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
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

		$configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
		$settings = $configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
			't3sbootstrap',
			'm1'
		);

		$t3sbconcatenate = $settings['t3sbconcatenate'];

		if ( $event->isInline() == FALSE ) {

			if ( $event->isPriority() == FALSE ) {
				// Java Scripts
				$jsCode = '';
				$faCode = '';
				if ( $t3sbconcatenate ) {
					foreach ( $event->getAssetCollector()->getJavaScripts() as $library => $jsFile ) {
						$jsCode .= '// *** T3SB identifier: '.$library.LF.LF;
						if ( $library == 'fontawesome' ) {
							if ( str_starts_with($jsFile['source'], 'http') ) {
								$faCode .= GeneralUtility::getURL($jsFile['source']).LF.LF;
							} else {
								if ( GeneralUtility::getFileAbsFileName($jsFile['source']) != FALSE ) {
									$faCode .= GeneralUtility::getURL(GeneralUtility::getFileAbsFileName($jsFile['source'])).LF.LF;
								}
							}
						} else {
							if ( str_starts_with($jsFile['source'], 'http') ) {
								$jsCode .= GeneralUtility::getURL($jsFile['source']).LF.LF;
							} else {
								if ( GeneralUtility::getFileAbsFileName($jsFile['source']) != FALSE ) {
									$jsCode .= GeneralUtility::getURL(GeneralUtility::getFileAbsFileName($jsFile['source'])).LF.LF;
								}
							}
						}

						$event->getAssetCollector()->removeJavaScript($library);
					}
				}

				if (!empty($jsCode) || !empty($faCode)) {

					if ( !empty($settings['t3sbminify']) && !empty($jsCode) ) {
						$url = 'https://www.toptal.com/developers/javascript-minifier/api/raw';
						$jsCode = self::minifyData($url, $jsCode);
					}
					// add Temp Java Scripts
					$file = self::inline2TempFile($faCode.$jsCode, 'js');
					if ($file) {
						$event->getAssetCollector()->addJavaScript('t3sbootstrapjsinline', $file);
					}
				}
			}

			return;
		}

		if ( $event->isPriority() == TRUE ) {

			$css = '';

			if ( $t3sbconcatenate ) {
				// Style Sheets
				foreach ($event->getAssetCollector()->getStyleSheets() as $library => $source) {
					$css .= LF.'/*** T3SB identifier: '.$library.' */'.LF;
					if (!empty($source['source'])) {
						if ( str_starts_with($source['source'], 'http') ) {
							$css .= GeneralUtility::getURL($source['source']).LF;
						} else {
							if ( GeneralUtility::getFileAbsFileName($source['source']) != FALSE ) {
								$css .= GeneralUtility::getURL(GeneralUtility::getFileAbsFileName($source['source'])).LF;
							}
						}
					}
					$event->getAssetCollector()->removeStyleSheet($library);
				}
			}

			// Inline Style Sheets
			foreach ($event->getAssetCollector()->getInlineStyleSheets() as $library => $source) {
				$css .= LF.'/*** T3SB identifier: '.$library.' */'.LF;
				$css .= $source['source'].LF.LF;
				$event->getAssetCollector()->removeInlineStyleSheet($library);
			}

			// add Temp Style Sheet
			if (!empty($css)) {

				if (!empty($settings['t3sbminify'])) {
					$url = 'https://www.toptal.com/developers/cssminifier/api/raw';
					$css = self::minifyData($url, $css);
				}

				$cssFile = self::inline2TempFile($css, 'css');
				if ($cssFile) {
					$event->getAssetCollector()->addStyleSheet('t3sbootstrapcss', $cssFile);
				}
			}

			return;

		} else {

			// Inline Java Scripts
			$assetJsInline = $event->getAssetCollector()->getInlineJavaScripts();
			$addheight ='';
			$jquery ='';
			$js = '';
			$function = '';

			foreach ($assetJsInline as $library => $source) {
				if (str_ends_with($library, 'function')) {
					$function .= $source['source'] .LF.LF;
				} elseif (str_starts_with($library, 'vanilla')) {
					$js .= $source['source'] .LF;
				} elseif ( str_starts_with($library, 'addheight-') ) {
					$addheight .= $source['source'] .LF.LF;
				} else {
					$jquery .= $source['source'] .LF.LF;
				}
				$event->getAssetCollector()->removeInlineJavaScript($library);
			}

			if ($addheight) {
				$addheight = "
	// Autoheight for background images
	var TYPO3 = TYPO3 || {};
	TYPO3.settings = {'ADDHEIGHT':{".rtrim(trim($addheight),",")."}};" .LF;
			}

			$source = '';
			if (!empty($function)) {
				$source .= $function.LF;
			}

			$source .= "function ready(fn) {".LF."	if (document.readyState != 'loading'){".LF."fn();".LF."	} else {".LF."document.addEventListener('DOMContentLoaded', fn);".LF."	}".LF."}".LF."ready(() => {".$addheight.$js."});".LF;

			if (!empty($jquery)) {
				$source .= LF."(function($){'use strict';".LF. $jquery .LF."})(jQuery);".LF;
			}

			if ( !empty($settings['t3sbminify']) ) {
				   $url = 'https://www.toptal.com/developers/javascript-minifier/api/raw';
				$source = self::minifyData($url, $source);
			}

			if (!empty($source)) {
				$jsFile = self::inline2TempFile($source, 'js');
				if ($jsFile) {
					$event->getAssetCollector()->addJavaScript('t3sbootstrapjs', $jsFile);
				}
			}
		}
	}


	/**
	  * Writes string to a temporary file named after the md5-hash of the string
	  *
	  * @param string $str CSS styles / JavaScript to write to file.
	  * @param string $ext Extension: "css" or "js
	  * @return string <script> or <link> tag for the file.
	  */
	public static function inline2TempFile($str, $ext)
	{
		 $script = '';
		 switch ($ext) {
			 case 'js':
				 $script = 'typo3temp/assets/t3sbootstrap_' . substr(md5($str), 0, 10) . '.js';
				 break;
			 case 'css':
				 $script = 'typo3temp/assets/t3sbootstrap_' . substr(md5($str), 0, 10) . '.css';
				 break;
		 }
		 if ($script) {
				 $pathSite = Environment::getPublicPath() . '/';
			 if (!@is_file($pathSite . $script)) {
				 GeneralUtility::writeFile($pathSite . $script, $str);
			 }
		 }
		 return $script;
	}


	public static function minifyData($url, $input)
	{
		 // init the request, set various options, and send it
		 $ch = curl_init();
		 curl_setopt_array($ch, [
			 CURLOPT_URL => $url,
			 CURLOPT_RETURNTRANSFER => true,
			 CURLOPT_POST => true,
			 CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
			 CURLOPT_POSTFIELDS => http_build_query([ "input" => $input ])
		 ]);

		 $output = curl_exec($ch);
		 // finally, close the request
		 curl_close($ch);

		if ( str_starts_with($output, 'error') ) {
			// input remains
			$output = $input;
		}

		 return $output;
	}


}
