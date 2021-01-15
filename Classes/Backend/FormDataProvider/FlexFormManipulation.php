<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\FormDataProvider;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;

/**
 * Fill the news records with default values
 */
class FlexFormManipulation implements FormDataProviderInterface
{

	/**
	 * Add or remove options to select-fields in flexforms
	 *
	 * @param array $result
	 * @return array
	 */
	public function addData(array $result)
	{
		$configurationManager =GeneralUtility::makeInstance(BackendConfigurationManager::class);
		$configurationManager->getDefaultBackendStoragePid();
		$setup = $configurationManager->getTypoScriptSetup();

		$flexforms = $setup['plugin.']['tx_t3sbootstrap.']['flexform.'];

		# if FlexFormManipulation
		if ( is_array($flexforms) ) {

			$cType = $result['databaseRow']['CType'];

			switch ($cType) {
				   case 't3sbs_card':
					$flexformFile = 'cardSetting.';
						break;
				   case 't3sbs_carousel':
					$flexformFile = 'carousel.';
						break;
				   case 't3sbs_button':
					$flexformFile = 'button.';
						break;
				   case 't3sbs_mediaobject':
					$flexformFile = 'mediaobject.';
						break;
				   case 'table':
					$flexformFile = 'table.';
						break;
				   case 'card_wrapper':
					$flexformFile = 'cardWrapper.';
						break;
				   case 'autoLayout_row':
					$flexformFile = 'autoLayoutRow.';
						break;
				   case 'button_group':
					$flexformFile = 'buttongroup.';
						break;
				   case 'container':
					$flexformFile = 'container.';
						break;
				   case 'two_columns':
					$flexformFile = 'twoColumns.';
						break;
				   case 'three_columns':
					$flexformFile = 'threeColumns.';
						break;
				   case 'four_columns':
					$flexformFile = 'fourColumns.';
						break;
				   case 'six_columns':
					$flexformFile = 'sixColumns.';
						break;
				   case 'background_wrapper':
					$flexformFile = 'backgroundWrapper.';
						break;
				   case 'parallax_wrapper':
					$flexformFile = 'parallaxWrapper.';
						break;
				   case 'carousel_container':
					$flexformFile = 'carouselContainer.';
						break;
				   case 'collapsible_accordion':
					$flexformFile = 'collapse.';
						break;
				   case 'collapsible_container':
					$flexformFile = 'collapseContainer.';
						break;
				   case 'modal':
					$flexformFile = 'modal.';
						break;
				   case 'tabs_container':
					$flexformFile = 'tabs.';
						break;
				   case 'tabs_tab':
					$flexformFile = 'tabsTab.';
						break;
				   default:
					  $flexformFile = 'bootstrap.';
			}

			$dataStructure = $result['processedTca']['columns']['tx_t3sbootstrap_flexform']['config']['ds'];

			foreach ($flexforms as $file=>$fields) {
				if ( $file == $flexformFile ) {
					foreach ($fields as $field=>$mod) {
						if ($mod['add']) {
							foreach (explode(',',$mod['add']) as $add) {
								if (is_array($dataStructure['sheets'])) {
									foreach ($dataStructure['sheets'] as $sheetName=>$fieldsInSheet) {
										foreach ($fieldsInSheet as $fieldArr) {
											foreach ($fieldArr as $fieldName) {
												if (is_array($fieldName)) {
													foreach ($fieldName as $key=>$name) {
														if ($name['config']['type'] == 'select') {
															if (substr($field, 0, -1) == $key) {
																array_push($dataStructure['sheets'][$sheetName]['ROOT']['el'][$key]['config']['items'],
																 [trim($add),lcfirst(GeneralUtility::underscoredToUpperCamelCase(trim($add)))]);
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
						if ($mod['reduce']) {
							foreach( explode(',',$mod['reduce']) as $reduce ) {
								if (is_array($dataStructure['sheets'])) {
									foreach ($dataStructure['sheets'] as $sheetName=>$fieldsInSheet) {
										foreach ($fieldsInSheet as $fieldArr) {
											foreach ($fieldArr as $fieldName) {
												if (is_array($fieldName)) {
													foreach ($fieldName as $key=>$name) {
														if ($name['config']['type'] == 'select') {
															if (substr($field, 0, -1) == $key) {
																foreach ($name['config']['items'] as $k=>$item ) {
																	if (trim($item[1]) == trim($reduce)) {
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

			$result['processedTca']['columns']['tx_t3sbootstrap_flexform']['config']['ds'] = $dataStructure;

		}

		return $result;
	}


}
