<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener\TCA;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class TcaCompilation
{

	public function __invoke(AfterTcaCompilationEvent $event): void
	{
		$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
		$tca = $event->getTca();

		// tt_content

		if (!empty($extconf['customHeaderClass'])) {
			$newItems = [];
			foreach ( explode(',',$extconf['customHeaderClass']) as $custom) {
				$customArray = explode(' ', $custom);
				$key = trim(end($customArray));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['tt_content']['columns']['tx_t3sbootstrap_header_class']['config']['valuePicker']['items'] = $newItems;
		}


		// pages

		if (!empty($extconf['customTitleColor'])) {
			$newItems = [];
			foreach ( explode(',',$extconf['customTitleColor']) as $custom) {
				$customArray = explode(' ', $custom);
				$key = trim(end($customArray));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['pages']['columns']['tx_t3sbootstrap_titlecolor']['config']['valuePicker']['items'] = $newItems;
		}


		if (!empty($extconf['customSubtitleColor'])) {
			$newItems = [];
			foreach ( explode(',',$extconf['customSubtitleColor']) as $custom) {
				$customArray = explode(' ', $custom);
				$key = trim(end($customArray));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['pages']['columns']['tx_t3sbootstrap_subtitlecolor']['config']['valuePicker']['items'] = $newItems;
		}


		// sys_file_reference

		if (!empty($extconf['figureClass'])) {
			$newItems = [];
			foreach ( explode(',',$extconf['figureClass']) as $custom) {
				$customArray = explode(' ', $custom);
				$key = trim(end($customArray));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['sys_file_reference']['columns']['tx_t3sbootstrap_extra_class']['config']['valuePicker']['items'] = $newItems;
		}


		if (!empty($extconf['imageClass'])) {
			$newItems = [];
			foreach ( explode(',',$extconf['imageClass']) as $custom) {
				$customArray = explode(' ', $custom);
				$key = trim(end($customArray));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['sys_file_reference']['columns']['tx_t3sbootstrap_extra_imgclass']['config']['valuePicker']['items'] = $newItems;
		}		
		
		$event->setTca($tca);
	}

}
