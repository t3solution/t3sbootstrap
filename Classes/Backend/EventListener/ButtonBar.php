<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener;

use TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use Psr\Http\Message\ServerRequestInterface;

#[AsEventListener(
	identifier: 't3sbootstrap/backend/modify-button-bar',
	method: 'removeButtons',
)]
final readonly class ButtonBar
{

	public function removeButtons(ModifyButtonBarEvent $event): void
	{
		$request = $this->getRequest();

		if (!empty($request->getQueryParams()['returnUrl'])) {

			$path = $request->getUri()->getPath();
			$returnUrl = $request->getQueryParams()['returnUrl'];
			$t3sbModule = false;
	
			if (($path === '/typo3/record/edit' && str_contains($returnUrl, 'web/T3sbootstrap')) || $path === '/typo3/module/web/T3sbootstrap') {
				$rootPageId = $request->getAttribute('site')->getRootPageId();
				if (!empty($request->getQueryParams()['id']) && (int)$request->getQueryParams()['id'] === $rootPageId) {
					$t3sbModule = true;
				}
				if (!empty($request->getQueryParams()['module']) && $request->getQueryParams()['module'] === 'web_t3sbootstrap') {
					$t3sbModule = true;
				}
				if ($t3sbModule) {
					// @extensionScannerIgnoreLine - getButtons() is falsely flagged by extension scanner
					$buttons = $event->getButtons();
					$showButtons = [];
					if (!empty($buttons['left'])) {
						foreach ($buttons['left'] as $leftButton) {
							if ($leftButton[0]->getClasses() !== 't3js-editform-new' 
								&& $leftButton[0]->getClasses() !== 't3js-editform-delete-record') {
								$showButtons['left'][] = $leftButton;
							}
						}
					
						$event->setButtons($showButtons);
					}
				}
			}
		}
	}
	
	private function getRequest(): ServerRequestInterface
	{
		return $GLOBALS['TYPO3_REQUEST'];
	}
	
}
