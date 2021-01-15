<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;


class t3sbConstantsUpdateWizard implements UpgradeWizardInterface
{

	/**
	 * Return the identifier for this wizard
	 * This should be the same string as used in the ext_localconf class registration
	 *
	 * @return string
	 */
	public function getIdentifier(): string
	{
	  return 't3sbConstantsUpdateWizard';
	}

	/**
	 * Return the speaking name of this wizard
	 *
	 * @return string
	 */
	public function getTitle(): string
	{
	  return 'T3Sbootstrap - Constants to database or outsourced file';
	}

	/**
	 * Return the description for this wizard
	 *
	 * @return string
	 */
	public function getDescription(): string
	{
	  return 'Migrate formerly default constants and your constants to database table "tx_t3sbootstrap_domain_model_config" or outsourced constants file';
	}

	/**
	 * Execute the update
	 *
	 * Called when a wizard reports that an update is necessary
	 *
	 * @return bool
	 */
	public function executeUpdate(): bool
	{
		 # v4.5.5
 		$this->updateTxT3sbootstrapDomainModelConfig();

 		return true;
	}

	/**
	 * Fills the database table field "tx_t3sbootstrap_domain_model_config" with formerly default constants
	 */
	protected function updateTxT3sbootstrapDomainModelConfig()
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');
		$statements = $queryBuilder
				 ->select('uid')
				 ->from('tx_t3sbootstrap_domain_model_config')
				 ->execute()
				 ->fetchAll();

		foreach ($statements as $statement) {
			$recordId = (int)$statement['uid'];
			$queryBuilder
				  ->update('tx_t3sbootstrap_domain_model_config')
				  ->where(
					 $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($recordId, \PDO::PARAM_INT))
				  )
				  ->set('compress', 1)
				  ->set('disable_prefix_comment', 1)
				  ->set('global_padding_top', 'pt-5')
				  ->set('loading_spinner_color', 'primary')
				  ->set('lightbox_selection', 1)
				  ->set('sectionmenu_anchor_offset', 29)
				  ->set('sectionmenu_scrollspy_offset', 130)
				  ->set('shrinking_nav_padding', 5)
				  ->set('sidebar_menu_position', 'above')
				  ->set('lang_menu_with_fa_icon', 1)
				  ->set('subheader_color', 'secondary')
				  ->set('date_format', 'd.m.Y')
				  ->set('fa_link_icons', 1)
				  ->set('jumbotron_carousel_interval', 500)
				  ->set('updated', 1)
				  ->set('navbar_lang_flags', 1)

				  ->execute();
		}


