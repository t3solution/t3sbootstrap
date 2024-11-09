<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
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

	public function render(): string
 {
	 $info = '';
	 $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
	 $flexFormDataArr = $flexFormService->convertFlexFormContentToArray($this->arguments['data']);
	 foreach ($flexFormDataArr as $key=>$flexFormData) {
			   if (str_starts_with($key, 'xxl')) {break;}
			   if ($key !== 'hidden') {
				   $value = !empty($flexFormData) ? $flexFormData : 0;
				   $info .= $key.'='.$value.', ';
			   }
		   }
	 return rtrim($info, ', ');
 }

}
