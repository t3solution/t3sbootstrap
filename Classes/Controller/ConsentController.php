<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use T3SBS\T3sbootstrap\Utility\YouTubeRenderer;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ConsentController extends ActionController
{

	/**
	 * action index
	 *
	 * @return void
	 */
	public function indexAction(): ResponseInterface
	{
		$consentRecordUid = (int)$this->settings['consent']['contentByUid'];
		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
		$fileObjects = $fileRepository->findByRelation('tt_content', 'assets', $consentRecordUid);
		$assignedValues = [];

		if ( !empty($fileObjects) ) {
			if ( $fileObjects[0]->getProperties()['mime_type'] == 'video/youtube') {
				if ( $this->settings['consent']['cookie'] && !empty($_COOKIE['contentconsent_'.$consentRecordUid])
				 && $_COOKIE['contentconsent_'.$consentRecordUid] == 'allow' ) {
					$contentConsent = TRUE;
				} else {
					$contentConsent = FALSE;
					$lazyload = $this->settings['lazyLoad'] ? 'true': 'false';
					$thumbnailRecordUid = $this->configurationManager->getContentObject()->data['uid'];
					$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
					$thumbnails = $fileRepository->findByRelation('tt_content', 'consentpreviewimage', $thumbnailRecordUid);
				}
				$autoplay = $fileObjects[0]->getProperties()['autoplay'];
				$videoId = GeneralUtility::makeInstance(YouTubeRenderer::class)->render($fileObjects[0]);
				$mute = $autoplay ? 1 : 0;
				$ratioArr = explode('/',$this->settings['consent']['autoSize']);
				$params = '?autoplay='.$autoplay.'&loop=0&playlist='.$videoId.'&mute='.$mute.'&rel=0&showinfo=0&controls=1&modestbranding=1';

				$assignedValues = [
					'contentConsent' => $contentConsent,
					'thumbnail' => !empty($thumbnails[0]) ? $thumbnails[0] : '',
					'ytuid' => $videoId,
					'params' => $params,
					'ratio' => trim($ratioArr[1]).'x'.trim($ratioArr[0]),
					'isYouTube' => TRUE,
				];
			}
		} else {
			$assignedValues = [
				'isYouTube' => FALSE,
			];
		}

		$this->view->assignMultiple($assignedValues);
		return $this->htmlResponse();
	}

}
