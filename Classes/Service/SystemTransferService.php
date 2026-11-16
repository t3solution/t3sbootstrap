<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Configuration\SiteWriter;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Site\Set\SetRegistry;
use TYPO3\CMS\Core\Site\SiteSettingsFactory;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * The part of the configuration that does not live in the database: the extension
 * configuration and, per site, the t3sbootstrap sets together with their settings.
 *
 * Both are repository files. Writing them is therefore optional in every step - the
 * service reports what would change, writes only when asked and the file is writable,
 * and otherwise hands out a snippet to paste. On a deployment setup settings.php and
 * config/sites/ are usually under version control, so a write here lives until the
 * next deployment; that is a decision for the administrator, not for this service.
 *
 * Everything is matched by prefix: extension configuration is EXTENSIONS.t3sbootstrap,
 * a set is ours when its name starts with t3sbs/t3sbootstrap, a setting when its key
 * starts with "bootstrap.". No other extension is read or touched.
 */
final class SystemTransferService
{
    public const SETTINGS_PREFIX = 'bootstrap.';
    public const SET_PREFIX = 't3sbs/t3sbootstrap';
    public const EXTENSION_KEY = 't3sbootstrap';

    /**
     * Paths that belong to the host, not to the configuration. They are exported for
     * documentation but never written on import - the target may well have its asset
     * directory somewhere else, and overwriting it unstyles the site.
     */
    private const HOST_SPECIFIC_KEYS = ['assetDir', 'flexformDir'];

    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly SiteFinder $siteFinder,
        private readonly SiteWriter $siteWriter,
        private readonly SiteSettingsFactory $siteSettingsFactory,
        private readonly SetRegistry $setRegistry,
        private readonly PackageManager $packageManager,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function export(): array
    {
        $configuration = $this->getExtensionConfiguration();

        return [
            'extensionConfiguration' => array_diff_key($configuration, array_flip(self::HOST_SPECIFIC_KEYS)),
            'hostSpecific' => array_intersect_key($configuration, array_flip(self::HOST_SPECIFIC_KEYS)),
            'environment' => $this->exportEnvironment(),
            'sites' => $this->exportSites(),
        ];
    }

    /**
     * What the source installation consisted of. Nothing here is ever imported - an
     * extension cannot install another one, and it should not try. It is the list the
     * administrator of the target needs to see: a setting that points at an extension
     * which is not there does nothing, and a set that is missing makes the whole site
     * configuration invalid.
     *
     * @return array{typo3: string, php: string, extensions: array<string, array{composer: string, version: string}>}
     */
    public function exportEnvironment(): array
    {
        $extensions = [];

        foreach ($this->packageManager->getActivePackages() as $package) {
            $composerName = (string)($package->getValueFromComposerManifest('name') ?? '');

            if ($this->isCorePackage($package, $composerName)) {
                continue;
            }

            $extensions[$package->getPackageKey()] = [
                'composer' => $composerName,
                'version' => (string)$package->getPackageMetaData()->getVersion(),
            ];
        }

        ksort($extensions);

        return [
            'typo3' => (new Typo3Version())->getVersion(),
            'php' => PHP_VERSION,
            'extensions' => $extensions,
        ];
    }

    /**
     * Every site that uses at least one t3sbootstrap set.
     *
     * @return array<string, array{sets: string[], settings: array<string, mixed>}>
     */
    public function exportSites(): array
    {
        $sites = [];

        foreach ($this->siteFinder->getAllSites(false) as $site) {
            $sets = $this->getOwnSets($site);
            if ($sets === []) {
                continue;
            }
            $sites[$site->getIdentifier()] = [
                'rootPageId' => $site->getRootPageId(),
                'sets' => $sets,
                'allSets' => $this->describeSets((array)($site->getRawConfiguration()['dependencies'] ?? [])),
                'settings' => $this->getOwnSettings($site),
            ];
        }

        return $sites;
    }

