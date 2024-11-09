<?php
namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class EmconfViewHelper extends AbstractViewHelper
{
	public function render()
 {
	 return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
 }
}
