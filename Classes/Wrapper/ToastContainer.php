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

		// Der top-0/top-70-Versatz haengt an der Navbar, die Platzierung selbst nicht.
		// Bisher stand beides im selben if - ohne Navbar fiel die im FlexForm
		// gewaehlte Ecke ersatzlos weg.
		$placement = $flexconf['placement'] ?? '';
		if (!empty($placement)) {
			if ($navbarEnable && str_starts_with($placement, 'top-0')) {
				$placement = str_replace('top-0', 'top-70', $placement);
			}
			$processedData['placement'] = ' ' . $placement;
		}

		return $processedData;
	}
}
