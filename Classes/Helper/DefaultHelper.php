<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class DefaultHelper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getContainerClass(array $processedData, string $extConfContainer): array
	{
		$container = '';
		if ($extConfContainer && $processedData['data']['tx_t3sbootstrap_container']) {
			if ( $processedData['data']['tx_container_parent'] === 0 ) {
				$t3sbconfig = self::getConfig();
				if ( $t3sbconfig['footer_pid'] === $processedData['data']['pid'] ) {
					if ( $t3sbconfig['footer_container'] === 'none' && $processedData['data']['colPos'] === 0 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
				} else {
					$pageContainer = self::getFrontendController()->page['tx_t3sbootstrap_container'] ? TRUE : FALSE;
					if ( $pageContainer === FALSE && $processedData['data']['colPos'] === 0 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
					if ( $t3sbconfig['jumbotron_container'] === 'none' && $processedData['data']['colPos'] === 3 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
					if ( $t3sbconfig['expandedcontent_containertop'] === 'none' && $processedData['data']['colPos'] === 20 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
					if ( $t3sbconfig['expandedcontent_containerbottom'] === 'none' && $processedData['data']['colPos'] === 21 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
					if ( $t3sbconfig['footer_container'] === 'none' && $processedData['data']['colPos'] === 4 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
				}
			}
		}
		if ($container) {
			$processedData['containerPre'] = '<div class="'.trim($container).'">';
			$processedData['containerPost'] = '</div>';
			$processedData['container'] = trim($container);
		} else {
			if ($processedData['be_layout'] === 'OneCol') {
				$processedData['containerError'] = self::getContainerError($processedData['data'], $container);
			}
		}

		return $processedData;
	}


	/**
	 * Returns the Container Error
	 */
	public function getContainerError(array $data, string $container): bool
	{
		$error = FALSE;
		if ($container) {
			if ( $data['tx_container_parent'] === 0 ) {
				$t3sbconfig = self::getConfig();
				if ( $t3sbconfig['footer_pid'] === $data['pid'] ) {
					if ( $t3sbconfig['footer_container'] === 'none' && $data['colPos'] === 0 ) {
						$error = TRUE;
					}
				} else {
					$pageContainer = self::getFrontendController()->page['tx_t3sbootstrap_container'] ? TRUE : FALSE;
					if ( $pageContainer === FALSE && $data['colPos'] === 0 ) {
						$error = TRUE;
					}
					if ( $t3sbconfig['jumbotron_container'] === 'none' && $data['colPos'] === 3 ) {
						$error = TRUE;
					}
					if ( $t3sbconfig['expandedcontent_containertop'] === 'none' && $data['colPos'] === 20 ) {
						$error = TRUE;
					}
					if ( $t3sbconfig['expandedcontent_containerbottom'] === 'none' && $data['colPos'] === 21 ) {
						$error = TRUE;
					}
					if ( $t3sbconfig['footer_container'] === 'none' && $data['colPos'] === 4 ) {
						$error = TRUE;
					}
				}
			}
		}

		return $error;
	}


	/**
	 * Returns the $processedData
	 */
	public function getDefaults(
		array $processedData,
		array $flexconf,
		array $extConf,
		int $defaultHeaderType,
		string $contentMarginTop,
		string $animateCss,
		string $parentCType
	): array
	{
		$cType = $processedData['data']['CType'];

		// default header type
		switch ( $cType ) {
			case 't3sbs_card':
				$processedData['header']['default'] = 4;
				break;
			case 't3sbs_mediaobject':
				$processedData['header']['default'] = 5;
				break;
			default:
				$processedData['header']['default'] = $defaultHeaderType;
		}

		// content element link
		$flexconf['bgwlink'] = !empty($flexconf['bgwlink']) ? $flexconf['bgwlink'] : '';

		if ( ($processedData['data']['tx_t3sbootstrap_header_celink'] && $processedData['data']['header_link'])
			|| (!empty($flexconf['bgwlink']) && $processedData['data']['header_link']) ) {


			if ( $cType == 't3sbs_card' ) {
				if (!empty($flexconf['button']['enable'])) {
					$processedData['card']['button']['link'] = $processedData['data']['header_link'];
				}
			}
			if ( $parentCType != 'listGroup_wrapper' ) {
				$processedData['class'] .= ' ce-link-content';
			}
			$processedData['celink'] = $processedData['data']['header_link'];
			$processedData['data']['header_link'] = '';
			// no image zoom if ce-link (did not work)
			$processedData['data']['image_zoom'] = '';
			$processedData['addmedia']['imagezoom'] = '';
		}
		// animate css for all CEs exept t3sbs_carousel & collapsible_accordion
		if ($animateCss && (!empty($processedData['data']['tx_t3sbootstrap_animateCss']) || !empty($flexconf['animate']))
		 && $cType != 't3sbs_carousel' && $cType != 'collapsible_accordion')
		{
			$processedData['isAnimateCss'] = TRUE;
			if ( !empty($processedData['data']['tx_t3sbootstrap_animateCss']) ) {
				$processedData['class'] .= ' animated '.$processedData['data']['tx_t3sbootstrap_animateCss'];
				if( $processedData['data']['tx_t3sbootstrap_animateCssRepeat'] ) {
					$processedData['dataAnimate'] = $processedData['data']['tx_t3sbootstrap_animateCss'];
					$processedData['class'] .= ' bt_hidden';
					$processedData['animateCssRepeat'] = TRUE;
				}
				if ($processedData['data']['tx_t3sbootstrap_animateCssDuration'] ) {
					$processedData['style'] .= ' animation-duration: '.$processedData['data']['tx_t3sbootstrap_animateCssDuration'].'s;';
				}
				if ($processedData['data']['tx_t3sbootstrap_animateCssDelay'] ) {
					$processedData['style'] .= ' animation-delay: '.$processedData['data']['tx_t3sbootstrap_animateCssDelay'].'s;';
				}
			}
		}

		// extend flexforms with custom fields
		$flexconf['ffExtra'] = $flexconf['ffExtra'] ?? '';
		if ( is_array($flexconf['ffExtra']) ) {
			$processedData['ffExtra'] = $flexconf['ffExtra'];
		}

		# default margin-top for each content-element if no margin-top
		$hasMarginTop = strpos($processedData['class'], 'mt-') || strpos($processedData['class'], 'my-') || strpos($processedData['class'], 'm-');
		if ($contentMarginTop && $processedData['data']['colPos'] == 0 && $hasMarginTop == FALSE ) {
			$processedData['class'] .= ' '.$contentMarginTop;
		}

		return $processedData;
	}


	/**
	 * Returns the t3sb configuration
	 */
	protected function getConfig(): array
	{
		$settings = [];
		$configurationManager = GeneralUtility::makeInstance(BackendConfigurationManager::class);
		$typoScriptSetup = $configurationManager->getTypoScriptSetup();
		foreach ( $typoScriptSetup['page.']['10.']['settings.']['config.'] as $key=>$config ) {
			$settings[GeneralUtility::camelCaseToLowerCaseUnderscored($key)] = $config;
		}

		return $settings;
	}


	/**
	 * Returns the frontend controller
	 */
	protected function getFrontendController(): TypoScriptFrontendController
	{
		return $GLOBALS['TSFE'];
	}
}
