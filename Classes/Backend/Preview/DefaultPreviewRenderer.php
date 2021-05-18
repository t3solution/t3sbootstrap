<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\Preview;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Service\FlexFormService;

class DefaultPreviewRenderer extends StandardContentPreviewRenderer
{

	/**
	* Dedicated method for rendering preview header HTML for
	* the page module only. Receives $item which is an instance of
	* GridColumnItem which has a getter method to return the record.
	*
	* @param GridColumnItem
	* @return string
	*/
	public function renderPageModulePreviewHeader(GridColumnItem $item): string
	{
		$record = $item->getRecord();
		$itemLabels = $item->getContext()->getItemLabels();
		$outHeader = '';

		$content = parent::renderPageModulePreviewContent($item);
		if ( ($content && $record['CType'] === 'list')
			|| ($content && $record['CType'] === 'bullets')
			|| ($content && $record['CType'] === 'table')
			|| ($content && $record['CType'] === 'uploads')
			|| ($content && $record['CType'] === 'login')
			|| ($content && $record['CType'] === 'shortcut')
			|| ($content && $record['CType'] === 'div')
			|| ($content && $record['CType'] === 'html')
		) {
			return '';
		}

		if ($record['CType'] === 't3sbs_card') {
			$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
			$flexconf = $flexformService->convertFlexFormContentToArray($record['pi_flexform']);
			if ( $flexconf['header']['text'] ) {
				 $outHeader .= parent::linkEditContent(parent::renderText($flexconf['header']['text']), $record) . '<br />';
			}
		}

		if ($record['header']) {
			$infoArr = [];
			parent::getProcessedValue($item, 'header_position,header_layout,header_link', $infoArr);
			$hiddenHeaderNote = '';
			// If header layout is set to 'hidden', display an accordant note:
			if ($record['header_layout'] == 100) {
				$hiddenHeaderNote = ' <em>[' . htmlspecialchars(parent::getLanguageService()->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.6')) . ']</em>';
			}
			$outHeader .= $record['date']
				? htmlspecialchars($itemLabels['date'] . ' ' . BackendUtility::date($record['date'])) . '<br />'
				: '';
			$outHeader .= '<strong>' . parent::linkEditContent(parent::renderText($record['header']), $record)
				. $hiddenHeaderNote . '</strong><br />';
		}

		$info = '';
		$contentTypeLabels = $item->getContext()->getContentTypeLabels();
		$contentType = $contentTypeLabels[$record['CType']];
		$info = '<div style="padding:5px; border: 1px solid #563d7c; color:#563d7c; margin-bottom:5px" >'.$contentType.'</div>';

		return $info.$outHeader;

	}


