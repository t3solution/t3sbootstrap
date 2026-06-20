<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\Rendering\RendererRegistry;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use T3SBS\T3sbootstrap\Utility\ResponsiveImagesUtility;

class MediaViewHelper extends AbstractTagBasedViewHelper
{

	/**
	 * @var string
	 */
	protected $tagName = 'img';

	/**
	 * Default crop configuration used as a fallback when a FileReference has no crop set.
	 */
	protected const DEFAULT_CROP = '{"default":{"cropArea":{"x":0,"y":0,"width":1,"height":1},"selectedRatio":"NaN","focusArea":null},"tablet":{"cropArea":{"x":0,"y":0,"width":1,"height":1},"selectedRatio":"NaN","focusArea":null},"mobile":{"cropArea":{"x":0,"y":0,"width":1,"height":1},"selectedRatio":"NaN","focusArea":null}}';

	/**
	 * Initialize arguments.
	 */
	public function initializeArguments(): void
	{
		parent::initializeArguments();

		$this->registerArgument('file', 'object', 'File', true);
		$this->registerArgument('additionalConfig', 'array', 'This array can hold additional configuration that is passed though to the Renderer object', false, []);
		$this->registerArgument('width', 'string', 'This can be a numeric value representing the fixed width of in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.');
		$this->registerArgument('height', 'string', 'This can be a numeric value representing the fixed height in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.');
		$this->registerArgument('cropVariant', 'string', 'select a cropping variant, in case multiple croppings have been specified or stored in FileReference', false, 'default');
		$this->registerArgument('loading', 'string', 'Native lazy-loading for images property. Can be "lazy", "eager" or "auto". Used on image files only.');
		$this->registerArgument('decoding', 'string', 'Provides an image decoding hint to the browser. Can be "sync", "async" or "auto"', false);
		$this->registerArgument('srcset', 'mixed', 'Image sizes that should be rendered.', false);
		$this->registerArgument(
			'sizes',
			'string',
			'Sizes query for responsive image.',
			false,
			'(min-width: %1$dpx) %1$dpx, 100vw'
		);
		$this->registerArgument('breakpoints', 'array', 'Image breakpoints from responsive design.', false, []);
		$this->registerArgument('imgtag', 'bool', 'Generate image tag', false, false);
		$this->registerArgument('lazyload', 'int', 'Generate markup that supports lazyloading', false, 0);
		$this->registerArgument('ratio', 'string', 'Image ratio', false, '');
		$this->registerArgument('mobileNoRatio', 'bool', 'no aspect ratio for mobile', false, '');
		$this->registerArgument('shift', 'string', 'Image shift', false, '');
		$this->registerArgument('hshift', 'string', 'Image horizontal shift', false, '');
		$this->registerArgument('columns', 'int', 'Columns for Image Gallery', false, 0);
		$this->registerArgument('placeholderSize', 'int', 'Size of the placeholder image for lazyloading (0 = disabled)', false, 0);
		$this->registerArgument('placeholderInline', 'bool', 'Embed placeholder image for lazyloading inline as data uri', false, false);
		$this->registerArgument('additionalAttributes', 'array', 'additional Attributes', false, false);
		$this->registerArgument(
			'ignoreFileExtensions',
			'mixed',
			'File extensions that won\'t generate responsive images',
			false,
			'svg'
		);
	}


	/**
	 * Render a given media file.
	 *
	 * @throws \UnexpectedValueException
	 * @throws Exception
	 */
	public function render(): string
	{
		$file = $this->arguments['file'] ?? null;
		$additionalConfig = (array)($this->arguments['additionalConfig'] ?? []);
		$width = ($this->arguments['width'] ?? 0);
		$height = ($this->arguments['height'] ?? 0);

		// get Resource Object (non ExtBase version)
		if (is_callable([$file, 'getOriginalResource'])) {
			// We have a domain model, so we need to fetch the FAL resource object from there
			$file = $file->getOriginalResource();
		}

		if (!$file instanceof FileInterface) {
			throw new \UnexpectedValueException('Supplied file object type ' . get_class($file) . ' must be FileInterface.', 1454252193);
		}
		
		$fileRenderer = GeneralUtility::makeInstance(RendererRegistry::class)->getRenderer($file);

		// Fallback to image when no renderer is found
		if ($fileRenderer === null) {
			return $this->renderImage($file, (string) $width, (string) $height);
		}

		$arguments = [];
		foreach (array_merge($this->arguments, $this->additionalArguments) as $argumentName => $argumentValue) {
			// Prevent "null" when given in fluid
			if (!empty($argumentValue) && $argumentValue !== 'null') {
				$arguments[$argumentName] = $argumentValue;
			}
		}

		$additionalConfig = array_merge_recursive($arguments, $additionalConfig);
		return $fileRenderer->render($file, $width, $height, $additionalConfig);

	}


