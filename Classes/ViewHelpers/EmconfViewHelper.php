<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class EmconfViewHelper extends AbstractViewHelper
{
	public function render()
 {
	 return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
 }
}
