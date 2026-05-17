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
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\YouTubeHelper;

class VideoRenderer implements SingletonInterface
{
	/**
	 * @var OnlineMediaHelperInterface
	 */
	protected $onlineMediaHelper;


	public function __construct(
		private readonly OnlineMediaHelperRegistry $onlineMediaHelperRegistry,
	) {}
	
	
	protected function getOnlineMediaHelper(FileInterface $file): YouTubeHelper
	{
		if ($this->onlineMediaHelper === null) {
			$origFile = $file;
			if ($origFile instanceof FileReference) {
				$origFile = $origFile->getOriginalFile();
			}

			if ($origFile instanceof File) {
				$this->onlineMediaHelper = $this->onlineMediaHelperRegistry->getOnlineMediaHelper($origFile);
			} else {
				$this->onlineMediaHelper = false;
			}
		}

		return $this->onlineMediaHelper;
	}

	/**
	 * Render for given File(Reference) html output
	 */
	public function render(FileReference $file): string
	{
		if ($file instanceof FileReference) {
			$origFile = $file->getOriginalFile();
		} else {
			$origFile = $file;
		}

		return $this->getOnlineMediaHelper($file)->getOnlineMediaId($origFile);
	}
}
