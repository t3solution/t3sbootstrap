<?php

namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use T3SBS\T3sbootstrap\Utility\ResponsiveImagesUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
	taken from https://extensions.typo3.org/extension/sms_responsive_images/ and modified
*/

class MediaViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\MediaViewHelper
{
	/**
	 * @var ResponsiveImagesUtility
	 */
	protected $responsiveImagesUtility;

	/**
	 * @param ResponsiveImagesUtility $responsiveImagesUtility
	 */
	public function injectResponsiveImagesUtility(ResponsiveImagesUtility $responsiveImagesUtility)
	{
		$this->responsiveImagesUtility = $responsiveImagesUtility;
	}


	/**
	 * Initialize arguments.
	 */
	public function initializeArguments()
	{
		parent::initializeArguments();
		$this->registerArgument('srcset', 'mixed', 'Image sizes that should be rendered.', false);
		$this->registerArgument(
			'sizes',
			'string',
			'Sizes query for responsive image.',
			false,
			'(min-width: %1$dpx) %1$dpx, 100vw'
		);
		$this->registerArgument('breakpoints', 'array', 'Image breakpoints from responsive design.', false, []);
		$this->registerArgument('picturefill', 'bool', 'Use rendering suggested by picturefill.js', false, true);
		$this->registerArgument('lazyload', 'int', 'Generate markup that supports lazyloading', false, 0);
		$this->registerArgument('ratio', 'string', 'Image ratio', false, '');
		$this->registerArgument('shift', 'string', 'Image shift', false, '');
		$this->registerArgument('columns', 'int', 'Columns for Image Gallery', false, 0);
		$this->registerArgument('placeholderSize', 'int', 'Size of the placeholder image for lazyloading (0 = disabled)', false, 0);
		$this->registerArgument('placeholderInline', 'bool', 'Embed placeholder image for lazyloading inline as data uri', false, false);
		$this->registerArgument(
			'ignoreFileExtensions',
			'mixed',
			'File extensions that won\'t generate responsive images',
			false,
			'svg'
		);
	}

	/**
	 * Render img tag
	 *
	 * @param	FileInterface $image
	 * @param	string		 $width
	 * @param	string		 $height
	 *
	 * @return string					Rendered img tag
	 */
	protected function renderImage(FileInterface $image, $width, $height)
	{

		if ($this->arguments['breakpoints']) {
			return $this->renderPicture($image, $width, $height);
		} elseif ($this->arguments['srcset']) {
			return $this->renderImageSrcset($image, $width, $height);
		} else {
			return parent::renderImage($image, $width, $height);
		}
	}

	/**
	 * Render picture tag
	 *
	 * @param	FileInterface $image
	 * @param	string		 $width
	 * @param	string		 $height
	 *
	 * @return string					Rendered picture tag
	 */
	protected function renderPicture(FileInterface $image, $width, $height)
	{
		// Get crop variants
		$cropString = $image instanceof FileReference ? $image->getProperty('crop') : '';

		if ( $this->arguments['ratio'] ) {
			$cropString = self::getCropString($image);
		}


		$cropVariantCollection = CropVariantCollection::create((string) $cropString);
		$cropVariant = $this->arguments['cropVariant'] ?: 'default';

		$cropArea = $cropVariantCollection->getCropArea($cropVariant);
		$focusArea = $cropVariantCollection->getFocusArea($cropVariant);

		// Generate fallback image
		$fallbackImage = $this->generateFallbackImage($image, $width, $cropArea);


		if ( $GLOBALS['_GET']['type'] == '98' ) {
			$lazyload = 0;
		} else {
			if ($this->arguments['lazyload']) {
				if ($this->arguments['lazyload'] == 1) {
					$lazyload = $this->arguments['lazyload'];
				} else {
					if ($this->arguments['lazyload'] == 2 && $image->getProperty('tx_t3sbootstrap_lazy_load')) {
						$lazyload = $this->arguments['lazyload'];
					} else {
						$lazyload = 0;
					}
				}
			} else {
				$lazyload = 0;
			}
		}
$placeholderSize = 0;
$placeholderInline = 0;
if ($lazyload) {
$placeholderSize = $this->arguments['placeholderSize'] ? $this->arguments['placeholderSize'] : 60;
$placeholderInline = $this->arguments['placeholderInline'] ? $this->arguments['placeholderInline'] : 1;
}
		// Generate picture tag
		$this->tag = $this->responsiveImagesUtility->createPictureTag(
			$image,
			$fallbackImage,
			$this->arguments['breakpoints'],
			$cropVariantCollection,
			$focusArea,
			null,
			$this->tag,
			$this->arguments['picturefill'],
			false,
			$lazyload,
			$this->arguments['ignoreFileExtensions'],
			$placeholderSize,
			$placeholderInline
		);


		return $this->tag->render();
	}

