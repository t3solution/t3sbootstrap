<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;

class MasonryWrapper implements SingletonInterface
{
	public function getProcessedData(array $processedData, array $flexconf): array
	{
		$processedData['masonryClass'] = $flexconf['colclass'] ?? '';

		return $processedData;
	}
}
