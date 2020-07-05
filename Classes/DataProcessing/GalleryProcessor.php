<?php
namespace T3SBS\T3sbootstrap\DataProcessing;

/*
 * This file is part of the TYPO3 CMS project.
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\Exception\ContentRenderingException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * This data processor will calculate rows, columns and dimensions for a gallery
 * based on several settings and can be used for f.i. the CType "textmedia"
 *
 * The output will be an array which contains the rows and columns,
 * including the file references and the calculated width and height for each media element,
 * but also some more information of the gallery, like position, width and counters
 *
 * Example TypoScript configuration:
 *
 * 10 = TYPO3\CMS\Frontend\DataProcessing\GalleryProcessor
 * 10 {
 *	 filesProcessedDataKey = files
 *	 mediaOrientation.field = imageorient
 *	 numberOfColumns.field = imagecols
 *	 equalMediaHeight.field = imageheight
 *	 equalMediaWidth.field = imagewidth
 *	 maxGalleryWidth = {$styles.content.mediatext.maxW}
 *	 as = gallery
 * }
 *
*/

class GalleryProcessor implements DataProcessorInterface
{
	/**
	 * The content object renderer
	 *
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObjectRenderer;

	/**
	 * The processor configuration
	 *
	 * @var array
	 */
	protected $processorConfiguration;

	/**
	 * Matching the tt_content field towards the imageOrient option
	 *
	 * @var array
	 */
	protected $availableGalleryPositions = [
		'horizontal' => [
			'center' => [0, 8],
			'right' => [1, 9, 17, 25],
			'left' => [2, 10, 18, 26]
		],
		'vertical' => [
			'above' => [0, 1, 2],
			'intext' => [17, 18, 25, 26, 66, 77],
			'below' => [8, 9, 10]
		]
	];

	/**
	 * Storage for processed data
	 *
	 * @var array
	 */
	protected $galleryData = [
		'position' => [
			'horizontal' => '',
			'vertical' => '',
			'noWrap' => false
		],
		'width' => 0,
		'count' => [
			'files' => 0,
			'columns' => 0,
			'rows' => 0,
		],
		'border' => [
			'enabled' => false,
			'width' => 0,
			'padding' => 0,
		],
		'rows' => []
	];

	/**
	 * @var int
	 */
	protected $numberOfColumns;

	/**
	 * @var int
	 */
	protected $mediaOrientation;

	/**
	 * @var int
	 */
	protected $maxGalleryWidth;

	/**
	 * @var int
	 */
	protected $equalMediaHeight;

	/**
	 * @var int
	 */
	protected $equalMediaWidth;

	/**
	 * @var string
	 */
	protected $cropVariant = 'default';

	/**
	 * The (filtered) media files to be used in the gallery
	 *
	 * @var FileInterface[]
	 */
	protected $fileObjects = [];

	/**
	 * The calculated dimensions for each media element
	 *
	 * @var array
	 */
	protected $mediaDimensions = [];

	/**
	 * @var string
	 */
	protected $beLayout = 'OneCol';

	/**
	 * @var int
	 */
	protected $colPos;

	/**
	 * @var int
	 */
	protected $parentgridColPos;

	/**
	 * @var boolean
	 */
	protected $minimumWidth;

	/**
	 * @var boolean
	 */
	protected $ratioWithHeight;

	/**
	 * @var string
	 */
	protected $pageContainer;

	/**
	 * @var string
	 */
	protected $parentgridGridelementsBackendLayout;

	/**
	 * @var string
	 */
	protected $rowWidth;

	/**
	 * @var string
	 */
	protected $cType;

	/**
	 * @var string
	 */
	protected $bodytext;

	/**
	 * @var int
	 */
	protected $maxWidthMediaObject;

	/**
	 * @var int
	 */
	protected $maxWidthToast;

	/**
	 * @var boolean
	 */
	protected $disableAutoRow;

	/**
	 * @var array
	 */
	protected $processedData;

	/**
	 * @var string
	 */
	protected $cardWrapper;


	/**
	 * Process data for a gallery, for instance the CType "textmedia"
	 *
	 * @param ContentObjectRenderer $cObj The content object renderer, which contains data of the content element
	 * @param array $contentObjectConfiguration The configuration of Content Object
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
	 * @return array the processed data as key/value store
	 * @throws ContentRenderingException
	 */
	public function process(
		ContentObjectRenderer $cObj,
		array $contentObjectConfiguration,
		array $processorConfiguration,
		array $processedData
	) {

		if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
			return $processedData;
		}

