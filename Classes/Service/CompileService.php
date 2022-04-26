<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use T3SBS\T3sbootstrap\Parser\ParserInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class CompileService
{
	/**
	 * @var string
	 */
	protected $tempDirectory = 'typo3temp/assets/t3sbootstrap/css/';

	/**
	 * @var string
	 */
	protected $tempDirectoryRelativeToRoot = '../../../../';

	/**
	 * @throws \Exception
	 */
	public function getCompiledFile(string $file): ?string
	{

		$absoluteFile = GeneralUtility::getFileAbsFileName($file);
		$configuration = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_t3sbootstrap.']['settings.'] ?? [];

		// Ensure cache directory exists
		if (!file_exists(Environment::getPublicPath() . '/' . $this->tempDirectory)) {
			GeneralUtility::mkdir_deep(Environment::getPublicPath() . '/' . $this->tempDirectory);
		}

		// Settings
		$settings = [
			'file' => [
				'absolute' => $absoluteFile,
				'relative' => $file,
				'info' => pathinfo($absoluteFile)
			],
			'cache' => [
				'tempDirectory' => $this->tempDirectory,
				'tempDirectoryRelativeToRoot' => $this->tempDirectoryRelativeToRoot,
			],
			'options' => [
				'override' => !empty($configuration['overrideParserVariables']) ? true: false,
				'sourceMap' => !empty($configuration['cssSourceMapping']) ? true : false,
				'compress' => true
			],
			'variables' => []
		];

		// Parser
		if (!empty($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/t3sbootstrap/css']['parser'])
			&& is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/t3sbootstrap/css']['parser'])
		) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/t3sbootstrap/css']['parser'] as $className) {
				$parser = GeneralUtility::makeInstance($className);
				if ($parser instanceof ParserInterface
					&& !empty($settings['file']['info']['extension'])
					&& $parser->supports($settings['file']['info']['extension'])
				) {
					if (!empty($configuration['overrideParserVariables'])) {
						$settings['variables'] = $this->getVariablesFromConstants($settings['file']['info']['extension']);
					}
					try {
						return $parser->compile($file, $settings);
					} catch (\Exception $e) {
						$this->clearCompilerCaches();
						throw $e;
					}
				}
			}
		}

		return null;
	}

	protected function getVariablesFromConstants(string $extension): array
	{
		$constants = $this->getConstants();
		$extension = strtolower($extension);
		$variables = [];

		// Fetch Google Font
		$variables['google-webfont'] = 'sans-serif';
		if (!empty($constants['page.theme.googleFont.enable'])
			&& (bool) $constants['page.theme.googleFont.enable']
			&& !empty($constants['page.theme.googleFont.font'])
			&& $constants['page.theme.googleFont.font'] !== ''
		) {
			$variables['google-webfont'] = $constants['page.theme.googleFont.font'];
		}

		// Fetch settings
		$prefix = 'plugin.t3sbootstrap.settings.' . $extension . '.';
		foreach ($constants as $constant => $value) {
			if (strpos($constant, $prefix) === 0) {
				$variables[substr($constant, strlen($prefix))] = $value;
			}
		}

		return $variables;
	}

	protected function getConstants(): array
	{
		if ($GLOBALS['TSFE']->tmpl->flatSetup === null
		|| !is_array($GLOBALS['TSFE']->tmpl->flatSetup)
		|| count($GLOBALS['TSFE']->tmpl->flatSetup) === 0) {
			$GLOBALS['TSFE']->tmpl->generateConfig();
		}
		return $GLOBALS['TSFE']->tmpl->flatSetup;
	}

	/**
	 * Clear all caches for the compiler.
	 */
	protected function clearCompilerCaches(): void
	{
		GeneralUtility::rmdir(Environment::getPublicPath() . '/' . $this->tempDirectory, true);
	}
}
