<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class CollapsibleContainer implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{

		$processedData['appearance'] = $flexconf['appearance'];
		if ($flexconf['appearance'] == 'accordion') {
			$processedData['flush'] = $flexconf['flush'] ? ' accordion-flush' : '';
		}
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$statements = $queryBuilder
			->select('tx_t3sbootstrap_flexform', 'tx_t3sbootstrap_header_fontawesome', 'tx_t3sbootstrap_header_class')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($processedData['data']['uid'], \PDO::PARAM_INT))
			)
			->execute()
			->fetchAll();

		$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
		foreach ($statements as $key=>$statement) {
			$flexformArr[$key] = $flexFormService->convertFlexFormContentToArray($statement['tx_t3sbootstrap_flexform']);
			$headerExtraClassArr[$key] = !empty($statement['tx_t3sbootstrap_header_class']) ? $statement['tx_t3sbootstrap_header_class'] : '';
			$headerFontawesomeArr[$key] = !empty($statement['tx_t3sbootstrap_header_fontawesome'])
			 ? '<i class="'.$statement['tx_t3sbootstrap_header_fontawesome'].'"></i> ' : '';
		}
		$processedData['flexformArr'] = $flexformArr;
		$processedData['headerExtraClassArr'] = $headerExtraClassArr;
		$processedData['headerFontawesomeArr'] = $headerFontawesomeArr;

		return $processedData;
	}

}
