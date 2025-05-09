<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class FlexFormManipulation implements FormDataProviderInterface
{

	/**
	 * Add or remove options to select-fields in flexforms
	 *
	 */
	public function addData(array $result): array
	{

		$configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $setup = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,
            't3sbootstrap',
            'm1'
        );

		$flexforms = !empty($setup['plugin.']['tx_t3sbootstrap.']['flexform.']) ? $setup['plugin.']['tx_t3sbootstrap.']['flexform.'] : [];

		# if FlexFormManipulation
		if ( !empty($flexforms) && !empty($result['databaseRow']['CType']) ) {

			$flexformFile = match ($result['databaseRow']['CType']) {
				't3sbs_card' => 'cardSetting.',
				't3sbs_carousel' => 'carousel.',
				't3sbs_button' => 'button.',
				't3sbs_mediaobject' => 'mediaobject.',
				'table' => 'table.',
				'card_wrapper' => 'cardWrapper.',
				'autoLayout_row' => 'autoLayoutRow.',
				'button_group' => 'buttongroup.',
				'container' => 'container.',
				'two_columns' => 'twoColumns.',
				'three_columns' => 'threeColumns.',
				'four_columns' => 'fourColumns.',
				'six_columns' => 'sixColumns.',
				'background_wrapper' => 'backgroundWrapper.',
				'parallax_wrapper' => 'parallaxWrapper.',
				'carousel_container' => 'carouselContainer.',
				'collapsible_accordion' => 'collapse.',
				'collapsible_container' => 'collapseContainer.',
				'modal' => 'modal.',
				'tabs_container' => 'tabs.',
				'tabs_tab' => 'tabsTab.',
				default => 'bootstrap.',
			};

			$dataStructure = [];

			if (!empty($result['processedTca']['columns']['tx_t3sbootstrap_flexform'])) {

				$dataStructure = $result['processedTca']['columns']['tx_t3sbootstrap_flexform']['config']['ds'];
	
				$addArr = [];
	
				foreach ($flexforms as $file=>$fields) {
					if ( $file === $flexformFile ) {
						foreach ($fields as $field=>$mod) {
							if ( !empty($mod['add']) ) {
								foreach (explode(',',$mod['add']) as $add) {
									if (!empty($dataStructure['sheets'])) {
										foreach ($dataStructure['sheets'] as $sheetName=>$fieldsInSheet) {
											foreach ($fieldsInSheet as $fieldArr) {
												foreach ($fieldArr as $fieldName) {
													if (!empty($fieldName) && $fieldName !== 'array' && is_array($fieldName)) {
														foreach ($fieldName as $key=>$name) {
															if (!empty($name['config']['type'])
																 && $name['config']['type'] === 'select'
																 && substr($field, 0, -1) === $key) {
																	$addArr = ['label' => trim($add), 'value' => lcfirst(GeneralUtility::underscoredToUpperCamelCase(trim($add)))];
																	$dataStructure['sheets'][$sheetName]['ROOT']['el'][$key]['config']['items'][] = $addArr;
															}
														}
													}
												}
											}
										}
									}
								}
							}
							if ($mod['reduce']) {
								foreach( explode(',',$mod['reduce']) as $reduce ) {
									if (!empty($dataStructure['sheets'])) {
										foreach ($dataStructure['sheets'] as $sheetName=>$fieldsInSheet) {
											foreach ($fieldsInSheet as $fieldArr) {
												foreach ($fieldArr as $fieldName) {
													if (!empty($fieldName) && $fieldName !== 'array' && is_array($fieldName)) {
														foreach ($fieldName as $key=>$name) {
															if (!empty($name['config']['type']) && $name['config']['type'] === 'select') {
																if (substr($field, 0, -1) == $key) {
																	foreach ($name['config']['items'] as $k=>$item ) {
																		if (!empty($item['value']) && trim($item['value']) === trim($reduce)) {
																			unset($dataStructure['sheets'][$sheetName]['ROOT']['el'][$key]['config']['items'][$k]);
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			$result['processedTca']['columns']['tx_t3sbootstrap_flexform']['config']['ds'] = $dataStructure;
		}

		return $result;
	}
}
