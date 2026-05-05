<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers\Backend;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Resource\ResourceFactory;

class InfoImageViewHelper extends AbstractViewHelper
{
	protected $escapeOutput = false;

	public function initializeArguments(): void
	{
		$this->registerArgument('uid', 'int', 'record uid', true);		
	}

	public function render(): array
	{
		$thumbnails = GeneralUtility::makeInstance(FileRepository::class)->findByRelation('tt_content', 'assets', $this->arguments['uid']);

		if (empty($thumbnails)) {
			// background image from flexform e.g. two_columns
			$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
			$contentQueryBuilder = $connectionPool->getQueryBuilderForTable('sys_file_reference');
			$images = $contentQueryBuilder
				->select('uid')
				->from('sys_file_reference')
				->where(
					$contentQueryBuilder->expr()->eq('uid_foreign', $contentQueryBuilder->createNamedParameter($this->arguments['uid'], Connection::PARAM_INT)),
				)
				->executeQuery()
				->fetchAllAssociative();

			$resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
			foreach ($images as $image) {
				$thumbnails[] = $resourceFactory->getFileReferenceObject($image['uid']);
			}
		}

        return $thumbnails;
	}

}