	/**
	* Dedicated method for rendering preview body HTML for
	* the page module only.
	*
	* @param GridColumnItem $item
	* @return string
	*/
	public function renderPageModulePreviewContent(GridColumnItem $item): string
	{
		$out = '';
		$record = $item->getRecord();
		$content = parent::renderPageModulePreviewContent($item);

		if ( ($content && $record['CType'] === 'list')
			|| ($content && substr($record['CType'], 0, 4) === 'menu')
			|| ($content && $record['CType'] === 'bullets')
			|| ($content && $record['CType'] === 'table')
			|| ($content && $record['CType'] === 'uploads')
			|| ($content && $record['CType'] === 'login')
			|| ($content && $record['CType'] === 'shortcut')
			|| ($content && $record['CType'] === 'div')
			|| ($content && $record['CType'] === 'html')
			) {
			return $content;
		}

		$contentTypeLabels = $item->getContext()->getContentTypeLabels();
		$languageService = parent::getLanguageService();

		$contentType = $contentTypeLabels[$record['CType']];
		if (isset($contentType)) {

			$extconf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('t3sbootstrap');
			$maxCharacters = $extconf['previewCropMaxCharacters'];
			$append = ' ...';

			if ($record['subheader']) {
				$out .= parent::linkEditContent(parent::renderText($record['subheader']), $record) . '<br />';
			}
			if ($record['bodytext']) {
				if ($extconf['previewCrop']) {
					$contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
					$text = $contentObject->cropHTML($record['bodytext'], $maxCharacters . '|' . $append . '|' . true);
				} else {
					$text = $record['bodytext'];
				}
				$out .= parent::linkEditContent(parent::renderText($text), $record);
			}
			if ($record['CType'] == 't3sbs_gallery') {
				$out .= 'Columns: '.$record['imagecols'];
				if ($record['CType'] == 't3sbs_gallery') {
					$out .= '<br />Aspect ratio: '.$record['tx_t3sbootstrap_image_ratio'];
				}
			}
			if ($record['CType'] === 't3sbs_card') {
				$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
				$flexconf = $flexformService->convertFlexFormContentToArray($record['pi_flexform']);
				if ( $flexconf['text']['top'] ) {
					if ($extconf['previewCrop']) {
						$contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
						$text = $contentObject->cropHTML($flexconf['text']['top'], $maxCharacters . '|' . $append . '|' . true);
					} else {
						$text = $flexconf['text']['top'];
					}
					$out .= parent::linkEditContent(parent::renderText($text), $record) . '<br />';
				}
				if ( $flexconf['text']['bottom'] ) {
					if ($extconf['previewCrop']) {
						$contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
						$text = $contentObject->cropHTML($flexconf['text']['bottom'], $maxCharacters . '|' . $append . '|' . true);
					} else {
						$text = $flexconf['text']['bottom'];
					}
					$out .= parent::linkEditContent(parent::renderText($text), $record) . '<br />';
				}
			}
			if ($record['CType'] === 't3sbs_carousel') {
				$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
				$flexconf = $flexformService->convertFlexFormContentToArray($record['tx_t3sbootstrap_flexform']);
	  			if ( $flexconf['shift'] ) {
					$flexconfOut .= '<br />- Shift: '.$flexconf['shift'];
				}
	  			if ( $flexconf['bgOverlay'] ) {
					$flexconfOut .= '<br />- Background-Overlay';
				}
	  			if ( $flexconf['shift'] ) {
					$flexconfOut .= '<br />- Shift: '.$flexconf['shift'];
				}
			}
			if ($record['CType'] === 't3sbs_button') {
				$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
				$flexconf = $flexformService->convertFlexFormContentToArray($record['tx_t3sbootstrap_flexform']);

				if ( $flexconf['style'] ) {
					$flexconfOut .= '<br />- Style: '.$flexconf['style'];
				}
				if ( $flexconf['size'] ) {
					$flexconfOut .= '<br />- Size: '.$flexconf['size'];
				}
				if ( $flexconf['block'] ) {
					$flexconfOut .= '<br />- Block: '.$flexconf['block'];
				}
				if ( $flexconf['outline'] ) {
					$flexconfOut .= '<br />- Outline';
				}
			}
			if ($record['CType'] === 't3sbs_toast') {
				$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
				$flexconf = $flexformService->convertFlexFormContentToArray($record['tx_t3sbootstrap_flexform']);

				if ( $flexconf['animation'] ) {
					$flexconfOut .= '<br />- Animation';
				}
				if ( $flexconf['autohide'] ) {
					$flexconfOut .= '<br />- Autohide';
				}
				if ( $flexconf['delay'] ) {
					$flexconfOut .= '<br />- Delay: '.$flexconf['delay'];
				}
				if ( $flexconf['placement'] ) {
					$flexconfOut .= '<br />- Placement: '.$flexconf['placement'];
				}
			}

			if ($flexconfOut)
			$out = $out.substr($flexconfOut, 6);

			$media = $record['assets'] ?: $record['image'];
			if ($media) {
				$field = $record['assets'] ? 'assets' : 'image';
				$out .= '<div style="margin:5px 5px 5px 0">'. parent::linkEditContent($this->getThumbCodeUnlinked($record, 'tt_content', $field), $record) . '</div>';
			}

		} else {
			$message = sprintf(
				 $languageService->sL('LLL:EXT:lang/locallang_core.xlf:labels.noMatchingValue'),
				 $record['CType']
			);
			$out .= '<span class="label label-warning">' . htmlspecialchars($message) . '</span>';
		}

		return $out;
	}


	/**
	* Dedicated method for wrapping a preview header and body HTML.
	*
	* @param string $previewHeader
	* @param string $previewContent
	* @param GridColumnItem $item
	* @return string
	*/
	public function wrapPageModulePreview($previewHeader, $previewContent, GridColumnItem $item): string
	{
			$content = '<span class="exampleContent">' . $previewHeader . $previewContent . '</span>';
			if ($item->isDisabled()) {
				return '<span class="text-muted">' . $content . '</span>';
			}

			return $content;
	}


}
