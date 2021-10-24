<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\Preview;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use B13\Container\Backend\Grid\ContainerGridColumn;
use B13\Container\Backend\Grid\ContainerGridColumnItem;
use B13\Container\Domain\Factory\Exception;
use B13\Container\Domain\Factory\PageView\Backend\ContainerFactory;
use B13\Container\Tca\Registry;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\Grid;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridRow;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class T3sbPreviewRenderer extends StandardContentPreviewRenderer
{

	/**
	* @var Registry
	*/
	protected $tcaRegistry;

	/**
	* @var ContainerFactory
	*/
	protected $containerFactory;

	public function __construct(Registry $tcaRegistry = null, ContainerFactory $containerFactory = null)
	{
		$this->tcaRegistry = $tcaRegistry ?? GeneralUtility::makeInstance(Registry::class);
		$this->containerFactory = $containerFactory ?? GeneralUtility::makeInstance(ContainerFactory::class);
	}


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

		if ($record['header']) {
			$infoArr = [];
			$this->getProcessedValue($item, 'header_position,header_layout,header_link', $infoArr);
			$hiddenHeaderNote = '';
			// If header layout is set to 'hidden', display an accordant note:
			if ($record['header_layout'] == 100) {
				$hiddenHeaderNote = ' <em>[' . htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.6')) . ']</em>';
			}
			$outHeader = $record['date']
				? htmlspecialchars($itemLabels['date'] . ' ' . BackendUtility::date($record['date'])) . '<br />'
				: '';
			$outHeader .= '<strong>' . $this->linkEditContent($this->renderText($record['header']), $record)
				. $hiddenHeaderNote . '</strong><br />';
		}

		if ($record['subheader']) {
			$outHeader .= parent::linkEditContent(parent::renderText($record['subheader']), $record) . '<br />';
		}

		$info = '';
		$contentTypeLabels = $item->getContext()->getContentTypeLabels();
		$contentType = $contentTypeLabels[$record['CType']];

		switch ($record['CType']) {
			case 'tabs_tab':
			 	$info = '<div style="padding:5px; background-color:rgba(86, 61, 124, .5); color:#fff; margin-bottom:5px" >'.$contentType.'</div>';
				break;
			case 'collapsible_accordion':
			 	$info = '<div style="padding:5px; background-color:rgba(86, 61, 124, .5); color:#fff; margin-bottom:5px" >'.$contentType.'</div>';
				break;
			default:
				$info = '<div style="padding:5px; background-color:#563d7c; color:#fff; margin-bottom:5px" >'.$contentType.'</div>';
		}

		return $info.$outHeader;
	}


	public function renderPageModulePreviewContent(GridColumnItem $item): string
	{
		$typo3Version = new Typo3Version();
		if ($typo3Version->getMajorVersion() === 11) {
			$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
			$pageRenderer->loadRequireJsModule(
				 'TYPO3/CMS/T3sbootstrap/Bootstrap',
				 'function() { console.log("Loaded bootstrap.js by t3sbootstrap."); }'
			);
		}
		$content = parent::renderPageModulePreviewContent($item);
		$context = $item->getContext();
		$record = $item->getRecord();
		$grid = GeneralUtility::makeInstance(Grid::class, $context);
		try {
			$container = $this->containerFactory->buildContainer((int)$record['uid']);
		} catch (Exception $e) {
			// not a container
			return $content;
		}

		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
		$flexconf = $flexformService->convertFlexFormContentToArray($record['tx_t3sbootstrap_flexform']);

		if ($record['CType'] == 'two_columns' || $record['CType'] == 'three_columns' || $record['CType'] == 'four_columns' || $record['CType'] == 'six_columns') {
   			if ( $flexconf['equalWidth'] ) {
				$out .= '<br />- Equal Width';
			}
   			if ( $flexconf['horizontalGutters'] ) {
				$out .= '<br />- Horizontal gutters: '.$flexconf['horizontalGutters'];
			}
		}
		if ($record['CType'] == 'two_columns') {
 			if ($flexconf['bgimages']) {
				$out .= '<br />- Has Background-image';
			}
  			if ( $flexconf['colHeight'] ) {
				$out .= '<br />- Min.-Height of the Element: '.$flexconf['colHeight'].'px';
			}
		}
		if ($record['CType'] == 'button_group') {
   			if ( $flexconf['vertical'] ) {
				$out .= '<br />- Vertical: '.$flexconf['vertical'];
			}
   			if ( $flexconf['size'] ) {
				$out .= '<br />- Size: '.$flexconf['size'];
			}
   			if ( $flexconf['align'] ) {
				$out .= '<br />- Align: '.$flexconf['align'];
			}
   			if ( $flexconf['fixedPosition'] ) {
				$out .= '<br />- FixedPosition: '.$flexconf['fixedPosition'];
			}
		}
		if ($record['CType'] == 'collapsible_accordion') {
			$active = $flexconf['active'] ? 'TRUE' : 'FALSE';
			$out .= '<br />- Active: '.$active;
		}
		if ($record['CType'] == 'collapsible_container' && is_string($flexconf['appearance'])) {
			$out .= '<br />- Appearance: '.ucfirst($flexconf['appearance']);
		}
		if ($record['CType'] == 'carousel_container') {
   			if ( $flexconf['width'] ) {
				$out .= '<br />- Width: '.$flexconf['width'].'px';
			}
   			if ( $flexconf['ratio'] ) {
				$out .= '<br />- Aspect ratio: '.$flexconf['ratio'];
			}
   			if ( $flexconf['interval'] ) {
				$out .= '<br />- Interval: '.$flexconf['interval'];
			}
   			if ( $flexconf['link'] ) {
				$out .= '<br />- Link: '.$flexconf['link'];
			}
   			if ( $flexconf['owlCarousel'] ) {
				$out .= '<br />- OWL Carousel';
				$out .= '<br />- OWL Style: '.$flexconf['owlStyle'];
				if ($flexconf['owlStyle'] == 1)
				$out .= '<br />- OWL Line: '.$flexconf['owlLine'];
			}
   			if ( $flexconf['multislider'] ) {
				$out .= '<br />- Multislider';
			}
   			if ( $flexconf['zoom'] ) {
				$out .= '<br />- Zoom';
			}
		}
		if ($record['CType'] == 'swiper_container') {
			if ( $flexconf['width'] ) {
				$out .= '<br />- Width: '.$flexconf['width'].'px';
			}
			if ( $flexconf['ratio'] ) {
				$out .= '<br />- Aspect ratio: '.$flexconf['ratio'];
			}
			if ( $flexconf['slidesPerView'] ) {
				$out .= '<br />- Slides per view: '.$flexconf['slidesPerView'];
			}
			if ( $flexconf['delay'] ) {
				$out .= '<br />- Delay between transitions (in ms): '.$flexconf['delay'];
			}
		}
		if ($record['CType'] == 'card_wrapper') {
			$out .= '<br />- Wrapper: Card '.$flexconf['card_wrapper'];
			if ( $flexconf['visibleCards'] ) {
				$out .= '<br />- Visible Cards: '.$flexconf['visibleCards'];
			}
			if ( $flexconf['interval'] ) {
				$out .= '<br />- Interval: '.$flexconf['interval'];
			}
		}
		if ($record['CType'] == 'background_wrapper') {
			if ( $flexconf['bgwlink'] ) {
				$out .= '<br />- Link the entire Content Element';
			}
			if ( $flexconf['origImage'] ) {
				$out .= '<br />- Image inside (no background-image but a real image)';
			}
			if ( $flexconf['bgAttachmentFixed'] ) {
				$out .= '<br />- Background-attachment - fixed';
			}
			if ( $flexconf['enableAutoheight'] ) {
				$out .= '<br />- Enable content overlay and autoheight for background-image';
			}
			if ( $flexconf['paddingTopBottom'] ) {
				$out .= '<br />- Padding (top and bottom ): '.$flexconf['paddingTopBottom'].' rem';
			}
			if ( $flexconf['noMediaPaddingTopBottom'] ) {
				$out .= '<br />- Padding (top and bottom ): '.$flexconf['noMediaPaddingTopBottom'].' rem';
			}
			if ( $flexconf['imgGrayscale'] ) {
				$out .= '<br />- Grayscale: '.$flexconf['imgGrayscale'];
			}
			if ( $flexconf['imgSepia'] ) {
				$out .= '<br />- Sepia: '.$flexconf['imgSepia'];
			}
			if ( $flexconf['imgOpacity'] ) {
				$out .= '<br />- Opacity: '.$flexconf['imgOpacity'];
			}
			if ( $flexconf['imageRaster'] ) {
				$out .= '<br />- Raster over the image/color';
			}
		}
		if ($record['CType'] == 'parallax_wrapper') {
			if ( $flexconf['speedFactor'] ) {
				$out .= '<br />- Speed Factor: '.$flexconf['speedFactor'];
			}
			if ( $flexconf['addHeight'] ) {
				$out .= '<br />- Surcharge height to parallax view: '.$flexconf['addHeight'].'px';
			}
			if ( $flexconf['mobile'] ) {
				$out .= '<br />- Disable on mobile devices!';
			}
		}
		if ($record['CType'] === 'modal') {

			if ( $flexconf['animation'] ) {
				$out .= '<br />- Animation';
			}
			if ( $flexconf['size'] ) {
				$out .= '<br />- Size: '.$flexconf['size'];
			}
			if ( $flexconf['buttonText'] ) {
				$out .= '<br />- Button Text: '.$flexconf['buttonText'];
			}
			if ( $flexconf['button'] ) {
				$out .= '<br />- Button: '.$flexconf['button'];
			}
			if ( $flexconf['style'] ) {
				$out .= '<br />- Style: '.$flexconf['style'];
			}
			if ( $flexconf['fixedPosition'] ) {
				$out .= '<br />- Fixed Position';
			}
		}
		if ($record['CType'] == 'tabs_container') {
			if ( $flexconf['display_type'] && is_string($flexconf['display_type']) ) {
				$out .= '<br />- Appearance: '.ucfirst($flexconf['display_type']);
			}
			if ( $flexconf['switch_effect'] ) {
				$out .= '<br />- Fade effect: '.$flexconf['switch_effect'];
			}
			if ( $flexconf['fill'] ) {
				$out .= '<br />- Fill and justify: '.$flexconf['fill'];
			}
		}
		if ($record['CType'] == 'masonry_wrapper') {
			$colclass = $flexconf['colclass'] ? $flexconf['colclass'] : 'col-sm-6 col-lg-4 mb-4';
			$out .= '<br />- Colclass: '.$colclass;
		}

		$flexconfOut = '';
		if ($out)
		$flexconfOut .= parent::linkEditContent('<div>'.substr($out, 6).'</div>', $record);

		$containerGrid = $this->tcaRegistry->getGrid($record['CType']);

		foreach ($containerGrid as $row => $cols) {
			$rowObject = GeneralUtility::makeInstance(GridRow::class, $context);
			foreach ($cols as $col) {
				$columnObject = GeneralUtility::makeInstance(ContainerGridColumn::class, $context, $col, $container);
				$rowObject->addColumn($columnObject);
				if (isset($col['colPos'])) {
					$records = $container->getChildrenByColPos($col['colPos']);
					foreach ($records as $contentRecord) {
						$columnItem = GeneralUtility::makeInstance(ContainerGridColumnItem::class, $context, $columnObject, $contentRecord, $container);
						$columnObject->addItem($columnItem);
					}
				}
			}
			$grid->addRow($rowObject);
		}

		$gridTemplate = $this->tcaRegistry->getGridTemplate($record['CType']);

		$view = GeneralUtility::makeInstance(StandaloneView::class);
		$view->setPartialRootPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:container/Resources/Private/Partials/']);
		$view->setTemplatePathAndFilename($gridTemplate);

		$view->assign('hideRestrictedColumns', (bool)(BackendUtility::getPagesTSconfig($context->getPageId())['mod.']['web_layout.']['hideRestrictedCols'] ?? false));
		$view->assign('newContentTitle', $this->getLanguageService()->getLL('newContentElement'));
		$view->assign('newContentTitleShort', $this->getLanguageService()->getLL('content'));
		$view->assign('allowEditContent', $this->getBackendUser()->check('tables_modify', 'tt_content'));
		$view->assign('containerGrid', $grid);
		$view->assign('defaultRecordDirectory', $this->hasDefaultDirectory() ? 'RecordDefault' : 'Record');

		$rendered = $view->render();

		$media = $record['assets'] ?: $record['image'];
		if ($media) {
			$field = $record['assets'] ? 'assets' : 'image';
			$flexconfOut .= '<div style="margin:5px">'. parent::linkEditContent($this->getThumbCodeUnlinked($record, 'tt_content', $field), $record) . '</div>';
		}

		$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

		if ($extconf['previewClosedCollapsible']) {
			$newContent = '<p><a class="collapsed" style="color:#c7254e" data-toggle="collapse" href="#collapseContainer-'.$record['uid'].'" role="button" aria-expanded="false" aria-controls="collapseContainer-'.$record['uid'].'">
				<span class="icon icon-size-small icon-state-default">
					<span class="icon-markup">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><g class="icon-color"><path d="M7.593 11.43L3.565 5.79A.5.5 0 0 1 3.972 5h8.057a.5.5 0 0 1 .407.791l-4.028 5.64a.5.5 0 0 1-.815-.001z"/></g></svg>
					</span>
				</span>
			</a></p>
			<div class="collapse" id="collapseContainer-'.$record['uid'].'">'.$rendered.'</div>';
		} else {
			$newContent = '<p><a style="color:#c7254e" data-toggle="collapse" href="#collapseContainer-'.$record['uid'].'" role="button" aria-expanded="true" aria-controls="collapseContainer-'.$record['uid'].'">
				<span class="icon icon-size-small icon-state-default">
					<span class="icon-markup">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><g class="icon-color"><path d="M7.593 11.43L3.565 5.79A.5.5 0 0 1 3.972 5h8.057a.5.5 0 0 1 .407.791l-4.028 5.64a.5.5 0 0 1-.815-.001z"/></g></svg>
					</span>
				</span>
			</a></p>
			<div class="collapse in show" id="collapseContainer-'.$record['uid'].'">'.$rendered.'</div>';
		}

		return $flexconfOut.$newContent;
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
			$content = '<span class="exampleContent" style="background-color: rgba(86, 61, 124, .1); display: block; padding:5px">' . $previewHeader . $previewContent . '</span>';
			if ($item->isDisabled()) {
				 return '<span class="text-muted">' . $content . '</span>';
			 }

			 return $content;
	}


	/**
	 * Check TYPO3 version to see whether the default record templates
	 * are located in RecordDefault/ instead of Record/.
	 * See: https://review.typo3.org/c/Packages/TYPO3.CMS/+/69769
	 *
	 * @return bool
	 */
	protected function hasDefaultDirectory(): bool
	{
		$typo3Version = new Typo3Version();

		if ($typo3Version->getMajorVersion() === 10) {
			return version_compare((new Typo3Version())->getVersion(), '10.4.17', '>');
		}

		if ($typo3Version->getMajorVersion() === 11) {
			return version_compare((new Typo3Version())->getVersion(), '11.3.0', '>');
		}

		return false;
	}

}
