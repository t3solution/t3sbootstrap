<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers;

use Closure;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Render an icon from a fluid template.
 * Example Usage:
 *   <i:icon iconfig="fa5:brands,xbox" additionalAttributes="{style:'color:red'}" preferredRenderTypes="webfont,svgSprite" />
 */
class IconViewHelper extends AbstractViewHelper
{

	protected $escapeOutput = false;

	public function initializeArguments(): void
	{
		$this->registerArgument('iconfig', 'string', 'The rendering configuration of the requested icon', true);
		$this->registerArgument('additionalAttributes', 'array', 'Additional attributes', false);
		$this->registerArgument('preferredRenderTypes', 'string', 'Comma separated list of the preferred renderTypes', false);
	}

	/**
	 * Render the header icon.
	 *
	 * @return string
	 */
	public function render(): string
	{

if (ExtensionManagementUtility::isLoaded('iconpack')) {
	
		/** @var IconpackFactory $iconpackFactory */
		$iconpackFactory = GeneralUtility::makeInstance(\Quellenform\Iconpack\IconpackFactory::class);
		return $iconpackFactory->getIconMarkup(
			$this->arguments['iconfig'],
			'native',
			$this->arguments['additionalAttributes'],
			$this->arguments['preferredRenderTypes']
		) ?? '';

}

        return '';
    }
}
