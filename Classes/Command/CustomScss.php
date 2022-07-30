<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Command\Command;
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
class CustomScss extends Command
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
			$bootstrapVersion = str_starts_with($settings['cdn']['bootstrap'], '5.') ? $settings['cdn']['bootstrap'] : '5.1.13';
			self::getSccFiles($bootstrapVersion);

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


	/**
	* remove dirs
	*/
	private function rmDir(string $path) : int
	{
		if (!is_dir ($path)) {
			return -1;
		}
		$dir = @opendir ($path);
			if (!$dir) {
			return -2;
		}
		while ($entry = @readdir($dir)) {
			if ($entry == '.' || $entry == '..') continue;
			if (is_dir ($path.'/'.$entry)) {
				$res = self::rmDir ($path.'/'.$entry);
				if ($res == -1) {
					@closedir ($dir);
					return -2;
				} else if ($res == -2) {
					@closedir ($dir);
					return -2;
				} else if ($res == -3) {
					@closedir ($dir);
					return -3;
				} else if ($res != 0) {
					@closedir ($dir);
					return -2;
				}
			} else if (is_file ($path.'/'.$entry) || is_link ($path.'/'.$entry)) {
				$res = @unlink ($path.'/'.$entry);
				if (!$res) {
					@closedir ($dir);
					return -2;
				}
			} else {
				@closedir ($dir);
				return -3;
			}
		}

		@closedir ($dir);
		$res = @rmdir ($path);

		if (!$res) {
			return -2;
		}

		return 0;
	}


	public function getSccFiles($bootstrapVersion): void
	{
		$localZipPath = GeneralUtility::getFileAbsFileName(self::localZipPath);
		if ( is_dir($localZipPath) ) {
			self::rmDir($localZipPath);
		}
		mkdir($localZipPath, 0777, true);
		$localZipFile = GeneralUtility::getFileAbsFileName(self::localZipPath.self::localZipFile);
		$zipFilename = 'v'.$bootstrapVersion.'.zip';
		$zipContent = GeneralUtility::getURL(self::zipFilePath . $zipFilename);
		GeneralUtility::writeFile($localZipFile, $zipContent);
		$extractTo = $localZipPath;
		$zip = new \ZipArchive;
		if ($zip->open($localZipFile) === TRUE) {
			$zip->extractTo($extractTo);
			$zip->close();
		} else {
			throw new \InvalidArgumentException('Sorry ZIP creation failed at this time!', 1657464667);
		}
		$renameFrom = GeneralUtility::getFileAbsFileName(self::localZipPath.'bootstrap-'.$bootstrapVersion.'/scss');
		$renameTo = GeneralUtility::getFileAbsFileName(self::localZipPath.'scss');
		if ( is_dir($renameFrom) ) {
			rename($renameFrom, $renameTo);
		}
		self::rmDir(GeneralUtility::getFileAbsFileName(self::localZipPath.'bootstrap-'.$bootstrapVersion));
		$zipFile = GeneralUtility::getFileAbsFileName(self::localZipPath.self::localZipFile);
		if (file_exists($zipFile)) unlink($zipFile);
	}

}
