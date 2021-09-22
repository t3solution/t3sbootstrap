<?php
namespace T3SBS\T3sbootstrap\ViewHelpers;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Resource\ResourceFactory;


class SrcsetViewHelper extends AbstractViewHelper
{
	use CompileWithRenderStatic;

	/**
	 */
	public function initializeArguments()
	{
		$this->registerArgument('path', 'string', 'Path to the image', false);
		$this->registerArgument('substr', 'string', 'substr path', false);
		$this->registerArgument('webp', 'bool', 'EXT:webp is loaded', false);
		$this->registerArgument('storageUid', 'int', 'storage uid', false, 0);
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

		$imgPath = $arguments['path'];

		if ($imgPath) {

			$storageUid = $arguments['storageUid'] ?: 0;
			$webp = $arguments['webp'];
			$objectManager = GeneralUtility::makeInstance(ObjectManager::class);
			$imageService = $objectManager->get(ImageService::class);
			$resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
			$file = $resourceFactory->getFileObjectFromCombinedIdentifier($storageUid.':/'.$imgPath);

			$width = 2560;
			$height = 623;

			$processingInstructions = array(
							   'width' => $width,
							   'height' => $height . 'c+100',
					   );
			$processedImage = $imageService->applyProcessingInstructions($file, $processingInstructions);
			$largeImageUri = $imageService->getImageUri($processedImage);


			$width = 1441;
			$height = 350;

			$processingInstructions = array(
							   'width' => $width,
							   'height' => $height . 'c+100',
					   );
			$processedImage = $imageService->applyProcessingInstructions($file, $processingInstructions);
			$mediumImageUri = $imageService->getImageUri($processedImage);


			$width = 993;
			$height = 242;

			$processingInstructions = array(
							   'width' => $width,
							   'height' => $height . 'c+100',
					   );
			$processedImage = $imageService->applyProcessingInstructions($file, $processingInstructions);
			$smallImageUri = $imageService->getImageUri($processedImage);

			$width = 576;
			$height = 141;

			$processingInstructions = array(
							   'width' => $width,
							   'height' => $height . 'c+100',

					   );
			$processedImage = $imageService->applyProcessingInstructions($file, $processingInstructions);
			$mobileImageUri = $imageService->getImageUri($processedImage);

			if ($webp) {
				$picture = '<picture>
				  <source media="(min-width: 1441px)" srcset="'.$largeImageUri.'.webp" type="image/webp">
				  <source media="(min-width: 1441px)" srcset="'.$largeImageUri.'">
				  <source media="(min-width: 993px)" srcset="'.$mediumImageUri.'.webp" type="image/webp">
				  <source media="(min-width: 993px)" srcset="'.$mediumImageUri.'">
				  <source media="(min-width: 576px)" srcset="'.$smallImageUri.'.webp" type="image/webp">
				  <source media="(min-width: 576px)" srcset="'.$smallImageUri.'">
				  <source srcset="'.$mobileImageUri.'.webp" type="image/webp">
				  <source srcset="'.$mobileImageUri.'">
				  <img src="'.$mobileImageUri.'" class="img-fluid" alt="">
				</picture>';
			} else {
				$picture = '<picture>
				  <source media="(min-width: 1441px)" srcset="'.$largeImageUri.'">
				  <source media="(min-width: 993px)" srcset="'.$mediumImageUri.'">
				  <source media="(min-width: 576px)" srcset="'.$smallImageUri.'">
				  <source srcset="'.$mobileImageUri.'">
				  <img src="'.$mobileImageUri.'" class="img-fluid" alt="">
				</picture>';
			}

			return $picture;

		} elseif ($arguments['substr']) {

			return substr($arguments['substr'], 1);
		}
	}


}
