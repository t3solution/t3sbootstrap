<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener\RTE;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\RteCKEditor\Form\Element\Event\AfterPrepareConfigurationForEditorEvent;

/**
 * Lets an installation decide which parts of the t3sbootstrap RTE are offered
 * to editors, without forking the YAML preset.
 *
 * Two levels, the lower one wins:
 *
 *   1. Extension Configuration, category "RTE" - the installation-wide default.
 *   2. Page TSconfig - per page tree:
 *
 *          RTE.t3sbootstrap.features {
 *              columns = 0
 *              codeBlock = 0
 *          }
 *
 * What this does NOT do: it never removes a CKEditor plugin from importModules.
 * A toolbar item that is gone only stops editors from creating NEW content of
 * that kind - the plugin stays loaded, so existing alerts, columns or code
 * blocks are still parsed, still editable and survive a save untouched.
 * Dropping the module instead would look tidier and would silently strip that
 * markup from every record an editor opens.
 *
 * Style definitions are a special case: in the "t3sbootstrap" preset the Styles
 * dropdown is also what keeps those classes through the round trip - the
 * General HTML Support whitelist there only covers <div>. Removing a style
 * group would therefore drop the classes from existing content, so every group
 * that gets switched off hands its element/class pairs over to htmlSupport
 * first. The dropdown loses the entry, the markup does not.
 */
