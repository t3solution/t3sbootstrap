<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\EventListener\TCA;

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

		// tt_content

		if (!empty($extconf['customFaIcons'])) {
			$tca = $event->getTca();
			$newItems = [];
			foreach ( explode(',',$extconf['customFaIcons']) as $custom) {
				$key = trim(end(explode(' ', $custom)));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['tt_content']['columns']['tx_t3sbootstrap_header_fontawesome']['config']['valuePicker']['items'] = $newItems;
			$event->setTca($tca);
		} else {
			$tca = $event->getTca();
			$defaultTCA = $tca['tt_content']['columns']['tx_t3sbootstrap_header_fontawesome']['config']['valuePicker']['items'];
			$tca = $defaultTCA;
		}

		if (!empty($extconf['customHeaderClass'])) {
			$tca = $event->getTca();
			$newItems = [];
			foreach ( explode(',',$extconf['customHeaderClass']) as $custom) {
				$key = trim(end(explode(' ', $custom)));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['tt_content']['columns']['tx_t3sbootstrap_header_class']['config']['valuePicker']['items'] = $newItems;
			$event->setTca($tca);
		}

		// pages

		if (!empty($extconf['customFaIconsPages'])) {
			$tca = $event->getTca();
			$newItems = [];
			foreach ( explode(',',$extconf['customFaIconsPages']) as $custom) {
				$key = trim(end(explode(' ', $custom)));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['pages']['columns']['tx_t3sbootstrap_fontawesome_icon']['config']['valuePicker']['items'] = $newItems;
			$event->setTca($tca);
		}

		if (!empty($extconf['customTitleColor'])) {
			$tca = $event->getTca();
			$newItems = [];
			foreach ( explode(',',$extconf['customTitleColor']) as $custom) {
				$key = trim(end(explode(' ', $custom)));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['pages']['columns']['tx_t3sbootstrap_titlecolor']['config']['valuePicker']['items'] = $newItems;
			$event->setTca($tca);
		}

		if (!empty($extconf['customSubtitleColor'])) {
			$tca = $event->getTca();
			$newItems = [];
			foreach ( explode(',',$extconf['customSubtitleColor']) as $custom) {
				$key = trim(end(explode(' ', $custom)));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['pages']['columns']['tx_t3sbootstrap_subtitlecolor']['config']['valuePicker']['items'] = $newItems;
			$event->setTca($tca);
		}

		// sys_file_reference

		if (!empty($extconf['figureClass'])) {
			$tca = $event->getTca();
			$newItems = [];
			foreach ( explode(',',$extconf['figureClass']) as $custom) {
				$key = trim(end(explode(' ', $custom)));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['sys_file_reference']['columns']['tx_t3sbootstrap_extra_class']['config']['valuePicker']['items'] = $newItems;
			$event->setTca($tca);
		}

		if (!empty($extconf['imageClass'])) {
			$tca = $event->getTca();
			$newItems = [];
			foreach ( explode(',',$extconf['imageClass']) as $custom) {
				$key = trim(end(explode(' ', $custom)));
				$newItems[] = [0 => $key, 1 => $custom];	
			}
			$tca['sys_file_reference']['columns']['tx_t3sbootstrap_extra_imgclass']['config']['valuePicker']['items'] = $newItems;
			$event->setTca($tca);
		}
	}

}