    /**
     * Compare the exported extension configuration against this installation and write
     * it when $dryRun is false. Values are merged over the existing ones, so keys a
     * newer version added are kept instead of falling back to empty.
     *
     * @param array<string, mixed> $imported
     * @return array{changes: array<string, array{from: mixed, to: mixed}>, changesCount: int, total: int, writable: bool, written: bool, snippet: string, skipped: string[]}
     */
    public function importExtensionConfiguration(array $imported, bool $dryRun = true): array
    {
        $current = $this->getExtensionConfiguration();
        $available = $this->getAvailableKeys();

        $skipped = [];
        $changes = [];
        $target = $current;

        foreach ($imported as $key => $value) {
            if (in_array($key, self::HOST_SPECIFIC_KEYS, true)) {
                continue;
            }
            if ($available !== [] && !in_array($key, $available, true)) {
                $skipped[] = $key;
                continue;
            }
            $value = is_bool($value) ? ($value ? '1' : '0') : $value;
            if (!array_key_exists($key, $current) || (string)$current[$key] !== (string)$value) {
                $changes[$key] = ['from' => $current[$key] ?? null, 'to' => $value];
            }
            $target[$key] = $value;
        }

        $writable = $this->isExtensionConfigurationWritable();
        $written = false;

        if (!$dryRun && $changes !== [] && $writable) {
            ksort($target);
            $this->extensionConfiguration->set(self::EXTENSION_KEY, $target);
            $written = true;
        }

        return [
            'changes' => $changes,
            'changesCount' => count($changes),
            'total' => count($imported),
            'writable' => $writable,
            'written' => $written,
            'snippet' => $changes === [] ? '' : $this->buildPhpSnippet($changes),
            'skipped' => $skipped,
        ];
    }

