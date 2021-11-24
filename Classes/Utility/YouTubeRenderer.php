<?php
namespace T3SBS\T3sbootstrap\Utility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * YouTube renderer class
 */
class YouTubeRenderer implements SingletonInterface
{
	/**
	 * @var OnlineMediaHelperInterface
	 */
	protected $onlineMediaHelper;


	/**
	 * Get online media helper
	 *
	 * @param FileInterface $file
	 * @return bool|OnlineMediaHelperInterface
	 */
	protected function getOnlineMediaHelper(FileInterface $file)
	{
		if ($this->onlineMediaHelper === null) {
			$origFile = $file;
			if ($origFile instanceof FileReference) {
				$origFile = $origFile->getOriginalFile();
			}
			if ($origFile instanceof File) {
				$this->onlineMediaHelper = GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class)->getOnlineMediaHelper($origFile);
			} else {
				$this->onlineMediaHelper = false;
			}
		}
		return $this->onlineMediaHelper;
	}

	/**
	 * Render for given File(Reference) html output
	 *
	 * @param FileInterface $file
	 * @return string
	 */
	public function render(FileInterface $file)
	{
		if ($file instanceof FileReference) {
			$origFile = $file->getOriginalFile();
		} else {
			$origFile = $file;
		}

		return $this->getOnlineMediaHelper($file)->getOnlineMediaId($origFile);
	}
}
