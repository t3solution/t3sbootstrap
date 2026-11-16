<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener;

use T3SBS\T3sbootstrap\Service\ConfigTransferService;
use TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
	identifier: 't3sbootstrap/backend/modify-button-bar',
	method: 'removeButtons',
)]
final readonly class ButtonBar
{

	public function removeButtons(ModifyButtonBarEvent $event): void
	{
		$request = $event->getRequest();

		if (!empty($request->getQueryParams()['returnUrl'])) {

			$path = $request->getUri()->getPath();
			$returnUrl = $request->getQueryParams()['returnUrl'];
			$t3sbModule = false;
	
			if (($path === '/typo3/record/edit' && str_contains($returnUrl, 'web/t3sbootstrap')) || $path === '/typo3/module/web/t3sbootstrap') {
				$rootPageId = (int)($request->getAttribute('site')?->getRootPageId() ?? 0);
				if (!empty($request->getQueryParams()['id']) && (int)$request->getQueryParams()['id'] === $rootPageId) {
					$t3sbModule = true;
				}
				if (!empty($request->getQueryParams()['module']) && $request->getQueryParams()['module'] === 'web_t3sbootstrap') {
					$t3sbModule = true;
				}
				// a pencil from the constants table opens one single field - the link carries
				// no page id, and New or Delete have no place in a form showing one setting
				if (!empty($request->getQueryParams()['columnsOnly'][ConfigTransferService::TABLE])) {
					$t3sbModule = true;
				}
				if ($t3sbModule) {
					// @extensionScannerIgnoreLine - getButtons() is falsely flagged by extension scanner
					$buttons = $event->getButtons();
					// keep every other group untouched, only the left one is filtered
					$showButtons = $buttons;
					if (!empty($buttons['left'])) {
						$showButtons['left'] = [];
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
	
}
