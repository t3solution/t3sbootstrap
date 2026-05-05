<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class FlexformViewHelper extends AbstractViewHelper
{
	/**
	 * @var bool
	 */
	protected $escapeOutput = false;

	public function initializeArguments(): void
	{
		parent::initializeArguments();
		$this->registerArgument('uid', 'int', 'data id', false);
		$this->registerArgument('data', 'string', 'flexform data', false);
	}

	public function render(): array
	{
		if (!empty($this->arguments['uid'])) {
			// BE Container Preview
			$record = BackendUtility::getRecord('tt_content', $this->arguments['uid'], '*');
					
			if (!empty($record['tx_t3sbootstrap_flexform'])) {

				return GeneralUtility::makeInstance(FlexFormTools::class)->convertFlexFormContentToArray($record['tx_t3sbootstrap_flexform']);

			} else {
				return [];
			}

		} elseif (!empty($this->arguments['data'])) {
			// FE
			return GeneralUtility::makeInstance(FlexFormTools::class)->convertFlexFormContentToArray($this->arguments['data']);

		} else {

			return [];
		}
    }
}
