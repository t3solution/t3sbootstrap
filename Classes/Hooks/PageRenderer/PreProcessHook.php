<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Hooks\PageRenderer;

use TYPO3\CMS\Core\Page\PageRenderer;
use T3SBS\T3sbootstrap\Service\CompileService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class PreProcessHook
{
	/**
	 * @var \T3SBS\T3sbootstrap\Service\CompileService
	 */
	protected $compileService;

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
					$compiledFile = $this->getCompileService()->getCompiledFile($file);
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