		$this->contentObjectRenderer = $cObj;
		$this->processorConfiguration = $processorConfiguration;
		$this->processedData = $processedData;
		$this->contentObjectConfiguration = $contentObjectConfiguration;

		$filesProcessedDataKey = (string)$cObj->stdWrapValue(
			'filesProcessedDataKey',
			$processorConfiguration,
			'files'
		);
		if (isset($processedData[$filesProcessedDataKey]) && is_array($processedData[$filesProcessedDataKey])) {
			$this->fileObjects = $processedData[$filesProcessedDataKey];

			if ( is_array($this->fileObjects[0]) ) {
				// image gallery
				foreach ( $this->fileObjects as $rows ) {
					foreach ( $rows as $fileObject ) {
						$fileObjects[] = $fileObject;
					}
				}
					$this->fileObjects = $fileObjects;
			}

			$this->galleryData['count']['files'] = count($this->fileObjects);
		} else {
			throw new ContentRenderingException('No files found for key ' . $filesProcessedDataKey . ' in $processedData.', 1436809789);
		}

		if (empty($this->fileObjects)) {
			return $processedData;
		}

		$this->ratioWithHeight = $this->getConfigurationValue('ratioWithHeight');
		$this->cropVariant = $this->getConfigurationValue('cropVariant') ?: 'default';
		$this->equalMediaHeight = (int)$this->getConfigurationValue('equalMediaHeight', 'imageheight');
		$this->equalMediaWidth = (int)$this->getConfigurationValue('equalMediaWidth', 'imagewidth');
		$this->numberOfColumns = (int)$this->getConfigurationValue('numberOfColumns', 'imagecols');
		$this->maxGalleryWidth = (int)$this->getConfigurationValue('maxGalleryWidth') ?: 1110;
		$this->mediaOrientation = (int)$this->getConfigurationValue('mediaOrientation', 'imageorient');
		$this->beLayout = $processedData['be_layout'];
		$this->colPos = (int)$processedData['data']['colPos'];
		$this->parentgridColPos = (int)$processedData['data']['parentgrid_colPos'];
		$this->minimumWidth = $this->getConfigurationValue('minimumWidth');
		$this->parentgridGridelementsBackendLayout = $processedData['data']['parentgrid_tx_gridelements_backend_layout'];
		$this->bodytext = $processedData['data']['bodytext'];
		$this->cType = $processedData['data']['CType'];
		$this->rowWidth = $processedData['data']['tx_t3sbootstrap_inTextImgRowWidth'];
		$this->maxWidthMediaObject = $this->getConfigurationValue('maxWidthMediaObject');
		$this->maxWidthToast = $this->getConfigurationValue('maxWidthToast');
		$this->disableAutoRow = $this->getConfigurationValue('disableAutoRow');

		$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
		$this->parentflexconf = $flexFormService->convertFlexFormContentToArray($this->processedData['data']['parentgrid_tx_t3sbootstrap_flexform']);

		$pageContainer = self::getFrontendController()->page['tx_t3sbootstrap_container'];
		$contentContainer = $processedData['data']['tx_t3sbootstrap_container'];
		if ( $pageContainer ) {
			$this->pageContainer = $pageContainer;
		} elseif ( $contentContainer ) {
			$this->pageContainer = $contentContainer;
		} else {
			$this->pageContainer = '';
		}

		$this->determineGalleryPosition();
		$this->calculateRowsAndColumns();
		$this->determineMaximumGalleryWidth();
		$this->calculateMediaWidthsAndHeights();

		$this->prepareGalleryData();

		$targetFieldName = (string)$cObj->stdWrapValue(
			'as',
			$processorConfiguration,
			'gallery'
		);

		$processedData[$targetFieldName] = $this->galleryData;

