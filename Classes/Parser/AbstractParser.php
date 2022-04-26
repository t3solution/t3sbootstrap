<?php 
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Parser;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
abstract class AbstractParser implements ParserInterface
{

	public function supports(string $extension): bool
	{
		return false;
	}

	public function compile(string $file, array $settings): string
	{
		return $file;
	}

	protected function isCached(string $file, array $settings): bool
	{
		$cacheIdentifier = $this->getCacheIdentifier($file, $settings);
		$cacheFile = $this->getCacheFile($cacheIdentifier, $settings['cache']['tempDirectory']);
		$cacheFileMeta = $this->getCacheFileMeta($cacheFile);

		return file_exists($cacheFile) && file_exists($cacheFileMeta);
	}

	protected function needsCompile(string $cacheFile, string $cacheFileMeta, array $settings): bool
	{
		$needCompilation = false;
		$fileModificationTime = filemtime($cacheFile);
		$metadata = unserialize((string) file_get_contents($cacheFileMeta), ['allowed_classes' => false]);

		foreach ($metadata['files'] as $file) {
			if (filemtime($file) > $fileModificationTime) {
				$needCompilation = true;
				break;
			}
		}

		if (!$needCompilation && $settings['variables'] !== $metadata['variables']) {
			$needCompilation = true;
		}

		if (!$needCompilation && $settings['options']['sourceMap'] !== $metadata['sourceMap']) {
			$needCompilation = true;
		}

		return $needCompilation;
	}

	protected function getCacheFile(string $cacheIdentifier, string $tempDirectory): string
	{
		return $tempDirectory . $cacheIdentifier . '.css';
	}

	protected function getCacheFileMeta(string $filename): string
	{
		return $filename . '.meta';
	}

	protected function getCacheIdentifier(string $file, array $settings): string
	{
		$filehash = md5($file);
		$hash = hash('sha256', $filehash . serialize($settings));
		$extension = pathinfo($file, PATHINFO_EXTENSION);

		return basename($file, '.' . $extension) . '-' . $hash;
	}

	protected function getPathSite(): string
	{
		return Environment::getPublicPath() . '/';
	}

	/**
	 * Clear all page caches
	 * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheGroupException
	 */
	protected function clearPageCaches(): void
	{
		GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
	}
}
