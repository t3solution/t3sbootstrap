<?php
namespace T3SBS\T3sbootstrap\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;
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
	public function indexAction()
	{

		$currentRecord = $this->configurationManager->getContentObject()->data['uid'];

		$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

		if ( $this->settings['consent']['cookie'] && isset($_COOKIE['contentconsent_'.$currentRecord]) && $_COOKIE['contentconsent_'.$currentRecord] == 'allow' ) {

			$contentConsent = TRUE;

		} else {

			$contentConsent = FALSE;

			$jsFooterFile = 'EXT:t3sbootstrap/Resources/Public/Scripts/ajax.js';
			$jsFooterFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($jsFooterFile);

			$pageRenderer->addInlineSetting('T3SB','lazyLoad', json_encode($this->settings));
			$pageRenderer->addJsFooterFile($jsFooterFile);

			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$thumbnails = $fileRepository->findByRelation('tt_content', 'consentpreviewimage', $currentRecord);

		}

		if ( $this->settings['consent']['autoSize'] ) {

			$inlineJS = 'jQuery(function(){
				var thumbnail = $("#c'.$currentRecord.' .content-consent.background-image");
				if (thumbnail.length) {
					var thumbHeight = thumbnail.outerWidth() * '.$this->settings['consent']['autoSize'].';
					thumbnail.css("min-height", parseInt(thumbHeight)+"px");
				}
			});';

			$pageRenderer->addJsFooterInlineCode(' Content Consent Thumbnail size - '.$currentRecord.' ',$inlineJS,'FALSE');
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
	public function ajaxAction()
	{
		$post = GeneralUtility::_POST();
		$currentRecord = $post['currentRecord'];

		if ($this->settings['consent']['cookie']) {
			$cookieExpire = $this->settings['cookieExpire'] ? (int)$this->settings['cookieExpire'] : 30;
			setcookie('contentconsent_'.$currentRecord, 'allow', time() + (86400 * $cookieExpire), '/');
		}
	}


}