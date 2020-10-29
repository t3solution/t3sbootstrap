<?php
declare(strict_types = 1);
namespace T3SBS\T3sbootstrap\ViewHelpers;

/*
 * This file is part of the jwtools2 project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\AbstractFileFolder;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Split file reference into file parts.
 * Used for poster in local video
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

	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param RenderingContextInterface $renderingContext
	 * @return string
	 */
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
