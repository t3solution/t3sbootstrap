<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\EventListener\AssetRenderer;

use TYPO3\CMS\Core\Page\Event\BeforeJavaScriptsRenderingEvent;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Psr\Http\Message\ServerRequestInterface;

#[AsEventListener(
    identifier: 't3sbootstrap/assetPreProcessing',
)]
final readonly class IsInline
{

    public function __invoke(BeforeJavaScriptsRenderingEvent $event): void
    {
        $request = $this->getRequest();

        if (ApplicationType::fromRequest($request)->isBackend()) {
            return;
        }
        
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            't3sbootstrap',
            'm1'
        );

        if (!empty($settings['disableAssetRenderer']) || !empty($settings['disableInlineJs'])) {
            return;
        }

        // Not inline, not priority: placeholder for future JS bundles
        if (!$event->isInline() && !$event->isPriority()) {
            return;
        }

        // Inline + priority -> move CSS to a temp file
        if ($event->isInline() && $event->isPriority()) {
            $this->processInlineCss($event);
            return;
        }

        // Inline + non-priority -> move JS to a temp file
        if ($event->isInline() && !$event->isPriority()) {
            $this->processInlineJs($event);
        }
    }

    private function processInlineCss(BeforeJavaScriptsRenderingEvent $event): void
    {
        $css = '';

        foreach ($event->getAssetCollector()->getInlineStyleSheets() as $library => $source) {
            $css .= LF . '/*** T3SB identifier: ' . $library . ' */' . LF;
            $css .= $source['source'] . LF . LF;
            $event->getAssetCollector()->removeInlineStyleSheet($library);
        }

        if (empty($css)) {
            return;
        }

        $cssFile = self::inline2TempFile($css, 'css');
        if ($cssFile) {
            // @extensionScannerIgnoreLine
            $event->getAssetCollector()->addStyleSheet('t3sbootstrapcss', $cssFile, ['media' => 'all']);
        }
    }

    private function processInlineJs(BeforeJavaScriptsRenderingEvent $event): void
    {
        $addheight = '';
        $jquery    = '';
        $js        = '';
        $function  = '';
        $rawJs     = '';

        foreach ($event->getAssetCollector()->getInlineJavaScripts() as $library => $source) {
            // Skip JSON data (TypoScript settings and the like)
            if (str_starts_with($source['source'], '{"')) {
                continue;
            }

            if (str_ends_with($library, 'function')) {
                $function .= $source['source'] . LF . LF;
            } elseif (str_starts_with($library, 'vanilla')) {
                $js .= $source['source'] . LF;
            } elseif (str_starts_with($library, 'addheight-')) {
                $addheight .= $source['source'] . LF . LF;
            } elseif (str_starts_with($library, 'jquery')) {
                $jquery .= $source['source'] . LF . LF;
            } else {
                $rawJs .= $source['source'] . LF . LF;
            }

            $event->getAssetCollector()->removeInlineJavaScript($library);
        }

        $source = $this->buildJsSource($function, $addheight, $js, $jquery, $rawJs);

        if (empty($source)) {
            return;
        }

        $jsFile = self::inline2TempFile($source, 'js');
        if ($jsFile) {
            $event->getAssetCollector()->addJavaScript('t3sbootstrapjs', $jsFile);
        }
    }

    private function buildJsSource(
        string $function,
        string $addheight,
        string $js,
        string $jquery,
        string $rawJs
    ): string {
        $source = '';

        if ($function) {
            $source .= $function . LF;
        }

        $addheightJs = '';
        if ($addheight) {
            $addheightJs = LF
                . '// Autoheight for background images' . LF
                . 'var TYPO3 = TYPO3 || {};' . LF
                . 'TYPO3.settings = {\'ADDHEIGHT\':{' . rtrim(trim($addheight), ',') . '}};' . LF;
        }

        // DOMContentLoaded wrapper - only when there is something to wrap. Without the
        // condition $source is never empty, the caller's early return never fires, and
        // every page without inline JS still gets a typo3temp file plus an extra request.
        if ($addheightJs !== '' || $js !== '') {
            $source .= <<<JS
                function ready(fn) {
                    if (document.readyState !== 'loading') {
                        fn();
                    } else {
                        document.addEventListener('DOMContentLoaded', fn);
                    }
                }
                ready(() => {{$addheightJs}{$js}});
                JS . LF;
        }

        if ($jquery) {
            $source .= LF . "(function($){'use strict';" . LF . $jquery . LF . '})(jQuery);' . LF;
        }

        if ($rawJs) {
            $source .= LF . $rawJs . LF;
        }

        return $source;
    }

    public static function inline2TempFile(string $str, string $ext): string
    {
        if (!in_array($ext, ['js', 'css'], true)) {
            return '';
        }

        $script   = 'typo3temp/assets/t3sbootstrap_' . substr(md5($str), 0, 10) . '.' . $ext;
        $fullPath = Environment::getPublicPath() . '/' . $script;

        // The file name is the hash of the content, so the expected size is known.
        // A mismatch means an aborted write: the file exists but is incomplete.
        // file_exists() alone was happy with that and kept serving it forever,
        // because the same check passed again on every following request. The
        // size comparison repairs it on the next page hit.
        clearstatcache(true, $fullPath);
        if (file_exists($fullPath) && filesize($fullPath) === strlen($str)) {
            return $script;
        }

        // Write in full, then rename. rename() is atomic within one filesystem:
        // a concurrent request sees the file either not at all or complete, never
        // half-written. Writing straight to the target name leaves that window
        // open, and half a script kills the JavaScript of the whole page.
        $tmpPath = $fullPath . '.' . uniqid('', true) . '.tmp';
        if (!GeneralUtility::writeFile($tmpPath, $str)) {
            return '';
        }

        if (!@rename($tmpPath, $fullPath)) {
            @unlink($tmpPath);

            // Another request may have finished in the same second - then the
            // file is there and the reference stays valid.
            clearstatcache(true, $fullPath);

            return file_exists($fullPath) ? $script : '';
        }

        return $script;
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}