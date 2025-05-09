<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Resource\FileRepository;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class DummyImageViewHelper extends AbstractViewHelper
{
	public function initializeArguments(): void
	{
		$this->registerArgument('uid', 'string', 'Uid of tt_content with custom dummy image for EXT:news', false);
	}

	public function render()
 {
	 $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
	 $fileObjects = $fileRepository->findByRelation('tt_content', 'image', (int)$this->arguments['uid']);
	 if (empty($fileObjects)) {
			   $fileObjects = $fileRepository->findByRelation('tt_content', 'assets', (int)$this->arguments['uid']);
		   }
	 return !empty($fileObjects) ? $fileObjects[0] : '';
 }

}
