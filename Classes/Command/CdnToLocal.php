<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class CdnToLocal extends Command
{

    protected $configurationManager;

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
		$this->setDescription('Write required CSS and JS to fileadmin/Resources/Public/');
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

		# check FA version & settings
		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
		if ( !empty($extConf['fontawesomeCss']) ) {
			if ( (int)$extConf['fontawesomeCss'] < 3 ) {
				# v5
				if ( (int)$settings['cdn']['fontawesome'] > 5 ) {
					$settings['cdn']['fontawesome'] = $settings['cdn']['fontawesome5latest'];
				}
			} elseif ( (int)$extConf['fontawesomeCss'] > 2 ) {
				# v6
				if ( (int)$settings['cdn']['fontawesome'] < 6 ) {
					$settings['cdn']['fontawesome'] = $settings['cdn']['fontawesome6latest'];
				}
			}
		} else {
			$settings['cdn']['fontawesome'] = '5.15.4';
		}

		foreach ($settings['cdn'] as $key=>$version) {

			if ($key == 'jquery') {
				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'jquery.min.js';
				$cdnPath = 'https://code.jquery.com/jquery-'.$version.'.min.js';
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'bootstrap') {
				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'bootstrap.min.css';
				if ($settings['cdn']['bootswatch']) {
					$bootswatchTheme = $settings['cdn']['bootswatch'];
					$cdnPath = 'https://cdn.jsdelivr.net/npm/bootswatch@'.$version.'/dist/'.$bootswatchTheme.'/'.$customFileName;
					self::writeCustomFile($customPath, $customFileName, $cdnPath, true);
				} else {
					$cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/css/'.$customFileName;
					self::writeCustomFile($customPath, $customFileName, $cdnPath, true);
				}

				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'bootstrap.min.js';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/js/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
				$customFileName = 'bootstrap.bundle.min.js';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/js/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'popperjs') {
				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'popper.js';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/'.$version.'/umd/popper.min.js';
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'fontawesome') {
				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'fontawesome.min.js';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/'.$version.'/js/all.min.js';
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'jqueryEasing') {
				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'jquery.easing.min.js';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/'.$version.'/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'highlight') {
				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'highlight.default.min.css';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/'.$version.'/styles/default.min.css';
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
				$customFileName = 'highlight.a11y-light.min.css';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/'.$version.'/styles/a11y-light.min.css';
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'highlight.min.js';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/'.$version.'/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
				$customFileName = 'highlight.php.min.js';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/'.$version.'/languages/php.min.js';
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'lazyload') {
				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'lazyload.min.js';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/vanilla-lazyload@'.$version.'/dist/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'picturefill') {
				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'picturefill.min.js';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/picturefill/'.$version.'/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'animate') {
				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'animate.compat.css';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/'.$version.'/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'baguetteBox') {
				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'baguetteBox.min.css';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/'.$version.'/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'baguetteBox.min.js';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/'.$version.'/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}
			if ($key == 'halkabox') {
				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'halkaBox.min.css';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/halkabox@'.$version.'/dist/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath, true);

				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'halkaBox.min.js';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/halkabox@'.$version.'/dist/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'glightbox') {
				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'glightbox.min.css';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/glightbox@'.$version.'/dist/css/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'glightbox.min.js';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/glightbox@'.$version.'/dist/js/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'cookieconsent') {
		 		$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'cookieconsent.min.css';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/cookieconsent@'.$version.'/build/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

		 		$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'cookieconsent.min.js';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/cookieconsent@'.$version.'/build/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'masonry') {
				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'masonry.pkgd.min.js';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/masonry/'.$version.'/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'jarallax') {
				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'jarallax.min.js';
				$cdnPath = 'https://unpkg.com/jarallax@'.$version.'/dist/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
				$customFileName = 'jarallax-video.min.js';
				$cdnPath = 'https://unpkg.com/jarallax@'.$version.'/dist/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}

			if ($key == 'swiper') {
				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'swiper-bundle.min.css';
				$cdnPath = 'https://unpkg.com/swiper@'.$version.'/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'swiper-bundle.min.js';
				$cdnPath = 'https://unpkg.com/swiper@'.$version.'/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}
		}

		return 0;
	}

	private function writeCustomFile($customPath, $customFileName, $cdnPath, $extend=false ) {
		$customFile = $customPath.$customFileName;
		$customContent = GeneralUtility::getURL($cdnPath);
		if ($extend && str_contains( (string)$customContent, '/*#')) {
			$customContentArr = explode('/*#' , $customContent);
			$customContent = $customContentArr[0];
		} elseif (str_contains((string)$customContent, '//#')) {
			$customContentArr = explode('//#' , $customContent);
			$customContent = $customContentArr[0];
		}
		if (file_exists($customFile)) {
			unlink($customFile);
		}
		if (!is_dir($customPath)) {
			mkdir($customPath, 0777, true);
		}

		GeneralUtility::writeFile($customFile, $customContent);
	}


}