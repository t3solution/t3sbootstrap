<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Resource\FileCollector;

class BsImageGalleryProcessor implements DataProcessorInterface
{

	public function __construct(
		private readonly FileCollector $fileCollector,
	) {}

	public function process(
		ContentObjectRenderer $cObj, 
		array $contentObjectConfiguration, 
		array $processorConfiguration, 
		array $processedData
	): array
	{
		if (!empty($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
			return $processedData;
		}

		// references / relations
		if (!empty($processorConfiguration['references.'])) {
			$referenceConfiguration = $processorConfiguration['references.'];
			$relationField = $cObj->stdWrapValue('fieldName', $referenceConfiguration);

			// If no reference fieldName is set, there's nothing to do
			if (!empty($relationField)) {
				// Fetch the references of the default element
				$relationTable = $cObj->stdWrapValue('table', $referenceConfiguration, $cObj->getCurrentTable());
				if (!empty($relationTable)) {
					// @extensionScannerIgnoreLine
					$this->fileCollector->addFilesFromRelation($relationTable, $relationField, $cObj->data);
				}
			}
		}

		// files
		$files = $cObj->stdWrapValue('files', $processorConfiguration);
		if ($files) {
			$files = GeneralUtility::intExplode(',', $files, true);
			$this->fileCollector->addFiles($files);
		}

		// collections
		$collections = $cObj->stdWrapValue('collections', $processorConfiguration);
		if (!empty($collections)) {
			$collections = GeneralUtility::trimExplode(',', $collections, true);
			$this->fileCollector->addFilesFromFileCollections($collections);
		}

		// folders
		$folders = $cObj->stdWrapValue('folders', $processorConfiguration);
		if (!empty($folders)) {
			$folders = GeneralUtility::trimExplode(',', $folders, true);
			$this->fileCollector->addFilesFromFolders($folders, !empty($processorConfiguration['folders.']['recursive']));
		}

		// make sure to sort the files
		$sortingProperty = $cObj->stdWrapValue('sorting', $processorConfiguration);
		if ($sortingProperty) {
			$sortingDirection = $cObj->stdWrapValue(
				'direction',
				!empty($processorConfiguration['sorting.']) ? $processorConfiguration['sorting.'] : [],
				'ascending'
			);

			$this->fileCollector->sort($sortingProperty, $sortingDirection);
		}

		$numberOfColumns = (int)($processedData['data']['imagecols'] ?: 3);

		$files = $this->fileCollector->getFiles();
		$galleryChunk = $files ? array_chunk($files, $numberOfColumns) : [];

		// set the files into a variable, default "files"
		$targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'files');
		$processedData[$targetVariableName] = $galleryChunk;

		return $processedData;
	}
}
