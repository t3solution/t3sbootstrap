<?php
namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Resource\FileRepository;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class DummyImageViewHelper extends AbstractViewHelper
{
	use CompileWithRenderStatic;

	/**
	 */
	public function initializeArguments()
	{
		$this->registerArgument('uid', 'string', 'Uid of tt_content with custom dummy image for EXT:news', false);
	}

	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param RenderingContextInterface $renderingContext
	 */
	public static function renderStatic(
		array $arguments,
		\Closure $renderChildrenClosure,
		RenderingContextInterface $renderingContext
	) {

		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
		$fileObjects = $fileRepository->findByRelation('tt_content', 'image', (int)$arguments['uid']);
		if (empty($fileObjects)) {
			$fileObjects = $fileRepository->findByRelation('tt_content', 'assets', (int)$arguments['uid']);		
		}

		return $fileObjects[0];
	}
	
}