	/**
	 * Render img tag with srcset/sizes attributes
	 *
	 * @param	FileInterface $image
	 * @param	string		 $width
	 * @param	string		 $height
	 *
	 * @return string					Rendered img tag
	 */
	protected function renderImageSrcset(FileInterface $image, $width, $height)
	{
		// Get crop variants
		$cropString = $image instanceof FileReference ? $image->getProperty('crop') : '';

		if ( $this->arguments['ratio'] ) {
			$cropString = self::getCropString($image);
		}

		$cropVariantCollection = CropVariantCollection::create((string) $cropString);

		$cropVariant = $this->arguments['cropVariant'] ?: 'default';
		$cropArea = $cropVariantCollection->getCropArea($cropVariant);
		$focusArea = $cropVariantCollection->getFocusArea($cropVariant);

		// Generate fallback image
		$fallbackImage = $this->generateFallbackImage($image, $width, $cropArea);

		if ( $GLOBALS['_GET']['type'] == '98' ) {
			$lazyload = 0;
		} else {
			if ($this->arguments['lazyload']) {
				if ($this->arguments['lazyload'] == 1) {
					$lazyload = $this->arguments['lazyload'];
				} else {
					if ($this->arguments['lazyload'] == 2 && $image->getProperty('tx_t3sbootstrap_lazy_load')) {
						$lazyload = $this->arguments['lazyload'];
					} else {
						$lazyload = 0;
					}
				}
			} else {
				$lazyload = 0;
			}
		}
$placeholderSize = 0;
$placeholderInline = 0;
if ($lazyload) {
$placeholderSize = $this->arguments['placeholderSize'] ? $this->arguments['placeholderSize'] : 60;
$placeholderInline = $this->arguments['placeholderInline'] ? $this->arguments['placeholderInline'] : 1;
}
		// Generate image tag
		$this->tag = $this->responsiveImagesUtility->createImageTagWithSrcset(
			$image,
			$fallbackImage,
			$this->arguments['srcset'],
			$cropArea,
			$focusArea,
			$this->arguments['sizes'],
			$this->tag,
			$this->arguments['picturefill'],
			false,
			$lazyload,
			$this->arguments['ignoreFileExtensions'],
			$placeholderSize,
			$placeholderInline
		);

		return $this->tag->render();
	}

	/**
	 * Generates a fallback image for picture and srcset markup
	 *
	 * @param	FileInterface $image
	 * @param	string		 $width
	 * @param	string		 $height
	 * @param	Area			 $cropArea
	 *
	 * @return FileInterface
	 */
	protected function generateFallbackImage(FileInterface $image, $width, Area $cropArea)
	{
		$processingInstructions = [
			'width' => $width,
			'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
		];
		$imageService = $this->getImageService();
		$fallbackImage = $imageService->applyProcessingInstructions($image, $processingInstructions);

		return $fallbackImage;
	}



	/**
	 * Returns an $cropString
	 *
	 * @return string
	 */
	protected function getCropString($image)
	{
		$rArr = explode(':',$this->arguments['ratio']);
		$l = $rArr[0] / $rArr[1];
		$p = $rArr[1] / $rArr[0];

		// Image Gallery only
		if ( $this->arguments['columns'] ) {
			$width = $width ? $width : 1110/$this->arguments['columns'];
		}

		$w = $image->getProperties()['height']/$image->getProperties()['width'] *$l;
		$h = 1;
		$x = $this->arguments['shift'] ? $this->arguments['shift'] : (1 - $w) / 2;
		$y = 0;

		if ( $w > 1 ) {
			$h = $image->getProperties()['width']/$image->getProperties()['height'] *$p;
			$w = 1;
			$x = 0;
			$y = $this->arguments['shift'] ? $this->arguments['shift'] : (1 - $h) / 2;
		}

		return '{"default":{"cropArea":{"x":'.$x.',"y":'.$y.',"width":'.$w.',"height":'.$h.'},"selectedRatio":"'.$this->arguments['ratio'].'","focusArea":null},"tablet":{"cropArea":{"x":'.$x.',"y":'.$y.',"width":'.$w.',"height":'.$h.'},"selectedRatio":"'.$this->arguments['ratio'].'","focusArea":null},"mobile":{"cropArea":{"x":'.$x.',"y":'.$y.',"width":'.$w.',"height":'.$h.'},"selectedRatio":"'.$this->arguments['ratio'].'","focusArea":null}}';

	}


}
