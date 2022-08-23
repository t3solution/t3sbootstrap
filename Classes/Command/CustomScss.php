<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class CustomScss extends CommandBase
{
	const localZipFile = 't3sb.zip';
	const localZipPath = 'fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/';
	const zipFilePath = 'https://github.com/twbs/bootstrap/archive/';


	public function initializeAction()
	{
		 parent::initializeAction();
	}


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

		if ( $settings['customScss'] && array_key_exists('customScss', $extConf) && $extConf['customScss'] === '1' ) {
			# get the Boostrap SCSS-Files
			$bootstrapVersion = str_starts_with($settings['cdn']['bootstrap'], '5.') ? $settings['cdn']['bootstrap'] : $settings['cdn']['bootstraplatest'];
			$bootstrapScssDir = 'fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/';
			$bootstrapScssPath = GeneralUtility::getFileAbsFileName($bootstrapScssDir);

			if ($settings['cdn']['noZip']) {
				self::getSccFilesNoZip($settings, $bootstrapVersion, $bootstrapScssPath);
			} else {
				self::getSccFiles($bootstrapVersion);
			}
			if (!file_exists(GeneralUtility::getFileAbsFileName(self::localZipPath.'scss/bootstrap.scss'))) {
				self::getSccFiles($settings, $bootstrapVersion, $bootstrapScssPath);
			}

			# Custom
			$customDir = !empty($settings['customScssPath']) ? $settings['customScssPath'] : 'fileadmin/T3SB/Resources/Public/SCSS/';
			$customPath = GeneralUtility::getFileAbsFileName($customDir);

			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
			$result = $queryBuilder
				  ->select('*')
				  ->from('pages')
				  ->where(
				  	 $queryBuilder->expr()->eq('sys_language_uid', 0),
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

					if ($key === 0) {
						$includeContent = '
@import "../../'.$customDir.'custom-variables";
@import "../../'.self::localZipPath.'scss/bootstrap";
@import "../../'.$customDir.'custom";
						';
					} else {
						$includeContent = '
@import "../../'.$customDir.'custom-variables-'.$siteroot['uid'].'";
@import "../../'.self::localZipPath.'scss/bootstrap";
@import "../../'.$customDir.'custom-'.$siteroot['uid'].'";
						';
					}

					GeneralUtility::writeFile($includeFile, $includeContent);
				}
			}

			$tempPath = GeneralUtility::getFileAbsFileName('typo3temp/assets/t3sbootstrap/css/');
			self::deleteFilesFromDirectory($tempPath);

			$customDir = self::localZipPath.'scss/';
			$customPath = GeneralUtility::getFileAbsFileName($customDir);
			$customFileName = 'bootstrap.scss';
			$customFile = $customPath.$customFileName;
			$customContent = GeneralUtility::getURL($customFile);
			$length = 0;
			if ($customContent) {
				$length = strlen($customContent);
			}

			foreach ( $settings['optimize'] as $component=>$import ) {
				if (!$import && $customContent) {
					$find = '@import "'.$component.'";';
					$replace = '// @import "'.$component.'";';
					$customContent = str_replace($find, $replace, $customContent);
				}
			}

			if ($customContent && $length < strlen($customContent)) {
				if (file_exists($customFile)) unlink($customFile);
				if (!is_dir($customPath)) mkdir($customPath, 0777, true);
				GeneralUtility::writeFile($customFile, $customContent);
			}

			if ( is_dir(GeneralUtility::getFileAbsFileName(self::localZipPath.'scss')) ) {

				return 0;

			} else {
				throw new \InvalidArgumentException('Check the bootstrap version in the constant editor for validity!', 1657204821);

				return 1;
			}

		} else {

			throw new \InvalidArgumentException('You have to activate SCSS in the EM config!', 1657204821);

			return 1;
		}

	}


	private function writeCustomFile($customPath, $customFileName, $settings, $name) {

		$customFile = $customPath.$customFileName;

		$keepVariables = (int)$settings['keepVariables'];
		if (file_exists($customFile)) {
			$copyFile = $customPath.'_'.time().'-'.$customFileName;
			if (!copy($customFile, $copyFile)) {
				return FALSE;
			} elseif (empty($keepVariables)) {
				unlink($customFile);
			}
		}
		if (!file_exists($customFile) && empty($keepVariables)) {
			if (!is_dir($customPath)) {
				mkdir($customPath, 0777, true);
			}
			$customContent = $name == '_variables' ? '// Overrides Bootstrap variables'.PHP_EOL.'// $enable-shadows: true;'.PHP_EOL.'// $enable-gradients: true;'.PHP_EOL.'// $enable-negative-margins: true;' :	 '// Your own SCSS';
			if ( $settings['bootswatch'] ) {
				$customContent = file_get_contents($settings['bootswatchURL'].strtolower($settings['bootswatch']).'/'.$name.'.scss');
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


	public function getSccFiles($bootstrapVersion): void
	{
		$localZipPath = GeneralUtility::getFileAbsFileName(self::localZipPath);
		if ( is_dir($localZipPath) ) {
			parent::rmDir($localZipPath);
		}
		mkdir($localZipPath, 0777, true);
		$localZipFile = GeneralUtility::getFileAbsFileName(self::localZipPath.self::localZipFile);
		$zipFilename = 'v'.$bootstrapVersion.'.zip';
		$zipContent = GeneralUtility::getURL(self::zipFilePath . $zipFilename);
		if ($zipContent) {
			GeneralUtility::writeFile($localZipFile, $zipContent);
			$extractTo = $localZipPath;
			$zip = new \ZipArchive;
			if ($zip->open($localZipFile) === TRUE) {
				$zip->extractTo($extractTo);
				$zip->close();
			} else {
				throw new \InvalidArgumentException('Sorry ZIP creation failed at this time! Set the constant "bootstrap.cdn.noZip=1" and try again.', 1657464538);
			}
			$renameFrom = GeneralUtility::getFileAbsFileName(self::localZipPath.'bootstrap-'.$bootstrapVersion.'/scss');
			$renameTo = GeneralUtility::getFileAbsFileName(self::localZipPath.'scss');
			if ( is_dir($renameFrom) ) {
				rename($renameFrom, $renameTo);
			}
			parent::rmDir(GeneralUtility::getFileAbsFileName(self::localZipPath.'bootstrap-'.$bootstrapVersion));
			$zipFile = GeneralUtility::getFileAbsFileName(self::localZipPath.self::localZipFile);
			if (file_exists($zipFile)) unlink($zipFile);
		} else {
			throw new \InvalidArgumentException('No content from GitHub archive!', 1657464783);
		}
	}


	public function getSccFilesNoZip($settings, $bootstrapVersion, $customPath): void
	{
		$gitURL = 'https://raw.githubusercontent.com/twbs/bootstrap/';
		$bootstrapPath = 'fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss';

		# get the Boostrap SCSS-Files
		if ( $bootstrapVersion > '5.1.3') {
			$scssList = '_accordion.scss, _alert.scss, _badge.scss, _breadcrumb.scss, _button-group.scss, _buttons.scss, _card.scss, _carousel.scss, _close.scss, _containers.scss, _dropdown.scss, _forms.scss, _functions.scss, _grid.scss, _helpers.scss, _images.scss, _list-group.scss, _maps.scss, _mixins.scss, _modal.scss, _nav.scss, _navbar.scss, _offcanvas.scss, _pagination.scss, _placeholders.scss, _popover.scss, _progress.scss, _reboot.scss, _root.scss, _spinners.scss, _tables.scss, _toasts.scss, _tooltip.scss, _transitions.scss, _type.scss, _utilities.scss, _variables.scss, bootstrap-grid.scss, bootstrap-reboot.scss, bootstrap-utilities.scss, bootstrap.scss';
		} else {
			$scssList = '_accordion.scss, _alert.scss, _badge.scss, _breadcrumb.scss, _button-group.scss, _buttons.scss, _card.scss, _carousel.scss, _close.scss, _containers.scss, _dropdown.scss, _forms.scss, _functions.scss, _grid.scss, _helpers.scss, _images.scss, _list-group.scss, _mixins.scss, _modal.scss, _nav.scss, _navbar.scss, _offcanvas.scss, _pagination.scss, _placeholders.scss, _popover.scss, _progress.scss, _reboot.scss, _root.scss, _spinners.scss, _tables.scss, _toasts.scss, _tooltip.scss, _transitions.scss, _type.scss, _utilities.scss, _variables.scss, bootstrap-grid.scss, bootstrap-reboot.scss, bootstrap-utilities.scss, bootstrap.scss';
		}

		$mixinsList = '_alert.scss, _backdrop.scss, _banner.scss, _border-radius.scss, _box-shadow.scss, _breakpoints.scss, _buttons.scss, _caret.scss, _clearfix.scss, _color-scheme.scss, _container.scss, _deprecate.scss, _forms.scss, _gradients.scss, _grid.scss, _image.scss, _list-group.scss, _lists.scss, _pagination.scss, _reset-text.scss, _resize.scss, _table-variants.scss, _text-truncate.scss, _transition.scss, _utilities.scss, _visually-hidden.scss';

		$utilitiesList = '_api.scss';

		$formsList = '_floating-labels.scss, _form-check.scss, _form-control.scss, _form-range.scss, _form-select.scss, _form-text.scss, _input-group.scss, _labels.scss, _validation.scss';

		$helpersList = '_clearfix.scss, _color-bg.scss, _colored-links.scss, _position.scss, _ratio.scss, _stacks.scss, _stretched-link.scss, _text-truncation.scss, _visually-hidden.scss, _vr.scss';

		foreach (explode(',', $scssList) as $scss ) {
			$customFileName = trim($scss);
			$customFile = $customPath.$customFileName;
			$cdnPath = $gitURL.'v'.trim($bootstrapVersion).'/scss/'.$customFileName;
			$customContent = GeneralUtility::getURL($cdnPath);
			if (file_exists($customFile)) unlink($customFile);
			if (!is_dir($customPath)) mkdir($customPath, 0777, true);
			GeneralUtility::writeFile($customFile, $customContent);
		}

		$customDir = $bootstrapPath.'/mixins/';
		$customPath = GeneralUtility::getFileAbsFileName($customDir);

		foreach (explode(',', $mixinsList) as $mixins ) {
			$customFileName = trim($mixins);
			$customFile = $customPath.$customFileName;
			$cdnPath = $gitURL.'v'.trim($bootstrapVersion).'/scss/mixins/'.$customFileName;
			$customContent = GeneralUtility::getURL($cdnPath);
			if (file_exists($customFile)) unlink($customFile);
			if (!is_dir($customPath)) mkdir($customPath, 0777, true);
			GeneralUtility::writeFile($customFile, $customContent);
		}

		$customDir = $bootstrapPath.'/utilities/';
		$customPath = GeneralUtility::getFileAbsFileName($customDir);

		foreach (explode(',', $utilitiesList) as $utils ) {
			$customFileName = trim($utils);
			$customFile = $customPath.$customFileName;
			$cdnPath = $gitURL.'v'.trim($bootstrapVersion).'/scss/utilities/'.$customFileName;
			$customContent = GeneralUtility::getURL($cdnPath);
			if (file_exists($customFile)) unlink($customFile);
			if (!is_dir($customPath)) mkdir($customPath, 0777, true);
			GeneralUtility::writeFile($customFile, $customContent);
		}

		$customDir = $bootstrapPath.'/forms/';
		$customPath = GeneralUtility::getFileAbsFileName($customDir);


		foreach (explode(',', $formsList) as $forms ) {
			$customFileName = trim($forms);
			$customFile = $customPath.$customFileName;
			$cdnPath = $gitURL.'v'.trim($bootstrapVersion).'/scss/forms/'.$customFileName;
			$customContent = GeneralUtility::getURL($cdnPath);
			if (file_exists($customFile)) unlink($customFile);
			if (!is_dir($customPath)) mkdir($customPath, 0777, true);
			GeneralUtility::writeFile($customFile, $customContent);
		}

		$customDir = $bootstrapPath.'/helpers/';
		$customPath = GeneralUtility::getFileAbsFileName($customDir);

		foreach (explode(',', $helpersList) as $helpers ) {
			$customFileName = trim($helpers);
			$customFile = $customPath.$customFileName;
			$cdnPath = $gitURL.'v'.trim($bootstrapVersion).'/scss/helpers/'.$customFileName;
			$customContent = GeneralUtility::getURL($cdnPath);
			if (file_exists($customFile)) unlink($customFile);
			if (!is_dir($customPath)) mkdir($customPath, 0777, true);
			GeneralUtility::writeFile($customFile, $customContent);
		}

		$customDir = $bootstrapPath.'/vendor/';
		$customPath = GeneralUtility::getFileAbsFileName($customDir);

		$customFileName = '_rfs.scss';
		$customFile = $customPath.$customFileName;
		$cdnPath = $gitURL.'v'.trim($bootstrapVersion).'/scss/vendor/_rfs.scss';
		$customContent = GeneralUtility::getURL($cdnPath);
		if (file_exists($customFile)) unlink($customFile);
		if (!is_dir($customPath)) mkdir($customPath, 0777, true);
		GeneralUtility::writeFile($customFile, $customContent);

	}

}
