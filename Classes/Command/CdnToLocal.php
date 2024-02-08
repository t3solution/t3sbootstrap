<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Http\RequestFactory;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class CdnToLocal extends CommandBase
{
    /**
     * Defines the allowed options for this command
     *
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Write required CSS and JS to fileadmin/ or EXT:t3sb_package/');
    }


    /**
     * Update all records
     *
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            't3sbootstrap',
            'm1'
        );

        if (empty($settings['sitepackage'])) {
            $baseDir = GeneralUtility::getFileAbsFileName('fileadmin/T3SB/');
        } else {
            if (ExtensionManagementUtility::isLoaded('t3sb_package')) {
                $baseDir = GeneralUtility::getFileAbsFileName('EXT:t3sb_package/T3SB/');
            } else {
                throw new \InvalidArgumentException('Your t3sb_package is not loaded!', 1657464787);
            }
        }

        # check FA version & settings
        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

        if (!empty($extConf['fontawesomeCss'])) {
            if ((int)$extConf['fontawesomeCss'] > 2) {
                if ((int)$settings['cdn']['fontawesome'] < 6) {
                    $settings['cdn']['fontawesome'] = $settings['cdn']['fontawesome6latest'];
                }
            }
        } else {
            $settings['cdn']['fontawesome'] = $settings['cdn']['fontawesome6latest'];
        }

        if (!empty($settings['cdn']['googlefonts']) && empty($settings['cdn']['noZip'])) {
            if (empty($settings['sitepackage'])) {
                self::getGoogleFonts($settings['cdn']['googlefonts'], $settings['gooleFontsWeights'], $baseDir);
            } else {
                self::getGoogleFontsSitepackage($settings['cdn']['googlefonts'], $settings['gooleFontsWeights'], $baseDir);
            }
        } else {
            $localZipPath = $baseDir.'Resources/Public/CSS/googlefonts/';
            if (is_dir($localZipPath)) {
                parent::rmDir($localZipPath);
            }
            $cssFile = $baseDir.'Resources/Public/CSS/googlefonts.css';
            if (file_exists($cssFile)) {
                unlink($cssFile);
            }
        }

        foreach ($settings['cdn'] as $key=>$version) {
            if ($key == 'jquery') {
                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'jquery.min.js';
                $cdnPath = 'https://code.jquery.com/jquery-'.$version.'.min.js';
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key == 'bootstrap') {
                $customPath = $baseDir.'Resources/Public/CSS/';
                $customFileName = 'bootstrap.min.css';
                if ($settings['cdn']['bootswatch']) {
                    $bootswatchTheme = $settings['cdn']['bootswatch'];
                    $cdnPath = 'https://cdn.jsdelivr.net/npm/bootswatch@'.$version.'/dist/'.$bootswatchTheme.'/'.$customFileName;
                    self::writeCustomFile($customPath, $customFileName, $cdnPath, true);
                } else {
                    $cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/css/'.$customFileName;
                    self::writeCustomFile($customPath, $customFileName, $cdnPath, true);
                }

                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'bootstrap.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/js/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
                $customFileName = 'bootstrap.bundle.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/js/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key == 'popperjs') {
                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'popper.js';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/'.$version.'/umd/popper.min.js';
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key == 'fontawesome') {
                if ((int)$extConf['fontawesomeCss'] == 1 || (int)$extConf['fontawesomeCss'] == 2) {
                    $customPath = $baseDir.'Resources/Public/FA6-Kit/';
                    if (!is_dir($customPath)) {
                        mkdir($customPath, 0777, true);
                    }
                } else {
                    $customPath = $baseDir.'Resources/Public/CSS/';
                    $customFileName = 'fontawesome.min.css';
                    $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/'.$version.'/css/all.min.css';
                    self::writeCustomFile($customPath, $customFileName, $cdnPath);

                    $src = GeneralUtility::getFileAbsFileName('EXT:t3sbootstrap/Resources/Public/Contrib/Fontawesome/webfonts');
                    if (is_dir($src)) {
                        $dest = $baseDir.'Resources/Public/webfonts/';
                        if (!is_dir($dest)) {
                            mkdir($dest, 0777, true);
                        }
                        $fileLists = GeneralUtility::getAllFilesAndFoldersInPath([], $src);
                        foreach ($fileLists as $file) {
                            copy($file, $dest.end(explode('/', $file)));
                        }
                    }

                    $customPath = $baseDir.'Resources/Public/JS/';
                    $customFileName = 'fontawesome.min.js';
                    $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/'.$version.'/js/all.min.js';
                    self::writeCustomFile($customPath, $customFileName, $cdnPath);
                }
            }

            if ($key == 'jqueryEasing') {
                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'jquery.easing.min.js';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/'.$version.'/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key == 'lazyload') {
                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'lazyload.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/vanilla-lazyload@'.$version.'/dist/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key == 'picturefill') {
                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'picturefill.min.js';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/picturefill/'.$version.'/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key == 'animate') {
                $customPath = $baseDir.'Resources/Public/CSS/';
                $customFileName = 'animate.compat.css';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/'.$version.'/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key == 'baguetteBox') {
                $customPath = $baseDir.'Resources/Public/CSS/';
                $customFileName = 'baguetteBox.min.css';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/'.$version.'/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);

                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'baguetteBox.min.js';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/'.$version.'/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }
            if ($key == 'halkabox') {
                $customPath = $baseDir.'Resources/Public/CSS/';
                $customFileName = 'halkaBox.min.css';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/halkabox@'.$version.'/dist/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath, true);

                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'halkaBox.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/halkabox@'.$version.'/dist/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key == 'glightbox') {
                $customPath = $baseDir.'Resources/Public/CSS/';
                $customFileName = 'glightbox.min.css';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/glightbox@'.$version.'/dist/css/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);

                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'glightbox.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/glightbox@'.$version.'/dist/js/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key == 'masonry') {
                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'masonry.pkgd.min.js';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/masonry/'.$version.'/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key == 'jarallax') {
                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'jarallax.min.js';
                $cdnPath = 'https://unpkg.com/jarallax@'.$version.'/dist/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
                $customFileName = 'jarallax-video.min.js';
                $cdnPath = 'https://unpkg.com/jarallax@'.$version.'/dist/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key == 'swiper') {
                $customPath = $baseDir.'Resources/Public/CSS/';
                $customFileName = 'swiper-bundle.min.css';
                $cdnPath = 'https://unpkg.com/swiper@'.$version.'/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
                $customPath = $baseDir.'Resources/Public/JS/';
                $customFileName = 'swiper-bundle.min.js';
                $cdnPath = 'https://unpkg.com/swiper@'.$version.'/'.$customFileName;
                self::writeCustomFile($customPath, $customFileName, $cdnPath);
            }
        }

        return Command::SUCCESS;
    }


    private function writeCustomFile($customPath, $customFileName, $cdnPath, $extend=false): void
    {
        $customFile = $customPath.$customFileName;
        $customContent = GeneralUtility::getURL($cdnPath);

        if ($extend && str_contains((string)$customContent, '/*#')) {
            $customContentArr = explode('/*#', $customContent);
            $customContent = $customContentArr[0];
        } elseif (str_contains((string)$customContent, '//#')) {
            $customContentArr = explode('//#', $customContent);
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


    private function getGoogleFonts($googleFonts, $gooleFontsWeights, $baseDir): void
    {
        $localZipPath = $baseDir.'Resources/Public/CSS/googlefonts/';
        if (is_dir($localZipPath)) {
            parent::rmDir($localZipPath);
        }
        mkdir($localZipPath, 0777, true);
        $googleFontsArr = explode(',', $googleFonts);
        foreach ($googleFontsArr as $font) {
            $fontFamily = trim($font);
            $font = str_replace(' ', '-', trim($font));
            foreach (explode(',', $gooleFontsWeights) as $style) {
                $style = trim($style);
                $zipFilename = strtolower($font).'?download=zip&subsets=latin&variants='.$style;
                $zipFilePath = 'https://gwfh.mranftl.com/api/fonts/';
                $zipContent = GeneralUtility::makeInstance(RequestFactory::class)->request($zipFilePath . $zipFilename)->getBody()->getContents();
                $fontArr[$fontFamily] = self::getGoogleFiles($zipContent, $baseDir);
            }
        }

        if (is_array($fontArr)) {
            $basePath = '/fileadmin/T3SB/';
            foreach ($fontArr as $fontFamily=>$googlePath) {
                $sliceArr[$fontFamily] = array_slice($googlePath, 0, 1);
            }
            $css = '';
            $headerData = '';
            foreach ($sliceArr as $fontFamily=>$googlePath) {
                foreach (explode(',', $gooleFontsWeights) as $i=>$style) {
                    $style = trim($style);
                    $file = str_replace('300', '', explode('.', $googlePath[0])[0]).$style;
                    $style = $style == 'regular' ? '400' : $style;
                    $css .= "@font-face {
    font-family: '".$fontFamily."';
    font-style: normal;
    font-weight: ".$style.";
    font-display: swap;
    src: url('".$basePath."Resources/Public/CSS/googlefonts/".$file.".eot');
    src: local(''),
            url('googlefonts/".$file.".eot?#iefix') format('embedded-opentype'),
        url('googlefonts/".$file.".woff2') format('woff2'),
        url('googlefonts/".$file.".woff') format('woff');
        url('googlefonts/".$file.".ttf') format('truetype'),
        url('googlefonts/".$file.".svg#".trim(str_replace(' ', '', $fontFamily))."') format('svg');
}".LF.LF;
                }
            }
            if (!empty($css)) {
                $cssFile = $baseDir.'Resources/Public/CSS/googlefonts.css';
                if (file_exists($cssFile)) {
                    unlink($cssFile);
                }
                GeneralUtility::writeFile($cssFile, $css);
            }
        }
    }

    private function getGoogleFontsSitepackage($googleFonts, $gooleFontsWeights, $baseDir): void
    {
        $localZipPath = $baseDir.'Resources/Public/CSS/googlefonts/';
        if (is_dir($localZipPath)) {
            parent::rmDir($localZipPath);
        }
        mkdir($localZipPath, 0777, true);
        $googleFontsArr = explode(',', $googleFonts);
        foreach ($googleFontsArr as $font) {
            $fontFamily = trim($font);
            $font = str_replace(' ', '-', trim($font));
            foreach (explode(',', $gooleFontsWeights) as $style) {
                $style = trim($style);
                $zipFilename = strtolower($font).'?download=zip&subsets=latin&variants='.$style;
                $zipFilePath = 'https://gwfh.mranftl.com/api/fonts/';
                $zipContent = GeneralUtility::makeInstance(RequestFactory::class)->request($zipFilePath . $zipFilename)->getBody()->getContents();
                $fontArr[$fontFamily] = self::getGoogleFiles($zipContent, $baseDir);
            }
        }

        if (is_array($fontArr)) {
            foreach ($fontArr as $fontFamily=>$googlePath) {
                $sliceArr[$fontFamily] = array_slice($googlePath, 0, 1);
            }
            $css = '';
            $headerData = '';

            foreach ($sliceArr as $fontFamily=>$googlePath) {
                foreach (explode(',', $gooleFontsWeights) as $i=>$style) {
                    $style = trim($style);
                    $file = str_replace('300', '', explode('.', $googlePath[0])[0]).$style;
                    $style = $style == 'regular' ? '400' : $style;
                    $googlefontsPath = 'googlefonts/';
                    $css .= "@font-face {
        font-family: '".$fontFamily."';
        font-style: normal;
        font-weight: ".$style.";
        font-display: swap;
        src: url('".$googlefontsPath.$file.".eot');
        src: local(''),
            url('".$googlefontsPath.$file.".eot?#iefix') format('embedded-opentype'),
            url('".$googlefontsPath.$file.".woff2') format('woff2'),
            url('".$googlefontsPath.$file.".woff') format('woff');
            url('".$googlefontsPath.$file.".ttf') format('truetype'),
            url('".$googlefontsPath.$file.".svg#".trim(str_replace(' ', '', $fontFamily))."') format('svg');
    }".LF.LF;
                }
            }
            if (!empty($css)) {
                $cssFile = $baseDir.'Resources/Public/CSS/googlefonts.css';
                if (file_exists($cssFile)) {
                    unlink($cssFile);
                }
                GeneralUtility::writeFile($cssFile, $css);
            }
        }
    }


    private function getGoogleFiles($zipContent, $baseDir='/'): array
    {
        $googleFileArr = [];
        if ($zipContent) {
            $localZipPath = $baseDir.'Resources/Public/CSS/googlefonts/';
            $localZipFile = $localZipPath.'googlefont.zip';

            GeneralUtility::writeFile($localZipFile, $zipContent);
            $zip = new \ZipArchive();
            if ($zip->open($localZipFile) === true) {
                $zip->extractTo($localZipPath);
                $zip->close();
            } else {
                throw new \InvalidArgumentException('Sorry ZIP creation failed at this time!', 1655291469);
            }
            if (file_exists($localZipFile)) {
                unlink($localZipFile);
            }
            $googleFiles = scandir($localZipPath);
            $css = '';

            foreach ($googleFiles as $googleFile) {
                if (str_ends_with($googleFile, 'woff')) {
                    $googleFileArr[] = $googleFile;
                }
            }
        } else {
            throw new \InvalidArgumentException('Check the spelling of the google fonts!', 1657464667);
        }

        return $googleFileArr;
    }


    private function generateRandomString($length = 4): string
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
