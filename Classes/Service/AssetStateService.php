<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Answers whether the downloads below typo3temp/assets/t3sbootstrap/ are still
 * usable, so a recurring scheduler task can exit without touching the network.
 *
 * Checks state, not the TYPO3 version: typo3temp/ is emptied without the version
 * changing (cache flush, "Remove Temporary Assets", a deployment of unchanged
 * code), and the version changes without the files going anywhere. What does
 * matter besides their presence is the configuration they were fetched for - the
 * files can be complete and still be the wrong versions.
 *
 * The verdict is remembered in sys_registry, which lives in the database and
 * therefore survives exactly what typo3temp/ does not.
 */
final class AssetStateService implements SingletonInterface
{
    private const REGISTRY_NAMESPACE = 'tx_t3sbootstrap';
    private const KEY_CDN = 'cdnFingerprint';
    private const KEY_BOOTSTRAP_SOURCES = 'bootstrapSourceVersion';
    private const KEY_SCSS = 'scssFingerprint';

    /**
     * Written by cdnToLocal, and the one file of the google font download that is
     * always there when it succeeded.
     */
    private const GOOGLEFONTS_CSS = 'T3SB-CSS/googlefonts.css';

    /**
     * Unpacked from the GitHub archive by customScss. The entry point of the
     * @import chain - if this is gone, the compile step dies.
     */
    private const BOOTSTRAP_SOURCE_ENTRY = 'T3SB-Bootstrap/Bootstrap/scss/bootstrap.scss';

    public function __construct(
        private readonly AssetPathService $assetPathService,
        private readonly CdnAssetMap $cdnAssetMap,
        private readonly Registry $registry,
    ) {}

    /**
     * @param array<string, mixed> $settings settings.bootstrap of the leading site
     * @param list<string> $googleFontsArr font lists collected from all sites
     */
    public function cdnNeedsRefresh(array $settings, array $googleFontsArr): bool
    {
        if ($this->cdnDownloadsAreMissing($settings, $googleFontsArr)) {
            return true;
        }

        return $this->cdnFingerprint($settings, $googleFontsArr)
            !== (string)$this->registry->get(self::REGISTRY_NAMESPACE, self::KEY_CDN, '');
    }

    /**
     * @param array<string, mixed> $settings
     * @param list<string> $googleFontsArr
     */
    public function rememberCdnState(array $settings, array $googleFontsArr): void
    {
        $this->registry->set(
            self::REGISTRY_NAMESPACE,
            self::KEY_CDN,
            $this->cdnFingerprint($settings, $googleFontsArr),
        );
    }

    /**
     * @param array<string, mixed> $settings
     * @param list<string> $googleFontsArr
     */
    private function cdnDownloadsAreMissing(array $settings, array $googleFontsArr): bool
    {
        foreach (array_keys($this->cdnAssetMap->build($settings['cdn'] ?? [])) as $relFile) {
            // resolveFile() creates nothing - getPath() would mkdir the very
            // directory being asked about and report it present every time.
            if ($this->assetPathService->resolveFile($relFile) === null) {
                return true;
            }
        }

        return $googleFontsArr !== []
            && $this->assetPathService->resolveFile(self::GOOGLEFONTS_CSS) === null;
    }

    /**
     * Everything that decides what gets downloaded: the versions, the integrity
     * hashes next to them, the font families from every site and their weights.
     *
     * @param array<string, mixed> $settings
     * @param list<string> $googleFontsArr
     */
    private function cdnFingerprint(array $settings, array $googleFontsArr): string
    {
        $relevant = $settings['cdn'] ?? [];
        $relevant['_weights'] = $settings['gooleFontsWeights'] ?? '';

        // The fonts come from all sites, not only from the one the versions do.
        $fonts = $googleFontsArr;
        sort($fonts);
        $relevant['_fonts'] = $fonts;

        ksort($relevant);

        return sha1(json_encode($relevant, JSON_THROW_ON_ERROR));
    }

    /**
     * The GitHub archive is several megabytes and was fetched on every single run.
     * It is needed when the sources are gone, or when they belong to another
     * Bootstrap version than the one configured now.
     */
    public function bootstrapSourcesNeedDownload(string $bootstrapVersion): bool
    {
        if ($this->assetPathService->resolveFile(self::BOOTSTRAP_SOURCE_ENTRY) === null) {
            return true;
        }

        return $bootstrapVersion
            !== (string)$this->registry->get(self::REGISTRY_NAMESPACE, self::KEY_BOOTSTRAP_SOURCES, '');
    }

    public function rememberBootstrapSourceVersion(string $bootstrapVersion): void
    {
        $this->registry->set(
            self::REGISTRY_NAMESPACE,
            self::KEY_BOOTSTRAP_SOURCES,
            $bootstrapVersion,
        );
    }

    /**
     * customScss writes two files nothing else can rebuild: the processed Bootstrap
     * entry point and the include file holding the @import chain. The rest of what it
     * touches comes from the configuration record, which the middleware restores.
     *
     * @param array<string, mixed> $settings settings.bootstrap
     */
    public function scssNeedsRefresh(array $settings, int $rootPageId): bool
    {
        if ($this->assetPathService->resolveFile(self::BOOTSTRAP_SOURCE_ENTRY) === null) {
            return true;
        }

        if ($this->assetPathService->resolveFile(self::includeFile($rootPageId)) === null) {
            return true;
        }

        return $this->scssFingerprint($settings)
            !== (string)$this->registry->get(self::REGISTRY_NAMESPACE, self::KEY_SCSS . '-' . $rootPageId, '');
    }

    /**
     * @param array<string, mixed> $settings settings.bootstrap
     */
    public function rememberScssState(array $settings, int $rootPageId): void
    {
        $this->registry->set(
            self::REGISTRY_NAMESPACE,
            self::KEY_SCSS . '-' . $rootPageId,
            $this->scssFingerprint($settings),
        );
    }

    private static function includeFile(int $rootPageId): string
    {
        return 'T3SB-SCSS/Bootstrap/bootstrap-' . $rootPageId . '.scss';
    }

    /**
     * The Bootstrap version decides which sources are unpacked, bootswatch what a
     * still empty record is initialised from, and the optimize flags which @import
     * lines are commented out of the entry point.
     *
     * The extension version belongs in here because the include file's relative
     * import paths are built by this code: an update may change them, and a stale
     * include kills the compile step.
     *
     * @param array<string, mixed> $settings settings.bootstrap
     */
    private function scssFingerprint(array $settings): string
    {
        $optimize = (array)($settings['optimize'] ?? []);
        ksort($optimize);

        $relevant = [
            'bootstrap' => (string)($settings['cdn']['bootstrap'] ?? ''),
            'bootswatch' => (string)($settings['cdn']['bootswatch'] ?? ''),
            'optimize' => $optimize,
            'extension' => ExtensionManagementUtility::getExtensionVersion('t3sbootstrap'),
        ];

        return sha1(json_encode($relevant, JSON_THROW_ON_ERROR));
    }
}
