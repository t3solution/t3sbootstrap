<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Site\SiteFinder;

#[AsCommand('t3sbootstrap:cdnToLocal', 'Write required CSS and JS to EXT:t3sb_package/')]
final class CdnToLocal extends CommandBase
{

    public function __construct(
        private readonly SiteFinder $siteFinder,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
        $getAllSites = $this->siteFinder->getAllSites();
        $noZip = false;
        if (!extension_loaded('zip')) {
            // PHP extension Zip is disabled
            $noZip = true;
        }

        // get settings from first site set
        $settings = [];
        foreach(array_reverse($getAllSites) as $siteSetting) {
            if (is_array($siteSetting->getConfiguration()['settings']['bootstrap'])) {
                $settings = $siteSetting->getConfiguration()['settings']['bootstrap'];
                break;
            }
        }

        // “T3S Bootstrap – VERSION” should be integrated
        if (empty($settings['cdn']['bootstrap'])) {
            throw new \RuntimeException('The optional site set “T3S Bootstrap – VERSION” should be integrated.', 1654474884);
        }

        // $baseDir for t3sb_package
        $baseDir = GeneralUtility::getFileAbsFileName('EXT:t3sb_package/Resources/');

        // google fonts
        $googleFontsArr = [];
        // get google fonts from all site sets
        foreach($getAllSites as $siteSetting) {
            if (!empty($siteSetting->getConfiguration()['settings']['bootstrap']['cdn']['googlefonts'])) {
                $googleFontsArr[] = $siteSetting->getConfiguration()['settings']['bootstrap']['cdn']['googlefonts'];
            }
        }

        if (!empty($googleFontsArr) && $noZip === false) {
            $googleFonts = '';
            foreach ($googleFontsArr as $googleFont) {
                $googleFonts .= ', ' . $googleFont;
            }
            $googleFonts = substr($googleFonts, 2);

            if (!empty($googleFonts)) {
                $this->getGoogleFonts($googleFonts, $settings['gooleFontsWeights'], $baseDir);
            }

        } else {
            // remove all googlefonts
            $localZipPath = $baseDir.'Public/T3SB-CSS/googlefonts/';

            if (is_dir($localZipPath)) {
                $this->rmDir($localZipPath);
            }
            $cssFile = $baseDir.'Public/T3SB-CSS/googlefonts.css';
            if (file_exists($cssFile)) {
                unlink($cssFile);
            }
        }

        // version
        foreach ($settings['cdn'] as $key=>$version) {
            if ($key === 'jquery') {
                $customPath = $baseDir.'Public/T3SB-JS/';
                $customFileName = 'jquery.min.js';
                $cdnPath = 'https://code.jquery.com/jquery-'.$version.'.min.js';
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'bootstrap') {
                $customPath = $baseDir.'Public/T3SB-CSS/';
                $customFileName = 'bootstrap.min.css';

                $cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/css/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath, true);

                $customPath = $baseDir.'Public/T3SB-JS/';
                $customFileName = 'bootstrap.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/js/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
                $customFileName = 'bootstrap.bundle.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/js/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'popperjs') {
                $customPath = $baseDir.'Public/T3SB-JS/';
                $customFileName = 'popper.js';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/'.$version.'/umd/popper.min.js';
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }
            if ($key === 'lazyload') {
                $customPath = $baseDir.'Public/T3SB-JS/';
                $customFileName = 'lazyload.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/vanilla-lazyload@'.$version.'/dist/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'animate') {
                $customPath = $baseDir.'Public/T3SB-CSS/';
                $customFileName = 'animate.compat.css';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'baguetteBox') {
                $customPath = $baseDir.'Public/T3SB-CSS/';
                $customFileName = 'baguetteBox.min.css';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);

                $customPath = $baseDir.'Public/T3SB-JS/';
                $customFileName = 'baguetteBox.min.js';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }
            if ($key === 'halkabox') {
                $customPath = $baseDir.'Public/T3SB-CSS/';
                $customFileName = 'halkaBox.min.css';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/halkabox@'.$version.'/dist/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath, true);

