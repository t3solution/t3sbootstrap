<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use TYPO3\CMS\Core\Http\RequestFactory;
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
class CdnToLocal extends CommandBase
{
	const localZipFile = 'googlefont.zip';
	const localZipPath = 'fileadmin/T3SB/Resources/Public/CSS/googlefonts/';
	const zipFilePath = 'https://gwfh.mranftl.com/api/fonts/';
	const localGoogleFile = 'fileadmin/T3SB/Resources/Public/CSS/googlefonts.css';

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
			if ( (int)$extConf['fontawesomeCss'] > 2 ) {
				if ( (int)$settings['cdn']['fontawesome'] < 6 ) {
					$settings['cdn']['fontawesome'] = $settings['cdn']['fontawesome6latest'];
				}
			}
		} else {
			$settings['cdn']['fontawesome'] = $settings['cdn']['fontawesome6latest'];
		}
		if ( !empty($settings['cdn']['googlefonts']) && empty($settings['cdn']['noZip']) ) {
			self::getGoogleFonts($settings['cdn']['googlefonts'], $settings['preloadGooleFonts'], $settings['gooleFontsWeights']);
		} else {
			$localZipPath = GeneralUtility::getFileAbsFileName(self::localZipPath);
			if ( is_dir($localZipPath) ) {
				parent::rmDir($localZipPath);
			}
			$cssFile = GeneralUtility::getFileAbsFileName(self::localGoogleFile);
			if (file_exists($cssFile)) unlink($cssFile);

			$customDir = 'fileadmin/T3SB/Configuration/TypoScript/';
			$customPath = GeneralUtility::getFileAbsFileName($customDir);
			$customFileName = 'preloadGooleFonts.typoscript';
			$customFile = $customPath.$customFileName;
			if (file_exists($customFile)) {unlink($customFile);}
			if (!is_dir($customPath)) {mkdir($customPath, 0777, true);}
			GeneralUtility::writeFile($customFile, '');
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


	private function getGoogleFonts($googleFonts, $preloadGooleFonts, $gooleFontsWeights) {
		$localZipPath = GeneralUtility::getFileAbsFileName(self::localZipPath);
		if ( is_dir($localZipPath) ) {
			parent::rmDir($localZipPath);
		}
		mkdir($localZipPath, 0777, true);
		$localZipFile = GeneralUtility::getFileAbsFileName(self::localZipPath.self::localZipFile);
		$googleFontsArr = explode(',', $googleFonts);
		foreach ($googleFontsArr as $font) {
			$fontFamily = trim($font);
			$font = str_replace(' ', '-', trim($font));
			foreach ( explode(',', $gooleFontsWeights) as $style ) {
				$style = trim($style);
				$zipFilename = strtolower($font).'?download=zip&subsets=latin&variants='.$style;
				$zipContent = GeneralUtility::makeInstance(RequestFactory::class)->request(self::zipFilePath . $zipFilename)->getBody()->getContents();
				$fontArr[$fontFamily] = self::getGoogleFiles($zipContent, $localZipFile, $localZipPath);
			}
		}

		if ( is_array($fontArr)) {
			foreach ($fontArr as $fontFamily=>$googlePath) {
				$sliceArr[$fontFamily] = array_slice($googlePath, 0, 1);
			}
			$css = '';
			$headerData = '';
			foreach ($sliceArr as $fontFamily=>$googlePath) {
				foreach ( explode(',', $gooleFontsWeights) as $i=>$style ) {
					$style = trim($style);
					$file = str_replace('300','', explode('.', $googlePath[0])[0]).$style;
					$style = $style == 'regular' ? '400' : $style;
					if (!empty($preloadGooleFonts)) {
						$num = self::generateRandomString();
						$s = $i + 1;
						$headerData .= '	22'.$num.$i.' = TEXT'.LF;
						$headerData .= '	22'.$num.$i.'.value = <link rel="preload" href="/fileadmin/T3SB/Resources/Public/CSS/googlefonts/'.
						$file.'.woff" as="font" type="font/woff" crossorigin="anonymous">'.LF;
						$headerData .= '	22'.$num.$s.' = TEXT'.LF;
						$headerData .= '	22'.$num.$s.'.value = <link rel="preload" href="/fileadmin/T3SB/Resources/Public/CSS/googlefonts/'.
						$file.'.woff2" as="font" type="font/woff2" crossorigin="anonymous">'.LF;
					}

$css .= "@font-face {
  font-family: '".$fontFamily."';
  font-style: normal;
  font-weight: ".$style.";
  font-display: swap;
  src: url('/fileadmin/T3SB/Resources/Public/CSS/googlefonts/".$file.".eot');
  src: local(''),
  		url('/fileadmin/T3SB/Resources/Public/CSS/googlefonts/".$file.".eot?#iefix') format('embedded-opentype'),
		url('/fileadmin/T3SB/Resources/Public/CSS/googlefonts/".$file.".woff2') format('woff2'),
		url('/fileadmin/T3SB/Resources/Public/CSS/googlefonts/".$file.".woff') format('woff');
		url('/fileadmin/T3SB/Resources/Public/CSS/googlefonts/".$file.".ttf') format('truetype'),
		url('/fileadmin/T3SB/Resources/Public/CSS/googlefonts/".$file.".svg#".trim(str_replace(' ', '', $fontFamily))."') format('svg');
}".LF.LF;
				}
			}

			if (!empty($preloadGooleFonts)) {
				$setup = 'page.headerData {'.LF.$headerData;
				$setup .= '}';
				$customDir = 'fileadmin/T3SB/Configuration/TypoScript/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 'preloadGooleFonts.typoscript';
				$customFile = $customPath.$customFileName;
				if (file_exists($customFile)) {unlink($customFile);}
				if (!is_dir($customPath)) {mkdir($customPath, 0777, true);}
				GeneralUtility::writeFile($customFile, $setup);
			}

			if (!empty($css)) {
				$cssFile = GeneralUtility::getFileAbsFileName(self::localGoogleFile);
				if (file_exists($cssFile)) unlink($cssFile);
				GeneralUtility::writeFile($cssFile, $css);
			}
		}
	}


	private function getGoogleFiles($zipContent, $localZipFile, $localZipPath) {
		if ($zipContent) {
			GeneralUtility::writeFile($localZipFile, $zipContent);
			$zip = new \ZipArchive;
			if ($zip->open($localZipFile) === TRUE) {
				$zip->extractTo($localZipPath);
				$zip->close();
			} else {
				throw new \InvalidArgumentException('Sorry ZIP creation failed at this time!', 1655291469);
			}
			$zipFile = GeneralUtility::getFileAbsFileName($localZipFile);
			if (file_exists($zipFile)) unlink($zipFile);
			$googleFiles = scandir($localZipPath);
			$css = '';
			$googleFileArr = [];
			foreach ($googleFiles as $googleFile) {
				if ( str_ends_with($googleFile, 'woff') ) {
					$googleFileArr[] = $googleFile;
				}
			}
		} else {
			throw new \InvalidArgumentException('Check the spelling of the google fonts!', 1657464667);
		}

		return $googleFileArr;
	}


	private function generateRandomString($length = 4) {
		 $characters = '0123456789';
		 $charactersLength = strlen($characters);
		 $randomString = '';
		 for ($i = 0; $i < $length; $i++) {
			 $randomString .= $characters[rand(0, $charactersLength - 1)];
		 }
		 return $randomString;
	}

}
