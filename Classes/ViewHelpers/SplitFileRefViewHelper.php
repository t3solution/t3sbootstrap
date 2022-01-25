<?php
declare(strict_types = 1);
namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\AbstractFileFolder;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class SplitFileRefViewHelper extends AbstractViewHelper
{
	use CompileWithRenderStatic;

	/**
	 * Initialize all arguments
	 */
	public function initializeArguments()
	{
		$this->registerArgument(
			'file',
			'object',
			'File object',
			true
		);
		$this->registerArgument(
			'as',
			'string',
			'The name of the variable with file parts',
			false,
			'fileParts'
		);
	}

	public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
	{
		$templateVariableContainer = $renderingContext->getVariableProvider();
		$file = $arguments['file'];

		// get Resource Object (non ExtBase version)
		if (is_callable([$file, 'getOriginalResource'])) {
			// We have a domain model, so we need to fetch the FAL resource object from there
			$file = $file->getOriginalResource();
		}

		if (!($file instanceof FileInterface || $file instanceof AbstractFileFolder)) {
			throw new \UnexpectedValueException('Supplied file object type ' . get_class($file) . ' must be FileInterface or AbstractFileFolder.', 1563891998);
		}

		$fileParts = GeneralUtility::split_fileref($file->getPublicUrl());

		$image= $fileParts['path'].$fileParts['filebody'].'.jpg';
		$fileParts['imgext'] = 'jpg';

		if (!file_exists($image)) {
			$image = $fileParts['path'].$fileParts['filebody'].'.png';
			$fileParts['imgext'] = 'png';
		}

		if (!file_exists($image)) {
			$fileParts = null;
		}

		$templateVariableContainer->add($arguments['as'], $fileParts);
		$content = $renderChildrenClosure();
		$templateVariableContainer->remove($arguments['as']);

		return $content;
	}
}
