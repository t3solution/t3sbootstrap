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
	 * @var array
	 */
	protected $processedData;

	/**
	 * @var string
	 */
	protected $contentContainer;



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

		$filesProcessedDataKey = (string)$cObj->stdWrapValue(
			'filesProcessedDataKey',
			$processorConfiguration,
			'files'
		);
		if (isset($processedData[$filesProcessedDataKey]) && is_array($processedData[$filesProcessedDataKey])) {
			$this->fileObjects = $processedData[$filesProcessedDataKey];
			$this->galleryData['count']['files'] = count($this->fileObjects);
		} else {
			throw new ContentRenderingException('No files found for key ' . $filesProcessedDataKey . ' in $processedData.', 1436809789);
		}

		$this->numberOfColumns = (int)$this->getConfigurationValue('numberOfColumns', 'imagecols');
		$this->mediaOrientation = (int)$this->getConfigurationValue('mediaOrientation', 'imageorient');
		$this->maxGalleryWidth = (int)$this->getConfigurationValue('maxGalleryWidth') ?: 1140;
		$this->equalMediaHeight = (int)$this->getConfigurationValue('equalMediaHeight', 'imageheight');
		$this->equalMediaWidth = (int)$this->getConfigurationValue('equalMediaWidth', 'imagewidth');
		$this->cropVariant = $this->getConfigurationValue('cropVariant') ?: 'default';
		$this->beLayout = $processedData['be_layout'];
		$this->colPos = (int)$processedData['data']['colPos'];
		$this->parentgridColPos = (int)$processedData['data']['parentgrid_colPos'];
		$this->minimumWidth = $this->getConfigurationValue('minimumWidth');
		$this->ratioWithHeight = $this->getConfigurationValue('ratioWithHeight');
		$this->parentgridGridelementsBackendLayout = $processedData['data']['parentgrid_tx_gridelements_backend_layout'];
		$this->pageContainer = self::getFrontendController()->page['tx_t3sbootstrap_container'] ? TRUE : FALSE;
		$this->contentContainer = $processedData['data']['tx_t3sbootstrap_container'];
		$this->bodytext = $processedData['data']['bodytext'];
		$this->cType = $processedData['data']['CType'];
		$this->rowWidth = $processedData['data']['tx_t3sbootstrap_inTextImgRowWidth'];
		$this->maxWidthMediaObject = $this->getConfigurationValue('maxWidthMediaObject');
		$this->maxWidthToast = $this->getConfigurationValue('maxWidthToast');

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
	}


	/**
	 * Get the gallery width if 'tx_t3sbootstrap_inTextImgRowWidth' is set to 'auto'
	 */
	protected function determineMaximumGalleryWidth()
	{

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

			} else {

				if ( $this->cType == 't3sbs_card' && $this->processedData['data']['tx_gridelements_container']
					 && $this->processedData['data']['parentgrid_tx_gridelements_backend_layout'] == 'card_wrapper') {

					$children = self::getContentRecord($this->processedData['data']['tx_gridelements_container'], 'tx_gridelements_container');

					$x = 0;
					if (is_array($children))
					$x = (int) floor(100 / count($children));

					if ( $x == 100 ) {
						$this->rowWidth = 100;
					} elseif ( $x == 50 ) {
						$this->rowWidth = 50;
					} elseif ( $x == 33 ) {
						$this->rowWidth = 33;
					} elseif ( $x < 33 ) {
						$this->rowWidth = 25;
					}

				} else {
					$this->rowWidth = 100;
				}
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

		if ( $this->processedData['data']['tx_gridelements_container'] && $this->pageContainer == FALSE ) {
			$parent = self::getContentRecord($this->processedData['data']['tx_gridelements_container']);
			if ( $parent[0]['tx_t3sbootstrap_container'] ) {
				$this->pageContainer = TRUE;
			} else {
				if ( $parent[0]['tx_gridelements_container'] ) {
					$grandParent = self::getContentRecord($parent[0]['tx_gridelements_container']);
					if ( $grandParent[0]['tx_t3sbootstrap_container'] ) {
						$this->pageContainer = TRUE;
					}
				}
			}
		} else {

			if ($this->pageContainer == FALSE)
			$this->pageContainer = $this->processedData['data']['tx_t3sbootstrap_container'] ? TRUE : FALSE;
		}

		if ($this->pageContainer == 'container') {
			$bsMaxGridWidth = 1140;
		} else {
			// styles.content.textmedia.maxW
			$bsMaxGridWidth = $this->maxGalleryWidth;
		}

		// nax media width
		$mediaWidth = $bsMaxGridWidth;

		if ( $this->rowWidth != 'none' ) {
			$rowWidth = (int) end(explode('-', $this->rowWidth));
		} else {
			$rowWidth = 100;
		}

		// calculate the default padding
		$padding = self::getDefaultPadding($rowWidth);

		if ( $this->colPos == 0 || $this->colPos == 1 || $this->colPos == 2	|| $this->colPos === -1 ) {

			if ($this->beLayout == 'OneCol') {
				$galleryWidth = ($bsMaxGridWidth * $rowWidth / 100 - $padding) - ($this->galleryData['count']['columns']-1) * 16;
				$mediaWidth = $galleryWidth / $this->galleryData['count']['columns'];

			} elseif ($this->beLayout == 'ThreeCol') {

				$bsAsideGridWidth = $bsMaxGridWidth / 12 * (int) self::getFrontendController()->page['tx_t3sbootstrap_smallColumns'];
				$bsMainGridWidth = $bsMaxGridWidth - $bsAsideGridWidth * 2;

				// Main
				if ( $this->colPos === 0 || ($this->colPos === -1 && $this->parentgridColPos === 0) ) {
					// 2x 8px (.5rem) = 16 px
					$galleryWidth = ($bsMainGridWidth * $rowWidth / 100 - $padding) - ($this->galleryData['count']['columns']-1) * 16;
					$mediaWidth = $galleryWidth / $this->galleryData['count']['columns'];
				// Aside
				} elseif ( $this->colPos === 1 || $this->colPos === 2 || ($this->colPos === -1 && $this->parentgridColPos === 1)
					 || ($this->colPos === -1 && $this->parentgridColPos === 2 ) ) {
					$galleryWidth = ($bsAsideGridWidth * $rowWidth / 100 - $padding) - ($this->galleryData['count']['columns']-1) * 16;
					$mediaWidth = $galleryWidth / $this->galleryData['count']['columns'];
				}

			} else {

				$bsAsideGridWidth = $bsMaxGridWidth / 12 * (int) self::getFrontendController()->page['tx_t3sbootstrap_smallColumns'];
				$bsMainGridWidth = $bsMaxGridWidth - $bsAsideGridWidth;

				// Main
				if ( $this->colPos === 0 || ($this->colPos === -1 && $this->parentgridColPos === 0) ) {
					// 2x 8px (.5rem) = 16 px
					$galleryWidth = ($bsMainGridWidth * $rowWidth / 100 - $padding) - ($this->galleryData['count']['columns']-1) * 16;
					$mediaWidth = $galleryWidth / $this->galleryData['count']['columns'];
				// Aside
				} elseif ( $this->colPos === 1 || $this->colPos === 2 ||
					($this->colPos === -1 && $this->parentgridColPos === 1) || ($this->parentgridColPos === 2 && $this->colPos === -1) ) {
					$galleryWidth = ($bsAsideGridWidth * $rowWidth / 100 - $padding) - ($this->galleryData['count']['columns']-1) * 16;
					$mediaWidth = $galleryWidth / $this->galleryData['count']['columns'];
				}
			}
		}

		// Jumbotron, footer && expanded content
		if ( $this->colPos == 3 || $this->colPos == 4 || $this->colPos == 20 || $this->colPos == 21 || $this->colPos === -1 )
		{
			$galleryWidth = ($bsMaxGridWidth * $rowWidth / 100 - $padding) - ($this->galleryData['count']['columns']-1) * 16;
			$mediaWidth = $galleryWidth / $this->galleryData['count']['columns'];
		}

		// Child of gridelement
		if ($this->parentgridGridelementsBackendLayout) {

			switch ($this->parentgridGridelementsBackendLayout) {
				case 'two_columns':
					$mediaWidth = $mediaWidth / 2 - 5;
					break;
				case 'three_columns':
					$mediaWidth = $mediaWidth / 3 - 20;
					break;
				case 'four_columns':
					$mediaWidth = $mediaWidth / 4 - 25;
					break;
				case 'six_columns':
					$mediaWidth = $mediaWidth / 6 - 25;
					break;
			}
		}

		// User entered a predefined width
		if ($this->equalMediaWidth) {

			if ( $this->equalMediaWidth < $mediaWidth ) {
				$mediaWidth = $this->equalMediaWidth;
			}

			if ( $this->rowWidth == 'none' ) {
				$mediaWidth = $this->equalMediaWidth;
			}

			if ( $this->minimumWidth && $mediaWidth < 575  ) {
				// set to 575px and therefore 100% wide on mobile (constant: minimumWidth=0)
				$mediaWidth = 575;
			}

			// User entered a predefined width & height
			if ($this->equalMediaHeight) {

				// Set the corrected dimensions for each media element
				foreach ($this->fileObjects as $key => $fileObject) {

					if ( $this->ratioWithHeight ) {
						$ratio = $this->equalMediaWidth .':'. $this->equalMediaHeight;
						$mediaHeight = '';

					} else {
						$ratio = '';
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

			if ( $this->minimumWidth && $mediaWidth < 575  ) {
				// set to 575px and therefore 100% wide on mobile (constant: minimumWidth=1)
				$mediaWidth = 575;
			}

			if ( $this->cType == 't3sbs_mediaobject' ) {
				$mediaWidth = $this->maxWidthMediaObject;
			}

			if ( $this->cType == 't3sbs_toast' ) {
				$mediaWidth = $this->maxWidthToast;
			}

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
	 * Returns the default padding
	 *
	 * @param int $rowWidth
	 *
	 * @return int $padding
	 */
	protected function getDefaultPadding($rowWidth)
	{

		if ( $this->galleryData['position']['noWrap'] === TRUE ) {

			$padding = 30;

		} else {

			if ( $this->galleryData['position']['vertical'] == 'above' || $this->galleryData['position']['vertical'] == 'below' ) {
				if ( $this->galleryData['position']['horizontal'] === 'center' ) {
					$padding = 30;
				} else {
					$padding = 20;
				}
				if ($rowWidth == 25) $padding = 8;
				if ($rowWidth == 33) $padding = 12;
				if ($rowWidth == 50) $padding = 16;
				if ($rowWidth == 66) $padding = 20;
				if ($rowWidth == 75) $padding = 24;
				if ($rowWidth == 100) $padding = 30;
			} else {
				if ( $this->mediaOrientation > 60 ) {
					$padding = 30;
				} else {
					$padding = 10;
				}
			}
		}

		return $padding;
	}

	/**
	 * Returns
	 *
	 * @return
	 */
	protected function getContentRecord($uid, $equal='uid')
	{
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
		$result = $queryBuilder
				 ->select('*')
				 ->from('tt_content')
				 ->where(
				 $queryBuilder->expr()->eq($equal, $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
				 )
				 ->execute()->fetchAll();

		return $result;
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
