<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ResponsiveImagesUtility implements SingletonInterface
{
	/**
	 * Image Service
	 *
	 * @var ImageService
	 */
	protected $imageService;

	/**
	 * Default media breakpoint configuration
	 *
	 * @var array
	 */
	protected $breakpointPrototype = [
		'cropVariant' => 'default',
		'media' => '',
		'sizes' => '(min-width: %1$dpx) %1$dpx, 100vw',
		'srcset' => []
	];

	/**
	 * @param ImageService $imageService
	 */
	public function __construct(ImageService $imageService)
	{
		 $this->imageService = $imageService;
	}


	/**
	 * Creates a picture tag with the provided image breakpoints
	 *
	 * @param	FileInterface		 $originalImage
	 * @param	FileInterface		 $fallbackImage
	 * @param	array				 $breakpoints
	 * @param	CropVariantCollection $cropVariantCollection
	 * @param	Area					 $focusArea
	 * @param	TagBuilder			 $tag
	 * @param	TagBuilder			 $fallbackTag
	 * @param	bool					 $picturefillMarkup
	 * @param	bool					 $absoluteUri
	 * @param	int					$lazyload
	 * @param	array|string			 $ignoreFileExtensions
	 * @param	int					 $placeholderSize
	 * @param	bool					 $placeholderInline
	 *
	 * @return TagBuilder
	 */
	public function createPictureTag(
		FileInterface $originalImage,
		FileInterface $fallbackImage,
		array $breakpoints,
		CropVariantCollection $cropVariantCollection,
		Area $focusArea = null,
		TagBuilder $tag = null,
		TagBuilder $fallbackTag = null,
		bool $picturefillMarkup = true,
		bool $absoluteUri = false,
		int $lazyload = 0,
		$ignoreFileExtensions = 'svg',
		int $placeholderSize = 0,
		bool $placeholderInline = false
	): TagBuilder {
		$tag = $tag ?: GeneralUtility::makeInstance(TagBuilder::class, 'picture');
		$fallbackTag = $fallbackTag ?: GeneralUtility::makeInstance(TagBuilder::class, 'img');

		$webpIsLoaded = ExtensionManagementUtility::isLoaded('webp');

		// Deal with file formats that can't be cropped separately
		if ($this->hasIgnoredFileExtension($originalImage, $ignoreFileExtensions)) {
			return $this->createSimpleImageTag(
				$originalImage,
				$fallbackImage,
				$fallbackTag,
				$focusArea,
				$absoluteUri,
				$lazyload
			);
		}

		// Normalize breakpoint configuration
		$breakpoints = $this->normalizeImageBreakpoints($breakpoints);

		// Use width of fallback image as reference for relative sizes
		$referenceWidth = $fallbackImage->getProperty('width');
		$referenceHeight = $fallbackImage->getProperty('height');

		// if lazyload enabled add data- prefix
		$attributePrefix = $lazyload && $lazyload != 3 ? 'data-' : '';

		// Use last breakpoint as fallback image if it doesn't define a media query
		$lastBreakpoint = array_pop($breakpoints);

		$cropArea = $cropVariantCollection->getCropArea($lastBreakpoint['cropVariant']);

		if ($lastBreakpoint && !$lastBreakpoint['media'] && $picturefillMarkup) {
			// Generate different image sizes for last breakpoint
			$cropArea = $cropVariantCollection->getCropArea($lastBreakpoint['cropVariant']);
			$srcset = $this->generateSrcsetImages(
				$originalImage,
				$referenceWidth,
				$lastBreakpoint['srcset'],
				$cropArea,
				$absoluteUri
			);
			$srcsetMode = substr(key($srcset), -1); // x or w

			// Set srcset attribute for fallback image
			$fallbackTag->addAttribute($attributePrefix . 'srcset', $this->generateSrcsetAttribute($srcset));

			// Set sizes query for fallback image
			if ($srcsetMode == 'w' && $lastBreakpoint['sizes']) {
				$fallbackTag->addAttribute('sizes', sprintf($lastBreakpoint['sizes'], $referenceWidth));
			}
		} else {

			// Breakpoint can't be used as fallback
			if ($lastBreakpoint) {
				array_push($breakpoints, $lastBreakpoint);
			}

			// Set srcset attribute for fallback image (not src as advised by picturefill)
			$fallbackImageUri =	$this->imageService->getImageUri($fallbackImage, $absoluteUri);
			if ($picturefillMarkup) {
				$fallbackTag->addAttribute($attributePrefix . 'srcset', $fallbackImageUri);
			} else {
				$fallbackTag->addAttribute($attributePrefix . 'src', $fallbackImageUri);
			}
		}

		$cropArea = $cropVariantCollection->getCropArea($lastBreakpoint['cropVariant']);

		// Create placeholder image for lazyloading
		if ($lazyload && $placeholderSize) {
			$fallbackTag->addAttribute('src', $this->generatePlaceholderImage(
				$originalImage,
				$placeholderSize,
				$cropArea,
				$placeholderInline,
				$absoluteUri
			));
		}

		// Provide image width to be consistent with TYPO3 core behavior
		$fallbackTag->addAttribute('width', $referenceWidth);
		$fallbackTag->addAttribute('height', $referenceHeight);

		// Add metadata to fallback image
		$this->addMetadataToImageTag($fallbackTag, $originalImage, $fallbackImage, $focusArea);

		if ( $webpIsLoaded ) {
			$types[0] = 'image/webp';
		}
		$types[1] = $originalImage->getProperties()['mime_type'];

		// Generate source tags for image breakpoints
		$sourceTags = [];
		foreach ($breakpoints as $breakpoint) {

			$cropArea = $cropVariantCollection->getCropArea($breakpoint['cropVariant']);

			foreach ( $types as $type ) {
				$sourceTag = $this->createPictureSourceTag(
					$originalImage,
					$referenceWidth,
					$breakpoint['srcset'],
					$breakpoint['media'],
					$breakpoint['sizes'],
					$cropArea,
					$absoluteUri,
					$lazyload,
					$webpIsLoaded,
					$type
				);

				$sourceTags[] = $sourceTag->render();
			}
		}

		// Fill picture tag
		$tag->setContent(
			implode('', $sourceTags) . $fallbackTag->render()
		);

		return $tag;
	}

	/**
	 * Creates a source tag that can be used inside of a picture tag
	 *
	 * @param	FileInterface $originalImage
	 * @param	int			 $defaultWidth
	 * @param	array|string	 $srcset
	 * @param	string		 $mediaQuery
	 * @param	string		 $sizesQuery
	 * @param	Area			 $cropArea
	 * @param	bool			 $absoluteUri
	 * @param	int			$lazyload
	 * @param	bool			 $webpIsLoaded
	 * @param	string		 $type
	 *
	 * @return TagBuilder
	 */
	public function createPictureSourceTag(
		FileInterface $originalImage,
		int $defaultWidth,
		$srcset,
		string $mediaQuery = '',
		string $sizesQuery = '',
		Area $cropArea = null,
		bool $absoluteUri = false,
		int $lazyload = 0,
		bool $webpIsLoaded = false,
		string $type = ''
	): TagBuilder {
		$cropArea = $cropArea ?: Area::createEmpty();

		// Generate different image sizes for srcset attribute
		$srcsetImages = $this->generateSrcsetImages($originalImage, $defaultWidth, $srcset, $cropArea, $absoluteUri, $webpIsLoaded, $type);
		$srcsetMode = substr(key($srcsetImages), -1); // x or w

		// Create source tag for this breakpoint
		$sourceTag = GeneralUtility::makeInstance(TagBuilder::class, 'source');
		if ($lazyload && $lazyload < 3) {
			$sourceTag->addAttribute('data-srcset', $this->generateSrcsetAttribute($srcsetImages));
		}
		$sourceTag->addAttribute('srcset', $this->generateSrcsetAttribute($srcsetImages));

		if ($mediaQuery) {
			$sourceTag->addAttribute('media', $mediaQuery);
		}
		if ($srcsetMode == 'w' && $sizesQuery) {
			$sourceTag->addAttribute('sizes', sprintf($sizesQuery, $defaultWidth));
		}

		if ($webpIsLoaded && $type == 'image/webp')
		 $sourceTag->addAttribute('type', $type);

		return $sourceTag;
	}

	/**
	 * Creates a simple image tag
	 *
	 * @param	FileInterface $image
	 * @param	FileInterface $fallbackImage
	 * @param	TagBuilder	 $tag
	 * @param	Area			 $focusArea
	 * @param	bool			 $absoluteUri
	 * @param	int			$lazyload
	 * @param	int			 $placeholderSize
	 * @param	bool			 $placeholderInline
	 *
	 * @return TagBuilder
	 */
	public function createSimpleImageTag(
		FileInterface $originalImage,
		FileInterface $fallbackImage = null,
		TagBuilder $tag = null,
		Area $focusArea = null,
		bool $absoluteUri = false,
		int $lazyload = 0,
		int $placeholderSize = 0,
		bool $placeholderInline = false
	): TagBuilder {
		$tag = $tag ?: GeneralUtility::makeInstance(TagBuilder::class, 'img');
		$fallbackImage = ($fallbackImage) ?: $originalImage;

		// if lazyload enabled add data- prefix
		$attributePrefix = $lazyload ? 'data-' : '';

		// Set image source
		$tag->addAttribute($attributePrefix . 'src',	$this->imageService->getImageUri($originalImage, $absoluteUri));

		// Create placeholder image for lazyloading
		if ($lazyload && $placeholderSize) {
			$tag->addAttribute('src', $this->generatePlaceholderImage(
				$originalImage,
				$placeholderSize,
				null,
				$placeholderInline,
				$absoluteUri
			));
		}

		// Set image proportions
		$tag->addAttribute('width', $fallbackImage->getProperty('width'));
		$tag->addAttribute('height', $fallbackImage->getProperty('height'));

		// Add metadata to image tag
		$this->addMetadataToImageTag($tag, $originalImage, $fallbackImage, $focusArea);

		return $tag;
	}

	/**
	 * Adds metadata to image tag
	 *
	 * @param TagBuilder	$tag
	 * @param FileInterface $originalImage
	 * @param FileInterface $fallbackImage
	 * @param Area			$focusArea
	 *
	 * @return void
	 */
	public function addMetadataToImageTag(
		TagBuilder $tag,
		FileInterface $originalImage,
		FileInterface $fallbackImage,
		Area $focusArea = null
	): void {
		$focusArea = $focusArea ?: Area::createEmpty();

		// Add focus area to image tag
		if (!$tag->hasAttribute('data-focus-area') && !$focusArea->isEmpty()) {
			$tag->addAttribute('data-focus-area', $focusArea->makeAbsoluteBasedOnFile($fallbackImage));
		}

		// The alt-attribute is mandatory to have valid html-code, therefore add it even if it is empty
		$alt = $originalImage->getProperty('alternative');
		if (!$tag->getAttribute('alt')) {
			$tag->addAttribute('alt', $alt);
		}
		$title = $originalImage->getProperty('title');
		if (!$tag->getAttribute('title') && $title) {
			$tag->addAttribute('title', $title);
		}
	}

	/**
	 * Renders different image sizes for use in a srcset attribute
	 *
	 * Input:
	 *	 1: $srcset = [200, 400]
	 *	 2: $srcset = ['200w', '400w']
	 *	 3: $srcset = ['1x', '2x']
	 *	 4: $srcset = '200, 400'
	 *
	 * Output:
	 *	 1+2+4: ['200w' => 'path/to/image@200w.jpg', '400w' => 'path/to/image@200w.jpg']
	 *	 3: ['1x' => 'path/to/image@1x.jpg', '2x' => 'path/to/image@2x.jpg']
	 *
	 * @param	FileInterface  $image
	 * @param	int				$defaultWidth
	 * @param	array|string	  $srcset
	 * @param	Area				 $cropArea
	 * @param	bool				 $absoluteUri
	 * @param	bool			$webpIsLoaded
	 * @param	string			 	$type
	 *
	 * @return array
	 */
	public function generateSrcsetImages(
		FileInterface $image,
		int $defaultWidth,
		$srcset,
		Area $cropArea = null,
		bool $absoluteUri = false,
		bool $webpIsLoaded = false,
		string $type = ''
	): array {
		$cropArea = $cropArea ?: Area::createEmpty();

		// Convert srcset input to array
		if (!is_array($srcset)) {
			$srcset = GeneralUtility::trimExplode(',', $srcset);
		}


		$images = [];
		foreach ($srcset as $widthDescriptor) {
			// Determine image width
			$srcsetMode = substr($widthDescriptor, -1);

			switch ($srcsetMode) {
				case 'x':
					$candidateWidth = (int) ($defaultWidth * (float) substr($widthDescriptor, 0, -1));
					break;

				case 'w':
					$candidateWidth = (int) substr($widthDescriptor, 0, -1);
					break;

				default:
					$candidateWidth = (int) $widthDescriptor;
					$srcsetMode = 'w';
					$widthDescriptor = $candidateWidth . 'w';
			}

			// Generate image
			$processingInstructions = [
				'width' => $candidateWidth,
				'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
			];
			$processedImage =  $this->imageService->applyProcessingInstructions($image, $processingInstructions);

			if ( $webpIsLoaded && $type == 'image/webp' ) {
				$webpIdentifier = $processedImage->getIdentifier().'.webp';
				$processedImage->setIdentifier($webpIdentifier);
			}

			// If processed file isn't as wide as it should be ([GFX][processor_allowUpscaling] set to false)
			// then use final width of the image as widthDescriptor if not input case 3 is used
			$processedWidth = $processedImage->getProperty('width');
			if ($srcsetMode === 'w' && $processedWidth !== $candidateWidth) {
				$widthDescriptor = $processedWidth . 'w';
			}

			$images[$widthDescriptor] =	$this->imageService->getImageUri($processedImage, $absoluteUri);
		}

		return $images;
	}


	/**
	 * Generates a tiny placeholder image for lazyloading
	 *
	 * @param FileInterface $image
	 * @param integer $width
	 * @param Area $cropArea
	 * @param boolean $inline
	 * @param boolean $absoluteUri
	 *
	 * @return string
	 */
	public function generatePlaceholderImage(
		FileInterface $image,
		int $width = 20,
		Area $cropArea = null,
		bool $inline = false,
		bool $absoluteUri = false
	): string {
		$cropArea = $cropArea ?: Area::createEmpty();

		$processingInstructions = [
			'width' => $width,
			'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
		];
		$processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);

		if ($inline) {
			return $this->generateDataUri($processedImage);
		} else {
			return $this->imageService->getImageUri($processedImage, $absoluteUri);
		}
	}

	/**
	 * Generates a data URI for the specified image file
	 *
	 * @param FileInterface $image
	 *
	 * @return string
	 */
	public function generateDataUri(FileInterface $image): string
	{
		return 'data:' . $image->getMimeType() . ';base64,' . base64_encode($image->getContents());
	}


	/**
	 * Generates the content for a srcset attribute from an array of image urls
	 *
	 * Input:
	 * [
	 *	 '200w' => 'path/to/image@200w.jpg',
	 *	 '400w' => 'path/to/image@400w.jpg'
	 * ]
	 *
	 * Output:
	 * 'path/to/image@200w.jpg 200w, path/to/image@400w.jpg 400w'
	 *
	 * @param	array	$srcsetImages
	 *
	 * @return string
	 */
	public function generateSrcsetAttribute(array $srcsetImages): string
	{
		$srcsetString = [];
		foreach ($srcsetImages as $widthDescriptor => $imageCandidate) {
			$srcsetString[] = $this->sanitizeSrcsetUrl($imageCandidate) . ' ' . $widthDescriptor;
		}

		return implode(', ', $srcsetString);
	}

	/**
	 * Normalizes the provided breakpoints configuration
	 *
	 * @param	array	$breakpoints
	 *
	 * @return array
	 */
	public function normalizeImageBreakpoints(array $breakpoints): array
	{
		foreach ($breakpoints as &$breakpoint) {

		if (!is_array($breakpoint)) {
			$breakpoint = [$breakpoint];
		}
			$breakpoint = array_replace($this->breakpointPrototype, $breakpoint);

		}
		ksort($breakpoints);

		return $breakpoints;
	}

	/**
	 * Check if the image has a file format that can't be cropped
	 *
	 * @param	FileInterface $image
	 * @param	array|string	 $ignoreFileExtensions
	 *
	 * @return bool
	 */
	public function hasIgnoredFileExtension(FileInterface $image, $ignoreFileExtensions = 'svg'): bool
	{
		$ignoreFileExtensions = (is_array($ignoreFileExtensions))
			? $ignoreFileExtensions
			: GeneralUtility::trimExplode(',', $ignoreFileExtensions);

		return in_array($image->getProperty('extension'), $ignoreFileExtensions);
	}


	/**
	 * Ensures that the provided url can be used safely in a srcset attribute
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public function sanitizeSrcsetUrl(string $url): string
	{
		return strtr($url, [
			' ' => '%20',
			',' => '%2C'
		]);
	}

}

