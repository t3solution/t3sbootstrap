<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;


/**
 * Command for update GRUAN access data
 */
class CustomScss extends Command
{

	/**
	 * @param ConfigurationManagerInterface $configurationManager
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
	{
		$this->configurationManager = $configurationManager;
	}


	/**
	 * Defines the allowed options for this command
	 *
	 * @inheritdoc
	 */
	protected function configure()
	{
		 $this->setDescription('T3SB Custom Scss - write a custom scss file');
	}


	/**
	 * Update all records
	 *
	 * @inheritdoc
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{

		$this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
		$settings = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
			't3sbootstrap',
			'm1'
		);

		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

		if ( $settings['customScss'] && array_key_exists('customScss', $extConf)
			 && $extConf['customScss'] === '1' && ExtensionManagementUtility::isLoaded('ws_scss' ) ) {

			# get the Boostrap SCSS-Files
			$scssList = '_alert.scss, _badge.scss, _breadcrumb.scss, _button-group.scss, _buttons.scss, _card.scss, _carousel.scss, _close.scss, _code.scss, _custom-forms.scss, _dropdown.scss, _forms.scss, _functions.scss, _grid.scss, _images.scss, _input-group.scss, _jumbotron.scss, _list-group.scss, _media.scss, _mixins.scss, _modal.scss, _nav.scss, _navbar.scss, _pagination.scss, _popover.scss, _print.scss, _progress.scss, _reboot.scss, _root.scss, _spinners.scss, _tables.scss, _toasts.scss, _tooltip.scss, _transitions.scss, _type.scss, _utilities.scss, _variables.scss, bootstrap-grid.scss, bootstrap-reboot.scss, bootstrap.scss';

			$mixinsList = '_alert.scss, _background-variant.scss, _badge.scss, _border-radius.scss, _box-shadow.scss, _breakpoints.scss, _buttons.scss, _caret.scss, _clearfix.scss, _deprecate.scss, _float.scss, _forms.scss, _gradients.scss, _grid-framework.scss, _grid.scss, _hover.scss, _image.scss, _list-group.scss, _lists.scss, _nav-divider.scss, _pagination.scss, _reset-text.scss, _resize.scss, _screen-reader.scss, _size.scss, _table-row.scss, _text-emphasis.scss, _text-hide.scss, _text-truncate.scss, _transition.scss, _visibility.scss';

			$utilitiesList = '_align.scss, _background.scss, _borders.scss, _clearfix.scss, _display.scss, _embed.scss, _flex.scss, _float.scss, _interactions.scss, _overflow.scss, _position.scss, _screenreaders.scss, _shadows.scss, _sizing.scss, _spacing.scss, _stretched-link.scss, _text.scss, _visibility.scss';

			$customDir = 'fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/';
			$customPath = GeneralUtility::getFileAbsFileName($customDir);
			$bootstrapVersion = GeneralUtility::isFirstPartOfStr($settings['cdn']['bootstrap'], '4.') ? $settings['cdn']['bootstrap'] : '4.5.3' ;

			foreach (explode(',', $scssList) as $scss ) {
				$customFileName = trim($scss);
				$customFile = $customPath.$customFileName;
				$cdnPath = 'https://raw.githubusercontent.com/twbs/bootstrap/v'.trim($bootstrapVersion).'/scss/'.$customFileName;
				$customContent = GeneralUtility::getURL($cdnPath);
				if (file_exists($customFile)) unlink($customFile);
				if (!is_dir($customPath)) mkdir($customPath, 0777, true);
				GeneralUtility::writeFile($customFile, $customContent);
			}

			$customDir = 'fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/mixins/';
			$customPath = GeneralUtility::getFileAbsFileName($customDir);

			foreach (explode(',', $mixinsList) as $mixins ) {
				$customFileName = trim($mixins);
				$customFile = $customPath.$customFileName;
				$cdnPath = 'https://raw.githubusercontent.com/twbs/bootstrap/v'.trim($bootstrapVersion).'/scss/mixins/'.$customFileName;
				$customContent = GeneralUtility::getURL($cdnPath);
				if (file_exists($customFile)) unlink($customFile);
				if (!is_dir($customPath)) mkdir($customPath, 0777, true);
				GeneralUtility::writeFile($customFile, $customContent);
			}

			$customDir = 'fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/utilities/';
			$customPath = GeneralUtility::getFileAbsFileName($customDir);

			foreach (explode(',', $utilitiesList) as $utils ) {
				$customFileName = trim($utils);
				$customFile = $customPath.$customFileName;
				$cdnPath = 'https://raw.githubusercontent.com/twbs/bootstrap/v'.trim($bootstrapVersion).'/scss/utilities/'.$customFileName;
				$customContent = GeneralUtility::getURL($cdnPath);
				if (file_exists($customFile)) unlink($customFile);
				if (!is_dir($customPath)) mkdir($customPath, 0777, true);
				GeneralUtility::writeFile($customFile, $customContent);
			}

			$customDir = 'fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/vendor/';
			$customPath = GeneralUtility::getFileAbsFileName($customDir);

			$customFileName = '_rfs.scss';
			$customFile = $customPath.$customFileName;
			$cdnPath = 'https://raw.githubusercontent.com/twbs/bootstrap/v'.trim($bootstrapVersion).'/scss/vendor/_rfs.scss';
			$customContent = GeneralUtility::getURL($cdnPath);
			if (file_exists($customFile)) unlink($customFile);
			if (!is_dir($customPath)) mkdir($customPath, 0777, true);
			GeneralUtility::writeFile($customFile, $customContent);

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
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/bootstrap";
@import "../../'.$customDir.'custom";
						';
					} else {
						$includeContent = '
@import "../../'.$customDir.'custom-variables-'.$siteroot['uid'].'";
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/bootstrap";
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

			return 0;

		} else {

			return 1;

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
 * Bootstrap v4.x (https://getbootstrap.com/)
 * Copyright 2011-2018 The Bootstrap Authors
 * Copyright 2011-2021 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */
';

if ( $settings['bootstrap']['functions'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/functions";';
}
if ( $settings['bootstrap']['variables'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/variables";';
}

if ( $settings['bootstrap']['mixins'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/mixins";';
}
if ( $settings['bootstrap']['root'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/root";';
}
if ( $settings['bootstrap']['reboot'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/reboot";';
}
if ( $settings['bootstrap']['type'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/type";';
}
if ( $settings['bootstrap']['images'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/images";';
}
if ( $settings['bootstrap']['code'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/code";';
}
if ( $settings['bootstrap']['grid'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/grid";';
}
if ( $settings['bootstrap']['tables'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/tables";';
}
if ( $settings['bootstrap']['forms'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/forms";';
}
if ( $settings['bootstrap']['buttons'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/buttons";';
}
if ( $settings['bootstrap']['transitions'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/transitions";';
}
if ( $settings['bootstrap']['dropdown'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/dropdown";';
}
if ( $settings['bootstrap']['button-group'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/button-group";';
}
if ( $settings['bootstrap']['input-group'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/input-group";';
}
if ( $settings['bootstrap']['custom-forms'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/custom-forms";';
}
if ( $settings['bootstrap']['nav'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/nav";';
}
if ( $settings['bootstrap']['navbar'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/navbar";';
}
if ( $settings['bootstrap']['card'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/card";';
}
if ( $settings['bootstrap']['breadcrumb'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/breadcrumb";';
}
if ( $settings['bootstrap']['pagination'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/pagination";';
}
if ( $settings['bootstrap']['badge'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/badge";';
}
if ( $settings['bootstrap']['jumbotron'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/jumbotron";';
}
if ( $settings['bootstrap']['alert'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/alert";';
}
if ( $settings['bootstrap']['progress'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/progress";';
}
if ( $settings['bootstrap']['media'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/media";';
}
if ( $settings['bootstrap']['list-group'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/list-group";';
}
if ( $settings['bootstrap']['close'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/close";';
}
if ( $settings['bootstrap']['toasts'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/toasts";';
}
if ( $settings['bootstrap']['modal'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/modal";';
}
if ( $settings['bootstrap']['tooltip'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/tooltip";';
}
if ( $settings['bootstrap']['popover'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/popover";';
}
if ( $settings['bootstrap']['carousel'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/carousel";';
}
if ( $settings['bootstrap']['spinners'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/spinners";';
}
if ( $settings['bootstrap']['utilities'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/utilities";';
}
if ( $settings['bootstrap']['print'] ) {
$bootstrapContent .= '
@import "../../fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/print";';
}

			$bootstrapFile = $customPath.$boottstrapFileName;

			GeneralUtility::writeFile($bootstrapFile, $bootstrapContent);

	}
}