#[AsEventListener(
	identifier: 't3sbootstrap/rte-feature-toggles',
	method: 'applyFeatureToggles',
)]
final readonly class FeatureToggles
{
	/**
	 * feature id => [
	 *   'extconf'       => key in the Extension Configuration,
	 *   'toolbar'       => toolbar items to drop,
	 *   'config'        => top level editor config keys to drop,
	 *   'styles'        => style.definitions name prefixes to drop,
	 *   'legacyExtconf' => former Extension Configuration key, read as long as
	 *                      the current one is not set,
	 *   'legacyFeature' => former page TSconfig id, same rule,
	 * ]
	 */
	private const FEATURES = [
		'alert' => [
			'extconf' => 'rteAlert',
			'toolbar' => ['alert'],
		],
		'columns' => [
			'extconf' => 'rteColumns',
			'toolbar' => ['columns'],
		],
		'codeBlock' => [
			'extconf' => 'rteCodeBlock',
			'toolbar' => ['codeBlock'],
			// No 'config' here on purpose. Dropping the codeBlock configuration
			// would take the language list with it, and CKEditor would fall back
			// to its own defaults - a stored language-typoscript would then find
			// no match on upcast and be saved as language-plaintext. Removing the
			// toolbar item is enough; existing blocks stay as they are.
		],
		'code' => [
			'extconf' => 'rteCode',
			'toolbar' => ['code'],
		],
		'margins' => [
			'extconf' => 'rteMargins',
			'toolbar' => ['margins'],
		],
		'badge' => [
			'extconf' => 'rteBadge',
			'toolbar' => ['badge'],
			// Badges waren zuvor 16 Eintraege im Styles-Dropdown und hingen an
			// "rteStyleBadges". Wer sie abgeschaltet hatte, soll sie nach dem
			// Update nicht ploetzlich wiederbekommen - siehe die Reihenfolge in
			// getDisabledFeatures().
			'legacyExtconf' => 'rteStyleBadges',
			'legacyFeature' => 'styleBadges',
		],
		'table' => [
			'extconf' => 'rteTable',
			'toolbar' => ['insertTable'],
		],
		'alignment' => [
			'extconf' => 'rteAlignment',
			'toolbar' => ['alignment'],
		],
		'indent' => [
			'extconf' => 'rteIndent',
			'toolbar' => ['outdent', 'indent'],
		],
		'blockQuote' => [
			'extconf' => 'rteBlockQuote',
			'toolbar' => ['blockQuote'],
		],
		'horizontalLine' => [
			'extconf' => 'rteHorizontalLine',
			'toolbar' => ['horizontalLine'],
		],
		'specialCharacters' => [
			'extconf' => 'rteSpecialCharacters',
			'toolbar' => ['specialCharacters'],
		],
		'findAndReplace' => [
			'extconf' => 'rteFindAndReplace',
			'toolbar' => ['findAndReplace'],
		],
		'showBlocks' => [
			'extconf' => 'rteShowBlocks',
			'toolbar' => ['showBlocks'],
		],
		'sourceEditing' => [
			'extconf' => 'rteSourceEditing',
			'toolbar' => ['sourceEditing'],
		],
		'styleColors' => [
			'extconf' => 'rteStyleColors',
			'styles'  => ['Block Color ', 'Text Color '],
		],
		'styleButtons' => [
			'extconf' => 'rteStyleButtons',
			'styles'  => ['Button '],
		],
		'styleTables' => [
			'extconf' => 'rteStyleTables',
			'styles'  => ['Table'],
		],
	];

	private const SEPARATORS = ['|', '-'];

	public function __construct(
		private ExtensionConfiguration $extensionConfiguration,
	) {}

	public function applyFeatureToggles(AfterPrepareConfigurationForEditorEvent $event): void
	{
		$configuration = $event->getConfiguration();
		$data = $event->getData();

		if (!$this->isT3sbootstrapPreset($configuration, $data)) {
			return;
		}

		$disabled = $this->getDisabledFeatures($data);
		if ($disabled === []) {
			return;
		}

		$toolbarItems = [];
		$configKeys = [];
		$stylePrefixes = [];

		foreach ($disabled as $feature) {
			$spec = self::FEATURES[$feature];
			$toolbarItems = array_merge($toolbarItems, $spec['toolbar'] ?? []);
			$configKeys = array_merge($configKeys, $spec['config'] ?? []);
			$stylePrefixes = array_merge($stylePrefixes, $spec['styles'] ?? []);
		}

		if ($stylePrefixes !== []) {
			$configuration = $this->removeStyleDefinitions($configuration, $stylePrefixes);
			if (empty($configuration['style']['definitions'])) {
				// An empty Styles dropdown is worse than no dropdown.
				unset($configuration['style']);
				$toolbarItems[] = 'style';
			}
		}

		foreach ($configKeys as $key) {
			unset($configuration[$key]);
		}

		if ($toolbarItems !== []) {
			$configuration = $this->removeToolbarItems($configuration, $toolbarItems);
		}

		$event->setConfiguration($configuration);
	}

	/**
	 * Only touch our own presets. The preset name is the primary signal; a
	 * sitepackage that renamed the preset but imports our YAML is recognised
	 * by the plugin modules it pulls in.
	 */
	private function isT3sbootstrapPreset(array $configuration, array $data): bool
	{
		$presetName = (string)($data['parameterArray']['fieldConf']['config']['richtextConfigurationName'] ?? '');
		if (str_starts_with($presetName, 't3sbootstrap')) {
			return true;
		}

		foreach ((array)($configuration['importModules'] ?? []) as $module) {
			$name = is_array($module) ? (string)($module['module'] ?? '') : (string)$module;
			if (str_contains($name, '@t3sbs/t3sbootstrap/')) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return list<string> feature ids switched off for this record
	 */
	private function getDisabledFeatures(array $data): array
	{
		try {
			$extconf = $this->extensionConfiguration->get('t3sbootstrap');
		} catch (\Throwable) {
			$extconf = [];
		}

		$pageTs = $data['pageTsConfig']['RTE.']['t3sbootstrap.']['features.'] ?? [];

		$disabled = [];
		foreach (self::FEATURES as $feature => $spec) {
			// Not configured at all means enabled - the shipped preset stays as it is.
			$enabled = true;

			foreach ([$spec['extconf'], $spec['legacyExtconf'] ?? null] as $extconfKey) {
				if ($extconfKey !== null
					&& array_key_exists($extconfKey, $extconf)
					&& $extconf[$extconfKey] !== ''
				) {
					$enabled = (bool)$extconf[$extconfKey];
				}
			}

			if (is_array($pageTs)) {
				foreach ([$spec['legacyFeature'] ?? null, $feature] as $tsKey) {
					if ($tsKey !== null && array_key_exists($tsKey, $pageTs)) {
						$enabled = (bool)(int)$pageTs[$tsKey];
					}
				}
			}

			if (!$enabled) {
				$disabled[] = $feature;
			}
		}

		return $disabled;
	}

	/**
	 * @param list<string> $prefixes
	 */
	private function removeStyleDefinitions(array $configuration, array $prefixes): array
	{
		$definitions = $configuration['style']['definitions'] ?? null;
		if (!is_array($definitions)) {
			return $configuration;
		}

		$kept = [];
		$rescue = [];

		foreach ($definitions as $definition) {
			$name = (string)($definition['name'] ?? '');
			$matches = false;
			foreach ($prefixes as $prefix) {
				if (str_starts_with($name, $prefix)) {
					$matches = true;
					break;
				}
			}

			if (!$matches) {
				$kept[] = $definition;
				continue;
			}

			$element = (string)($definition['element'] ?? '');
			$classes = (array)($definition['classes'] ?? []);
			if ($element !== '' && $classes !== []) {
				foreach ($classes as $class) {
					$rescue[$element][(string)$class] = (string)$class;
				}
			}
		}

		$configuration['style']['definitions'] = $kept;

		// Keep the classes parseable although the dropdown no longer offers them.
		foreach ($rescue as $element => $classes) {
			$configuration['htmlSupport']['allow'][] = [
				'name' => $element,
				'classes' => array_values($classes),
			];
		}

		return $configuration;
	}

	/**
	 * @param list<string> $remove
	 */
	private function removeToolbarItems(array $configuration, array $remove): array
	{
		$remove = array_flip($remove);

		if (isset($configuration['toolbar']['items']) && is_array($configuration['toolbar']['items'])) {
			$configuration['toolbar']['items'] = $this->filterToolbar($configuration['toolbar']['items'], $remove);
		} elseif (isset($configuration['toolbar']) && array_is_list($configuration['toolbar'])) {
			$configuration['toolbar'] = $this->filterToolbar($configuration['toolbar'], $remove);
		}

		return $configuration;
	}

	private function filterToolbar(array $items, array $remove): array
	{
		$filtered = [];
		foreach ($items as $item) {
			$name = is_array($item) ? (string)($item['name'] ?? '') : (string)$item;
			if ($name !== '' && isset($remove[$name])) {
				continue;
			}
			$filtered[] = $item;
		}

		return $this->collapseSeparators($filtered);
	}

	/**
	 * Removing items leaves double or dangling separators behind.
	 */
	private function collapseSeparators(array $items): array
	{
		$result = [];
		foreach ($items as $item) {
			if (!is_string($item) || !in_array($item, self::SEPARATORS, true)) {
				$result[] = $item;
				continue;
			}

			$previous = end($result);
			if ($previous === false || (is_string($previous) && in_array($previous, self::SEPARATORS, true))) {
				// A line break beats a plain separator at the same position.
				if ($item === '-' && $previous === '|') {
					array_pop($result);
					$result[] = '-';
				}
				continue;
			}

			$result[] = $item;
		}

		while ($result !== []) {
			$last = end($result);
			if (is_string($last) && in_array($last, self::SEPARATORS, true)) {
				array_pop($result);
				continue;
			}
			break;
		}

		return array_values($result);
	}
}
