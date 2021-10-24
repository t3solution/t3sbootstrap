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
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;


/**
 * Command for update GRUAN access data
 */
class CdnToLocal extends Command
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
					$cdnPath = 'https://cdn.jsdelivr.net/npm/bootswatch@'.$settings['cdn']['bootstrap'].'/dist/'.$bootswatchTheme.'/'.$customFileName;
					self::writeCustomFile($customPath, $customFileName, $cdnPath, true);
				} else {
					$cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$settings['cdn']['bootstrap'].'/dist/css/'.$customFileName;
					self::writeCustomFile($customPath, $customFileName, $cdnPath, true);
				}

				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'bootstrap.min.js';
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
				// v3 for OWL Slider Style 1
				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'animate.min.css';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/'.$customFileName;
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
			if ($key == 'magnificpopup') {
				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'magnific-popup.min.css';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/'.$version.'/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath, true);

				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'jquery.magnific-popup.min.js';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/'.$version.'/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}
			if ($key == 'lightcase') {
				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'lightcase.css';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/lightcase@'.$version.'/src/css/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/fonts/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'lightcase.eot';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/lightcase@'.$version.'/src/fonts/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/fonts/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'lightcase.svg';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/lightcase@'.$version.'/src/fonts/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/fonts/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'lightcase.ttf';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/lightcase@'.$version.'/src/fonts/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/fonts/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'lightcase.woff';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/lightcase@'.$version.'/src/fonts/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'lightcase.min.js';
				$cdnPath = 'https://cdn.jsdelivr.net/npm/lightcase@'.$version.'/src/js/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);
			}
			if ($key == 'owlCarousel') {
				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'owl.carousel.min.css';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/'.$version.'/assets/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customFileName = 'owl.theme.default.css';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/'.$version.'/assets/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'owl.carousel.js';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/'.$version.'/'.$customFileName;
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

			if ($key == 'ytPlayer') {
		 		$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'jquery.mb.YTPlayer.min.css';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/'.$version.'/css/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/font/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'ytp-regular.eot';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/'.$version.'/css/font/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/font/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'ytp-regular.ttf';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/'.$version.'/css/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/font/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'ytp-regular.woff';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/'.$version.'/css/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/images/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'raster.png';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/'.$version.'/css/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/images/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'raster@2x.png';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/'.$version.'/css/images/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/images/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'raster_dot.png';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/'.$version.'/css/images/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/CSS/images/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'raster_dot@2x.png';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/'.$version.'/css/images/'.$customFileName;
				self::writeCustomFile($customPath, $customFileName, $cdnPath);

				$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'jquery.mb.YTPlayer.min.js';
				$cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/'.$version.'/'.$customFileName;
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

		if ($extend && strpos($customContent, '/*#') !== false) {

			$customContentArr = explode('/*#' , $customContent);
			$customContent = $customContentArr[0];

		} elseif (strpos((string)$customContent, '//#') !== false) {

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