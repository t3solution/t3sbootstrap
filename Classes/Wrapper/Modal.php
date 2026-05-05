<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use Psr\Http\Message\ServerRequestInterface;

class Modal implements SingletonInterface
{
	public function getProcessedData(array $processedData, array $flexconf): array
	{
		$processedData['class']  = $processedData['class'] ?? '';
		$onPageLoad              = !empty($flexconf['onPageLoad']);

		$processedData['modal'] = [
			'animation'     => $flexconf['animation']  ?? '',
			'size'          => $flexconf['size']        ?? '',
			'button'        => $flexconf['button']      ?? '',
			'style'         => $flexconf['style']       ?? '',
			'onPageLoad'    => $onPageLoad,
			'showOnPageLoad'=> $onPageLoad,
			'cookie'        => !empty($flexconf['cookie']),
			'showHeader'    => true,
			'nextModal'     => $flexconf['nextModal'] ?? '0',
			'prevModal'     => $flexconf['prevModal'] ?? '0',
		];

		$processedData['modal']['buttonText'] = $this->resolveButtonText($processedData, $flexconf);

		if (!empty($flexconf['fixedPosition'])) {
			$processedData['modal']['fixedClass']  =
				'fixedModalButton fixedPosition fixedPosition-' . $flexconf['fixedPosition'];
			$processedData['modal']['fixedButton'] = true;

			if (!empty($flexconf['rotate'])) {
				$processedData['class'] .= ' rotateFixedPosition rotate-' . $flexconf['rotate'];
			}
		}

		if (!empty($processedData['data']['header_position'])) {
			$position = match ($processedData['data']['header_position']) {
				'left'  => 'start',
				'right' => 'end',
				default => $processedData['data']['header_position'],
			};
			$processedData['class'] .= ' text-' . $position;
		}

		if (!empty($flexconf['whiteclosebutton'])) {
			$processedData['modal']['whiteclosebutton'] = true;
		}

		if ($onPageLoad) {
			$processedData = $this->applyPageLoadCookieLogic($processedData);
		}

		return $processedData;
	}

	private function resolveButtonText(array $processedData, array $flexconf): string
	{
		if (!empty($flexconf['buttonText'])) {
			return $flexconf['buttonText'];
		}

		if (!empty($processedData['data']['header'])) {
			return $processedData['data']['header'];
		}

		return !empty($processedData['modal']['button']) ? 'Modal-Button' : 'Modal-Link';
	}

	private function applyPageLoadCookieLogic(array $processedData): array
	{
		$processedData['modal']['showHeader'] = false;

		$uid     = 't3sb_modal-' . $processedData['data']['uid'];
		$request = $this->getRequest();
		$cookies = $request->getCookieParams();
		$hasCookie = isset($cookies[$uid]) && $cookies[$uid] === 'allow';

		if ($hasCookie) {
			if ($processedData['modal']['cookie']) {
				$processedData['modal']['showOnPageLoad'] = false;
			} else {
				// Cookie ablaufen lassen — Hinweis: setcookie() hier nur als Notlösung,
				// besser über PSR-7 Response in einem Middleware-Layer setzen
				setcookie($uid, '', time() - 3600, '/');
			}
		} else {
			if ($processedData['modal']['cookie']) {
				setcookie($uid, 'allow', time() + 3600, '/');
			}
		}

		return $processedData;
	}

	private function getRequest(): ServerRequestInterface
	{
		return $GLOBALS['TYPO3_REQUEST'];
	}
}