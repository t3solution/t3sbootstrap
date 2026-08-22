<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\SingletonInterface;

class VideoRenderer implements SingletonInterface
{

	public function __construct(
		private readonly OnlineMediaHelperRegistry $onlineMediaHelperRegistry,
	) {}


	private function resolveHelper(FileInterface $file): ?OnlineMediaHelperInterface
	{
		$origFile = $file instanceof FileReference ? $file->getOriginalFile() : $file;
		if (!$origFile instanceof File) {
			return null;
		}

		return $this->onlineMediaHelperRegistry->getOnlineMediaHelper($origFile) ?: null;
	}

	/**
	 * Render for given File(Reference) html output
	 */
	public function render(FileReference $file): string
	{
		return $this->resolveHelper($file)?->getOnlineMediaId($file->getOriginalFile()) ?? '';
	}
}
