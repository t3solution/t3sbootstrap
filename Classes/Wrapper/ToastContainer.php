<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;

class ToastContainer implements SingletonInterface
{
	public function getProcessedData(array $processedData, array $flexconf, string $navbarEnable): array
	{
		$processedData['style']        = ($processedData['style'] ?? '') . ' z-index:1;';
		$processedData['style']       .= !empty($flexconf['toastwidth'])
			? ' width:' . $flexconf['toastwidth'] . 'px;' : '';

		$processedData['animation']    = !empty($flexconf['animation'])    ? 'true' : 'false';
		$processedData['autohide']     = !empty($flexconf['autohide'])     ? 'true' : 'false';
		$processedData['delay']        = $flexconf['delay']        ?? 0;
		$processedData['cookie']       = $flexconf['cookie']       ?? '';
		$processedData['expires']      = $flexconf['expires']      ?? '';
		$processedData['multipleToast']= $flexconf['multipleToast'] ?? false;

		$placement = $flexconf['placement'] ?? '';
		if ($navbarEnable && !empty($placement)) {
			$placement = str_starts_with($placement, 'top-0')
				? str_replace('top-0', 'top-70', $placement)
				: $placement;
			$processedData['placement'] = ' ' . $placement;
		}

		return $processedData;
	}
}
