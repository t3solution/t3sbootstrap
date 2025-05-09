<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers\Backend;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class InfoImageViewHelper extends AbstractViewHelper
{
	protected $escapeOutput = false;

	public function initializeArguments(): void
	{
		$this->registerArgument('uid', 'int', 'record uid', true);
	}

	public function render(): array
	{
        return GeneralUtility::makeInstance(FileRepository::class)->findByRelation('tt_content', 'assets', $this->arguments['uid']);
	}
}
