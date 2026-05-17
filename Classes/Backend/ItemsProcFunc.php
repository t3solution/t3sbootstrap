<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

final class ItemsProcFunc
{
	public function __construct(
		private readonly ConnectionPool $connectionPool,
	) {
	}

	public function user_cards(array &$params): void
	{
		$this->getChildItems($params, 't3sbs_card', 'Card');
	}

	public function user_carousels(array &$params): void
	{
		$this->getChildItems($params, 't3sbs_carousel', 'Carousel');
	}

	public function user_cardwrapper(array &$params): void
	{
		$currentUid = $this->resolveCurrentUid($params);

		$queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
		$cardwrappers = $queryBuilder
			->select('uid', 'header')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq(
					'CType',
					$queryBuilder->createNamedParameter('card_wrapper')
				)
			)
			->executeQuery()
			->fetchAllAssociative();

		foreach ($cardwrappers as $cardwrapper) {
			if ((int)$cardwrapper['uid'] === $currentUid) {
				continue;
			}

			$label = !empty($cardwrapper['header'])
				? $cardwrapper['header']
				: 'uid=' . $cardwrapper['uid'];

			$params['items'][] = [
				'label' => 'Card Wrapper: ' . $label,
				'value' => (int)$cardwrapper['uid'],
			];
		}
	}

	private function getChildItems(array &$params, string $ctype, string $label): void
	{
		$currentUid = $this->resolveCurrentUid($params);

		// All elements of the specified CType that are already assigned to a container
		$itemsQueryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
		$items = $itemsQueryBuilder
			->select('uid', 'header', 'tx_container_parent')
			->from('tt_content')
			->where(
				$itemsQueryBuilder->expr()->eq(
					'CType',
					$itemsQueryBuilder->createNamedParameter($ctype)
				),
				$itemsQueryBuilder->expr()->gt(
					'tx_container_parent',
					$itemsQueryBuilder->createNamedParameter(0, Connection::PARAM_INT)
				)
			)
			->executeQuery()
			->fetchAllAssociative();

		if ($items === []) {
			return;
		}

		// Items that are already in the CURRENT container (only if the UID actually exists)
		$childrenUids = [];
		if ($currentUid > 0) {
			$childrenQueryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
			$children = $childrenQueryBuilder
				->select('uid')
				->from('tt_content')
				->where(
					$childrenQueryBuilder->expr()->eq(
						'CType',
						$childrenQueryBuilder->createNamedParameter($ctype)
					),
					$childrenQueryBuilder->expr()->eq(
						'tx_container_parent',
						$childrenQueryBuilder->createNamedParameter($currentUid, Connection::PARAM_INT)
					)
				)
				->executeQuery()
				->fetchAllAssociative();

			$childrenUids = array_map('intval', array_column($children, 'uid'));
		}

		// Add items to the shortlist that are NOT in the current container
		foreach ($items as $item) {
			$itemUid = (int)$item['uid'];

			if (in_array($itemUid, $childrenUids, true)) {
				continue;
			}

			$itemLabel = !empty($item['header'])
				? $item['header']
				: 'uid=' . $itemUid;

			$params['items'][] = [
				'label' => $label . ': ' . $itemLabel,
				'value' => $itemUid,
			];
		}
	}

	/**
	 * Returns the UID of the currently edited record, or 0 for new records.
	 */
	private function resolveCurrentUid(array $params): int
	{
		$uid = $params['row']['uid'] ?? 0;

		// For new records, the UID is a string such as 'NEW123abc'
		if (!is_numeric($uid)) {
			return 0;
		}

		return (int)$uid;
	}
}