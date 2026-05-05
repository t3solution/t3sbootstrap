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

        // Nicht-inline, nicht-priority: Platzhalter für künftige JS-Bundles
        if (!$event->isInline() && !$event->isPriority()) {
            return;
        }

        // Inline + priority → CSS in Temp-Datei auslagern
        if ($event->isInline() && $event->isPriority()) {
            $this->processInlineCss($event);
            return;
        }

        // Inline + nicht-priority → JS in Temp-Datei auslagern
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
            $event->getAssetCollector()->addStyleSheet('t3sbootstrapcss', $cssFile);
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
            // JSON-Daten (z.B. TypoScript-Settings) überspringen
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

        // DOMContentLoaded-Wrapper
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

        if (!file_exists($fullPath)) {
            $written = GeneralUtility::writeFile($fullPath, $str);
            if (!$written) {
                return '';
            }
        }

        return $script;
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}