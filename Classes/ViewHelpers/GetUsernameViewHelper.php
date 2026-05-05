<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Context\Context;

class GetUsernameViewHelper extends AbstractViewHelper
{
	public function __construct(
		private readonly Context $context
	) {}

	public function render()
 {
	 $frontendUserUsername = $this->context->getPropertyFromAspect('frontend.user', 'username', '');
	 return $frontendUserUsername;
 }
}