    /**
     * Same for one site: missing sets are added to the existing dependencies, settings
     * are merged into the site's own settings.yaml.
     *
     * @param array{sets?: string[], settings?: array<string, mixed>} $imported
     * @return array{site: string, setsMissing: string[], changes: array<string, array{from: mixed, to: mixed}>, writable: bool, written: bool, snippetSets: string, snippetSettings: string, errors: string[]}
     */
    public function importSite(string $siteIdentifier, array $imported, bool $dryRun = true): array
    {
        $errors = [];
        $written = false;

        try {
            $site = $this->siteFinder->getSiteByIdentifier($siteIdentifier);
        } catch (\Throwable) {
            return [
                'site' => $siteIdentifier,
                'setsMissing' => [],
                'changes' => [],
                'writable' => false,
                'written' => false,
                'snippetSets' => '',
                'snippetSettings' => '',
                'errors' => ['Site "' . $siteIdentifier . '" wurde nicht gefunden.'],
            ];
        }

        $presentSets = $this->getOwnSets($site);
        $setsMissing = array_values(array_diff($this->filterOwnSetNames((array)($imported['sets'] ?? [])), $presentSets));

        $currentSettings = $this->getOwnSettings($site);
        $defaults = $this->getSetDefaults($site);
        $changes = [];

        foreach ($this->filterOwnSettings((array)($imported['settings'] ?? [])) as $key => $value) {
            $from = $currentSettings[$key] ?? ($defaults[$key] ?? null);
            if ($from !== $value) {
                $changes[$key] = ['from' => $from, 'to' => $value];
            }
        }

        $writable = $this->isSiteWritable($siteIdentifier);

        if (!$dryRun && ($setsMissing !== [] || $changes !== []) && $writable) {
            try {
                if ($setsMissing !== []) {
                    $this->writeDependencies($site, $setsMissing);
                    // reload, the sets decide which settings count as default
                    $this->siteFinder->siteConfigurationChanged();
                    $site = $this->siteFinder->getSiteByIdentifier($siteIdentifier);
                    // set before the second step, so a failure there is not reported
                    // as "nothing happened" while the sets are already in the file
                    $written = true;
                }
                if ($changes !== []) {
                    $this->writeSettings($site, $changes);
                    $written = true;
                }
                $this->siteFinder->siteConfigurationChanged();
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }

        return [
            'site' => $siteIdentifier,
            'setsMissing' => $setsMissing,
            'changes' => $changes,
            'writable' => $writable,
            'written' => $written,
            'snippetSets' => $setsMissing === [] ? '' : $this->buildSetsSnippet($setsMissing),
            'snippetSettings' => $changes === [] ? '' : $this->buildSettingsSnippet($changes),
            'errors' => $errors,
        ];
    }

    /**
     * A ready to paste composer require line for a list of extensions. Deliberately one
     * single line: a line continuation with a backslash would only work in a posix shell,
     * and it is the display that should wrap, not the command.
     *
     * A version that starts with dev- is a branch and is passed through unchanged, anything
     * else becomes a caret constraint. An extension without a composer name is left out -
     * it was installed without composer and cannot be required that way; the caller is
     * told how many those were.
     *
     * @param array<string, array{composer: string, version: string}> $extensions
     * @return array{snippet: string, required: int, skipped: string[]}
     */
    public function buildComposerRequire(array $extensions): array
    {
        $packages = [];
        $skipped = [];

        foreach ($extensions as $key => $extension) {
            $name = trim((string)($extension['composer'] ?? ''));
            if ($name === '') {
                $skipped[] = (string)$key;
                continue;
            }

            $version = trim((string)($extension['version'] ?? ''));
            $packages[] = $name . $this->buildVersionConstraint($version);
        }

        if ($packages === []) {
            return ['snippet' => '', 'required' => 0, 'skipped' => $skipped];
        }

        sort($packages);

        return [
            'snippet' => 'composer require ' . implode(' ', $packages),
            'required' => count($packages),
            'skipped' => $skipped,
        ];
    }

    /**
     * A branch stays a branch, a version becomes a caret constraint, and nothing at all
     * stays nothing - composer then takes whatever is current.
     */
    private function buildVersionConstraint(string $version): string
    {
        if ($version === '') {
            return '';
        }

        if (str_starts_with($version, 'dev-') || str_ends_with($version, '-dev')) {
            return ':' . $version;
        }

        // a version has to start with a digit to be usable as a constraint
        return preg_match('/^\d/', $version) === 1 ? ':^' . $version : '';
    }

    /**
     * The core's own extensions are left out of the inventory, they are implied by the
     * TYPO3 version. Two tests, because one does not hold in both installation types:
     * in composer mode the packages are named typo3/cms-*, in a classic installation
     * they sit below typo3/sysext/ and may carry no composer name at all.
     */
    private function isCorePackage(PackageInterface $package, string $composerName): bool
    {
        if ($composerName !== '') {
            return str_starts_with($composerName, 'typo3/cms-');
        }

        $path = str_replace('\\', '/', $package->getPackagePath());

        return str_contains($path, '/typo3/sysext/');
    }

    /**
     * Every set the site depends on, with the extension that provides it. Ours are marked,
     * the others are listed just as plainly - a t3sbootstrap site usually sits on a
     * sitepackage and a handful of companions, and leaving them out of the file would hide
     * exactly what the target is missing.
     *
     * @param array<int, mixed> $dependencies
     * @return array<int, array{name: string, extension: string, own: bool}>
     */
    public function describeSets(array $dependencies): array
    {
        $providers = $this->getSetProviders();
        $sets = [];

        foreach ($dependencies as $dependency) {
            $name = (string)$dependency;
            if ($name === '') {
                continue;
            }
            $sets[] = [
                'name' => $name,
                'extension' => $providers[$name] ?? '',
                'own' => str_starts_with($name, self::SET_PREFIX),
            ];
        }

        // ours first - they are the ones the import touches. usort keeps the order
        // inside both groups, which is the order of the site configuration.
        usort($sets, static fn(array $a, array $b): int => ($b['own'] <=> $a['own']));

        return $sets;
    }

    /**
     * What an export of this page would carry about its site: every set with the extension
     * behind it. Shown in the module, so the inventory is visible before the file leaves
     * the installation and not only on the target.
     *
     * @return array<int, array{name: string, extension: string, own: bool}>
     */
    public function describeSetsForPage(int $pid): array
    {
        $identifier = $this->getSiteIdentifierForPage($pid);

        if ($identifier === '') {
            return [];
        }

        try {
            $site = $this->siteFinder->getSiteByIdentifier($identifier);
        } catch (\Throwable) {
            return [];
        }

        return $this->describeSets((array)($site->getRawConfiguration()['dependencies'] ?? []));
    }

    /**
     * The t3sbootstrap settings of this page's site, as they stand in settings.yaml.
     * This is what an import writes on the target, so the module shows it too.
     *
     * @return array<string, mixed>
     */
    public function getSettingsForPage(int $pid): array
    {
        $identifier = $this->getSiteIdentifierForPage($pid);

        if ($identifier === '') {
            return [];
        }

        try {
            return $this->getOwnSettings($this->siteFinder->getSiteByIdentifier($identifier));
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * The extension configuration as the export carries it - without the two paths that
     * belong to the host and are never written on a target.
     *
     * @return array<string, mixed>
     */
    public function getExportedExtensionConfiguration(): array
    {
        return array_diff_key($this->getExtensionConfiguration(), array_flip(self::HOST_SPECIFIC_KEYS));
    }

    /**
     * Compares the source environment against this installation. Reports only what is
     * missing here - a differing version is not worth a warning, the target being newer
     * is the normal case when moving an installation.
     *
     * @param array<string, mixed> $environment
     * @param array<int, array{name: string, extension: string, own: bool}> $allSets
     * @return array{missingSets: array<int, array{name: string, extension: string, own: bool}>, missingExtensions: array<string, array{composer: string, version: string}>, complete: bool, typo3: string, php: string}
     */
    public function checkEnvironment(array $environment, array $allSets): array
    {
        $missingSets = [];
        foreach ($allSets as $set) {
            if (!$this->setRegistry->hasSet((string)($set['name'] ?? ''))) {
                $missingSets[] = $set;
            }
        }

        $missingExtensions = [];
        foreach ((array)($environment['extensions'] ?? []) as $key => $extension) {
            if (!$this->packageManager->isPackageActive((string)$key)) {
                $missingExtensions[(string)$key] = [
                    'composer' => (string)($extension['composer'] ?? ''),
                    'version' => (string)($extension['version'] ?? ''),
                ];
            }
        }

        return [
            'missingSets' => $missingSets,
            'missingExtensions' => $missingExtensions,
            'complete' => $missingSets === [] && $missingExtensions === [],
            'typo3' => (string)($environment['typo3'] ?? ''),
            'php' => (string)($environment['php'] ?? ''),
        ];
    }

    /**
     * Set name to the extension key that ships it, read from the packages themselves -
     * a SetDefinition does not carry where it came from.
     *
     * @return array<string, string>
     */
    private function getSetProviders(): array
    {
        $providers = [];

        foreach ($this->packageManager->getActivePackages() as $package) {
            foreach ((array)glob($package->getPackagePath() . 'Configuration/Sets/*/config.yaml') as $file) {
                try {
                    $definition = Yaml::parseFile((string)$file);
                } catch (\Throwable) {
                    continue;
                }
                $name = (string)($definition['name'] ?? '');
                if ($name !== '') {
                    $providers[$name] = $package->getPackageKey();
                }
            }
        }

        return $providers;
    }

    /**
     * Identifier of the site a page belongs to, empty when the page is outside every site.
     */
    public function getSiteIdentifierForPage(int $pid): string
    {
        if ($pid <= 0) {
            return '';
        }
        try {
            return $this->siteFinder->getSiteByPageId($pid)->getIdentifier();
        } catch (\Throwable) {
            return '';
        }
    }

    public function isExtensionConfigurationWritable(): bool
    {
        $file = Environment::getConfigPath() . '/system/settings.php';

        return is_file($file) && is_writable($file);
    }

    public function isSiteWritable(string $siteIdentifier): bool
    {
        $folder = Environment::getConfigPath() . '/sites/' . $siteIdentifier;

        return is_dir($folder) && is_writable($folder);
    }

    /**
     * @return array<string, mixed>
     */
    private function getExtensionConfiguration(): array
    {
        try {
            $configuration = $this->extensionConfiguration->get(self::EXTENSION_KEY);
        } catch (\Throwable) {
            return [];
        }
        if (!is_array($configuration)) {
            return [];
        }
        ksort($configuration);

        return $configuration;
    }

    /**
     * Keys the current ext_conf_template.txt knows. Used to report values of an older
     * or newer export instead of writing them blindly.
     *
     * @return string[]
     */
    private function getAvailableKeys(): array
    {
        return array_keys($this->getExtensionConfiguration());
    }

    /**
     * @return string[]
     */
    private function getOwnSets(Site $site): array
    {
        return $this->filterOwnSetNames((array)($site->getRawConfiguration()['dependencies'] ?? []));
    }

    /**
     * @param array<int, mixed> $sets
     * @return string[]
     */
    private function filterOwnSetNames(array $sets): array
    {
        $own = [];
        foreach ($sets as $set) {
            $set = (string)$set;
            if (str_starts_with($set, self::SET_PREFIX)) {
                $own[] = $set;
            }
        }

        return array_values(array_unique($own));
    }

    /**
     * The site's own t3sbootstrap settings - only what differs from the set defaults,
     * so the export stays small and readable.
     *
     * @return array<string, mixed>
     */
    private function getOwnSettings(Site $site): array
    {
        $local = $this->siteSettingsFactory->loadLocalSettings($site->getIdentifier())
            ?? (array)($site->getRawConfiguration()['settings'] ?? []);

        return $this->filterOwnSettings($this->flatten($local));
    }

    /**
     * Set defaults of this site, flat. Needed to tell "set to the default" from "not set".
     *
     * @return array<string, mixed>
     */
    private function getSetDefaults(Site $site): array
    {
        try {
            $settings = $this->siteSettingsFactory->createSettings($site->getSets());
        } catch (\Throwable) {
            return [];
        }

        $defaults = [];
        foreach ($settings->getIdentifiers() as $key) {
            if (str_starts_with($key, self::SETTINGS_PREFIX)) {
                $defaults[$key] = $settings->get($key);
            }
        }

        return $defaults;
    }

    /**
     * @param array<string, mixed> $settings
     * @return array<string, mixed>
     */
    public function filterOwnSettings(array $settings): array
    {
        $own = [];
        foreach ($settings as $key => $value) {
            if (str_starts_with((string)$key, self::SETTINGS_PREFIX)) {
                $own[(string)$key] = $value;
            }
        }
        ksort($own);

        return $own;
    }

    /**
     * settings.yaml holds flat keys since v13, but a tree written by v12 is still valid.
     * Both are read, the export is always flat.
     *
     * @param array<mixed> $settings
     * @return array<string, mixed>
     */
    public function flatten(array $settings, string $prefix = ''): array
    {
        $flat = [];
        foreach ($settings as $key => $value) {
            $path = $prefix === '' ? (string)$key : $prefix . '.' . $key;
            if (is_array($value) && $value !== [] && !array_is_list($value)) {
                $flat += $this->flatten($value, $path);
                continue;
            }
            $flat[$path] = $value;
        }

        return $flat;
    }

    /**
     * Append the missing sets to the existing dependencies. The list is built here and
     * written whole - SiteWriter merges arrays recursively by index, which would
     * scramble a sequence.
     *
     * @param string[] $setsMissing
     */
    private function writeDependencies(Site $site, array $setsMissing): void
    {
        $configuration = $site->getRawConfiguration();
        $dependencies = array_values(array_filter(
            (array)($configuration['dependencies'] ?? []),
            static fn($set): bool => is_string($set) && $set !== '',
        ));

        foreach ($setsMissing as $set) {
            if (!in_array($set, $dependencies, true)) {
                $dependencies[] = $set;
            }
        }

        $configuration['dependencies'] = $dependencies;
        $this->siteWriter->write($site->getIdentifier(), $configuration);
    }

    /**
     * Merge the changed keys into the site's settings.yaml. The current file is read
     * first and written back whole, because SiteWriter::writeSettings() replaces the
     * file with what it is handed - every other extension's settings live in there too.
     *
     * @param array<string, array{from: mixed, to: mixed}> $changes
     */
    private function writeSettings(Site $site, array $changes): void
    {
        $this->siteWriter->writeSettings(
            $site->getIdentifier(),
            $this->mergeSettings($this->loadRawSettings($site), $changes, $this->getSetDefaults($site)),
        );
    }

    /**
     * The settings.yaml as it stands on disk. Deliberately not loadLocalSettings(), which
     * resolves %env()% placeholders - the whole file is written back, and a resolved
     * placeholder would be baked in permanently, including those of other extensions.
     *
     * @return array<string, mixed>
     */
    private function loadRawSettings(Site $site): array
    {
        $file = Environment::getConfigPath() . '/sites/' . $site->getIdentifier() . '/settings.yaml';

        if (!is_file($file)) {
            return [];
        }

        try {
            $settings = Yaml::parseFile($file);
        } catch (\Throwable) {
            // an unreadable file must not be replaced by an empty one
            throw new \RuntimeException(
                'Die settings.yaml der Site "' . $site->getIdentifier() . '" konnte nicht gelesen werden.',
                1763020001
            );
        }

        return is_array($settings) ? $settings : [];
    }

    /**
     * The merge itself, without any site around it: everything that is not ours stays
     * exactly as it is, our keys are replaced, and a value that matches the set default
     * is dropped instead of written.
     *
     * @param array<string, mixed> $settings the current settings.yaml
     * @param array<string, array{from: mixed, to: mixed}> $changes
     * @param array<string, mixed> $defaults
     * @return array<string, mixed>
     */
    public function mergeSettings(array $settings, array $changes, array $defaults = []): array
    {
        foreach ($changes as $key => $change) {
            // a key written as a tree by an older version would shadow the flat one
            if (ArrayUtility::isValidPath($settings, $key, '.')) {
                $settings = ArrayUtility::removeByPath($settings, $key, '.');
            }
            unset($settings[$key]);

            // keep the file minimal: the set default needs no line of its own
            if (array_key_exists($key, $defaults) && $defaults[$key] === $change['to']) {
                continue;
            }
            $settings[$key] = $change['to'];
        }

        ksort($settings);

        return $settings;
    }

    /**
     * @param array<string, array{from: mixed, to: mixed}> $changes
     */
    private function buildPhpSnippet(array $changes): string
    {
        $lines = [];
        foreach ($changes as $key => $change) {
            $lines[] = sprintf("    '%s' => %s,", $key, $this->exportPhpValue($change['to']));
        }

        return "// config/system/settings.php, in EXTENSIONS['t3sbootstrap']\n" . implode("\n", $lines);
    }

    /**
     * @param string[] $missing
     */
    private function buildSetsSnippet(array $missing): string
    {
        $lines = [];
        foreach ($missing as $set) {
            $lines[] = '  - ' . $set;
        }

        return "# config/sites/<site>/config.yaml, unter dependencies:\n" . implode("\n", $lines);
    }

    /**
     * @param array<string, array{from: mixed, to: mixed}> $changes
     */
    private function buildSettingsSnippet(array $changes): string
    {
        $settings = [];
        foreach ($changes as $key => $change) {
            $settings[$key] = $change['to'];
        }
        ksort($settings);

        return "# config/sites/<site>/settings.yaml\n"
            . Yaml::dump($settings, 99, 2, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE | Yaml::DUMP_OBJECT_AS_MAP);
    }

    private function exportPhpValue(mixed $value): string
    {
        return match (true) {
            is_bool($value) => $value ? 'true' : 'false',
            is_int($value), is_float($value) => (string)$value,
            default => "'" . str_replace("'", "\\'", (string)$value) . "'",
        };
    }
}
