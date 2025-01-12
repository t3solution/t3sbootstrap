<?php
	
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Resource\FileCollector;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class BsImageGalleryProcessor implements DataProcessorInterface
{
	/**
	 * Process data of a record to resolve File objects to the view
	 *
	 * @param ContentObjectRenderer $cObj The data of the content element or page
	 * @param array $contentObjectConfiguration The configuration of Content Object
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
	 * @return array the processed data as key/value store
	 */
	public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
	{
		if (!empty($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
			return $processedData;
		}

		// gather data
		/** @var FileCollector $fileCollector */
		$fileCollector = GeneralUtility::makeInstance(FileCollector::class);

		// references / relations
		if (!empty($processorConfiguration['references.'])) {
			$referenceConfiguration = $processorConfiguration['references.'];
			$relationField = $cObj->stdWrapValue('fieldName', $referenceConfiguration);

			// If no reference fieldName is set, there's nothing to do
			if (!empty($relationField)) {
				// Fetch the references of the default element
				$relationTable = $cObj->stdWrapValue('table', $referenceConfiguration, $cObj->getCurrentTable());
				if (!empty($relationTable)) {
					$fileCollector->addFilesFromRelation($relationTable, $relationField, $cObj->data);
				}
			}
		}

		// files
		$files = $cObj->stdWrapValue('files', $processorConfiguration);
		if ($files) {
			$files = GeneralUtility::intExplode(',', $files, true);
			$fileCollector->addFiles($files);
		}

		// collections
		$collections = $cObj->stdWrapValue('collections', $processorConfiguration);
		if (!empty($collections)) {
			$collections = GeneralUtility::trimExplode(',', $collections, true);
			$fileCollector->addFilesFromFileCollections($collections);
		}

		// folders
		$folders = $cObj->stdWrapValue('folders', $processorConfiguration);
		if (!empty($folders)) {
			$folders = GeneralUtility::trimExplode(',', $folders, true);
			$fileCollector->addFilesFromFolders($folders, !empty($processorConfiguration['folders.']['recursive']));
		}

		// make sure to sort the files
		$sortingProperty = $cObj->stdWrapValue('sorting', $processorConfiguration);
		if ($sortingProperty) {
			$sortingDirection = $cObj->stdWrapValue(
				'direction',
				!empty($processorConfiguration['sorting.']) ? $processorConfiguration['sorting.'] : [],
				'ascending'
			);

			$fileCollector->sort($sortingProperty, $sortingDirection);
		}

		$numberOfColumns = $processedData['data']['imagecols'] ? (int)$processedData['data']['imagecols'] : 3;
		$galleryChunk = [];
		if ($fileCollector->getFiles()) {
			$galleryChunk = array_chunk($fileCollector->getFiles(), $numberOfColumns);
		}

		// set the files into a variable, default "files"
		$targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'files');
		$processedData[$targetVariableName] = $galleryChunk;

		return $processedData;
	}
}
