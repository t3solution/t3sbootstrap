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
#[AsEventListener(
	identifier: 't3sbootstrap/TcaPageOptOutTab',
	method: 'addPageOptOutTab',
)]
final readonly class TcaCompilation
{
	/**
	 * Page types that never render a page of their own - a tab about hiding the
	 * navbar or the jumbotron would be pointless there.
	 *   199 spacer, 254 folder, 255 recycler, 3 link, 4 shortcut
	 */
	private const SKIP_DOKTYPES = [3, 4, 199, 254, 255];

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
	
	
	/**
	 * Hangs the "T3SB" tab onto every page type, after all TCA overrides ran.
	 *
	 * addToAllTCAtypes() in Configuration/TCA/Overrides/pages.php only reaches the
	 * types that exist at that moment, and an extension defining its own page type
	 * writes a complete 'showitem' - which drops the tab again. After the
	 * compilation every type is known and nothing overwrites it any more.
	 */
	public function addPageOptOutTab(AfterTcaCompilationEvent $event): void
	{
		$tca = $event->getTca();

		// No palette, no tab. The palette is built in Configuration/TCA/Overrides/pages.php
		// from whatever the Extension Configuration switched on - "pageOptOut", "contentIndent"
		// or both - so asking for it covers every case without naming the switches here.
		if (empty($tca['pages']['palettes']['t3sbPage'])) {
			return;
		}

		foreach ($tca['pages']['types'] ?? [] as $doktype => $typeConfig) {
			if (in_array((int)$doktype, self::SKIP_DOKTYPES, true)) {
				continue;
			}

			$showitem = (string)($typeConfig['showitem'] ?? '');

			// Already there - addToAllTCAtypes got to this type before us.
			if ($showitem === '' || str_contains($showitem, '--palette--;;t3sbPage')) {
				continue;
			}

			$tca['pages']['types'][$doktype]['showitem'] =
				$showitem . ',--div--;T3SB,--palette--;;t3sbPage';
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
