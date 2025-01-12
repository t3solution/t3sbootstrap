<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class FlexformViewHelper extends AbstractViewHelper
{
	/**
	 * @var bool
	 */
	protected $escapeOutput = false;

	public function initializeArguments(): void
	{
		parent::initializeArguments();
		$this->registerArgument('data', 'string', 'xml data', true);
	}

	public function render(): array
	{
	 
		if (!empty($this->arguments['data'])) {
			$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
			return $flexFormService->convertFlexFormContentToArray($this->arguments['data']);	
		} else {
			return [];
		}
	}

}
