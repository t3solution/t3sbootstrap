<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class CollapsibleAccordion implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf, array $parentflexconf): array
	{

		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);

		$file = $fileRepository->findByRelation('tt_content', 'assets', (int)$processedData['data']['uid']);
		if (!empty($file)) {$file = $file[0];}
		$processedData['appearance'] = $parentflexconf['appearance'];
		$processedData['show'] = !empty($flexconf['active']) ? ' show' : '';
		$processedData['collapsed'] = !empty($flexconf['active']) ? '' : ' collapsed';
		$processedData['expanded'] = !empty($flexconf['active']) ? 'true' : 'false';
		$processedData['alwaysOpen'] = !empty($parentflexconf['alwaysOpen']) ? 'true' : 'false';
		$processedData['buttonstyle'] = !empty($flexconf['style']) ? $flexconf['style'] : 'primary';
		$processedData['collapsibleByPid'] = !empty($flexconf['collapsibleByPid']) ? $flexconf['collapsibleByPid'] : '';
		$processedData['media'] = $file ? $file : '';

		$processedData['appearance'] = $parentflexconf['appearance'];

		return $processedData;
	}

}
