<?php

declare(strict_types=0);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\Exception\ContentRenderingException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class GalleryProcessor implements DataProcessorInterface
{
    public const bsMaxGridWidth = 1320;
    public const maxGalleryWidth = 1296;
    public const minimumWidth = 575;
    public const gridColumns = 12;
    public const gridGutterWidth = 24; // default 1.5rem => 24px

    protected $contentObjectRenderer;
    protected $contentObjectConfiguration;
    protected $processorConfiguration;
    protected $numberOfColumns;
    protected $mediaOrientation;
    protected $maxGalleryWidth;
    protected $equalMediaHeight;
    protected $equalMediaWidth;
    protected $cropVariant = 'default';
    protected $fileObjects = [];
    protected $mediaDimensions = [];
    protected $beLayout = 'OneCol';
    protected $colPos;
    protected $parentgridColPos;
    protected $minimumWidth;
    protected $ratioWithHeight;
    protected $pageContainer;
    protected $rowWidth;
    protected $columns;
    protected $cType;
    protected $bodytext;
    protected $maxWidthMediaObject;
    protected $maxWidthToast;
    protected $disableAutoRow;
    protected $parentflexconf;
    protected $processedData;
    protected $processedParentData;
    protected $cardWrapper;
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
    ): array {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $this->contentObjectRenderer = $cObj;
        $this->processorConfiguration = $processorConfiguration;
        $this->processedData = $processedData;
        $this->contentObjectConfiguration = $contentObjectConfiguration;

        if (!empty($this->processedData['data']['tx_container_parent'])) {
            $this->processedParentData = self::getContentRecord($this->processedData['data']['tx_container_parent']);
        } else {
            $this->processedParentData = [];
        }

        $filesProcessedDataKey = (string)$cObj->stdWrapValue(
            'filesProcessedDataKey',
            $processorConfiguration,
            'files'
        );

        if (isset($this->processedData[$filesProcessedDataKey]) && is_array($this->processedData[$filesProcessedDataKey])) {
            $this->fileObjects = $this->processedData[$filesProcessedDataKey];

            if (!empty($this->fileObjects[0])) {
                $fileObjects = [];
                // image gallery
                foreach ($this->fileObjects as $fileObject) {
                    $fileObjects[] = $fileObject;
                }
                $this->fileObjects = $fileObjects;
            }
            $this->galleryData['count']['files'] = count($this->fileObjects);
        } else {
            throw new ContentRenderingException('No files found for key ' . $filesProcessedDataKey . ' in $processedData.', 1436809789);
        }

        if (empty($this->fileObjects)) {
            return $this->processedData;
        }

        $this->ratioWithHeight = $this->getConfigurationValue('ratioWithHeight');
        $this->cropVariant = $this->getConfigurationValue('cropVariant') ?: 'default';
        $this->equalMediaHeight = (int)$this->getConfigurationValue('equalMediaHeight', 'imageheight');
        $this->equalMediaWidth = (int)$this->getConfigurationValue('equalMediaWidth', 'imagewidth');
        $this->numberOfColumns = (int)$this->getConfigurationValue('numberOfColumns', 'imagecols');
        $this->maxGalleryWidth = (int)$this->getConfigurationValue('maxGalleryWidth') ?: self::maxGalleryWidth;
        $this->mediaOrientation = (int)$this->getConfigurationValue('mediaOrientation', 'imageorient');
        $this->beLayout = $this->processedData['be_layout'];
        $this->colPos = (int)$this->processedData['data']['colPos'];
        $this->minimumWidth = $this->getConfigurationValue('minimumWidth');
        $this->bodytext = $this->processedData['data']['bodytext'];
        $this->cType = $this->processedData['data']['CType'];
        $this->rowWidth = $this->processedData['data']['tx_t3sbootstrap_inTextImgRowWidth'];
        $this->maxWidthMediaObject = $this->getConfigurationValue('maxWidthMediaObject');
        $this->maxWidthToast = $this->getConfigurationValue('maxWidthToast');
        $this->disableAutoRow = $this->getConfigurationValue('disableAutoRow');

        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $this->parentflexconf = !empty($this->processedParentData['tx_t3sbootstrap_flexform'])
         ? $flexFormService->convertFlexFormContentToArray($this->processedParentData['tx_t3sbootstrap_flexform']) : [];

        $pageContainer = self::getFrontendController()->page['tx_t3sbootstrap_container'];
        $contentContainer = $this->processedData['data']['tx_t3sbootstrap_container'];
        if ($pageContainer) {
            $this->pageContainer = $pageContainer;
        } elseif ($contentContainer) {
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

        $this->processedData[$targetFieldName] = $this->galleryData;

        return $this->processedData;
    }

    /**
     * Get configuration value from processorConfiguration
     * with when $dataArrayKey fallback to value from cObj->data array
     *
     * @param string $key
     * @param string|NULL $dataArrayKey
     * @return string
     */
    protected function getConfigurationValue(string $key, string|null $dataArrayKey = null): string
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
        $this->galleryData['position']['alignCenter'] = false;
        if ($this->mediaOrientation === 66 || $this->mediaOrientation === 77) {
            $this->galleryData['position']['alignCenter'] = true;
        }
    }


    /**
     * Get the gallery width if 'tx_t3sbootstrap_inTextImgRowWidth' is set to 'auto'
     */
    protected function determineMaximumGalleryWidth(): void
    {
        if ($this->rowWidth == 'auto' && $this->disableAutoRow) {
            $this->rowWidth = 'none';
        }

        if ($this->rowWidth == 'auto') {
            if ($this->cType == 'textmedia' || $this->cType == 'textpic' || $this->cType == 'image') {
                if ($this->bodytext) {
                    if ($this->galleryData['position']['vertical'] === 'intext') {
                        if ($this->galleryData['count']['columns'] === 1) {
                            $this->rowWidth = 33;
                        } else {
                            $this->rowWidth = 50;
                        }
                    } else {
                        // above or below
                        $this->rowWidth = 66;
                    }
                    if ($this->galleryData['position']['vertical'] == 'above' || $this->galleryData['position']['vertical'] == 'below') {
                        $this->rowWidth = 100;
                    }
                } else {
                    $this->rowWidth = 100;
                }

                // Cards inside a card-wrapper
            } elseif ($this->cType == 't3sbs_card' && $this->processedData['data']['tx_container_parent']
             && $this->processedParentData['CType'] == 'card_wrapper') {
                $this->rowWidth = 100;
            } else {
                $this->rowWidth = 100;
            }
        }
    }


    /**
     * Calculate the amount of rows and columns
     */
    protected function calculateRowsAndColumns(): void
    {
        // If no columns defined, set it to 1
        $columns = max((int)$this->numberOfColumns, 1);

        if ($columns === 88) {
            $columns = 1;
        } else {
            // When more columns than media elements, set the columns to the amount of media elements
            if ($columns > $this->galleryData['count']['files']) {
                if ($this->processedData['data']['CType'] !== 't3sbs_gallery') {
                    $columns = $this->galleryData['count']['files'];
                }
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
        if ($this->equalMediaWidth == 0 && $this->equalMediaHeight == 0) {

            // container
            $pageContainer = self::getPageContainer($this->pageContainer);
            if (str_contains($pageContainer, 'container-fluid')) {
                $bsMaxGridWidth = !empty($_COOKIE['viewportWidth']) ? (int)$_COOKIE['viewportWidth'] : 1920;
                $bsMaxGridWidth = $bsMaxGridWidth - self::gridGutterWidth;
            } else {
                $bsMaxGridWidth = self::maxGalleryWidth;
            }

            // row width
            if (is_int($this->rowWidth)) {
                $rowWidth = $this->rowWidth;
            } elseif ($this->rowWidth != 'none' && is_string($this->rowWidth)) {
                $rowWidth = (int) end(explode('-', $this->rowWidth));
            } else {
                $rowWidth = 100;
            }
            if ($this->cType == 't3sbs_gallery') {
                $rowWidth = 100;
            }

            if ($this->colPos == 0
                || $this->colPos == 1
                || $this->colPos == 2
                || ($this->colPos > 199 && $this->processedParentData['colPos'] < 3)
            ) {
                if ($this->processorConfiguration['overrideSmallColumns']) {
                    $defaultSmallColumns = $this->processorConfiguration['overrideSmallColumns'];
                } else {
                    $defaultSmallColumns = 0;
                }
                $smallColumns = $defaultSmallColumns ?: self::getFrontendController()->page['tx_t3sbootstrap_smallColumns'];

                if ($this->beLayout == 'OneCol') {
                    $bsGridWidth = $bsMaxGridWidth;
                } elseif ($this->beLayout == 'ThreeCol') {

                    // Three columns
                    $bsMaxGridWidth = $bsMaxGridWidth + self::gridGutterWidth;
                    $bsAsideGridWidth = $bsMaxGridWidth / self::gridColumns * (int) $smallColumns;
                    $bsMainGridWidth = $bsMaxGridWidth - $bsAsideGridWidth * 2;

                    // Main
                    if ($this->colPos === 0 || ($this->colPos > 199 && $this->processedParentData['colPos'] === 0)) {
                        $bsGridWidth = $bsMainGridWidth;
                    // Aside
                    } elseif ($this->colPos === 1 || $this->colPos === 2
                     || ($this->colPos > 199 && $this->processedParentData['colPos'] === 1)
                     || ($this->colPos > 199 && $this->processedParentData['colPos'] === 2)) {
                        $bsGridWidth = $bsAsideGridWidth;
                    }
                } else {

                    // Two columns
                    $bsMaxGridWidth = $bsMaxGridWidth + self::gridGutterWidth;
                    $bsAsideGridWidth = $bsMaxGridWidth / self::gridColumns * (int) $smallColumns;
                    $bsMainGridWidth = $bsMaxGridWidth - $bsAsideGridWidth;

                    // Main
                    if ($this->colPos === 0 || ($this->colPos > 199 && $this->processedParentData['colPos'] === 0)) {
                        $bsGridWidth = $bsMainGridWidth;
                    // Aside
                    } elseif ($this->colPos === 1 || $this->colPos === 2
                     || ($this->colPos > 199 && $this->processedParentData['colPos'] === 1)
                     || ($this->colPos > 199 && $this->processedParentData['colPos'] === 2)) {
                        $bsGridWidth = $bsAsideGridWidth;
                    }
                }
            }

            // Jumbotron, footer && expanded content
            if ($this->colPos == 3
                || $this->colPos == 4
                || $this->colPos == 20
                || $this->colPos == 21
                || (($this->colPos > 199) && ($this->processedParentData['colPos'] > 2))
            ) {
                $bsGridWidth = $bsMaxGridWidth;
            }

            // Modal - INFO: https://getbootstrap.com/docs/5.3/components/modal/#optional-sizes
            if (isset($this->processedParentData['CType']) && $this->processedParentData['CType'] == 'modal') {
                $size = $this->parentflexconf['size'];

                if ($size == 'modal-fullscreen') {
                    $bsGridWidth = $bsMaxGridWidth;
                } elseif ($size == 'modal-xl') {
                    $bsGridWidth = 1140;
                } elseif ($size == 'modal-lg') {
                    $bsGridWidth = 800;
                } elseif ($size == 'modal-sm') {
                    $bsGridWidth = 300;
                } else {
                    $bsGridWidth = 500;
                }
            }

            if (isset($this->processedParentData['CType']) && $this->processedParentData['CType'] == 'collapsible_accordion') {
                $bsGridWidth = $bsGridWidth - self::gridGutterWidth - 17;
            }

            // Child of grid container
            if (!empty($this->processedParentData['CType']) && ($this->processedParentData['CType'] == 'two_columns'
             || $this->processedParentData['CType'] == 'three_columns'
             || $this->processedParentData['CType'] == 'four_columns'
             || $this->processedParentData['CType'] == 'six_columns')) {
                $bsGridWidth = $bsGridWidth + self::gridGutterWidth;
                $bsGridWidth = self::getCalculatedGridWidth($bsGridWidth);
            } else {
                if ($this->beLayout == 'OneCol') {
                    $bsGridWidth = $bsGridWidth + self::gridGutterWidth;
                }
            }

            $rowWidth = $rowWidth ? $rowWidth : 100;

            $galleryWidth = $bsGridWidth / 100 * $rowWidth;

            // Card Wrapper
            if ($this->cType == 't3sbs_card' && $this->processedData['data']['tx_container_parent']
                         && $this->processedParentData['CType'] == 'card_wrapper') {
                $galleryWidth = $galleryWidth - self::gridGutterWidth;
                if ($this->parentflexconf['card_wrapper'] === 'group' || $this->parentflexconf['card_wrapper'] === 'columns') {
                    $this->processedData['data']['tx_t3sbootstrap_gutters'] = '';
                    $this->galleryData['count']['columns'] = -1;
                    // Masonry (columns)
                    if ($this->parentflexconf['card_wrapper'] === 'columns' && str_contains($this->parentflexconf['colclass'], 'col-lg-')) {
                        foreach (explode(' ', $this->parentflexconf['colclass']) as $class) {
                            if (str_contains($class, 'col-lg-')) {
                                $countChildren = 12 / (int)end(explode('-', $class));
                            }
                        }
                        $galleryWidth = $galleryWidth + self::gridGutterWidth - self::gridGutterWidth * $countChildren;
                    } else {
                        // Group
                        $countChildren = self::countContentRecord($this->processedData['data']['tx_container_parent'], 'tt_content', 'tx_container_parent');
                    }
                    $galleryWidth = $galleryWidth / $countChildren;
                } elseif ($this->parentflexconf['card_wrapper'] === 'slider') {
                    // Slider
                    $this->galleryData['count']['columns'] = -1;
                    $this->processedData['data']['tx_t3sbootstrap_gutters'] = '';
                } elseif ($this->parentflexconf['card_wrapper'] === 'flipper') {
                    // Flipper
                    $this->galleryData['count']['columns'] = -1;
                    $this->processedData['data']['tx_t3sbootstrap_gutters'] = '';
                } else {
                    $gutter = !empty($this->parentflexconf['gutter']) ? $this->parentflexconf['gutter'] : 0;
                    $visibleCards = !empty($this->parentflexconf['visibleCards']) ? $this->parentflexconf['visibleCards'] : 1;
                    $this->processedData['data']['tx_t3sbootstrap_gutters'] = 'gx-'.$gutter;
                    $this->galleryData['count']['columns'] = $visibleCards;
                }
            }
        }



        // User entered a predefined width
        if ($this->equalMediaWidth) {
            $mediaWidth = self::checkMediaWidth($this->equalMediaWidth);

            // User entered a predefined width & height
            if ($this->equalMediaHeight) {
                // Set the corrected dimensions for each media element
                foreach ($this->fileObjects as $key => $fileObject) {
                    if ($this->ratioWithHeight) {
                        $ratio = $this->equalMediaWidth .':'. $this->equalMediaHeight;
                        $mediaHeight = '';
                    } else {
                        $ratio = '';
                        if ($fileObject instanceof \TYPO3\CMS\Core\Resource\FileReference) {
                            $mediaHeight = $this->getCroppedDimensionalProperty($fileObject, 'height')
                             * ($mediaWidth / max($this->getCroppedDimensionalProperty($fileObject, 'width'), 1));
                        }
                    }

                    $mediaHeight = !empty($mediaHeight) ? floor($mediaHeight) : '';
                    $this->mediaDimensions[$key] = [
                        'width' => floor($mediaWidth),
                        'height' => $mediaHeight,
                        'ratio' => $ratio
                    ];
                }
            } else {
                // Set the corrected dimensions for each media element
                foreach ($this->fileObjects as $key => $fileObject) {
                    if (is_array($fileObject)) {
                        foreach ($fileObject as $fO) {
                            $fileObject = $fO;
                        }
                    }
                    $mediaHeight = $this->getCroppedDimensionalProperty($fileObject, 'height')
                     * ($mediaWidth / max($this->getCroppedDimensionalProperty($fileObject, 'width'), 1));

                    $mediaHeight = !empty($mediaHeight) ? floor($mediaHeight) : '';
                    $this->mediaDimensions[$key] = [
                        'width' => floor($mediaWidth),
                        'height' => $mediaHeight,
                        'ratio' => ''
                    ];
                }
            }

            // User entered a predefined height only
        } elseif ($this->equalMediaHeight) {

            // Set the corrected dimensions for each media element
            foreach ($this->fileObjects as $key => $fileObject) {
                $mediaHeight = $this->equalMediaHeight;
                if (is_array($fileObject)) {
                    $fileObject = $fileObject[0];
                }
                if ($fileObject instanceof \TYPO3\CMS\Core\Resource\FileReference) {
                    $mediaWidth = $this->getCroppedDimensionalProperty($fileObject, 'width')
                     * ($mediaHeight / max($this->getCroppedDimensionalProperty($fileObject, 'height'), 1));
                }
                $mediaHeight = !empty($mediaHeight) ? floor($mediaHeight) : '';
                $this->mediaDimensions[$key] = [
                    'width' => floor($mediaWidth),
                    'height' => $mediaHeight,
                    'ratio' => ''
                ];
            }

            // Automatic setting of width and height
        } else {
            $gutterWidth = 0;
            $ratio = 0;

            if (!empty($this->processedData['data']['tx_t3sbootstrap_gutters'])) {
                // margin rem in px (base 16px)
                $gx = [ 'gx-0' => 0, 'gx-1' => 4, 'gx-2' => 8, 'gx-3' => 16, 'gx-4' => 24, 'gx-5' => 48 ];
                $gutterWidth = $gx[$this->processedData['data']['tx_t3sbootstrap_gutters']];
                if (!empty($this->parentflexconf['noGutters'])) {
                    $gutterWidth = 0;
                }
            }


            if ($this->processedData['data']['CType'] === 't3sbs_gallery') {
                $galleryWidth = $galleryWidth - self::gridGutterWidth + $gutterWidth;
                $gutterWidth = $gutterWidth * $this->galleryData['count']['columns'];
                $mediaWidth = ceil(($galleryWidth - $gutterWidth) / $this->galleryData['count']['columns']);
            } else {
                if ($this->galleryData['count']['columns'] > 1) {
                    if ($this->galleryData['position']['noWrap']) {
                        # imageorient 25 & 26
                        if ($this->processedData['data']['tx_t3sbootstrap_gutters'] ==  'gx-0') {
                            $galleryWidth = $galleryWidth - self::gridGutterWidth;
                        } else {
                            $galleryWidth = $galleryWidth - $gutterWidth * $this->galleryData['count']['columns'];
                        }
                        $mediaWidth = ceil($galleryWidth / $this->galleryData['count']['columns']);
                    } else {
                        if ($this->galleryData['position']['alignCenter']) {
                            # imageorient 66 & 77
                            if ($this->processedData['data']['tx_t3sbootstrap_gutters'] ==	'gx-0') {
                                $galleryWidth = $galleryWidth - self::gridGutterWidth;
                            } else {
                                $galleryWidth = $galleryWidth + self::gridGutterWidth;
                            }
                            $gutterWidth = $gutterWidth * $this->galleryData['count']['columns'];
                            $mediaWidth = ceil(($galleryWidth - $gutterWidth) / $this->galleryData['count']['columns']);
                        } else {
                            if ($this->galleryData['position']['vertical'] === 'above' || $this->galleryData['position']['vertical'] === 'below') {
                                # imageorient 0 - 10
                                $galleryWidth = $galleryWidth - self::gridGutterWidth + $gutterWidth;
                                $gutterWidth = $gutterWidth * $this->galleryData['count']['columns'];
                                $mediaWidth = ceil(($galleryWidth - $gutterWidth) / $this->galleryData['count']['columns']);
                            } else {
                                # imageorient 17 & 18
                                if ($this->processedData['data']['tx_t3sbootstrap_gutters'] ==	 'gx-0') {
                                    $galleryWidth = $galleryWidth - self::gridGutterWidth / 2;
                                    $mediaWidth = ceil($galleryWidth / $this->galleryData['count']['columns']);
                                } else {
                                    $galleryWidth = $galleryWidth - self::gridGutterWidth / 2 + $gutterWidth;
                                    $gutterWidth = $gutterWidth * $this->galleryData['count']['columns'];
                                    $mediaWidth = ceil(($galleryWidth - $gutterWidth) / $this->galleryData['count']['columns']);
                                }
                            }
                        }
                    }
                } else {
                    if ($this->galleryData['count']['columns'] === -1 && $this->parentflexconf['card_wrapper'] !== 'slider') {
                        $mediaWidth = $galleryWidth;
                    } elseif ($this->mediaOrientation === 17 || $this->mediaOrientation === 18) {
                        // workaround
                        $mediaWidth = $galleryWidth - 7;
                    } elseif (!empty($this->parentflexconf) && isset($this->parentflexconf['card_wrapper']) && $this->parentflexconf['card_wrapper'] === 'slider') {
                        $mediaWidth = ($galleryWidth - (int)$this->parentflexconf['spaceBetween']) / (int)$this->parentflexconf['breakpoints992'];
                        $ratio = $this->parentflexconf['ratio'];
                    } else {
                        $mediaWidth = $galleryWidth - self::gridGutterWidth;
                    }
                }
            }

            $mediaWidth = self::checkMediaWidth($mediaWidth);

            // Set the corrected dimensions for each media element
            foreach ($this->fileObjects as $key => $fileObject) {
                if (is_array($fileObject)) {
                    $fileObject = $fileObject[0];
                }
                if ($fileObject instanceof \TYPO3\CMS\Core\Resource\FileReference) {
                    $mediaHeight = $this->getCroppedDimensionalProperty($fileObject, 'height')
                     * ($mediaWidth / max($this->getCroppedDimensionalProperty($fileObject, 'width'), 1));
                }
                $mediaHeight = !empty($mediaHeight) ? floor($mediaHeight) : '';
                $this->mediaDimensions[$key] = [
                    'width' => floor($mediaWidth),
                    'height' => $mediaHeight,
                    'ratio' => $ratio
                ];
            }
        }

        $this->galleryData['width'] = (int) ceil($mediaWidth);
    }


    /**
     * When retrieving the height or width for a media file
     * a possible cropping needs to be taken into account.
     */
    protected function getCroppedDimensionalProperty(FileInterface $fileObject, string $dimensionalProperty): int|array
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
    protected function prepareGalleryData(): void
    {
        for ($row = 1; $row <= $this->galleryData['count']['rows']; $row++) {
            $this->galleryData['count']['columns'] = $this->galleryData['count']['columns'] === -1 ? 3 : $this->galleryData['count']['columns'];
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
     * @return string
     */
    protected function getPageContainer($pageContainer): string
    {
        if ($this->processedData['data']['tx_container_parent'] && !$pageContainer) {
            if ($this->processedParentData['tx_t3sbootstrap_container']) {
                $pageContainer = $this->processedParentData['tx_t3sbootstrap_container'];
            } else {
                if ($this->processedParentData['tx_container_parent']) {
                    $grandParent = self::getContentRecord($this->processedParentData['tx_container_parent']);
                    if ($grandParent['tx_t3sbootstrap_container']) {
                        $pageContainer = $grandParent['tx_t3sbootstrap_container'];
                    }
                }
            }
        }

        // Container if Jumbotron, footer OR expanded content
        if ($this->colPos == 3
            || $this->colPos == 4
            || $this->colPos == 20
            || $this->colPos == 21
            || ($this->colPos > 199 && $this->processedParentData['colPos'] > 2)
            || ($this->colPos > 199 && $this->processedData['data']['CType'] == 'background_wrapper')
        ) {
            $t3sbconfig = self::getContentRecord((int)$this->getConfigurationValue('configuid'), 'tx_t3sbootstrap_domain_model_config');

            $jumbotronContainer = $t3sbconfig['jumbotron_container'];
            $footerContainer = $t3sbconfig['footer_container'];
            $expandedcontentTopContainer = $t3sbconfig['expandedcontent_containertop'];
            $expandedcontentBottomContainer = $t3sbconfig['expandedcontent_containerbottom'];

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
                default:
                    if ($this->colPos > 199) {
                        if ($this->processedParentData['colPos'] == 3) {
                            $pageContainer = $jumbotronContainer;
                        } elseif ($this->processedParentData['colPos'] == 4) {
                            $pageContainer = $footerContainer;
                        } elseif ($this->processedParentData['colPos'] == 20) {
                            $pageContainer = $expandedcontentTopContainer;
                        } elseif ($this->processedParentData['colPos'] == 21) {
                            $pageContainer = $expandedcontentBottomContainer;
                        }
                    }
                    break;
            }

            if (!$footerContainer && $t3sbconfig['footer_pid'] && $this->colPos > 199 && $this->processedParentData['colPos'] == 0
             && $this->processedData['data']['CType'] == 'background_wrapper') {
                $pageContainer = $this->processedParentData['data']['tx_t3sbootstrap_container'];
            }
        }

        return !empty($this->processorConfiguration['overrideContainer']) ? $this->processorConfiguration['overrideContainer'] : $pageContainer;
    }


    /**
     * Returns content record
     *
     * @param int $uid
     * @param string $table
     * @param string $equal
     *
     * @return array $result
     */
    protected function getContentRecord($uid, $table='tt_content', $equal='uid'): array
    {
        return BackendUtility::getRecord($table, $uid, '*');
    }


    /**
     * Returns content record
     *
     * @param int $uid
     * @param string $table
     * @param string $equal
     *
     * @return int $result
     */
    protected function countContentRecord($uid, $table='tt_content', $equal='uid'): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $result = $queryBuilder
             ->count('uid')
             ->from($table)
             ->where(
                 $queryBuilder->expr()->eq($equal, $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
             )
             ->executeQuery()->fetchOne();

        return $result;
    }


    /**
     * Returns calculated grid width
     *
     * @param int $bsGridWidth
     *
     * @return int $gridWidth
     */
    protected function getCalculatedGridWidth($bsGridWidth): int
    {
        switch ($this->processedParentData['CType']) {
            case 'two_columns':
                if ($this->processedData['data']['colPos'] === 221) {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'one');
                } else {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'two');
                }
                break;
            case 'three_columns':
                if ($this->processedData['data']['colPos'] === 231) {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'one');
                } elseif ($this->processedData['data']['colPos'] === 232) {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'two');
                } else {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'three');
                }
                break;
            case 'four_columns':
                if ($this->processedData['data']['colPos'] === 241) {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'one');
                } elseif ($this->processedData['data']['colPos'] === 242) {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'two');
                } elseif ($this->processedData['data']['colPos'] === 243) {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'three');
                } else {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'four');
                }
                break;
            case 'six_columns':
                if ($this->processedData['data']['colPos'] === 261) {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'one');
                } elseif ($this->processedData['data']['colPos'] === 262) {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'two');
                } elseif ($this->processedData['data']['colPos'] === 263) {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'three');
                } elseif ($this->processedData['data']['colPos'] === 264) {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'four');
                } elseif ($this->processedData['data']['colPos'] === 265) {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'five');
                } else {
                    $gridWidth = self::getGridWidth($bsGridWidth, 'six');
                }
                break;
        }

        return $gridWidth;
    }


    /**
     * Returns $ mediaWidth if there is a parent grid element
     *
     * @param int $bsGridWidth
     * @param string $suffix
     *
     * @return int $gridWidth
     */
    protected function getGridWidth($bsGridWidth, $suffix): int
    {
        if (!empty($this->parentflexconf['sm_'.$suffix])) {
            $gridWidth = $bsGridWidth / self::gridColumns * (int) $this->parentflexconf['sm_'.$suffix];
        } elseif (!empty($this->parentflexconf['md_'.$suffix])) {
            $gridWidth = $bsGridWidth / self::gridColumns * (int) $this->parentflexconf['md_'.$suffix];
        } elseif (!empty($this->parentflexconf['lg_'.$suffix])) {
            $gridWidth = $bsGridWidth / self::gridColumns * (int) $this->parentflexconf['lg_'.$suffix];
        } elseif (!empty($this->parentflexconf['xl_'.$suffix])) {
            $gridWidth = $bsGridWidth / self::gridColumns * (int) $this->parentflexconf['xl_'.$suffix];
        } elseif (!empty($this->parentflexconf['xxl_'.$suffix])) {
            $gridWidth = $bsGridWidth / self::gridColumns * (int) $this->parentflexconf['xxl_'.$suffix];
        } else {
            $gridWidth = $bsGridWidth / self::gridColumns * ($this->numberOfColumns);
        }

        return $gridWidth;
    }


    /**
     * Returns $mediaWidth and check some conditions
     *
     * @param int $mediaWidth
     *
     * @return int $mediaWidth
     */
    protected function checkMediaWidth($mediaWidth): int
    {
        if ($this->minimumWidth && $mediaWidth < self::minimumWidth) {
            // set to 575px and therefore 100% wide on mobile (constant: minimumWidth=1)
            $mediaWidth = self::minimumWidth;
        }
        // t3sbs_mediaobject
        if ($this->cType == 't3sbs_mediaobject' && $this->maxWidthMediaObject < $mediaWidth) {
            $mediaWidth = $this->maxWidthMediaObject;
        }
        // t3sbs_toast
        if ($this->cType == 't3sbs_toast' && $this->maxWidthToast < $mediaWidth) {
            $mediaWidth = $this->maxWidthToast;
        }
        // card slider
        if (!empty($this->parentflexconf['width']) && !empty($this->parentflexconf['card_wrapper']) && $this->parentflexconf['card_wrapper'] === 'slider') {
            $mediaWidth = (int)$this->parentflexconf['width'];
        }
        // masonry_wrapper
        if (!empty($this->processedParentData['CType']) && $this->processedParentData['CType'] == 'masonry_wrapper') {
            $classPrefix = 'col-lg-';
            $mediaWidth = $this->getMansoryColumns($classPrefix);
            if (!$mediaWidth) {
                $classPrefix = 'col-xl-';
                $mediaWidth = $this->getMansoryColumns($classPrefix);
            }
            if (!$mediaWidth) {
                $classPrefix = 'col-xxl-';
                $mediaWidth = $this->getMansoryColumns($classPrefix);
            }
            if (!$mediaWidth) {
                $classPrefix = 'col-sm-';
                $mediaWidth = $this->getMansoryColumns($classPrefix);
            }
            if (!$mediaWidth) {
                $mediaWidth = self::bsMaxGridWidth / 2;
            }
        }

        return (int) $mediaWidth;
    }


    /**
     * Returns mansory columns
     *
     * @param string $classPrefix
     *
     * @return int $mediaWidth
     */
    protected function getMansoryColumns($classPrefix): int
    {
        # 2 columns
        $pos = strpos($this->parentflexconf['colclass'], $classPrefix.'6');
        if ($pos !== false) {
            $mediaWidth = self::bsMaxGridWidth / 2 - self::gridGutterWidth;
        }
        # 3 columns
        $pos = strpos($this->parentflexconf['colclass'], $classPrefix.'4');
        if ($pos !== false) {
            $mediaWidth = self::bsMaxGridWidth / 3 - self::gridGutterWidth;
        }
        # 4 columns
        $pos = strpos($this->parentflexconf['colclass'], $classPrefix.'3');
        if ($pos !== false) {
            $mediaWidth = self::bsMaxGridWidth / 4 - self::gridGutterWidth;
        }
        # 6 columns
        $pos = strpos($this->parentflexconf['colclass'], $classPrefix.'2');
        if ($pos !== false) {
            $mediaWidth = self::bsMaxGridWidth / 6 - self::gridGutterWidth;
        }

        return (int) $mediaWidth;
    }


    /**
     * Returns the frontend controller
     */
    protected function getFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
