<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Hooks\PageRenderer;

use TYPO3\CMS\Core\Page\PageRenderer;
use T3SBS\T3sbootstrap\Service\CompileService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PreProcessHook
{
    /**
     * Assets written by t3sbootstrap:cdnToLocal and referenced from TypoScript
     * or from the Fluid templates. Before v5.3.50 they lived in
     * into the asset directory on the first frontend request after an update.
     */
    private const MANAGED_ASSETS = [
        'T3SB-CSS/bootstrap.min.css',
        'T3SB-CSS/googlefonts.css',
        'T3SB-CSS/animate.compat.css',
        'T3SB-CSS/baguetteBox.min.css',
        'T3SB-CSS/halkaBox.min.css',
        'T3SB-CSS/glightbox.min.css',
        'T3SB-CSS/swiper-bundle.min.css',
        'T3SB-JS/jquery.min.js',
        'T3SB-JS/popper.js',
        'T3SB-JS/bootstrap.min.js',
        'T3SB-JS/bootstrap.bundle.min.js',
        'T3SB-JS/lazyload.min.js',
        'T3SB-JS/baguetteBox.min.js',
        'T3SB-JS/halkaBox.min.js',
        'T3SB-JS/glightbox.min.js',
        'T3SB-JS/swiper-bundle.min.js',
        'T3SB-JS/masonry.pkgd.min.js',
        'T3SB-JS/jarallax.min.js',
        'T3SB-JS/jarallax-video.min.js',
    ];

    /**
     * @var \T3SBS\T3sbootstrap\Service\CompileService
     */
    protected $compileService;

    /**
     * @var \T3SBS\T3sbootstrap\Service\AssetPathService
     */
    protected $assetPathService;


    /**
     * @param array $params
     * @param PageRenderer $pagerenderer
     */
    public function execute(&$params, &$pagerenderer): void
    {
        if (!($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface ||
            !ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {
            return;
        }

        foreach (['cssLibs', 'cssFiles'] as $key) {
            $files = [];
            if (is_array($params[$key])) {
                foreach ($params[$key] as $file => $settings) {
                    $compiledFile = $this->getCompileService()->getCompiledFile($GLOBALS['TYPO3_REQUEST'], $file);
                    if ($compiledFile !== null) {
                        $settings['file'] = $compiledFile;
                        $files[$compiledFile] = $settings;
                    } else {
                        $files[$file] = $settings;
                    }
                }
                $params[$key] = $files;
            }
        }
    }



    /**
     * Get the compile service
     */
    protected function getCompileService(): CompileService
    {
        if ($this->compileService === null) {
            $this->compileService = GeneralUtility::makeInstance(CompileService::class);
        }
        return $this->compileService;
    }
}
