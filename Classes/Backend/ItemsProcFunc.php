<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;

class ItemsProcFunc
{

	public function user_cards(array &$params): void
	{
		$this->getChildItems($params, 't3sbs_card', 'Card');
	}


	public function user_cardwrapper(array &$params): void
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$cardwrappers = $queryBuilder
		->select('*')
		->from('tt_content')
		->where(
			$queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('card_wrapper', Connection::PARAM_STR))
		)
		->executeQuery()
		->fetchAllAssociative();

		foreach( $cardwrappers as $cardwrapper) {
			if ($cardwrapper['uid'] !== $params['row']['uid']) {
				$label = $cardwrapper['header'] ?: 'uid='.$cardwrapper['uid'];
				$params['items'][] = ['label' => 'Card Wrapper: '.$label, 'value' => $cardwrapper['uid']];
			}
		}

	}
	
	
	public function user_carousels(array &$params): void
	{
		$this->getChildItems($params, 't3sbs_carousel', 'Carousel');
	}
	
	
	private function getChildItems(array &$params, string $ctype, string $label): void
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$items = $queryBuilder
			->select('*')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter($ctype, Connection::PARAM_STR)),
				$queryBuilder->expr()->gt('tx_container_parent', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT))
			)
			->executeQuery()
			->fetchAllAssociative();
	
		$children = $queryBuilder
			->select('uid')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter($ctype, Connection::PARAM_STR)),
				$queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($params['row']['uid'], Connection::PARAM_INT))
			)
			->executeQuery()
			->fetchAllAssociative();
	
		$childrenList = implode(',', array_column($children, 'uid'));
		foreach ($items as $item) {
			if (!GeneralUtility::inList($childrenList, $item['uid'])) {
				$itemLabel = $item['header'] ?: 'uid='.$item['uid'];
				$params['items'][] = ['label' => $label.': '.$itemLabel, 'value' => $item['uid']];
			}
		}
	}

}
