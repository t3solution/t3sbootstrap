<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Controller;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * ConsentController
 */
class ConsentController extends ActionController
{

	/**
	 * action index
	 *
	 * @return void
	 */
	public function indexAction(): void
	{
		$currentRecord = $this->configurationManager->getContentObject()->data['uid'];

		if ( $this->settings['consent']['cookie'] && isset($_COOKIE['contentconsent_'.$currentRecord]) && $_COOKIE['contentconsent_'.$currentRecord] == 'allow' ) {

			$contentConsent = TRUE;

		} else {

			$contentConsent = FALSE;

			$lazyload = $this->settings['lazyLoad'] ? 'true': 'false';
			if($lazyload)
			GeneralUtility::makeInstance(AssetCollector::class)
				->addInlineJavaScript('contentconsent', $lazyload);

			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$thumbnails = $fileRepository->findByRelation('tt_content', 'consentpreviewimage', $currentRecord);
		}

		if ( $this->settings['consent']['autoSize'] ) {
			$inlineJS = '
	var thumbnail = $("#c'.$currentRecord.' .content-consent.background-image");
	if (thumbnail.length) {
		var thumbHeight = thumbnail.outerWidth() * '.$this->settings['consent']['autoSize'].';
		thumbnail.css("min-height", parseInt(thumbHeight)+"px");
	}';

			if($inlineJS)
			GeneralUtility::makeInstance(AssetCollector::class)
				  ->addInlineJavaScript('contentconsentthumbnailautosize-'.$currentRecord, $inlineJS);
		}

		$assignedValues = [
			'currentRecord' => $currentRecord,
			'contentConsent' => $contentConsent,
			'thumbnail' => $thumbnails[0],
		];
		$this->view->assignMultiple($assignedValues);
	}


	/**
	 * Displays the selected result with ajax
	 *
	 * @return void
	 */
	public function ajaxAction(): void
	{
		$post = GeneralUtility::_POST();
		$currentRecord = $post['currentRecord'];
		if ($this->settings['consent']['cookie']) {
			$cookieExpire = $this->settings['cookieExpire'] ? (int)$this->settings['cookieExpire'] : 30;
			setcookie('contentconsent_'.$currentRecord, 'allow', time() + (86400 * $cookieExpire), '/');
		}
	}
}
