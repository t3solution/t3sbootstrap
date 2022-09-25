<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\Preview;

use B13\Container\Backend\Grid\ContainerGridColumn;
use B13\Container\Backend\Grid\ContainerGridColumnItem;
use B13\Container\ContentDefender\ContainerColumnConfigurationService;
use B13\Container\Domain\Factory\Exception;
use B13\Container\Domain\Factory\PageView\Backend\ContainerFactory;
use B13\Container\Tca\Registry;
use B13\Container\Domain\Service\ContainerService;
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

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
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

    /**
     * @var ContainerColumnConfigurationService
     */
    protected $containerColumnConfigurationService;

    /**
     * @var ContainerService
     */
    protected $containerService;


	public function __construct(
		Registry $tcaRegistry,
		ContainerFactory $containerFactory,
		ContainerColumnConfigurationService $containerColumnConfigurationService,
		ContainerService $containerService
	)
	{
		$this->tcaRegistry = $tcaRegistry;
		$this->containerFactory = $containerFactory;
		$this->containerColumnConfigurationService = $containerColumnConfigurationService;
		$this->containerService = $containerService;
	}


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

		if ( !empty($record['header']) ) {
			$infoArr = [];
			parent::getProcessedValue($item, 'header_position,header_layout,header_link', $infoArr);
			$hiddenHeaderNote = '';
			// If header layout is set to 'hidden', display an accordant note:
			if ($record['header_layout'] == 100) {
				$hiddenHeaderNote = ' <em>[' . htmlspecialchars(parent::getLanguageService()
				->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.6')) . ']</em>';
			}
			$outHeader = $record['date']
				? htmlspecialchars($itemLabels['date'] . ' ' . BackendUtility::date($record['date'])) . '<br />'
				: '';
			$outHeader .= '<strong>' . $this->linkEditContent(parent::renderText($record['header']), $record)
				. $hiddenHeaderNote . '</strong><br />';
		}

		if ( !empty($record['subheader']) ) {
			$outHeader .= parent::linkEditContent(parent::renderText($record['subheader']), $record) . '<br />';
		}

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
		$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
		if ($typo3Version->getMajorVersion() === 11) {
			$pageRenderer->loadRequireJsModule(
				 'TYPO3/CMS/T3sbootstrap/Bootstrap',
				 'function() { console.log("Loaded bootstrap.js by t3sbootstrap!"); }'
			);
			$pageRenderer->addCssFile('EXT:t3sbootstrap/Resources/Public/Backend/bestyles-v11.css');
		} else {
			$pageRenderer->addCssFile('EXT:t3sbootstrap/Resources/Public/Backend/bestyles-v10.css');
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

		$out = '';
		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
		$flexconf = $flexformService->convertFlexFormContentToArray($record['tx_t3sbootstrap_flexform']);

		if ($record['CType'] == 'two_columns' || $record['CType'] == 'three_columns' || $record['CType'] == 'four_columns'
		 || $record['CType'] == 'six_columns' || $record['CType'] == 'row_columns') {

			if ($record['CType'] == 'row_columns') {
				if (!empty($flexconf['xs_rowclass'])) {
					$out .= '<br />- row-cols-*: '.$flexconf['xs_rowclass'];
				}
				if (!empty($flexconf['sm_rowclass'])) {
					$out .= '<br />- row-cols-sm: '.$flexconf['sm_rowclass'];
				}
				if (!empty($flexconf['md_rowclass'])) {
					$out .= '<br />- row-cols-md: '.$flexconf['md_rowclass'];
				}
				if (!empty($flexconf['lg_rowclass'])) {
					$out .= '<br />- row-cols-lg: '.$flexconf['lg_rowclass'];
				}
				if (!empty($flexconf['xl_rowclass'])) {
					$out .= '<br />- row-cols-xl: '.$flexconf['xl_rowclass'];
				}
				if (!empty($flexconf['xxl_rowclass'])) {
					$out .= '<br />- row-cols-xxl: '.$flexconf['xxl_rowclass'];
				}
			} else {
					if ( !empty($flexconf['equalWidth']) ) {
					$out .= '<br />- Equal Width';
				}
			}
   			if ( !empty($flexconf['horizontalGutters'])) {
				$out .= '<br />- Horizontal gutters: '.$flexconf['horizontalGutters'];
			}
   			if ( !empty($flexconf['verticalGutters'])) {
				$out .= '<br />- Vertical gutters: '.$flexconf['verticalGutters'];
			}

		}
		if ($record['CType'] == 'two_columns') {
 			if ( !empty($flexconf['bgimages']) ) {
				$out .= '<br />- Has Background-image';
			}
  			if ( !empty($flexconf['colHeight']) ) {
				$out .= '<br />- Min.-Height of the Element: '.$flexconf['colHeight'].'px';
			}
		}
		if ($record['CType'] == 'button_group') {
   			if ( !empty($flexconf['vertical']) ) {
				$out .= '<br />- Vertical: '.$flexconf['vertical'];
			}
   			if ( !empty($flexconf['size']) ) {
				$out .= '<br />- Size: '.$flexconf['size'];
			}
   			if ( !empty($flexconf['align']) ) {
				$out .= '<br />- Align: '.$flexconf['align'];
			}
   			if ( !empty($flexconf['fixedPosition']) ) {
				$out .= '<br />- FixedPosition: '.$flexconf['fixedPosition'];
			}
		}
		if ($record['CType'] == 'collapsible_accordion') {
			$active = !empty($flexconf['active']) ? 'TRUE' : 'FALSE';
			$out .= '<br />- Active: '.$active;
		}
		if ($record['CType'] == 'collapsible_container' && is_string($flexconf['appearance'])) {
			$out .= '<br />- Appearance: '.ucfirst($flexconf['appearance']);
		}
		if ($record['CType'] == 'carousel_container') {
   			if ( !empty($flexconf['width']) ) {
				$out .= '<br />- Width: '.$flexconf['width'].'px';
			}
   			if ( !empty($flexconf['ratio']) ) {
				$out .= '<br />- Aspect ratio: '.$flexconf['ratio'];
			}
   			if ( !empty($flexconf['interval']) ) {
				$out .= '<br />- Interval: '.$flexconf['interval'];
			}
   			if ( !empty($flexconf['link']) ) {
				$out .= '<br />- Link: '.$flexconf['link'];
			}
   			if ( !empty($flexconf['zoom']) ) {
				$out .= '<br />- Zoom';
			}
		}
		if ($record['CType'] == 'swiper_container') {
			if ( !empty($flexconf['width']) ) {
				$out .= '<br />- Width: '.$flexconf['width'].'px';
			}
			if ( !empty($flexconf['ratio']) ) {
				$out .= '<br />- Aspect ratio: '.$flexconf['ratio'];
			}
			if ( !empty($flexconf['slidesPerView']) ) {
				$out .= '<br />- Slides per view: '.$flexconf['slidesPerView'];
			}
			if ( !empty($flexconf['delay']) ) {
				$out .= '<br />- Delay between transitions (in ms): '.$flexconf['delay'];
			}
		}
		if (!empty($flexconf['card_wrapper']) && $record['CType'] == 'card_wrapper') {
			$out .= '<br />- Wrapper: Card '.$flexconf['card_wrapper'];
			if ( !empty($flexconf['visibleCards']) ) {
				$out .= '<br />- Visible Cards: '.$flexconf['visibleCards'];
			}
			if ( !empty($flexconf['interval']) ) {
				$out .= '<br />- Interval: '.$flexconf['interval'];
			}
		}
		if ($record['CType'] == 'background_wrapper') {
			if ( !empty($flexconf['bgwlink']) ) {
				$out .= '<br />- Link the entire Content Element';
			}
			if ( !empty($flexconf['origImage']) ) {
				$out .= '<br />- Image inside (no background-image but a real image)';
			}
			if ( !empty($flexconf['bgAttachmentFixed']) ) {
				$out .= '<br />- Background-attachment - fixed';
			}
			if ( !empty($flexconf['enableAutoheight']) ) {
				$out .= '<br />- Enable content overlay and autoheight for background-image';
			}
			if ( !empty($flexconf['paddingTopBottom']) ) {
				$out .= '<br />- Padding (top and bottom ): '.$flexconf['paddingTopBottom'].' rem';
			}
			if ( !empty($flexconf['noMediaPaddingTopBottom']) ) {
				$out .= '<br />- Padding (top and bottom ): '.$flexconf['noMediaPaddingTopBottom'].' rem';
			}
			if ( !empty($flexconf['imgGrayscale']) ) {
				$out .= '<br />- Grayscale: '.$flexconf['imgGrayscale'];
			}
			if ( !empty($flexconf['imgSepia']) ) {
				$out .= '<br />- Sepia: '.$flexconf['imgSepia'];
			}
			if ( !empty($flexconf['imgOpacity']) ) {
				$out .= '<br />- Opacity: '.$flexconf['imgOpacity'];
			}
			if ( !empty($flexconf['imageRaster']) ) {
				$out .= '<br />- Raster over the image/color';
			}
		}
		if ($record['CType'] == 'parallax_wrapper') {
			if ( !empty($flexconf['speedFactor']) ) {
				$out .= '<br />- Speed Factor: '.$flexconf['speedFactor'];
			}
			if ( !empty($flexconf['addHeight']) ) {
				$out .= '<br />- Surcharge height to parallax view: '.$flexconf['addHeight'].'px';
			}
			if ( !empty($flexconf['mobile']) ) {
				$out .= '<br />- Disable on mobile devices!';
			}
		}
		if ($record['CType'] === 'modal') {

			if ( !empty($flexconf['animation']) ) {
				$out .= '<br />- Animation';
			}
			if ( !empty($flexconf['size']) ) {
				$out .= '<br />- Size: '.$flexconf['size'];
			}
			if ( !empty($flexconf['buttonText']) ) {
				$out .= '<br />- Button Text: '.$flexconf['buttonText'];
			}
			if ( !empty($flexconf['button']) ) {
				$out .= '<br />- Button: '.$flexconf['button'];
			}
			if ( !empty($flexconf['style']) ) {
				$out .= '<br />- Style: '.$flexconf['style'];
			}
			if ( !empty($flexconf['fixedPosition']) ) {
				$out .= '<br />- Fixed Position';
			}
		}
		if ($record['CType'] == 'tabs_container') {
			if ( !empty($flexconf['display_type']) && is_string($flexconf['display_type']) ) {
				$out .= '<br />- Appearance: '.ucfirst($flexconf['display_type']);
			}
			if ( !empty($flexconf['switch_effect']) ) {
				$out .= '<br />- Fade effect: '.$flexconf['switch_effect'];
			}
			if ( !empty($flexconf['fill']) ) {
				$out .= '<br />- Fill and justify: '.$flexconf['fill'];
			}
		}
		if ($record['CType'] == 'masonry_wrapper') {
			$colclass = !empty($flexconf['colclass']) ? $flexconf['colclass'] : 'col-sm-6 col-lg-4 mb-4';
			$out .= '<br />- Colclass: '.$colclass;
		}

			if ($record['CType'] === 'toast_container') {
				if ( !empty($flexconf['animation']) ) {
					$out .= '<br />- Animation';
				}
				if ( !empty($flexconf['autohide']) ) {
					$out .= '<br />- Autohide';
				}
				if ( !empty($flexconf['delay']) ) {
					$out .= '<br />- Delay: '.$flexconf['delay'];
				}
				if ( !empty($flexconf['placement']) ) {
					$out .= '<br />- Placement: '.$flexconf['placement'];
				}
				if ( !empty($flexconf['cookie']) ) {
					$out .= '<br />- Cookie is set ('.$flexconf['expires'].') days';
				}
			}

		$flexconfOut = '';
		if ($out)
		$flexconfOut .= parent::linkEditContent('<div>'.substr($out, 6).'</div>', $record);

		$containerGrid = $this->tcaRegistry->getGrid($record['CType']);

		foreach ($containerGrid as $row => $cols) {
			$rowObject = GeneralUtility::makeInstance(GridRow::class, $context);
			foreach ($cols as $col) {
				$newContentElementAtTopTarget = $this->containerService->getNewContentElementAtTopTargetInColumn($container, $col['colPos']);
                if ($this->containerColumnConfigurationService->isMaxitemsReached($container, $col['colPos'])) {
                    $columnObject = GeneralUtility::makeInstance(ContainerGridColumn::class, $context, $col, $container, $newContentElementAtTopTarget, false);
                } else {
                    $columnObject = GeneralUtility::makeInstance(ContainerGridColumn::class, $context, $col, $container, $newContentElementAtTopTarget);
                }
				$rowObject->addColumn($columnObject);
				if (!empty($col['colPos'])) {
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
		$view->assign('newContentTitle', parent::getLanguageService()->getLL('newContentElement'));
		$view->assign('newContentTitleShort', parent::getLanguageService()->getLL('content'));
		$view->assign('allowEditContent', parent::getBackendUser()->check('tables_modify', 'tt_content'));
		$view->assign('containerGrid', $grid);
		$view->assign('defaultRecordDirectory', $this->hasDefaultDirectory() ? 'RecordDefault' : 'Record');

		$rendered = $view->render();

		$media = !empty($record['assets']) ?: $record['image'];
		if ($media) {
			$field = !empty($record['assets']) ? 'assets' : 'image';
			$flexconfOut .= '<div style="margin:5px">'. parent::linkEditContent(parent::getThumbCodeUnlinked($record, 'tt_content', $field), $record) . '</div>';
		}

		$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
		$typo3Version = new Typo3Version();
		$show = $typo3Version->getMajorVersion() == 10 ? 'in' : 'show';

		if ($extconf['previewClosedCollapsible']) {
			$newContent = '<p style="margin-top:8px;margin-left:5px"><a data-toggle="collapse" href="#collapseContainer-'.$record['uid'].'" role="button" aria-expanded="false" aria-controls="collapseContainer-'.$record['uid'].'"><span class="icon icon-size-small icon-state-default"><span class="icon-markup"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><g class="icon-color" style="color:red"><path d="M7.593 11.43L3.565 5.79A.5.5 0 0 1 3.972 5h8.057a.5.5 0 0 1 .407.791l-4.028 5.64a.5.5 0 0 1-.815-.001z"/></g></svg></span></span></a></p>
<div class="collapse" id="collapseContainer-'.$record['uid'].'"><div class="card card-body p-3">'.$rendered.'</div></div>';
		} else {
			$newContent = '<p style="margin-top:8px;margin-left:5px"><a data-toggle="collapse" href="#collapseContainer-'.$record['uid'].'" role="button" aria-expanded="true" aria-controls="collapseContainer-'.$record['uid'].'"><span class="icon icon-size-small icon-state-default"><span class="icon-markup"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><g class="icon-color" style="color:red"><path d="M7.593 11.43L3.565 5.79A.5.5 0 0 1 3.972 5h8.057a.5.5 0 0 1 .407.791l-4.028 5.64a.5.5 0 0 1-.815-.001z"/></g></svg></span></span></a></p>
<div class="collapse '.$show.'" id="collapseContainer-'.$record['uid'].'"><div class="card card-body p-3">'.$rendered.'</div></div>';
		}

		return $flexconfOut.$newContent;
	}


	/**
	 * Dedicated method for wrapping a preview header and body HTML.
	 */
	public function wrapPageModulePreview(string $previewHeader, string $previewContent, GridColumnItem $item): string
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
