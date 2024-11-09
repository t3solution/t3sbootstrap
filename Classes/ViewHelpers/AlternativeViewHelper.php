<?php
namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class AlternativeViewHelper extends AbstractViewHelper
{
   /**
	 */
	public function initializeArguments(): void
	{
		$this->registerArgument('title', 'string', 'title', true);
		$this->registerArgument('name', 'string', 'name', true);
	}

	public function render()
 {
	 if ( $this->arguments['title'] ) {
			   return $this->arguments['title'];
		   } else {
			   $name = explode(".", $this->arguments['name']);
			   return $name[0];
		   }
 }
}