	/**
	 * Render img tag
	 */
	protected function renderImage(FileInterface $image, string $width, string $height): string
	{
		if (!empty($this->arguments['imgtag'])) {
			return $this->renderImageTag($image, $width, $height);
		}
		if (!empty($this->arguments['breakpoints'])) {
			return $this->renderPicture($image, $width, $height);
		}

		return self::renderImage($image, $width, $height);
	}

	/**
	 * Render picture tag
	 */
	protected function renderPicture(FileInterface $image, string $width, string $height): string
	{
		// Get crop variants
		$cropString = $image instanceof FileReference ? (string)$image->getProperty('crop') : '';

		// Apply the default crop BEFORE it is read, otherwise json_decode('') is null
		if (empty($cropString)) {
			$cropString = self::DEFAULT_CROP;
		}

		$mobileImgManipulation = null;
		if ( $this->arguments['mobileNoRatio'] && $this->arguments['ratio'] ) {
			$decodedCrop = json_decode($cropString);
			$mobileImgManipulation = $decodedCrop->mobile ?? null;
		}

		if ( $this->arguments['ratio'] && $image->getExtension() !== 'pdf') {
			$cropString = $this->getCropString($image, $cropString);
			if ( $this->arguments['mobileNoRatio'] ) {
				$cropObject = json_decode($cropString);
				if (is_object($cropObject)) {
					$cropObject->mobile = $mobileImgManipulation;
					$cropString = json_encode($cropObject);
				}
			}
		}

		$cropVariantCollection = CropVariantCollection::create((string) $cropString);
		$cropVariant = $this->arguments['cropVariant'] ?: 'default';
		$cropArea = $cropVariantCollection->getCropArea($cropVariant);
		$focusArea = $cropVariantCollection->getFocusArea($cropVariant);

		// Generate fallback image
		$fallbackImage = $this->generateFallbackImage($image, $width, $cropArea);

		if ( !empty($GLOBALS['_GET']['type']) && $GLOBALS['_GET']['type'] === '98') {
			$lazyload = 0;
		} else {
			if ($this->arguments['lazyload']) {
				if ($this->arguments['lazyload'] === 1) {
					$lazyload = 1;
				} elseif ($this->arguments['lazyload'] === 3) {
					$lazyload = 3;
					$this->tag->addAttribute('loading', 'auto');
				} else {
					if ($this->arguments['lazyload'] === 2 && $image->getProperty('tx_t3sbootstrap_lazy_load')) {
						$lazyload = 2;
					} else {
						$lazyload = 0;
					}
				}
			} else {
				$lazyload = 0;
			}
		}

		$placeholderSize = 0;
		$placeholderInline = false;
		if ($lazyload) {
			$placeholderSize = !empty($this->arguments['placeholderSize']) ? $this->arguments['placeholderSize']: 60;
			$placeholderInline = !empty($this->arguments['placeholderInline']) ? $this->arguments['placeholderInline'] : TRUE;
		}

		foreach( $this->arguments['breakpoints'] as $bpKey=>$breakpoint ) {
			$breakpointArr[$bpKey]['cropVariant'] = $breakpoint['cropVariant'];
			$breakpointArr[$bpKey]['media'] = $breakpoint['media'];
			$breakpointArr[$bpKey]['srcset'] = '';
			if (!empty($breakpoint['srcset'])) {
				foreach( explode(',', $breakpoint['srcset']) as $key=>$srcset ) {
					if ($width > (int)$srcset) {
						$breakpointArr[$bpKey]['srcset'] .= $srcset.',';
					} else {
						$breakpointArr[$bpKey]['srcset'] .= $srcset;
						break;
					}
				}
			}
		}

		$this->arguments['breakpoints'] = $breakpointArr;

		$responsiveImagesUtility = GeneralUtility::makeInstance(ResponsiveImagesUtility::class);
		// Generate picture tag
		$this->tag = $responsiveImagesUtility->createPictureTag(
			$image,
			$fallbackImage,
			$this->arguments['breakpoints'],
			$cropVariantCollection,
			$focusArea,
			null,
			$this->tag,
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
	 */
	protected function generateFallbackImage(FileInterface $image, $width, Area $cropArea): FileInterface
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
	 * Render image tag
	 */
	protected function renderImageTag(FileInterface $image, string $width, string $height): string
	{
		 $cropVariant = 'default';
		 $cropString = $image instanceof FileReference ? (string)$image->getProperty('crop') : '';

		// Apply the default crop BEFORE it is read, otherwise json_decode('') is null
		if (empty($cropString)) {
			$cropString = self::DEFAULT_CROP;
		}

		$mobileImgManipulation = null;
		if ( $this->arguments['mobileNoRatio'] && $this->arguments['ratio'] ) {
			$decodedCrop = json_decode($cropString);
			$mobileImgManipulation = $decodedCrop->mobile ?? null;
		}

		if ( $this->arguments['ratio'] ) {
			$cropString = $this->getCropString($image, $cropString);
			if ( $this->arguments['mobileNoRatio'] ) {
				$cropObject = json_decode($cropString);
				if (is_object($cropObject)) {
					$cropObject->mobile = $mobileImgManipulation;
					$cropString = json_encode($cropObject);
				}
			}
		}

		$cropVariantCollection = CropVariantCollection::create((string)$cropString);
		$cropArea = $cropVariantCollection->getCropArea($cropVariant);
		if ( $this->arguments['ratio'] ) {
			$cropAreaWidth = (float)$cropArea->getWidth();
			if ($cropAreaWidth > 0) {
				$m = $cropArea->getHeight() / $cropAreaWidth;
				$height = ceil((float)$height * (float)$m);
			}
		}

		$processingInstructions = [
			'width' => $width,
			'height' => $height,
			'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
		];

		$imageService = $this->getImageService();
		$processedImage = $imageService->applyProcessingInstructions($image, $processingInstructions);

		$imageUri = $imageService->getImageUri($processedImage);

		if (!$this->tag->hasAttribute('data-focus-area')) {
			$focusArea = $cropVariantCollection->getFocusArea($cropVariant);
			if (!$focusArea->isEmpty()) {
				$this->tag->addAttribute('data-focus-area', $focusArea->makeAbsoluteBasedOnFile($image));
			}
		}
		$this->tag->addAttribute('src', $imageUri);
		$this->tag->addAttribute('width', $processedImage->getProperty('width'));
		$this->tag->addAttribute('height', $processedImage->getProperty('height'));
		if ($this->arguments['lazyload'] === 3) {
			$this->tag->addAttribute('loading', 'auto');
		}

		 $alt = $image->getProperty('alternative');
		 $title = $image->getProperty('title');


		// The alt-attribute is mandatory to have valid html-code, therefore add it even if it is empty
		 if (empty($this->additionalArguments['alt'])) {
			 $this->tag->addAttribute('alt', $alt);
		 }
		 if (empty($this->additionalArguments['title']) && !empty($title)) {
			 $this->tag->addAttribute('title', $title);
		 }

		 return $this->tag->render();
	}

	/**
	 * Returns an $cropString
	 */
	protected function getCropString(FileInterface $image, string $cropString): string
	{
		$cropObject = json_decode($cropString);
		// Invalid / empty crop JSON -> nothing to recalculate
		if (!is_object($cropObject)) {
			return $cropString;
		}

		if (!empty($this->arguments['breakpoints'])) {

			$imgWidth = (int)($image->getProperties()['width'] ?? 0);
			$imgHeight = (int)($image->getProperties()['height'] ?? 0);

			// No reliable dimensions (e.g. SVG without width/height) -> avoid division by zero
			if ($imgWidth < 1 || $imgHeight < 1) {
				return json_encode($cropObject);
			}

			$rArr = explode(':', (string)$this->arguments['ratio']);
			$rW = (float)($rArr[0] ?? 0);
			$rH = (float)($rArr[1] ?? 0);

			// Malformed ratio (missing part or zero) -> avoid division by zero
			if ($rW <= 0 || $rH <= 0) {
				return json_encode($cropObject);
			}

			foreach($this->arguments['breakpoints'] as $cv) {
				$cropVariant = $cv['cropVariant'];

				// Crop variant not present in the stored crop data
				if (!isset($cropObject->$cropVariant->cropArea)) {
					continue;
				}

				$cropObject->$cropVariant->selectedRatio = $this->arguments['ratio'];
				$cropedWidth = $imgWidth * $cropObject->$cropVariant->cropArea->width;
				$cropedHeight = $imgHeight * $cropObject->$cropVariant->cropArea->height;

				if ( $rW > $rH ) {
					// landscape
					$pxHeight = ($cropedWidth / $rW) * $rH;
					$pxHeight = !empty($pxHeight) ? $pxHeight : 1;
					if ( $imgHeight > $pxHeight ) {
						$cHeight = $pxHeight / $imgHeight;
						$cropObject->$cropVariant->cropArea->height = $cHeight;
					} else {
						$cHeight = $imgHeight / $pxHeight;
						$pxWidth = $cropedHeight / $rH * $rW;
						$cWidth = $pxWidth / $imgWidth;
						$cropObject->$cropVariant->cropArea->width = $cWidth;
					}
				} elseif ($rW === $rH) {
					// square
					if ( $imgWidth > $imgHeight ) {
						$pxWidth = $cropedHeight / $rH * $rW;
						$cWidth = $pxWidth / $imgWidth;
						$cropObject->$cropVariant->cropArea->width = $cWidth;
					} else {
						$pxHeight = $cropedWidth / $rW * $rH;
						$cHeight = $pxHeight / $imgHeight;
						$cropObject->$cropVariant->cropArea->height = $cHeight;
					}
				} else {
					// portrait
					$pxWidth = $cropedHeight / $rH * $rW;
					if ( $imgWidth > $pxWidth ) {
						$cWidth = $pxWidth / $imgWidth;
						$cropObject->$cropVariant->cropArea->width = $cWidth;
					} else {
						$pxWidth = !empty($pxWidth) ? $pxWidth : 1;
						$cWidth = $imgWidth / $pxWidth;
						$pxHeight = $cropedWidth / $rH * $rW;
						$cHeight = $pxHeight / $imgHeight;
						$cropObject->$cropVariant->cropArea->height = $cHeight;
					}
				}

				if ( $this->arguments['shift'] || $this->arguments['hshift'] ) {
					if ( $cropedWidth > $cropedHeight ) {
						// landscape
						$shift = $cropObject->$cropVariant->cropArea->x + $this->arguments['hshift']/100;
						if ( 1-$cropObject->$cropVariant->cropArea->width <= $shift ) {
							$shift = 1-$cropObject->$cropVariant->cropArea->width;
						}
						$cropObject->$cropVariant->cropArea->x = $shift;
					} elseif ( $cropedWidth < $cropedHeight ) {
						// portrait
						$shift = $cropObject->$cropVariant->cropArea->y + $this->arguments['shift']/100;
						if ( 1-$cropObject->$cropVariant->cropArea->height <= $shift ) {
							$shift = 1-$cropObject->$cropVariant->cropArea->height;
						}
						$cropObject->$cropVariant->cropArea->y = $shift;
					} else {
						// square
						$shift = $this->arguments['hshift'] ? $this->arguments['hshift'] : $this->arguments['shift'];
						if ( 1-$cropObject->$cropVariant->cropArea->width <= $shift ) {
							$shift = 1-$cropObject->$cropVariant->cropArea->width;
						}
						$cropObject->$cropVariant->cropArea->x = $shift;
					}
				}
			}
		}

		return json_encode($cropObject);
	}

	protected function getImageService(): ImageService
	{
		return GeneralUtility::makeInstance(ImageService::class);
	}
}
