<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

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
        if (!empty($this->arguments['title'])) {
            return $this->arguments['title'];
        }

        if (!empty($this->arguments['name'])) {
            $name = explode(".", $this->arguments['name']);
            return $name[0];
        }

        return '';
    }
}
