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
use T3SBS\T3sbootstrap\Service\AssetPathService;

#[AsCommand('t3sbootstrap:cdnToLocal', 'Write required CSS and JS into the configured site package')]
final class CdnToLocal extends CommandBase
{

    public function __construct(
        private readonly SiteFinder $siteFinder,
        private readonly RequestFactory $requestFactory,
        private readonly AssetPathService $assetPathService,
    ) {
        parent::__construct();
    }

    /**
     * Kept for the private helpers below, which have no $output of their own.
     */
    private ?OutputInterface $output = null;

    private bool $hadFailure = false;

    /**
     * Something went wrong - the command reports FAILURE at the end.
     */
    private function warn(string $message): void
    {
        $this->hadFailure = true;
        $this->output?->writeln('<comment>' . $message . '</comment>');
    }

    /**
     * Worth saying, but not a failure - a font simply not having every weight is
     * normal and must not keep the scheduler task red forever.
     */
    private function notice(string $message): void
    {
        $this->output?->writeln('<info>' . $message . '</info>');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->hadFailure = false;
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

        // $baseDir for the generated assets
        $baseDir = $this->assetPathService->getPath();

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

        } elseif (!empty($googleFontsArr)) {
            // Fonts are configured but the zip extension is gone. Deleting them here
            // would take the locally hosted fonts away from a running site for a
            // reason that has nothing to do with the configuration.
            $this->warn(
                'PHP extension "zip" is not available - the local Google Fonts were kept as they are. '
                . 'Enable ext-zip and run this command again to refresh them.'
            );
        } else {
            // no fonts configured any more - remove all googlefonts
            $localZipPath = $baseDir.'T3SB-CSS/googlefonts/';

            if (is_dir($localZipPath)) {
                $this->rmDir($localZipPath);
            }
            $cssFile = $baseDir.'T3SB-CSS/googlefonts.css';
            if (file_exists($cssFile)) {
                unlink($cssFile);
            }
        }

        // version
        foreach ($settings['cdn'] as $key=>$version) {
            if ($key === 'jquery') {
                $customPath = $baseDir.'T3SB-JS/';
                $customFileName = 'jquery.min.js';
                $cdnPath = 'https://code.jquery.com/jquery-'.$version.'.min.js';
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'bootstrap') {
                $customPath = $baseDir.'T3SB-CSS/';
                $customFileName = 'bootstrap.min.css';

                $cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/css/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath, true);

                $customPath = $baseDir.'T3SB-JS/';
                $customFileName = 'bootstrap.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/js/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
                $customFileName = 'bootstrap.bundle.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/bootstrap@'.$version.'/dist/js/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'popperjs') {
                $customPath = $baseDir.'T3SB-JS/';
                $customFileName = 'popper.js';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/'.$version.'/umd/popper.min.js';
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }
            if ($key === 'lazyload') {
                $customPath = $baseDir.'T3SB-JS/';
                $customFileName = 'lazyload.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/vanilla-lazyload@'.$version.'/dist/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'animate') {
                $customPath = $baseDir.'T3SB-CSS/';
                $customFileName = 'animate.compat.css';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'baguetteBox') {
                $customPath = $baseDir.'T3SB-CSS/';
                $customFileName = 'baguetteBox.min.css';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);