$configList='contentOnlyOnRootpage,jqueryHeader,compress,disablePrefixComment,containerError,slideLeftAside,slideRightAside,pageContentExtraClass,bodyExtraClass,asideExtraClass,mainExtraClass,globalPaddingTop,stickyFooterExtraPadding,contentMarginTop,loadingSpinner,loadingSpinnerColor,sectionmenuAnchorOffset,sectionmenuScrollspyOffset,sectionmenuStickyTop,lightboxSelection,magnifying,backgroundImage.enable,backgroundImage.slide,lastModifiedContentElement,recentlyUpdatedContentElements,dateFormat,subheaderColor,sidebarMenuPosition,shrinkingNavPadding,favicon,cardFlipperOnClick,langMenuWithFaIcon,faLinkIcons,jumbotronCarouselInterval,jumbotronCarouselPause';

		$navbarList='extraRow,rightMenuUidList,langFlags,dropdownAnimate,navbarRightMenuUidList,navbarExtraRow';

		$queryBuilder = $connectionPool->getQueryBuilderForTable('sys_template');
		$statements = $queryBuilder
			 ->select('*')
			 ->from('sys_template')
			 ->execute()
			 ->fetchAll();

		foreach ( $statements as $statement) {
			$TSparserObject = GeneralUtility::makeInstance(TypoScriptParser::class);
			$TSparserObject->parse($statement['constants']);
			$tsObjectArr[$statement['pid']] = $TSparserObject->setup;
		}

		ksort($tsObjectArr);

		foreach ( $tsObjectArr as $key=>$constants ) {
			foreach ( $constants as $cKey=>$constant ) {
				if ($cKey == 'bootstrap.' ) {
					foreach ($constant as $vKey=>$firstLevelValue) {
						if (is_array($firstLevelValue)) {
							$vKey = $vKey == 'db.' ? 'config.' : $vKey;
							foreach ($firstLevelValue as $sKey=>$secondLevelValue ) {
								if ( $sKey == 'rightMenuUidList') $sKey = 'navbarRightMenuUidList';
								if ( $sKey == 'extraRow') $sKey = 'navbarExtraRow';
								if ( $sKey == 'langFlags') $sKey = 'navbarLangFlags';
								if ($sKey != 'if.') {
									if (is_array($secondLevelValue)) {
										foreach ($secondLevelValue as $tKey=>$thirdLevelValue ) {
											if ($tKey != 'if.') {
												if (is_array($thirdLevelValue)) {
													foreach ($thirdLevelValue as $fKey=>$foursLevelValue ) {
														if ($fKey != 'if.') {
															if (GeneralUtility::inList($configList, trim($fKey))
															 || GeneralUtility::inList($navbarList, trim($fKey))) {
																$dbArr[$key][$fKey] = $foursLevelValue;
															} else {
																$foursLevelConstant = $cKey.$vKey.$sKey.$tKey.$fKey;
																$constArr[$key][] = $foursLevelConstant.' = '.$foursLevelValue;
															}
														}
													}
												} else {
													if (GeneralUtility::inList($configList, trim($tKey))
													 || GeneralUtility::inList($navbarList, trim($tKey))) {
														$dbArr[$key][$tKey] = $thirdLevelValue;
													} else {
														$thirdLevelConstant = $cKey.$vKey.$sKey.$tKey;
														if ( $thirdLevelConstant == 'bootstrap.config.backgroundImage.enable' ) {
															$thirdLevelConstant = 'bootstrap.config.backgroundImageEnable';
														}
														if ( $thirdLevelConstant == 'bootstrap.config.backgroundImage.slide' ) {
															$thirdLevelConstant = 'bootstrap.config.backgroundImageSlide';
														}
														$constArr[$key][] = $thirdLevelConstant.' = '.$thirdLevelValue;
													}
												}
											}
										}
									} else {
										$secondLevelConstant = $cKey.$vKey.$sKey;
										if ($secondLevelConstant == 'bootstrap.carousel.interval') {
											$sKey = 'jumbotronCarouselInterval';
											$secondLevelConstant = 'bootstrap.config.jumbotronCarouselInterval';
										}
										if ($secondLevelConstant == 'bootstrap.carousel.pause') {
											$sKey = 'jumbotronCarouselPause';
											$secondLevelConstant = 'bootstrap.config.jumbotronCarouselPause';
										}
										if (GeneralUtility::inList($configList, trim($sKey)) || GeneralUtility::inList($navbarList, trim($sKey))) {
											if ($sKey == 'dropdownAnimate') $sKey = 'navbarDropdownAnimate';
											$dbArr[$key][$sKey] = $secondLevelValue;
										} else {
											$constArr[$key][] = $secondLevelConstant.' = '.$secondLevelValue;
										}
									}
								}
							}
						} else {
							if (GeneralUtility::inList($configList, trim($vKey)) || GeneralUtility::inList($navbarList, trim($vKey))) {
								$dbArr[$key][$vKey] = $firstLevelValue;
							} else {
								$constArr[$key][] = $cKey.$vKey.' = '.$firstLevelValue;
							}
						}
					}
				}
			}
		}

		foreach ($constArr as $key=>$const) {
			$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, $key)->get();
			if (count($rootLineArray) > 1) {
				$constantsArr[$rootLineArray[0]['uid']][$key] = $const;
			} else {
				// root
				$constantsArr[$key][0] = $const;
			}
		}

		if ( count($constantsArr) ) {
			$filecontent = '';
			foreach ($constantsArr as $rootUid=>$constants) {
				foreach ( $constants as $subUid=>$constant ) {
					if ($subUid === 0) {
						// root
						$filecontent .= '['.$rootUid.' in tree.rootLineIds]'.PHP_EOL;
						foreach ( $constant as $bKey=>$value ) {
							$filecontent .= $value.PHP_EOL;
						}
						$filecontent .= '[END]'.PHP_EOL.PHP_EOL;
					} else {

						$filecontent .= '['.$rootUid.' in tree.rootLineIds && '.$subUid.' in tree.rootLineIds]'.PHP_EOL;
						foreach ( $constant as $xbKey=>$xvalue ) {
							$filecontent .= $xvalue.PHP_EOL;
						}
						$filecontent .= '[END]'.PHP_EOL.PHP_EOL;
					}
				}
			}
		}

		$customDir = 'fileadmin/T3SB/Configuration/TypoScript/';
		$customPath = GeneralUtility::getFileAbsFileName($customDir);
		$customFileName = 't3sbUpdate.typoscript';
		$customFile = $customPath.$customFileName;

		if (file_exists($customFile)) {
			unlink($customFile);
		}
		if (!is_dir($customPath)) {
			mkdir($customPath, 0777, true);
		}

		// write the constants
		GeneralUtility::writeFile($customFile, $filecontent);

		// set to db
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');

		foreach($dbArr as $pid=>$columns) {
			foreach($columns as $field=>$value) {
				$queryBuilder
					->update('tx_t3sbootstrap_domain_model_config')
					->where(
						$queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT))
					)
					->set(''.GeneralUtility::camelCaseToLowerCaseUnderscored($field).'', $value)
					->execute();
			}
		}
	}


	/**
	 * Is an update necessary?
	 *
	 * Is used to determine whether a wizard needs to be run.
	 * Check if data for migration exists.
	 *
	 * @return bool
	 */
	public function updateNecessary(): bool
	{
		$updateNeeded = false;
		// Check if the database table even exists
		if ( $this->checkIfWizardIsRequired() ) {
			$updateNeeded = true;
		}

		return $updateNeeded;
	}


	/**
	 * Returns an array of class names of prerequisite classes
	 *
	 * This way a wizard can define dependencies like "database up-to-date" or
	 * "reference index updated"
	 *
	 * @return string[]
	 */
	public function getPrerequisites(): array
	{
		return '';
	}


	/**
	 * Check if there are record within database table with an empty "compress" field.
	 *
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	protected function checkIfWizardIsRequired(): bool
	{
		 $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		 $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');
		 $numberOfEntries = $queryBuilder
			 ->count('uid')
			 ->from('tx_t3sbootstrap_domain_model_config')
			 ->where(
				$queryBuilder->expr()->eq('updated', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
			)
			 ->execute()
			 ->fetchColumn();

		return (bool)$numberOfEntries;
	}


}