                $customPath = $baseDir.'Public/T3SB-JS/';
                $customFileName = 'halkaBox.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/halkabox@'.$version.'/dist/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'glightbox') {
                $customPath = $baseDir.'Public/T3SB-CSS/';
                $customFileName = 'glightbox.min.css';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/glightbox@'.$version.'/dist/css/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);

                $customPath = $baseDir.'Public/T3SB-JS/';
                $customFileName = 'glightbox.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/glightbox@'.$version.'/dist/js/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'masonry') {
                $customPath = $baseDir.'Public/T3SB-JS/';
                $customFileName = 'masonry.pkgd.min.js';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/masonry/'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'jarallax') {
                $customPath = $baseDir.'Public/T3SB-JS/';
                $customFileName = 'jarallax.min.js';
                $cdnPath = 'https://unpkg.com/jarallax@'.$version.'/dist/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
                $customFileName = 'jarallax-video.min.js';
                $cdnPath = 'https://unpkg.com/jarallax@'.$version.'/dist/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'swiper') {
                $customPath = $baseDir.'Public/T3SB-CSS/';
                $customFileName = 'swiper-bundle.min.css';
                $cdnPath = 'https://unpkg.com/swiper@'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
                $customPath = $baseDir.'Public/T3SB-JS/';
                $customFileName = 'swiper-bundle.min.js';
                $cdnPath = 'https://unpkg.com/swiper@'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }
        }

        return Command::SUCCESS;
    }


    private function writeCustomFile(string $customPath, string $customFileName, string $cdnPath, bool $extend = false): void
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
            if (!mkdir($customPath, 0755, true) && !is_dir($customPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $customPath), 1657348966);
            }
        }

        GeneralUtility::writeFile($customFile, $customContent);
    }


    private function getGoogleFonts(string $googleFonts, string $gooleFontsWeights, string $baseDir): void
    {
        $localZipPath = $baseDir.'Public/T3SB-CSS/googlefonts/';
        if (is_dir($localZipPath)) {
            $this->rmDir($localZipPath);
        }
        if (!mkdir($localZipPath, 0755, true) && !is_dir($localZipPath)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $localZipPath), 1657363669);
        }
        $googleFontsArr = explode(',', $googleFonts);
        $fontArr = [];

        foreach ($googleFontsArr as $font) {
            $fontFamily = trim($font);
            $font = str_replace(' ', '-', trim($font));

            foreach (explode(',', $gooleFontsWeights) as $style) {
                $style = trim($style);
                $zipFilename = strtolower($font).'?download=zip&subsets=latin&variants='.$style;
                $zipFilePath = 'https://gwfh.mranftl.com/api/fonts/';
                $zipContent = GeneralUtility::makeInstance(RequestFactory::class)->request($zipFilePath . $zipFilename)->getBody()->getContents();
                $fontArr[$fontFamily] = $this->getGoogleFiles($zipContent, $baseDir);
            }
        }

        $sliceArr = [];
        foreach ($fontArr as $fontFamily=>$googlePath) {
            $sliceArr[$fontFamily] = array_slice($googlePath, 0, 1);
        }
        $css = '';

        foreach ($sliceArr as $fontFamily=>$googlePath) {

            $gp = explode('.', $googlePath[0]);
            $gp = explode('-', $gp[0]);
            $replace = end($gp);

            foreach (explode(',', $gooleFontsWeights) as $i=>$style) {
                $style = trim($style);
                $file = str_replace($replace, '', explode('.', $googlePath[0])[0]).$style;
                $style = $style === 'regular' ? '400' : $style;
                $css .= "@font-face {
    font-family: '".$fontFamily."';
    font-style: normal;
    font-weight: ".$style.";
    font-display: swap;
    src: url('googlefonts/".$file.".woff2') format('woff2'),
         url('googlefonts/".$file.".ttf') format('truetype');
}".LF.LF;
            }
        }
        if (!empty($css)) {
            $cssFile = $baseDir.'Public/T3SB-CSS/googlefonts.css';
            if (file_exists($cssFile)) {
                unlink($cssFile);
            }
            GeneralUtility::writeFile($cssFile, $css);
        }
    }


    private function getGoogleFiles(string $zipContent, string $baseDir = '/'): array
    {
        $googleFileArr = [];
        if ($zipContent) {
            $localZipPath = $baseDir.'Public/T3SB-CSS/googlefonts/';
            $localZipFile = $localZipPath.'googlefont.zip';
            GeneralUtility::writeFile($localZipFile, $zipContent);
            $zip = new \ZipArchive();
            if ($zip->open($localZipFile) === true) {
                $zip->extractTo($localZipPath);
                $zip->close();
            } else {
                throw new \InvalidArgumentException('Sorry ZIP creation failed at this time - try again later.', 1655291469);
            }
            if (file_exists($localZipFile)) {
                unlink($localZipFile);
            }
            $googleFiles = scandir($localZipPath);
            foreach ($googleFiles as $googleFile) {
                if (str_ends_with($googleFile, 'ttf')) {
                    $googleFileArr[] = $googleFile;
                }
            }
        } else {
            throw new \InvalidArgumentException('Check the spelling of the google fonts!', 1657464667);
        }

        return $googleFileArr;
    }

}