		return $processedData;
	}

	/**
	 * Get configuration value from processorConfiguration
	 * with when $dataArrayKey fallback to value from cObj->data array
	 *
	 * @param string $key
	 * @param string|NULL $dataArrayKey
	 * @return string
	 */
	protected function getConfigurationValue($key, $dataArrayKey = null)
	{
		$defaultValue = '';
		if ($dataArrayKey && isset($this->contentObjectRenderer->data[$dataArrayKey])) {
			$defaultValue = $this->contentObjectRenderer->data[$dataArrayKey];
		}
		return $this->contentObjectRenderer->stdWrapValue(
			$key,
			$this->processorConfiguration,
			$defaultValue
		);
	}

	/**
	 * Define the gallery position
	 *
	 * Gallery has a horizontal and a vertical position towards the text
	 * and a possible wrapping of the text around the gallery.
	 */
	protected function determineGalleryPosition()
	{
		foreach ($this->availableGalleryPositions as $positionDirectionKey => $positionDirectionValue) {
			foreach ($positionDirectionValue as $positionKey => $positionArray) {
				if (in_array($this->mediaOrientation, $positionArray, true)) {
					$this->galleryData['position'][$positionDirectionKey] = $positionKey;
				}
			}
		}

		if ($this->mediaOrientation === 25 || $this->mediaOrientation === 26) {
			$this->galleryData['position']['noWrap'] = true;
		}
		if ($this->mediaOrientation === 66 || $this->mediaOrientation === 77) {
			$this->galleryData['position']['alignCenter'] = true;
		}
	}


	/**
	 * Get the gallery width if 'tx_t3sbootstrap_inTextImgRowWidth' is set to 'auto'
	 */
	protected function determineMaximumGalleryWidth()
	{
		if ( $this->rowWidth == 'auto' && $this->disableAutoRow ) {
			$this->rowWidth == 'none';
		}

		if ( $this->rowWidth == 'auto' ) {

			if ( $this->cType == 'textmedia' || $this->cType == 'textpic' || $this->cType == 'image' ) {

				if ( $this->bodytext ) {

					if ( $this->galleryData['position']['vertical'] === 'intext' ) {

						if ( $this->galleryData['count']['columns'] === 1 ) {
							$this->rowWidth = 33;
						} else {
							$this->rowWidth = 50;
						}

					} else {
						// above or below
						if ( $this->mediaOrientation === 0 || $this->mediaOrientation === 8 ) {
							$this->rowWidth = 100;
						} else {
							$this->rowWidth = 66;
						}
					}

				} else {
					$this->rowWidth = 100;
				}

			// Cards inside a card-wrapper
			} elseif ( $this->cType == 't3sbs_card' && $this->processedData['data']['tx_gridelements_container']
					 && $this->processedData['data']['parentgrid_tx_gridelements_backend_layout'] == 'card_wrapper') {

				$children = self::getContentRecord($this->processedData['data']['tx_gridelements_container'], 'tt_content', 'tx_gridelements_container');
				$this->galleryData['count']['columns'] = count($children);
				$this->rowWidth = 100;

				$this->cardWrapper = $this->parentflexconf['card_wrapper'];

				if ($this->parentflexconf['card_wrapper'] == 'deck' || $this->parentflexconf['card_wrapper'] == 'group' ) {

					$x = 0;
					if (is_array($children))
					$x = (int) floor(100 / $this->galleryData['count']['columns']);

					if ( $x == 100 ) {
						$p = 100;
					} elseif ( $x == 50 ) {
						$p = 50;
					} elseif ( $x == 33 ) {
						$p = 33;
					} elseif ( $x == 25 ) {
						$p = 25;
					} elseif ( $x == 20 ) {
						$p = 20;
					} elseif ( $x == 16 ) {
						$p = 16;
					}

					if ( $p != 25 ) {
						// 1% = space between
						$p = $p - 1;
						$block = '.card-deck .card {-ms-flex: 0 0 '. $p .'%; flex: 0 0 '. $p .'%;}';
						$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
						$pageRenderer->addCssInlineBlock('CARD-WRAPPER'.$this->processedData['data']['tx_gridelements_container'],$block);
					}
				} elseif ($this->parentflexconf['card_wrapper'] == 'columns' ) {

					$this->galleryData['count']['columns'] = 4;

				} elseif ($this->parentflexconf['card_wrapper'] == 'slider' ) {

					$this->galleryData['count']['columns'] = 3;

				} elseif ($this->parentflexconf['card_wrapper'] == 'flipper' ) {

					$this->galleryData['count']['columns'] = 4;
				}

			} else {
				$this->rowWidth = 100;
			}
		}
	}


	/**
	 * Calculate the amount of rows and columns
	 */
	protected function calculateRowsAndColumns()
	{
		// If no columns defined, set it to 1
		$columns = max((int)$this->numberOfColumns, 1);

		if ($columns === 88) {
			$columns = 1;
		} else {
			// When more columns than media elements, set the columns to the amount of media elements
			if ($columns > $this->galleryData['count']['files']) {
				$columns = $this->galleryData['count']['files'];
			}
		}

		if ($columns === 0) {
			$columns = 1;
		}

		// Calculate the rows from the amount of files and the columns
		$rows = ceil($this->galleryData['count']['files'] / $columns);

		$this->galleryData['count']['columns'] = $columns;
		$this->galleryData['count']['rows'] = (int)$rows;
	}


	/**
	 * Calculate the width/height of the media elements
	 *
	 * Based on the width of the gallery, defined equal width or height by a user, the spacing between columns and
	 * the use of a border, defined by user, where the border width and padding are taken into account
	 *
	 * File objects MUST already be filtered. They need a height and width to be shown in the gallery
	 */
	protected function calculateMediaWidthsAndHeights()
	{

		if ( $this->equalMediaWidth == 0 && $this->equalMediaHeight == 0 ) {

			$pageContainer = self::getPageContainer($this->pageContainer);
			// if a cookie with the name 'windowWidth' has a value
			$windowWidth = (int) $_COOKIE['windowWidth'];

			$gridelementsBackendLayout = FALSE;
			// nested gridelements
			if ($this->colPos === -1 && $this->parentgridColPos === -1 ) {

				$parent = self::getContentRecord($this->processedData['data']['tx_gridelements_container']);
				$grandParent = self::getContentRecord($parent[0]['tx_gridelements_container']);
				$greatGrandParent = self::getContentRecord($grandParent[0]['tx_gridelements_container']);

				if ( $parent[0]['colPos'] > -1 ) {
					$this->colPos = $parent[0]['colPos'];
				} elseif ( $grandParent[0]['colPos'] > -1 ) {
					$this->colPos = $grandParent[0]['colPos'];
					if ( $grandParent[0]['tx_gridelements_backend_layout'] == 'collapsible_container' ) $gridelementsBackendLayout = 'collapsible_container';
					if ( $grandParent[0]['tx_gridelements_backend_layout'] == 'tabs_container' ) $gridelementsBackendLayout = 'tabs_container';
					if ( $grandParent[0]['tx_gridelements_backend_layout'] == 'background_wrapper' ) $gridelementsBackendLayout = 'background_wrapper';
					if ( $grandParent[0]['tx_gridelements_backend_layout'] == 'container' && !$pageContainer ) {
						$pageContainer = $grandParent[0]['parentgrid_tx_t3sbootstrap_container'];
					}
				} elseif ( $greatGrandParent[0]['colPos'] > -1 ) {
					$this->colPos = $greatGrandParent[0]['colPos'];
					if ( $greatGrandParent[0]['tx_gridelements_backend_layout'] == 'collapsible_container' ) $gridelementsBackendLayout = 'collapsible_container';
					if ( $greatGrandParent[0]['tx_gridelements_backend_layout'] == 'tabs_container' ) $gridelementsBackendLayout = 'tabs_container';
					if ( $greatGrandParent[0]['tx_gridelements_backend_layout'] == 'background_wrapper' ) $gridelementsBackendLayout = 'background_wrapper';
					if ( $greatGrandParent[0]['tx_gridelements_backend_layout'] == 'container' && !$pageContainer ) {
						$pageContainer = $greatGrandParent[0]['parentgrid_tx_t3sbootstrap_container'];
					}
				}
			}


			if ( $pageContainer == 'container-fluid' ) {
				// maxGalleryWidth => styles.content.textmedia.maxW - deafult 2560
				$bsMaxGridWidth = $windowWidth ?: $this->maxGalleryWidth;
			} elseif ( $pageContainer == 'container-fluid px-0' ) {
				$bsMaxGridWidth = $windowWidth ?: $this->maxGalleryWidth;
			} else {
				$bsMaxGridWidth = 1140;
			}

			if ( is_int($this->rowWidth) ) {
				$rowWidth = $this->rowWidth;
			} elseif ( $this->rowWidth != 'none' && is_string($this->rowWidth) ) {
				$rowWidth = (int) end(explode('-', $this->rowWidth));
			} else {
				$rowWidth = 100;
			}

			if ( $this->cType == 't3sbs_gallery' ) {
				$rowWidth = 100;
			}

			if ( $this->colPos == 0
				|| $this->colPos == 1
				|| $this->colPos == 2
				|| ($this->colPos === -1 && $this->parentgridColPos < 3)
			)
			{
				if ( $this->processorConfiguration['overrideSmallColumns'] ) {
					$defaultSmallColumns = $this->processorConfiguration['overrideSmallColumns'];
				} elseif (isset($GLOBALS['TSFE']->pagesTSconfig['TCAdefaults.']['pages.']['tx_t3sbootstrap_smallColumns'])) {
					$defaultSmallColumns = $GLOBALS['TSFE']->pagesTSconfig['TCAdefaults.']['pages.']['tx_t3sbootstrap_smallColumns'];
				} else {
					$defaultSmallColumns = 0;
				}

				$smallColumns = $defaultSmallColumns ?: self::getFrontendController()->page['tx_t3sbootstrap_smallColumns'];

				if ($this->beLayout == 'OneCol') {

					$bsGridWidth = $bsMaxGridWidth;

				} elseif ($this->beLayout == 'ThreeCol') {

					$bsAsideGridWidth = $bsMaxGridWidth / 12 * (int) $smallColumns;
					$bsMainGridWidth = $bsMaxGridWidth - $bsAsideGridWidth * 2;

					// Main
					if ( $this->colPos === 0 || ($this->colPos === -1 && $this->parentgridColPos === 0) ) {
						$bsGridWidth = $bsMainGridWidth;
					// Aside
					} elseif ( $this->colPos === 1 || $this->colPos === 2
					 || ($this->colPos === -1 && $this->parentgridColPos === 1)
					 || ($this->colPos === -1 && $this->parentgridColPos === 2 ) ) {
						$bsGridWidth = $bsAsideGridWidth;
					}

				} else {
					// Two columns
					$bsAsideGridWidth = $bsMaxGridWidth / 12 * (int) $smallColumns;
					$bsMainGridWidth = $bsMaxGridWidth - $bsAsideGridWidth;

					// Main
					if ( $this->colPos === 0 || ($this->colPos === -1 && $this->parentgridColPos === 0) ) {
						$bsGridWidth = $bsMainGridWidth;
					// Aside
					} elseif ( $this->colPos === 1 || $this->colPos === 2
					 || ($this->colPos === -1 && $this->parentgridColPos === 1)
					 || ($this->colPos === -1 && $this->parentgridColPos === 2) ) {
						$bsGridWidth = $bsAsideGridWidth;
					}
				}
			}

			// Jumbotron, footer && expanded content
			if ( $this->colPos == 3
				|| $this->colPos == 4
				|| $this->colPos == 20
				|| $this->colPos == 21
				|| ($this->colPos === -1 && $this->parentgridColPos > 2)
			)
			{
				$bsGridWidth = $bsMaxGridWidth;
			}

			// Modal
			if ( $this->processedData['data']['parentgrid_tx_gridelements_backend_layout'] == 'modal' ) {

				$size = $this->parentflexconf['size'];

				if ( $size == 'modal-lg' ) {
					// 798 - 2 * 16
					$bsGridWidth = 796;
				} elseif ($size == 'modal-sm') {
					// 298 - 2 * 16
					$bsGridWidth = 296;
				} else {
					// Default: 498 - 2 * 16
					$bsGridWidth = 496;
				}
			}

			if ( $gridelementsBackendLayout == 'background_wrapper' ) {
				$bsGridWidth = $bsGridWidth - 36;
			}

			if ( $gridelementsBackendLayout == 'collapsible_container' ) {
				$bsGridWidth = $bsGridWidth - 42;
			}

			// Child of gridelement
			if ( $this->parentgridGridelementsBackendLayout == 'two_columns'
			 || $this->parentgridGridelementsBackendLayout == 'three_columns'
			 || $this->parentgridGridelementsBackendLayout == 'four_columns'
			 || $this->parentgridGridelementsBackendLayout == 'six_columns' ) {

				$bsGridWidth = self::getCalculatedGridWidth($bsGridWidth);
			} else {
				$bsGridWidth = $bsGridWidth - 30;
			}

			$galleryWidth = $bsGridWidth / 100 * $rowWidth;
			$imagePadding = self::getImagePadding();
			$mediaWidth = ($galleryWidth - $imagePadding ) / $this->galleryData['count']['columns'];

		}

		// User entered a predefined width
		if ( $this->equalMediaWidth ) {

			$mediaWidth = self::checkMediaWidth($this->equalMediaWidth);

			// User entered a predefined width & height
			if ( $this->equalMediaHeight ) {

				// Set the corrected dimensions for each media element
				foreach ($this->fileObjects as $key => $fileObject) {

					if ( $this->ratioWithHeight ) {
						$ratio = $this->equalMediaWidth .':'. $this->equalMediaHeight;
						$mediaHeight = '';

					} else {
						$ratio = '';
						$mediaHeight = $this->getCroppedDimensionalProperty($fileObject, 'height')
						 * ($mediaWidth / max($this->getCroppedDimensionalProperty($fileObject, 'width'), 1));
					}

					$this->mediaDimensions[$key] = [
						'width' => floor($mediaWidth),
						'height' => floor($mediaHeight),
						'ratio' => $ratio
					];
				}

			} else {

				// Set the corrected dimensions for each media element
				foreach ($this->fileObjects as $key => $fileObject) {
					$mediaHeight = $this->getCroppedDimensionalProperty($fileObject, 'height')
					 * ($mediaWidth / max($this->getCroppedDimensionalProperty($fileObject, 'width'), 1));

					$this->mediaDimensions[$key] = [
						'width' => floor($mediaWidth),
						'height' => floor($mediaHeight),
						'ratio' => ''
					];
				}
			}

		// User entered a predefined height only
		} elseif ($this->equalMediaHeight) {

			// Set the corrected dimensions for each media element
			foreach ($this->fileObjects as $key => $fileObject) {

				  $mediaHeight = $this->equalMediaHeight;
				  $mediaWidth = $this->getCroppedDimensionalProperty($fileObject, 'width')
				   * ($mediaHeight / max($this->getCroppedDimensionalProperty($fileObject, 'height'), 1));

				$this->mediaDimensions[$key] = [
					'width' => floor($mediaWidth),
					'height' => floor($mediaHeight),
					'ratio' => ''
				];
			}

		// Automatic setting of width and height
		} else {

			$mediaWidth = self::checkMediaWidth($mediaWidth);

			// Set the corrected dimensions for each media element
			foreach ($this->fileObjects as $key => $fileObject) {

				$mediaHeight = $this->getCroppedDimensionalProperty($fileObject, 'height')
				 * ($mediaWidth / max($this->getCroppedDimensionalProperty($fileObject, 'width'), 1));

				$this->mediaDimensions[$key] = [
					'width' => floor($mediaWidth),
					'height' => floor($mediaHeight),
					'ratio' => ''
				];
			}
		}

		$this->galleryData['width'] = floor($mediaWidth);
	}


	/**
	 * When retrieving the height or width for a media file
	 * a possible cropping needs to be taken into account.
	 *
	 * @param FileInterface $fileObject
	 * @param string $dimensionalProperty 'width' or 'height'
	 *
	 * @return int
	 */
	protected function getCroppedDimensionalProperty(FileInterface $fileObject, $dimensionalProperty)
	{
		if (!$fileObject->hasProperty('crop') || empty($fileObject->getProperty('crop'))) {
			return $fileObject->getProperty($dimensionalProperty);
		}

		$croppingConfiguration = $fileObject->getProperty('crop');
		$cropVariantCollection = CropVariantCollection::create((string)$croppingConfiguration);
		return (int) $cropVariantCollection->getCropArea($this->cropVariant)->makeAbsoluteBasedOnFile($fileObject)->asArray()[$dimensionalProperty];
	}


	/**
	 * Prepare the gallery data
	 *
	 * Make an array for rows, columns and configuration
	 */
	protected function prepareGalleryData()
	{
		for ($row = 1; $row <= $this->galleryData['count']['rows']; $row++) {
			for ($column = 1; $column <= $this->galleryData['count']['columns']; $column++) {
				$fileKey = (($row - 1) * $this->galleryData['count']['columns']) + $column - 1;
				$this->galleryData['rows'][$row]['columns'][$column] = [
					'media' => $this->fileObjects[$fileKey] ?? null,
					'dimensions' => [
						'width' => $this->mediaDimensions[$fileKey]['width'] ?? null,
						'height' => $this->mediaDimensions[$fileKey]['height'] ?? null,
						'ratio' => $this->mediaDimensions[$fileKey]['ratio'] ?? null
					]
				];
			}
		}
	}


	/**
	 * Returns the page container
	 *
	 * @param string $pageContainer
	 * @return $container
	 */
	protected function getPageContainer($pageContainer)
	{
		if ( $this->processedData['data']['tx_gridelements_container'] && !$pageContainer ) {
			$parent = self::getContentRecord($this->processedData['data']['tx_gridelements_container']);
			if ( $parent[0]['tx_t3sbootstrap_container'] ) {
				$pageContainer = $parent[0]['tx_t3sbootstrap_container'];
			} else {
				if ( $parent[0]['tx_gridelements_container'] ) {
					$grandParent = self::getContentRecord($parent[0]['tx_gridelements_container']);
					if ( $grandParent[0]['tx_t3sbootstrap_container'] ) {
						$pageContainer = $grandParent[0]['tx_t3sbootstrap_container'];
					}
				}
			}
		}

		// Container if Jumbotron, footer OR expanded content
		if ( $this->colPos == 3
			|| $this->colPos == 4
			|| $this->colPos == 20
			|| $this->colPos == 21
			|| $this->colPos === -1 && $this->parentgridColPos > 2
			|| ($this->colPos === -1 && $this->processedData['data']['parentgrid_tx_gridelements_backend_layout'] == 'background_wrapper')
		)
		{
			$t3sbconfig = self::getContentRecord($this->getConfigurationValue('configuid'), 'tx_t3sbootstrap_domain_model_config');

			$jumbotronContainer = $t3sbconfig[0]['jumbotron_container'];
			$footerContainer = $t3sbconfig[0]['footer_container'];
			$expandedcontentTopContainer = $t3sbconfig[0]['expandedcontent_containertop'];
			$expandedcontentBottomContainer = $t3sbconfig[0]['expandedcontent_containerbottom'];

			switch ($this->colPos) {
				case 3: // jumbotron
					$pageContainer = $jumbotronContainer;
					break;
				case 4: // Footer
					$pageContainer = $footerContainer;
					break;
				case 20: // Expanded content Top Container
					$pageContainer = $expandedcontentTopContainer;
					break;
				case 21: // Expanded content Bottom Container
					$pageContainer = $expandedcontentBottomContainer;
					break;
				case -1: // Expanded content Bottom Container
					if ( $this->parentgridColPos == 3 ) {
						$pageContainer = $jumbotronContainer;
					} elseif ( $this->parentgridColPos == 4 ) {
						$pageContainer = $footerContainer;
					} elseif ( $this->parentgridColPos == 20 ) {
						$pageContainer = $expandedcontentTopContainer;
					} elseif ( $this->parentgridColPos == 21 ) {
						$pageContainer = $expandedcontentBottomContainer;
					}
					break;
			}

			if (!$footerContainer && $t3sbconfig[0]['footer_pid'] && $this->colPos === -1 && $this->processedData['data']['parentgrid_colPos'] == 0
			 && $this->processedData['data']['parentgrid_tx_gridelements_backend_layout'] == 'background_wrapper') {
				$pageContainer = $this->processedData['data']['parentgrid_tx_t3sbootstrap_container'];
			}
		}

		return $pageContainer;
	}


	/**
	 * Returns content record
	 *
	 * @param int $uid
	 * @param string $table
	 * @param string $equal
	 *
	 * @return $result
	 */
	protected function getContentRecord($uid, $table='tt_content', $equal='uid')
	{
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
		$result = $queryBuilder
			 ->select('*')
			 ->from($table)
			 ->where(
			 $queryBuilder->expr()->eq($equal, $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
			 )
			 ->execute()->fetchAll();

		return $result;
	}


	/**
	 * Returns calculated grid width
	 *
	 * @param int $bsGridWidth
	 *
	 * @return $mediaWidth
	 */
	protected function getCalculatedGridWidth($bsGridWidth)
	{
		switch ($this->parentgridGridelementsBackendLayout) {
			case 'two_columns':
				if ( $this->processedData['data']['tx_gridelements_columns'] === 0 ) {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'one');
				} else {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'two');
				}
				break;
			case 'three_columns':
				if ( $this->processedData['data']['tx_gridelements_columns'] === 0 ) {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'one');
				} elseif ( $this->processedData['data']['tx_gridelements_columns'] === 1 ) {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'two');
				} else {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'three');
				}
				break;
			case 'four_columns':
				if ( $this->processedData['data']['tx_gridelements_columns'] === 0 ) {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'one');
				} elseif ( $this->processedData['data']['tx_gridelements_columns'] === 1 ) {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'two');
				} elseif ( $this->processedData['data']['tx_gridelements_columns'] === 2 ) {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'three');
				} else {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'four');
				}
				break;
			case 'six_columns':
				if ( $this->processedData['data']['tx_gridelements_columns'] === 0 ) {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'one');
				} elseif ( $this->processedData['data']['tx_gridelements_columns'] === 1 ) {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'two');
				} elseif ( $this->processedData['data']['tx_gridelements_columns'] === 2 ) {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'three');
				} elseif ( $this->processedData['data']['tx_gridelements_columns'] === 3 ) {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'four');
				} elseif ( $this->processedData['data']['tx_gridelements_columns'] === 4 ) {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'five');
				} else {
					$mediaWidth = self::getGridWidth($bsGridWidth, 'six');
				}
				break;
		}

		return $mediaWidth;
	}


	/**
	 * Returns $ mediaWidth if there is a parent grid element
	 *
	 * @param int $bsGridWidth
	 * @param string $suffix
	 *
	 * @return int $mediaWidth
	 */
	protected function getGridWidth($bsGridWidth, $suffix)
	{

		// # of Bootstrap columns
		$columns = 12;
		// Bootstrap gutter width
		$gutterWidth = 30;

		if ( $this->parentflexconf['noGutters'] ) {
			$gutterWidth = 0;
		}

		if ($this->parentflexconf['md_'.$suffix] != 0) {
			$mediaWidth = $bsGridWidth / $columns * $this->parentflexconf['md_'.$suffix] - $gutterWidth;
		} elseif ($this->parentflexconf['lg_'.$suffix] != 0) {
			$mediaWidth = $bsGridWidth / $columns * $this->parentflexconf['lg_'.$suffix] - $gutterWidth;
		} elseif ($this->parentflexconf['xl_'.$suffix] != 0) {
			$mediaWidth = $bsGridWidth / $columns * $this->parentflexconf['xl_'.$suffix] - $gutterWidth;
		} else {
			$mediaWidth = $bsGridWidth / $columns * ($columns-1) - $gutterWidth;
		}

		return $mediaWidth;
	}


	/**
	 * Returns $imagepadding
	 *
	 * @return int $imagepadding
	 */
	protected function getImagePadding()
	{

		if ( $this->cType == 't3sbs_card' && $this->processedData['data']['tx_gridelements_container']
		 && $this->processedData['data']['parentgrid_tx_gridelements_backend_layout'] == 'card_wrapper') {
			if ( $this->cardWrapper == 'flipper' ) {
				$imagePadding = 30 * ($this->galleryData['count']['columns'] - 1);
			} else {
				$imagePadding = $bsGridWidth / 100 * $this->galleryData['count']['columns'] + $this->galleryData['count']['columns'];
			}
		} else {

			if ( $this->galleryData['position']['noWrap'] ) {
				// beside the text
				if ( $this->galleryData['count']['columns'] == 1 ) {
					$imagePadding = 22;
				} else {
					$imagePadding = 16 * ($this->galleryData['count']['columns']);
				}
			} else {

				if ( $this->galleryData['position']['vertical'] === 'intext' ) {

					if ( $this->galleryData['position']['alignCenter'] ) {

						if ( $this->galleryData['count']['columns'] == 1 ) {
							$imagePadding = 20 * ($this->galleryData['count']['columns']);
						} elseif ( $this->galleryData['count']['columns'] == 2 ) {
							$imagePadding = 16 * ($this->galleryData['count']['columns']);
						} else {
							$imagePadding = 20 * ($this->galleryData['count']['columns']);
						}

					} else {

						if ( $this->galleryData['count']['columns'] == 1 ) {
							$imagePadding = 0;
						} elseif ( $this->galleryData['count']['columns'] == 2 ) {

							$imagePadding = 8 * ($this->galleryData['count']['columns']);

						} else {

							$imagePadding = 12 * ($this->galleryData['count']['columns']);
						}
					}

				} else {

					if ( $this->galleryData['count']['columns'] == 1 ) {
						$imagePadding = 0;
					} elseif ( $this->galleryData['count']['columns'] == 2 ) {

						$imagePadding = 8 * ($this->galleryData['count']['columns']);

					} else {

						$imagePadding = 12 * ($this->galleryData['count']['columns']);
					}
				}
			}
		}

		if ( $this->processedData['data']['parentgrid_tx_gridelements_backend_layout'] == 'modal' ) {
			$imagePadding = 0;
		}

		return $imagePadding;
	}


	/**
	 * Returns $mediaWidth and check some conditions
	 *
	 * @param int $mediaWidth
	 *
	 * @return int $mediaWidth
	 */
	protected function checkMediaWidth($mediaWidth)
	{

		if ( $this->minimumWidth && $mediaWidth < 575  ) {
			// set to 575px and therefore 100% wide on mobile (constant: minimumWidth=1)
			$mediaWidth = 575;
		}

		if ( $this->cType == 't3sbs_mediaobject' && $this->maxWidthMediaObject < $mediaWidth ) {
			$mediaWidth = $this->maxWidthMediaObject;
		}

		if ( $this->cType == 't3sbs_toast' && $this->maxWidthToast < $mediaWidth ) {
			$mediaWidth = $this->maxWidthToast;
		}

		return (int) $mediaWidth;
	}


	/**
	 * Returns $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController()
	{
		return $GLOBALS['TSFE'];
	}


}
