<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Components;

use TYPO3\CMS\Core\SingletonInterface;

class Toast implements SingletonInterface
{

	public function getProcessedData(array $processedData, array $flexconf): array
	{
		$processedData['animation'] = !empty($flexconf['animation']) ? 'true' : 'false';
		$processedData['autohide'] = !empty($flexconf['autohide']) ? 'true' : 'false';
		$processedData['delay']     = $flexconf['delay'] ?? '';
		$processedData['style'] .= !empty($flexconf['toastwidth']) ? ' width:'.$flexconf['toastwidth'].'px;' : '';
		$processedData['placement'] = $flexconf['placement'] ?? '';
		$processedData['cookie'] = !empty($flexconf['cookie']);
		$processedData['expires']   = $flexconf['expires'] ?? '';
		$processedData['style'] .= ' z-index:1;';

		return $processedData;
	}

}
