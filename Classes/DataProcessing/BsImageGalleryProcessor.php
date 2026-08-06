<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Resource\FileCollector;

class BsImageGalleryProcessor implements DataProcessorInterface
{
	public function process(
		ContentObjectRenderer $cObj,
		array $contentObjectConfiguration,
		array $processorConfiguration,
		array $processedData
	): array {
		if (!empty($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
			return $processedData;
		}

		// One instance per call—otherwise, multiple CEs
		// on a single page will accumulate their files in the same collector instance.
		$fileCollector = GeneralUtility::makeInstance(FileCollector::class);

		// references / relations
		if (!empty($processorConfiguration['references.'])) {
			$referenceConfiguration = $processorConfiguration['references.'];
			$relationField = $cObj->stdWrapValue('fieldName', $referenceConfiguration);

			if (!empty($relationField)) {
				$relationTable = $cObj->stdWrapValue('table', $referenceConfiguration, $cObj->getCurrentTable());
				if (!empty($relationTable)) {
					// @extensionScannerIgnoreLine
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

		$numberOfColumns = (int)($processedData['data']['imagecols'] ?: 3);
		$files = $fileCollector->getFiles();
		$galleryChunk = $files ? array_chunk($files, $numberOfColumns) : [];

		// set the files into a variable, default "files"
		$targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'files');
		$processedData[$targetVariableName] = $galleryChunk;

		return $processedData;
	}
}
