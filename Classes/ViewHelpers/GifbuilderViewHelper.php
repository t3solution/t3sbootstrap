<?php
namespace T3SBS\T3sbootstrap\ViewHelpers;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Http\ApplicationType;


class GifbuilderViewHelper extends AbstractViewHelper
{
	use CompileWithRenderStatic;

	/**
	 */
	public function initializeArguments()
	{
		$this->registerArgument('uid', 'int', 'Uid of page with media', false);
		$this->registerArgument('path', 'string', 'Path to the CSS/JS file which should be included', true);
		$this->registerArgument('substr', 'string', 'substr the path', false);
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

		$path = $arguments['path'];
		$uid = $arguments['uid'];
		$substr = $arguments['substr'];

		if (defined('TYPO3') && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {
			$objectManager = GeneralUtility::makeInstance(ObjectManager::class);
			$imageService = $objectManager->get(ImageService::class);
			$resourceFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\ResourceFactory::class);
			$file = $resourceFactory->getFileObjectFromCombinedIdentifier('0:/'.$path);
			$width = 800;
			$height = 450;
			$processingInstructions = array(
								  'width' => $width . 'c',
								  'height' => $height . 'c',
								  'minWidth' => $width,
								  'minHeight' => $height,
								  'maxWidth' => $width,
								  'maxHeight' => $height,
						);
			$processedImage = $imageService->applyProcessingInstructions($file, $processingInstructions);
			$mediumImageUri = $imageService->getImageUri($processedImage);
			$width = 400;
			$height = 225;
			$processingInstructions = array(
								  'width' => $width . 'c',
								  'height' => $height . 'c',
								  'minWidth' => $width,
								  'minHeight' => $height,
								  'maxWidth' => $width,
								  'maxHeight' => $height,
						);
			$processedImage = $imageService->applyProcessingInstructions($file, $processingInstructions);
			$smallImageUri = $imageService->getImageUri($processedImage);


$picture = '<picture>

  <source media="(min-width: 56.25em)" srcset="'.$path.'">

  <source media="(min-width: 37.5em)" srcset="'.$mediumImageUri.'">

  <source srcset="'.$smallImageUri.'">

  <img src="'.$path.'" alt="">
</picture>';

return '<picture>

  <source media="(min-width: 56.25em)" srcset="'.$path.'">

  <source media="(min-width: 37.5em)" srcset="'.$mediumImageUri.'">

  <source srcset="'.$smallImageUri.'">

  <img src="'.$path.'" alt="">
</picture>';

		}
	}


	/**
	 * generateSrcsetImages
	 *
	 * @return string $css
	 */
	private function generateSrcsetImages( $file, $image ) {

		$processingInstructions = ['crop' => $file instanceof FileReference ? $file->getReferenceProperty('crop') : null];
		$cropVariantCollection = CropVariantCollection::create((string) $processingInstructions['crop']);
		$cropVariant = 'mobile';
		$cropArea = $cropVariantCollection->getCropArea($cropVariant);
		$processingInstructions = [
			'width' => 576,
			'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
		];
		$processedImage = $this->imageService()->applyProcessingInstructions($image, $processingInstructions);
		$bgImages[576] = $this->imageService()->getImageUri($processedImage);

		return $bgImages;
	}





}
