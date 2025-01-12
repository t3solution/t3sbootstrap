<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Context\Context;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class GetUsernameViewHelper extends AbstractViewHelper
{
	public function __construct(
		private readonly Context $context
	) {}

	public function render()
 {
	 $frontendUserUsername = $this->context->getPropertyFromAspect('frontend.user', 'username', '');
	 return $frontendUserUsername;
	 #		return strval($GLOBALS['TSFE']->fe_user->user['username']);
 }
}
