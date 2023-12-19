<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use T3SBS\T3sbootstrap\Utility\ResponsiveImagesUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Resource\Rendering\RendererRegistry;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

#ConfigurationManagerInterface

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * 	taken from https://extensions.typo3.org/extension/sms_responsive_images/ and modified
 */
class MediaViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'img';
    
	/**
	 * @var ResponsiveImagesUtility
	 */
	protected $responsiveImagesUtility;

	/**
	 * @var ConfigurationManager
	 */
	protected $configurationManager;


	/**
	 * @param ResponsiveImagesUtility $responsiveImagesUtility
	 */
	public function injectResponsiveImagesUtility(ResponsiveImagesUtility $responsiveImagesUtility)
	{
		$this->responsiveImagesUtility = $responsiveImagesUtility;
	}


	/**
	 * @param ConfigurationManager $configurationManager
	 */
	public function injectConfigurationManager(ConfigurationManager $configurationManager)
	{
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Initialize arguments.
	 */
	public function initializeArguments()
	{
#		parent::initializeArguments();

        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('alt', 'string', 'Specifies an alternate text for an image', false);
        $this->registerArgument('file', 'object', 'File', true);
        $this->registerArgument('additionalConfig', 'array', 'This array can hold additional configuration that is passed though to the Renderer object', false, []);
        $this->registerArgument('width', 'string', 'This can be a numeric value representing the fixed width of in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.');
        $this->registerArgument('height', 'string', 'This can be a numeric value representing the fixed height in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.');
        $this->registerArgument('cropVariant', 'string', 'select a cropping variant, in case multiple croppings have been specified or stored in FileReference', false, 'default');
        $this->registerArgument('fileExtension', 'string', 'Custom file extension to use for images');
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
		$this->registerArgument('imgtag', 'bool', 'Use rendering suggested by picturefill.js', false, false);
		$this->registerArgument('picturefill', 'bool', 'Use rendering suggested by picturefill.js', false, true);
		$this->registerArgument('lazyload', 'int', 'Generate markup that supports lazyloading', false, 0);
		$this->registerArgument('ratio', 'string', 'Image ratio', false, '');
		$this->registerArgument('mobileNoRatio', 'bool', 'no aspect ratio for mobile', false, '');
		$this->registerArgument('shift', 'string', 'Image shift', false, '');
		$this->registerArgument('hshift', 'string', 'Image horizontal shift', false, '');
		$this->registerArgument('columns', 'int', 'Columns for Image Gallery', false, 0);
		$this->registerArgument('placeholderSize', 'int', 'Size of the placeholder image for lazyloading (0 = disabled)', false, 0);
		$this->registerArgument('placeholderInline', 'bool', 'Embed placeholder image for lazyloading inline as data uri', false, false);
		$this->registerArgument('additionalAttributes', 'string', 'additional Attributes', false, false);
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
        $file = $this->arguments['file'];
        $additionalConfig = (array)$this->arguments['additionalConfig'];
        $width = $this->arguments['width'];
        $height = $this->arguments['height'];

        // get Resource Object (non ExtBase version)
        if (is_callable([$file, 'getOriginalResource'])) {
            // We have a domain model, so we need to fetch the FAL resource object from there
            $file = $file->getOriginalResource();
        }

        if (!$file instanceof FileInterface) {
            throw new \UnexpectedValueException('Supplied file object type ' . get_class($file) . ' must be FileInterface.', 1454252193);
        }

        if ((string)$this->arguments['fileExtension'] && !GeneralUtility::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], (string)$this->arguments['fileExtension'])) {
            throw new Exception(
                'The extension ' . $this->arguments['fileExtension'] . ' is not specified in $GLOBALS[\'TYPO3_CONF_VARS\'][\'GFX\'][\'imagefile_ext\']'
                . ' as a valid image file extension and can not be processed.',
                1619030957
            );
        }

        $fileRenderer = GeneralUtility::makeInstance(RendererRegistry::class)->getRenderer($file);

        // Fallback to image when no renderer is found
        if ($fileRenderer === null) {
            return $this->renderImage($file, $width, $height, $this->arguments['fileExtension'] ?? null);
        }
        $additionalConfig = array_merge_recursive($this->arguments, $additionalConfig);
        return $fileRenderer->render($file, $width, $height, $additionalConfig);
    }
    
    
	/**
	 * Render img tag
	 *
	 * @param	FileInterface $image
	 * @param	string		 $width
	 * @param	string		 $height
	 * @param	string|null $fileExtension
	 * @return string Rendered img tag
	 */
	protected function renderImage(FileInterface $image, $width, $height, ?string $fileExtension=null)
	{
		if (!empty($this->arguments['imgtag'])) {
			return self::renderImageTag($image, $width, $height, $fileExtension);
		} else {
			if (!empty($this->arguments['breakpoints'])) {
				return $this->renderPicture($image, $width, $height);
			} else {
				return parent::renderImage($image, $width, $height, $fileExtension);
			}
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
		if ( $this->arguments['mobileNoRatio'] && $this->arguments['ratio'] ) {
			$mobileImgManipulation = json_decode($cropString)->mobile;
		}

		if (empty($cropString)) {
			$cropString = '{"default":{"cropArea":{"x":0,"y":0,"width":1,"height":1},"selectedRatio":"NaN","focusArea":null},"tablet":{"cropArea":{"x":0,"y":0,"width":1,"height":1},"selectedRatio":"NaN","focusArea":null},"mobile":{"cropArea":{"x":0,"y":0,"width":1,"height":1},"selectedRatio":"NaN","focusArea":null}}';
		}

		if ( $this->arguments['ratio'] && $image->getExtension() !== 'pdf') {
			$cropString = self::getCropString($image, $cropString);
			if ( $this->arguments['mobileNoRatio'] ) {
				$cropObject = json_decode($cropString);
				$cropObject->mobile = $mobileImgManipulation;
				$cropString = json_encode($cropObject);
			}
		}

		$cropVariantCollection = CropVariantCollection::create((string) $cropString);
		$cropVariant = $this->arguments['cropVariant'] ?: 'default';
		$cropArea = $cropVariantCollection->getCropArea($cropVariant);
		$focusArea = $cropVariantCollection->getFocusArea($cropVariant);

		// Generate fallback image
		$fallbackImage = $this->generateFallbackImage($image, $width, $cropArea);

		if ( !empty($GLOBALS['_GET']['type']) && $GLOBALS['_GET']['type'] == '98') {
			$lazyload = 0;
		} else {
			if ($this->arguments['lazyload']) {
				if ($this->arguments['lazyload'] == 1) {
					$lazyload = 1;
				} elseif ($this->arguments['lazyload'] == 3) {
					$lazyload = 3;
					$this->tag->addAttribute('loading', 'auto');
				} else {
					if ($this->arguments['lazyload'] == 2 && $image->getProperty('tx_t3sbootstrap_lazy_load')) {
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
			foreach( explode(',', $breakpoint['srcset']) as $key=>$srcset ) {
				if ($width > (int)$srcset) {
					$breakpointArr[$bpKey]['srcset'] .= $srcset.',';
				} else {
					$breakpointArr[$bpKey]['srcset'] .= $srcset;
					break;
				}
			}
		}

		$this->arguments['breakpoints'] = $breakpointArr;

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
	 * Generates a fallback image for picture and srcset markup
	 *
	 * @param	FileInterface $image
	 * @param	string		 $width
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
		$imageService = self::getImageService();
		$fallbackImage = $imageService->applyProcessingInstructions($image, $processingInstructions);

		return $fallbackImage;
	}

	/**
	 * Render image tag
	 *
	 * @param	FileInterface $image
	 * @param	string		 $width
	 * @param	string		 $height
	 *
	 * @return string					Rendered image tag
	 */
	protected function renderImageTag(FileInterface $image, $width, $height, $fileExtension)
	{
		 $cropVariant = 'default';
		 $cropString = $image instanceof FileReference ? $image->getProperty('crop') : '';

		if ( $this->arguments['mobileNoRatio'] && $this->arguments['ratio'] ) {
			$mobileImgManipulation = json_decode($cropString)->mobile;
		}

		if ( $this->arguments['ratio'] ) {
			$cropString = self::getCropString($image, $cropString);
			if ( $this->arguments['mobileNoRatio'] ) {
				$cropObject = json_decode($cropString);
				$cropObject->mobile = $mobileImgManipulation;
				$cropString = json_encode($cropObject);
			}
		}

		$cropVariantCollection = CropVariantCollection::create((string)$cropString);
		$cropArea = $cropVariantCollection->getCropArea($cropVariant);
		if ( $this->arguments['ratio'] ) {
			$m = $cropArea->getHeight() / $cropArea->getWidth();
			$height = ceil((float)$height * (float)$m);
		}

		$processingInstructions = [
			'width' => $width,
			'height' => $height,
			'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
		];

		if (!empty($fileExtension)) {
			$processingInstructions['fileExtension'] = $fileExtension;
		}
		$imageService = self::getImageService();
		$processedImage = $imageService->applyProcessingInstructions($image, $processingInstructions);

		$settings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

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
		if ($this->arguments['lazyload'] == 3) {
			$this->tag->addAttribute('loading', 'auto');
		}

		 $alt = $image->getProperty('alternative');
		 $title = $image->getProperty('title');

		 // The alt-attribute is mandatory to have valid html-code, therefore add it even if it is empty
		if (empty($this->arguments['alt'])) {
			$this->tag->addAttribute('alt', $alt);
		 }
		 if (empty($this->arguments['title']) && $title) {
			$this->tag->addAttribute('title', $title);
		 }

		 return $this->tag->render();
	}

	/**
	 * Returns an $cropString
	 *
	 * @return string
	 */
	protected function getCropString($image, $cropString)
	{
		$cropObject = json_decode($cropString);
		if (!empty($this->arguments['breakpoints'])) {
			foreach($this->arguments['breakpoints'] as $cv) {
				$cropVariant = $cv['cropVariant'];
				$cropObject->$cropVariant->selectedRatio = $this->arguments['ratio'];
				$cropedWidth = $image->getProperties()['width'] * $cropObject->$cropVariant->cropArea->width;
				$cropedHeight = $image->getProperties()['height'] * $cropObject->$cropVariant->cropArea->height;
				$rArr = explode(':',$this->arguments['ratio']);

				if ( $rArr[0] > $rArr[1] ) {
					// landscape
					$pxHeight = ($cropedWidth / $rArr[0]) * $rArr[1];
					$pxHeight = !empty($pxHeight) ? $pxHeight : 1;
					if ( $image->getProperties()['height'] > $pxHeight ) {
						$cHeight = $pxHeight / $image->getProperties()['height'];
						$cropObject->$cropVariant->cropArea->height = $cHeight;
					} else {
						$cHeight = $image->getProperties()['height'] / $pxHeight;
						$pxWidth = $cropedHeight / $rArr[1] * $rArr[0];
						$cWidth = $pxWidth / $image->getProperties()['width'];
						$cropObject->$cropVariant->cropArea->width = $cWidth;
					}
				} elseif ($rArr[0] == $rArr[1]) {
					// square
					if ( $image->getProperties()['width'] > $image->getProperties()['height'] ) {
						$pxWidth = $cropedHeight / $rArr[1] * $rArr[0];
						$cWidth = $pxWidth / $image->getProperties()['width'];
						$cropObject->$cropVariant->cropArea->width = $cWidth;
					} else {
						$pxHeight = $cropedWidth / $rArr[0] * $rArr[1];
						$cHeight = $pxHeight / $image->getProperties()['height'];
						$cropObject->$cropVariant->cropArea->height = $cHeight;
					}
				} else {
					// portrait
					$pxWidth = $cropedHeight / $rArr[1] * $rArr[0];
					if ( $image->getProperties()['width'] > $pxWidth ) {
						$cWidth = $pxWidth / $image->getProperties()['width'];
						$cropObject->$cropVariant->cropArea->width = $cWidth;
					} else {
						$cWidth = $image->getProperties()['width'] / $pxWidth;
						$pxHeight = $cropedWidth / $rArr[1] * $rArr[0];
						$cHeight = $pxHeight / $image->getProperties()['height'];
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
