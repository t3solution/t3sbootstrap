<?php
declare(strict_types = 1);

namespace T3SBS\T3sbootstrap\Utility;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;

final class ConnectionPoolUtility
{
	
	public function __construct(
		private readonly ConnectionPool $connectionPool,
	) {}
	

	public function selectChapterIndex(int $currentPageUid, int $sysLanguageUid=0)
	{
		$queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
		$result = $queryBuilder
			->select('uid', 'header', 'tx_t3sbootstrap_chapter')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('sys_language_uid', $sysLanguageUid),
				$queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($currentPageUid, Connection::PARAM_INT)),
				$queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
				$queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
				$queryBuilder->expr()->eq('colPos', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT))
			)
			->executeQuery()
			->fetchAssociative();
	
		return $result;
	}

}