                $customPath = $baseDir.'T3SB-JS/';
                $customFileName = 'baguetteBox.min.js';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }
            if ($key === 'halkabox') {
                $customPath = $baseDir.'T3SB-CSS/';
                $customFileName = 'halkaBox.min.css';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/halkabox@'.$version.'/dist/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath, true);

                $customPath = $baseDir.'T3SB-JS/';
                $customFileName = 'halkaBox.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/halkabox@'.$version.'/dist/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'glightbox') {
                $customPath = $baseDir.'T3SB-CSS/';
                $customFileName = 'glightbox.min.css';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/glightbox@'.$version.'/dist/css/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);

                $customPath = $baseDir.'T3SB-JS/';
                $customFileName = 'glightbox.min.js';
                $cdnPath = 'https://cdn.jsdelivr.net/npm/glightbox@'.$version.'/dist/js/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'masonry') {
                $customPath = $baseDir.'T3SB-JS/';
                $customFileName = 'masonry.pkgd.min.js';
                $cdnPath = 'https://cdnjs.cloudflare.com/ajax/libs/masonry/'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'jarallax') {
                $customPath = $baseDir.'T3SB-JS/';
                $customFileName = 'jarallax.min.js';
                $cdnPath = 'https://unpkg.com/jarallax@'.$version.'/dist/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
                $customFileName = 'jarallax-video.min.js';
                $cdnPath = 'https://unpkg.com/jarallax@'.$version.'/dist/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }

            if ($key === 'swiper') {
                $customPath = $baseDir.'T3SB-CSS/';
                $customFileName = 'swiper-bundle.min.css';
                $cdnPath = 'https://unpkg.com/swiper@'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
                $customPath = $baseDir.'T3SB-JS/';
                $customFileName = 'swiper-bundle.min.js';
                $cdnPath = 'https://unpkg.com/swiper@'.$version.'/'.$customFileName;
                $this->writeCustomFile($customPath, $customFileName, $cdnPath);
            }
        }

        // a skipped download must not look like a clean run in the scheduler
        return $this->hadFailure ? Command::FAILURE : Command::SUCCESS;
    }


    private function writeCustomFile(string $customPath, string $customFileName, string $cdnPath, bool $extend = false): void
    {
        $customFile = $customPath.$customFileName;

        // getURL() returns false on a 404 or an unreachable CDN. Download first and
        // bail out on failure - the old file has to survive a failed download, and
        // writeFile() would get false under strict_types.
        $customContent = GeneralUtility::getURL($cdnPath);

        if (!is_string($customContent) || $customContent === '') {
            $this->warn(sprintf('Download failed, "%s" was left untouched: %s', $customFileName, $cdnPath));
            return;
        }

        if ($extend && str_contains($customContent, '/*#')) {
            $customContentArr = explode('/*#', $customContent);
            $customContent = $customContentArr[0];
        } elseif (str_contains($customContent, '//#')) {
            $customContentArr = explode('//#', $customContent);
            $customContent = $customContentArr[0];
        }

        if (!is_dir($customPath)) {
            if (!mkdir($customPath, 0755, true) && !is_dir($customPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $customPath), 1657348966);
            }
        }

        // write beside the old file and swap it in atomically, so a crash in the
        // middle can never leave a truncated asset behind
        $tempFile = $customFile.'.'.getmypid().'.tmp';
        GeneralUtility::writeFile($tempFile, $customContent);

        if (!@rename($tempFile, $customFile)) {
            @unlink($tempFile);
            throw new \RuntimeException(sprintf('File "%s" was not written', $customFile), 1657348967);
        }
    }


    private function getGoogleFonts(string $googleFonts, string $gooleFontsWeights, string $baseDir): void
    {
        $localZipPath = $baseDir.'T3SB-CSS/googlefonts/';

        // Build into a staging directory. The existing fonts are only removed once
        // the run succeeded - a failing font server must not leave the site
        // without the fonts it is already serving.
        $stagingPath = $baseDir.'T3SB-CSS/googlefonts.tmp/';
        if (is_dir($stagingPath)) {
            $this->rmDir($stagingPath);
        }
        if (!mkdir($stagingPath, 0755, true) && !is_dir($stagingPath)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $stagingPath), 1657363669);
        }

        $weights = array_values(array_filter(array_map('trim', explode(',', $gooleFontsWeights)), static fn($w): bool => $w !== ''));

        // family => weight => base file name (without extension)
        $fontFiles = [];

        foreach (explode(',', $googleFonts) as $font) {
            $fontFamily = trim($font);
            if ($fontFamily === '') {
                continue;
            }
            $slug = strtolower(str_replace(' ', '-', $fontFamily));

            foreach ($weights as $style) {
                $url = 'https://gwfh.mranftl.com/api/fonts/'.$slug
                     . '?download=zip&subsets=latin&variants='.$style;

                try {
                    $zipContent = $this->requestFactory->request($url)->getBody()->getContents();
                } catch (\Throwable $e) {
                    // Not every family has every weight - "Metrophobic" for example
                    // only exists as "regular". A missing variant is skipped, it
                    // must not take the whole font run down with it.
                    $this->notice(sprintf(
                        'Skipped "%s" (%s): %s',
                        $fontFamily,
                        $style,
                        str_contains($e->getMessage(), '404') ? 'this weight does not exist for that font' : $e->getMessage()
                    ));
                    continue;
                }

                $files = $this->getGoogleFiles($zipContent, $stagingPath);

                if ($files === []) {
                    $this->notice(sprintf('Skipped "%s" (%s): the archive contained no font file.', $fontFamily, $style));
                    continue;
                }

                // exactly one variant per archive - remember its base name, so the
                // css below never has to guess a file name
                $fontFiles[$fontFamily][$style] = preg_replace('/\.ttf$/', '', $files[0]);
            }

            if (empty($fontFiles[$fontFamily])) {
                $this->warn(sprintf('No weight of "%s" could be downloaded - check the spelling of the font name.', $fontFamily));
            }
        }

        if ($fontFiles === []) {
            $this->rmDir($stagingPath);
            $this->warn('No Google Font could be downloaded - the local fonts were left untouched.');

            return;
        }

        // Everything is on disk - swap the staging directory in. The old fonts are
        // parked first, so a failing rename cannot leave the site with no fonts.
        $backupPath = rtrim($localZipPath, '/').'.old/';

        if (is_dir($backupPath)) {
            $this->rmDir($backupPath);
        }
        if (is_dir($localZipPath) && !@rename($localZipPath, $backupPath)) {
            $this->rmDir($stagingPath);
            throw new \RuntimeException(sprintf('Directory "%s" could not be replaced', $localZipPath), 1657363671);
        }
        if (!@rename($stagingPath, $localZipPath)) {
            if (is_dir($backupPath)) {
                @rename($backupPath, $localZipPath);
            }
            $this->rmDir($stagingPath);
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $localZipPath), 1657363670);
        }

        $this->rmDir($backupPath);

        // Only the weights that really arrived get a @font-face - a rule for a
        // weight that was skipped would send the browser after a 404.
        $css = '';

        foreach ($fontFiles as $fontFamily => $variants) {
            foreach ($variants as $style => $file) {
                // cast: php turns a numeric array key like '300' into an int, and
                // str_contains() would reject that under strict_types
                $style = (string)$style;
                $isItalic = str_contains($style, 'italic');
                $weight = str_replace('italic', '', $style);
                $weight = $weight === '' || $weight === 'regular' ? '400' : $weight;

                $css .= "@font-face {
    font-family: '".$fontFamily."';
    font-style: ".($isItalic ? 'italic' : 'normal').";
    font-weight: ".$weight.";
    font-display: swap;
    src: url('googlefonts/".$file.".woff2') format('woff2'),
         url('googlefonts/".$file.".ttf') format('truetype');
}".LF.LF;
            }
        }

        $cssFile = $baseDir.'T3SB-CSS/googlefonts.css';
        if (file_exists($cssFile)) {
            unlink($cssFile);
        }
        GeneralUtility::writeFile($cssFile, $css);
    }


    private function getGoogleFiles(string $zipContent, string $targetPath): array
    {
        $googleFileArr = [];

        if ($zipContent === '') {
            return $googleFileArr;
        }

        $before = array_values(array_filter(
            (array)scandir($targetPath),
            static fn($f): bool => str_ends_with((string)$f, 'ttf')
        ));

        $localZipFile = $targetPath.'googlefont.zip';
        GeneralUtility::writeFile($localZipFile, $zipContent);
        $zip = new \ZipArchive();

        if ($zip->open($localZipFile) === true) {
            $zip->extractTo($targetPath);
            $zip->close();
        } else {
            unlink($localZipFile);
            return $googleFileArr;
        }

        if (file_exists($localZipFile)) {
            unlink($localZipFile);
        }

        // only what this archive added - scanning the whole directory would hand
        // the files of a previously extracted font to the next one
        foreach ((array)scandir($targetPath) as $googleFile) {
            $googleFile = (string)$googleFile;
            if (str_ends_with($googleFile, 'ttf') && !in_array($googleFile, $before, true)) {
                $googleFileArr[] = $googleFile;
            }
        }

        return $googleFileArr;
    }

}
