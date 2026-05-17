<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener\TCA;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
	identifier: 't3sbootstrap/TcaPostProcessing',
	method: 'modifyValuePicker',
)]
final readonly class TcaCompilation
{
	public function __construct(
		private readonly ExtensionConfiguration $extensionConfiguration,
	) {}

	public function modifyValuePicker(AfterTcaCompilationEvent $event): void
	{
		$extconf = $this->extensionConfiguration->get('t3sbootstrap');
		$tca = $event->getTca();

		$map = [
			'customHeaderClass'   => 'tt_content.tx_t3sbootstrap_header_class',
			'customTitleColor'    => 'pages.tx_t3sbootstrap_titlecolor',
			'customSubtitleColor' => 'pages.tx_t3sbootstrap_subtitlecolor',
			'figureClass'         => 'sys_file_reference.tx_t3sbootstrap_extra_class',
			'imageClass'          => 'sys_file_reference.tx_t3sbootstrap_extra_imgclass',
		];
		
		foreach ($map as $extconfKey => $tcaPath) {
			if (!empty($extconf[$extconfKey])) {
				[$table, $column] = explode('.', $tcaPath);
				$tca[$table]['columns'][$column]['config']['valuePicker']['items']
					= $this->buildValuePickerItems($extconf[$extconfKey]);
			}
		}		
		
		$event->setTca($tca);
	}
	
	
	private function buildValuePickerItems(string $extconfValue): array
	{
		$newItems = [];
		foreach (explode(',', $extconfValue) as $custom) {
			$customArray = explode(' ', $custom);
			$key = trim(end($customArray));
			$newItems[] = ['label' => $key, 'value' => $custom];
		}
		return $newItems;
	}

}
