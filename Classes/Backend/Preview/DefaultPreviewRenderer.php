<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\Preview;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class DefaultPreviewRenderer extends StandardContentPreviewRenderer
{

	/**
	* Dedicated method for rendering preview header HTML for
	* the page module only. Receives $item which is an instance of
	* GridColumnItem which has a getter method to return the record.
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
			if ( !empty($record['tx_t3sbootstrap_cardheader']) ) {
				 $outHeader .= parent::linkEditContent(parent::renderText($record['tx_t3sbootstrap_cardheader']), $record) . '<br />';
			}
		}
		if (!empty($record['header'])) {
			$infoArr = [];
			parent::getProcessedValue($item, 'header_position,header_layout,header_link', $infoArr);
			$hiddenHeaderNote = '';
			// If header layout is set to 'hidden', display an accordant note:
			if ($record['header_layout'] == 100) {
				$hiddenHeaderNote = ' <em>[' . htmlspecialchars(parent::getLanguageService()
				->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.6')) . ']</em>';
			}
			$outHeader .= $record['date']
				? htmlspecialchars($itemLabels['date'] . ' ' . BackendUtility::date($record['date'])) . '<br />'
				: '';
			$outHeader .= '<strong>' . parent::linkEditContent(parent::renderText($record['header']), $record)
				. $hiddenHeaderNote . '</strong><br />';
		}


		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
		$flexconf = $flexformService->convertFlexFormContentToArray($record['tx_t3sbootstrap_flexform']);

		$flexconfSettings = '';

		if ($record['CType'] === 't3sbs_card') {
			if (!empty($flexconf['effect'])) {
				$flexconfSettings .= 'hover_effect=1, ';
			}
			if (!empty($flexconf['title']['onTop'])) {
				$flexconfSettings .= 'title_onTop=1, ';
			}
			if (!empty($flexconf['button']['enable'])) {
				$flexconfSettings .= 'button_link=1, ';
			}
			if (!empty($flexconf['cardborder'])) {
				$flexconfSettings .= 'border=1, ';
			}
		}

		if ($record['CType'] === 't3sbs_button') {

			if ( !empty($flexconf['style']) ) {
				$flexconfSettings .= 'style='.$flexconf['style'].', ';
			}
			if ( !empty($flexconf['size']) ) {
				$flexconfSettings .= 'size='.$flexconf['size'].', ';
			}
			if ( !empty($flexconf['block']) ) {
				$flexconfSettings .= 'block='.$flexconf['block'].', ';
			}
			if ( !empty($flexconf['outline']) ) {
				$flexconfSettings .= 'outline=1, ';
			}
		}

		if ($record['CType'] == 't3sbs_gallery') {
			$flexconfSettings .= 'columns='.$record['imagecols'].', ';
			if ( !empty($record['tx_t3sbootstrap_image_ratio'])) {
				$flexconfSettings .= 'ratio='.$record['tx_t3sbootstrap_image_ratio'].', ';
			}
			if ( !empty($record['file_collections'])) {
				$flexconfSettings .= 'file_collection_uids='.$record['file_collections'].', ';
			}
		}

		$flexconfOut = '';
		
		if ($record['CType'] === 't3sbs_carousel') {
			if ( !empty($flexconf['shift']) ) {
				$flexconfOut .= 'shift='.$flexconf['shift'].', ';
			}
			if ( !empty($flexconf['bgOverlay']) ) {
				$flexconfOut .= 'bgOverlay=1, ';
			}
		}

		if ($record['CType'] === 't3sbs_toast') {
			if ( !empty($flexconf['animation']) ) {
				$flexconfOut .= 'animation=1, ';
			}
			if ( !empty($flexconf['autohide']) ) {
				$flexconfOut .= 'autohide=1';
			}
			if ( !empty($flexconf['delay']) ) {
				$flexconfOut .= 'delay='.$flexconf['delay'].', ';
			}
			if ( !empty($flexconf['placement']) ) {
				$flexconfOut .= 'placement='.$flexconf['placement'].', ';
			}
			if ( !empty($flexconf['cookie']) ) {
				$flexconfOut .= 'cookie ('.$flexconf['expires'].') days, ';
			}
		}

		if (!empty($flexconfSettings)) {
			$flexconfSettings = '<div style="padding:1rem;color:#7e00b4;border: 1px solid #7e00b4;display:block;margin-bottom:.75rem;"><strong>Settings:</strong> ' . rtrim($flexconfSettings, ', ') . '</div>';
		}

		return $flexconfSettings.$outHeader;
	}


	/**
	* Dedicated method for rendering preview body HTML for
	* the page module only.
	*/
	public function renderPageModulePreviewContent(GridColumnItem $item): string
	{

		$preview = '';
		$record = $item->getRecord();
		$content = parent::renderPageModulePreviewContent($item);

		if ( ($content && $record['CType'] === 'list')
			|| ($content && str_starts_with($record['CType'], 'menu'))
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
		$contentType = $contentTypeLabels[$record['CType']];
		if (!empty($contentType)) {
			if (!empty($record['subheader'])) {
				$preview .= parent::linkEditContent(parent::renderText($record['subheader']), $record) . '<br />';
			}
			if (!empty($record['bodytext'])) {
				$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
				$maxCharacters = $extconf['previewCropMaxCharacters'];
				$append = ' ...';
				if (!empty($extconf['previewCrop'])) {
					$contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
					$text = $contentObject->cropHTML($record['bodytext'], $maxCharacters . '|' . $append . '|' . true);
				} else {
					$text = $record['bodytext'];
				}
				$preview .= parent::linkEditContent(parent::renderText($text), $record);
			}

			$media = !empty($record['assets']) ?: $record['image'];
			if ($media) {
				$field = !empty($record['assets']) ? 'assets' : 'image';
				$preview .= '<div style="margin:5px 5px 5px 0">'.
				 parent::linkEditContent($this->getThumbCodeUnlinked($record, 'tt_content', $field), $record) . '</div>';
			}

		} else {
			$languageService = parent::getLanguageService();
			$message = sprintf(
				$languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.noMatchingValue'),
				$record['CType']
			);
			$preview .= '<span class="label label-warning">' . htmlspecialchars($message) . '</span>';
		}

		return $preview;
	}


	/**
	* Dedicated method for wrapping a preview header and body HTML.
	*/
	public function wrapPageModulePreview(string $previewHeader, string $previewContent, GridColumnItem $item): string
	{
			$content = '<span class="exampleContent">' . $previewHeader . $previewContent . '</span>';
			if (!empty($item->isDisabled())) {
				return '<span class="text-muted">' . $content . '</span>';
			}

			return $content;
	}
}
