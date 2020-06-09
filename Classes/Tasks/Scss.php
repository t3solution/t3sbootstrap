<?php
namespace T3SBS\T3sbootstrap\Tasks;

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
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

class Scss extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager)
	{
		$this->configurationManager = $configurationManager;
	}


	public function execute() {

		$this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
		$settings = $this->configurationManager->getConfiguration(
			\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
			't3sbootstrap',
			'm1'
		);

		if ( $settings['customScss'] ) {

			# Custom
			$customDir = $settings['customScssPath'] ? $settings['customScssPath'] : 'fileadmin/T3SB/Resources/Public/SCSS/';
			$customPath = GeneralUtility::getFileAbsFileName($customDir);

			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
			$result = $queryBuilder
				  ->select('*')
				  ->from('pages')
				  ->where(
					 $queryBuilder->expr()->eq('is_siteroot', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
				  )
				  ->execute();
			$siteroots = $result->fetchAll();

			foreach ($siteroots as $key=>$siteroot) {

				if ($key === 0) {
					$customFileName = 'custom-variables.scss';
					$customFileNameOverride = 'custom.scss';
					$boottstrapFileName = 'bootstrap.scss';
				} else {
					$customFileName = 'custom-variables-'.$siteroot['uid'].'.scss';
					$customFileNameOverride = 'custom-'.$siteroot['uid'].'.scss';
					$boottstrapFileName = 'bootstrap-'.$siteroot['uid'].'.scss';
				}

				self::writeCustomFile($customPath, $customFileName, $settings, '_variables');
				self::writeCustomFile($customPath, $customFileNameOverride, $settings, '_bootswatch');

				if ( $settings['rollyourown'] ) {
					self::rollYourOwn($boottstrapFileName, $customPath, $settings);
				}

				# Include
				$includeDir = 'uploads/tx_t3sbootstrap/';
				$includePath = GeneralUtility::getFileAbsFileName($includeDir);

				if ($key === 0) {
					self::deleteFilesFromDirectory($includePath);
					$includeFileName = 'bootstrap.scss';
				} else {
					$includeFileName = 'bootstrap-'.$siteroot['uid'].'.scss';
				}

				$includeFile = $includePath.$includeFileName;

				if (!file_exists($includeFile)) {

					if (!is_dir($includePath)) {
						mkdir($includePath, 0777, true);
					}

if ( $settings['rollyourown'] ) {

					if ($key === 0) {
						$includeContent = '
@import "../../'.$customDir.'custom-variables";
@import "../../'.$customDir.'bootstrap";
@import "../../'.$customDir.'custom";
						';
					} else {
						$includeContent = '
@import "../../'.$customDir.'custom-variables-'.$siteroot['uid'].'";
@import "../../'.$customDir.'bootstrap";
@import "../../'.$customDir.'custom-'.$siteroot['uid'].'";
						';
					}


} else {

					if ($key === 0) {
						$includeContent = '
@import "../../'.$customDir.'custom-variables";
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/bootstrap";
@import "../../'.$customDir.'custom";
						';
					} else {
						$includeContent = '
@import "../../'.$customDir.'custom-variables-'.$siteroot['uid'].'";
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/bootstrap";
@import "../../'.$customDir.'custom-'.$siteroot['uid'].'";
						';
					}

}
					GeneralUtility::writeFile($includeFile, $includeContent);
				}
			}

			# clean typo3temp/var/cache/data/ws_scss/
			if ( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('ws_scss') ) {
				$tempDir = 'typo3temp/var/cache/data/ws_scss/';
			} else {
				$tempDir = '';
			}


			$tempPath = GeneralUtility::getFileAbsFileName($tempDir);
			self::deleteFilesFromDirectory($tempPath);

			return TRUE;

		} else {

			return FALSE;

		}

	}


	private function writeCustomFile($customPath, $customFileName, $settings, $name) {

		$customFile = $customPath.$customFileName;

		if (file_exists($customFile)) {
			$copyFile = $customPath.'_'.time().'-'.$customFileName;
			if (!copy($customFile, $copyFile)) {
				return FALSE;
			} else {
				unlink($customFile);
			}
		}

		if (!file_exists($customFile)) {
			if (!is_dir($customPath)) {
				mkdir($customPath, 0777, true);
			}

			$customContent = $name == '_variables' ? '// Overrides Bootstrap variables' :  '// Your own SCSS';

			if ( $settings['bootswatch'] ) {
				$customContent = GeneralUtility::getURL($settings['bootswatchURL'].strtolower($settings['bootswatch']).'/'.$name.'.scss');
				if ($name == '_variables') {
					$customContent = str_replace(' !default', '', $customContent);
				}
			}

			GeneralUtility::writeFile($customFile, $customContent);
		}

		return TRUE;
	}


	private function deleteFilesFromDirectory($directory){
		if (is_dir($directory)) {
			if ($dh = opendir($directory)) {
				while (($file = readdir($dh)) !== false) {
					if ($file!='.' && $file !='..' && $file[0] != '_') {
						unlink(''.$directory.''.$file.'');
					}
				}
				closedir($dh);
			}
		}
	}


	private function rollYourOwn($boottstrapFileName, $customPath, $settings){

# Bootstrap
$bootstrapContent = '/*!
 * Bootstrap v4.3 (https://getbootstrap.com/)
 * Copyright 2011-2018 The Bootstrap Authors
 * Copyright 2011-2018 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */
';

if ( $settings['bootstrap']['functions'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/functions";';
}
if ( $settings['bootstrap']['variables'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/variables";';
}

if ( $settings['bootstrap']['mixins'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/mixins";';
}
if ( $settings['bootstrap']['root'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/root";';
}
if ( $settings['bootstrap']['reboot'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/reboot";';
}
if ( $settings['bootstrap']['type'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/type";';
}
if ( $settings['bootstrap']['images'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/images";';
}
if ( $settings['bootstrap']['code'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/code";';
}
if ( $settings['bootstrap']['grid'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/grid";';
}
if ( $settings['bootstrap']['tables'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/tables";';
}
if ( $settings['bootstrap']['forms'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/forms";';
}
if ( $settings['bootstrap']['buttons'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/buttons";';
}
if ( $settings['bootstrap']['transitions'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/transitions";';
}
if ( $settings['bootstrap']['dropdown'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/dropdown";';
}
if ( $settings['bootstrap']['button-group'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/button-group";';
}
if ( $settings['bootstrap']['input-group'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/input-group";';
}
if ( $settings['bootstrap']['custom-forms'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/custom-forms";';
}
if ( $settings['bootstrap']['nav'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/nav";';
}
if ( $settings['bootstrap']['navbar'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/navbar";';
}
if ( $settings['bootstrap']['card'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/card";';
}
if ( $settings['bootstrap']['breadcrumb'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/breadcrumb";';
}
if ( $settings['bootstrap']['pagination'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/pagination";';
}
if ( $settings['bootstrap']['badge'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/badge";';
}
if ( $settings['bootstrap']['jumbotron'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/jumbotron";';
}
if ( $settings['bootstrap']['alert'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/alert";';
}
if ( $settings['bootstrap']['progress'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/progress";';
}
if ( $settings['bootstrap']['media'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/media";';
}
if ( $settings['bootstrap']['list-group'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/list-group";';
}
if ( $settings['bootstrap']['close'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/close";';
}
if ( $settings['bootstrap']['toasts'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/toasts";';
}
if ( $settings['bootstrap']['modal'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/modal";';
}
if ( $settings['bootstrap']['tooltip'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/tooltip";';
}
if ( $settings['bootstrap']['popover'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/popover";';
}
if ( $settings['bootstrap']['carousel'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/carousel";';
}
if ( $settings['bootstrap']['spinners'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/spinners";';
}
if ( $settings['bootstrap']['utilities'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/utilities";';
}
if ( $settings['bootstrap']['print'] ) {
$bootstrapContent .= '
@import "../../typo3conf/ext/t3sbootstrap/Resources/Public/Contrib/Bootstrap/scss/print";';
}

			$bootstrapFile = $customPath.$boottstrapFileName;

			GeneralUtility::writeFile($bootstrapFile, $bootstrapContent);

	}


}
