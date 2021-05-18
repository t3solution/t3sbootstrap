<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use T3SBS\T3sbootstrap\Helper\ClassHelper;
use T3SBS\T3sbootstrap\Helper\StyleHelper;
use T3SBS\T3sbootstrap\Helper\DefaultHelper;
use T3SBS\T3sbootstrap\Helper\GalleryHelper;
use T3SBS\T3sbootstrap\Helper\WrapperHelper;
use T3SBS\T3sbootstrap\Helper\GridHelper;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\FileRepository;


class BootstrapProcessor implements DataProcessorInterface
{
	/**
	 * Process data
	 *
	 * @param ContentObjectRenderer $cObj The data of the content element or page
	 * @param array $contentObjectConfiguration The configuration of Content Object
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
	 * @return array the processed data as key/value store
	 */
	public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration,	 array $processedData)
	{
		$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
		$flexconf = $flexFormService->convertFlexFormContentToArray($processedData['data']['tx_t3sbootstrap_flexform']);
		$parentflexconf = [];
		$parentUid = $processedData['data']['tx_container_parent'];
		if ($parentUid) {
			$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
			$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
			$statement = $queryBuilder
				->select('CType', 'tx_t3sbootstrap_flexform', 'tx_container_parent')
				->from('tt_content')
				->where(
					$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($parentUid, \PDO::PARAM_INT))
				)
				->execute()
				->fetch();

			$parentCType = $statement['CType'];
			$parentflexconf = $flexFormService->convertFlexFormContentToArray($statement['tx_t3sbootstrap_flexform']);
			$parentContainer = $statement['tx_container_parent'];
		}

		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

		$defaultHelper = GeneralUtility::makeInstance(DefaultHelper::class);
		$classHelper = GeneralUtility::makeInstance(ClassHelper::class);
		$styleHelper = GeneralUtility::makeInstance(StyleHelper::class);
		$wrapperHelper = GeneralUtility::makeInstance(WrapperHelper::class);

		# used for js-conditions
		$processedData['winWidth'] = (int)$processorConfiguration['breakpoint.'][$contentObjectConfiguration['settings.']['breakpoint']];

		// class
		$class = $classHelper->getAllClass($processedData['data'], $flexconf, $extConf);
		$processedData['class'] = $processedData['class'] ? $processedData['class'].' '.$class : $class;
		// header class
		$processedData['header'] = $classHelper->getHeaderClass($processedData['data']);
		// style
		$style = $styleHelper->getBgColor($processedData['data']);
		$processedData['style'] = $processedData['style'] ? $processedData['style'].' '.$style : $style;


		/**
		 * Grid System
		 */
		if ( $processedData['data']['CType'] == 'two_columns'
		 || $processedData['data']['CType'] == 'three_columns'
		 || $processedData['data']['CType'] == 'four_columns'
		 || $processedData['data']['CType'] == 'six_columns' ) {

			$gridHelper = GeneralUtility::makeInstance(GridHelper::class);
			$processedData = $gridHelper->getGrid($processedData, $flexconf);
			$processedData['style'] .= $flexconf['colHeight'] ? ' min-height: '.$flexconf['colHeight'].'px;' : '';
			$processedData['verticalAlign'] = $flexconf['colHeight']
			 && $flexconf['verticalAlign'] ? ' d-flex align-items-' . $flexconf['verticalAlign'] : '';
			// w/ background-image
			if ( $processedData['data']['CType'] == 'two_columns' && $flexconf['bgimages']) {
				$bgimages = $this->getBackgroundImageUtility()
					->getBgImage($processedData['data']['uid'], 'tt_content', FALSE, FALSE,
					 $flexconf, FALSE, $processedData['data']['uid'], $extConf['webp']);
				if ($bgimages) {
					$processedData['bgimages'] = $bgimages;
					$processedData['bgimagePosition'] = $flexconf['bgimagePosition'];
				}
			}
			$processedData['isTxContainer'] = TRUE;
		}

		/**
		 * Card Wrapper
		 */
		if ( $processedData['data']['CType'] == 'card_wrapper' ) {
			$processedData = $wrapperHelper->getCardWrapper($processedData, $flexconf);
			$processedData['isTxContainer'] = TRUE;
		}

		/**
		 * Button group
		 */
		if ( $processedData['data']['CType'] == 'button_group' ) {
			$processedData['class'] .= $flexconf['vertical'] ? ' btn-group-vertical' : ' btn-group';
			$processedData['buttonGroupClass'] = $flexconf['align'] ? ' '.$flexconf['align'] : '';
			if ( $flexconf['fixedPosition'] ) {
				$processedData['buttonGroupClass'] .= ' d-none fixedGroupButton fixedPosition fixedPosition-'.$flexconf['fixedPosition'];
				$processedData['class'] .= $flexconf['rotate'] ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
				$processedData['class'] .= $flexconf['vertical'] ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
			}
			$processedData['isTxContainer'] = TRUE;
		}

		/**
		 * Background Wrapper
		 */
		if ( $processedData['data']['CType'] == 'background_wrapper') {
			$processedData = $wrapperHelper->getBackgroundWrapper($processedData, $flexconf,
				 $contentObjectConfiguration['settings.']['cdnEnable'], $extConf['webp']);
			$processedData['isTxContainer'] = TRUE;
		}

		/**
		 * Parallax Wrapper
		 */
		if ( $processedData['data']['CType'] == 'parallax_wrapper' && $processedData['data']['assets'] ) {
			$processedData = $wrapperHelper->getParallaxWrapper($processedData, $flexconf, $extConf['webp']);
			$processedData['isTxContainer'] = TRUE;
		}

		/**
		 * Carousel container
		 */
		if ( $processedData['data']['CType'] == 'carousel_container' ) {
			$processedData = $wrapperHelper->getCarouselContainer($processedData, $flexconf);
			$processedData['isTxContainer'] = TRUE;
			if ($flexconf['zoom']) {
				$processedData['lightBox'] = TRUE;
			}
			if ( $processorConfiguration['carouselFiles'] ) {
				$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
				$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
				$statement = $queryBuilder
					->select('uid')
					->from('tt_content')
					->where(
						$queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($processedData['data']['uid'], \PDO::PARAM_INT))
					)
					->execute()
					->fetchAll();
				$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
				foreach($statement as $element) {
					$filesFromRepository[$element['uid']] = $fileRepository->findByRelation('tt_content', 'image', $element['uid']);
				}
				$processedData['carouselSlides'] = $filesFromRepository;
			}
		}

		/**
		 * Collapse Container
		 */
		if ( $processedData['data']['CType'] == 'collapsible_container' ) {
			$processedData['appearance'] = $flexconf['appearance'];
			$processedData['isTxContainer'] = TRUE;
		}

		/**
		 * Collapsible
		 */
		if ( $processedData['data']['CType'] == 'collapsible_accordion' ) {
			$processedData = $wrapperHelper->getCollapsible($processedData, $flexconf, $parentflexconf);
			$processedData['isTxContainer'] = TRUE;
		}

		/**
		 * Modal
		 */
		if ( $processedData['data']['CType'] == 'modal' ) {
			$processedData['modal']['animation'] = $flexconf['animation'];
			$processedData['modal']['size'] = $flexconf['size'];
			$processedData['modal']['button'] = $flexconf['button'];
			$processedData['modal']['style'] = $flexconf['style'];
			if ( $flexconf['buttonText'] ) {
				$processedData['modal']['buttonText'] = $flexconf['buttonText'];
			} elseif ( $processedData['data']['header'] ) {
				$processedData['modal']['buttonText'] = $processedData['data']['header'];
			} else {
				$processedData['modal']['buttonText'] = $processedData['modal']['button'] ? 'Modal-Button' :'Modal-Link';
			}
			if ( $flexconf['fixedPosition'] ) {
				$processedData['modal']['fixedClass'] = 'fixedModalButton fixedPosition fixedPosition-'.$flexconf['fixedPosition'];
				$processedData['class'] .= $flexconf['rotate'] ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
				$processedData['modal']['fixedButton'] = TRUE;
			}
			$processedData['isTxContainer'] = TRUE;
		}

		/**
		 * Lightbox avtive
		 */
		if ( $processedData['data']['CType'] == 't3sbs_gallery' || $processedData['data']['image_zoom'] ) {
			$processedData['lightBox'] = TRUE;
		}

		/**
		 * Tabs / Pills
		 */
		if ( $processedData['data']['CType'] == 'tabs_container'
		 || $processedData['data']['CType'] == 'tabs_tab' ) {

			if ( $flexconf['display_type'] == 'verticalpills') {
				$processedData['pill']['asideWidth'] = (int)$flexconf['aside_width'];
				$processedData['pill']['mainWidth'] = $flexconf['aside_width'] ? 12 - (int)$flexconf['aside_width'] : 9;
			}
			$processedData['tab']['displayType'] = $flexconf['display_type'];
			$processedData['tab']['switchEffect'] =	 $parentflexconf['switch_effect'];
			$processedData['tab']['contentByPid'] =	 $flexconf['contentByPid'];
			$processedData['tab']['fill'] =	 $flexconf['fill'] ? ' '.$flexconf['fill']: '';
			$processedData['isTxContainer'] = TRUE;
		}

		/**
		 * AutoLayout row
		 */
		if ( $processedData['data']['CType'] == 'autoLayout_row' ) {
			$processedData['isTxContainer'] = TRUE;
		}

		/**
		 * Container
		 */
		if ( $processedData['data']['CType'] == 'container' ) {
			$processedData['isTxContainer'] = TRUE;
		}

		/**
		 * Button
		 */
		if ( $processedData['data']['CType'] == 't3sbs_button' ) {
			$outline = $flexconf['outline'] ? 'outline-':'';
			$typolinkButtonClass = 'btn btn-'.$outline.$flexconf['style'];
			if ( $processedData['data']['CType'] != 'button_group' ) {
				$typolinkButtonClass .= $flexconf['size'] ? ' '.$flexconf['size']:'';
				$typolinkButtonClass .= $flexconf['block'] ? ' btn-block':'';
				if ($processedData['data']['header_position']) {
					$processedData['class'] .= ' text-'.$processedData['data']['header_position'];
				}
				if ( $flexconf['fixedPosition'] ) {
					$processedData['class'] .= ' d-none fixedPosition fixedPosition-'.$flexconf['fixedPosition'];
					$typolinkButtonClass .= $flexconf['rotate'] ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
				}
			}

			if ( $flexconf['fixedPosition'] ) {
				$processedData['fixedButton'] = TRUE;
			}

			$processedData['parentCType'] = $parentCType;
			$processedData['typolinkButtonClass'] = trim($typolinkButtonClass);
		}

		/**
		 * Cards - CardProcessor.php
		 */
		if ( $processedData['data']['CType'] == 't3sbs_card' ) {
			if ($processedData['gallery']['width'] && $flexconf['maxwidth']) {
				$processedData['style'] .= ' max-width: '.$processedData['gallery']['width'].'px;';
			}
		}

		/**
		 * Carousel
		 */
		if ( $processedData['data']['CType'] == 't3sbs_carousel' ) {
			$processedData['dimensions']['width'] = $parentflexconf['width'] ?: '';
			$processedData['carouselLink'] = $parentflexconf['link'];

			if ($parentflexconf['link'] == 'button' && $processedData['data']['header_link']){
				$processedData['data']['button_link'] = $processedData['data']['header_link'];
				$processedData['data']['header_link'] = '';
			}

			$flexconf['captionVAlign'] = $flexconf['captionVAlign'] ? $flexconf['captionVAlign'] : 'end';

			if ($flexconf['bgOverlay'] == 'caption') {
				$innerCaptionStyle = $processedData['style'].' padding:15px 0';
				$processedData['style'] = '';
			} elseif ($flexconf['bgOverlay'] == 'image') {
				$innerCaptionStyle = '';
			} else {
				$processedData['style'] = '';
			}

			$processedData['origImage'] = $parentflexconf['origImage'];

			if ($parentflexconf['buttontext'])
			$processedData['buttontext'] = trim(explode('|', $parentflexconf['buttontext'])[$processedData['data']['sys_language_uid']]);

			if ($extConf['animateCss'] && $parentflexconf['animate']){
				$processedData['animate'] = $parentflexconf['animate'] ?
				 ' caption-animated animated align-items-'.$flexconf['captionVAlign'].' '.$parentflexconf['animate'] : '';
				$processedData['innerStyle'] = $innerCaptionStyle;
			} elseif ($processedData['data']['tx_t3sbootstrap_bgcolor']) {
				$height = $flexconf['captionVAlign'] == 'top' ? '' : 'h-100';
				$processedData['animate'] = ' '.$height.' d-flex align-items-'.$flexconf['captionVAlign'];
				$processedData['innerStyle'] = $innerCaptionStyle;
			} else {
				$height = $flexconf['captionVAlign'] == 'end' ? '' : 'h-100';
				$processedData['animate'] = ' '.$height.' d-flex align-items-'.$flexconf['captionVAlign'];
			}

			$animate = ($extConf['animateCss'] && $parentflexconf['animate']) || $processedData['data']['tx_t3sbootstrap_bgcolor'] ? TRUE : FALSE;
			$processedData['style'] .= $styleHelper->getCarouselCaptionStyle( $flexconf, $animate );

			if (empty($processedData['files'])) {
				$ratio = $parentflexconf['ratio'] ? $parentflexconf['ratio'] : '16:9';
				$noImgHeight = explode(':', $ratio);
				$noImgHeight = (int) round($parentflexconf['width'] / $noImgHeight[0] * $noImgHeight[1]);
				$processedData['animate'] .= ' position-static';
				$processedData['style'] .= ' min-height:'.$noImgHeight.'px;';
				$processedData['style'] .= $flexconf['captionVAlign'] == 'end' ? ' padding-bottom:50px;' : '';
			}
			if ( $parentflexconf['multislider'] ) {
				$processedData['multislider'] = TRUE;
			}

			$processedData['zoom'] = $parentflexconf['zoom'];
			$carouselRatioArr = explode(':', $parentflexconf['ratio']);
			if ( !empty($carouselRatioArr[0]) ) {
				$processedData['ratio'] = $parentflexconf['ratio'];
				if ($flexconf['shift']){
					$processedData['shift'] = (int)$flexconf['shift'] / 100;
				} else {
					$processedData['shift'] = '';
				}
			} else {
				$processedData['ratio'] = '';
			}
		}

		/**
		 * Media object
		 */
		if ( $processedData['data']['CType'] == 't3sbs_mediaobject' ) {

			$processedData['mediaobject']['order'] = $flexconf['order'] == 'right' ? 'right' : 'left';
			$figureclass = $flexconf['order'] == 'right' ? 'd-flex ml-3' : 'd-flex mr-3';

			switch ( $processedData['data']['imageorient'] ) {
				 case 91:
				 	$figureclass .= ' align-self-center';
				break;
				 case 92:
				 	$figureclass .= ' align-self-start';
				break;
				 case 93:
				 	$figureclass .= ' align-self-end';
				break;
				 default:
				 	$figureclass .= '';
			}
		}

		/**
		 * Menu
		 */
		if ($processedData['data']['CType']) {
			if ( substr($processedData['data']['CType'], 0, 4) == 'menu' ) {

				$processedData['menudirection'] = ' '.$flexconf['menudirection'];
				$processedData['menupills'] = $flexconf['menupills'] ? ' nav-pills' :'';
				$processedData['menuHorizontalAlignment'] = $flexconf['menudirection'] == 'flex-row'
				 ? ' '.$flexconf['menuHorizontalAlignment'] :'';

				if ( $processedData['data']['CType'] == 'menu_section' ) {

					$processedData['pageLink'] = FALSE;
					# if more than 1 page for section-menu
					if (count(explode( ',' , $processedData['data']['pages'])) > 1) {
						$processedData['pageLink'] = TRUE;
					} else {
						// if current page is selected
						if ( $GLOBALS['TSFE']->id == $processedData['data']['pid'] ) {
							$processedData['onlyCurrentPageSelected'] = TRUE;
						} else {
							$processedData['pageLink'] = TRUE;
						}
					}
				}

				if ($flexconf['menuHorizontalAlignment'] == 'nav-fill variant') {
					$processedData['menupills'] = '';
				}
			}
		}

		/**
		 * Table
		 */
		if ( $processedData['data']['CType'] == 'table' ) {
			$tableClassArr = explode(',', $flexconf['tableClass']);
			if ( count($tableClassArr) > 1 ) {
				$tableclass = 'table';
				foreach ($tableClassArr as $tc) {
					if ( strlen($tc) > 5 ) {
						$tableclass .= substr($tc, 5);
					}
				}
			} else {
				$tableclass = $flexconf['tableClass'] ? ' '.$flexconf['tableClass']:'';
			}
			$tableclass .= $flexconf['tableInverse'] ? ' table-dark' : '';
			$tableclass .= $processedData['data']['tx_t3sbootstrap_extra_class'] ? ' '.$processedData['data']['tx_t3sbootstrap_extra_class'] : '';
			$processedData['tableclass'] = trim($tableclass);
			$processedData['tableResponsive'] = $flexconf['tableResponsive'] ? TRUE : FALSE;
		}

		/**
		 * Textmedia / Textpic / Image
		 */
		$processedData['codesnippet'] = FALSE;
		if ( $processedData['data']['CType'] == 'textmedia'
		 || $processedData['data']['CType'] == 'textpic'
		 || $processedData['data']['CType'] == 'image' ) {
			// if codesnippet
			if ($extConf['codesnippet'] && $processedData['data']['bodytext']) {
				if (strpos($processedData['data']['bodytext'], '<pre>') !== FALSE) {
					$processedData['codesnippet'] = TRUE;
				}
			}
			// if media
			if ($processedData['data']['assets'] || $processedData['data']['image'] || $processedData['data']['media']) {
				$imageorient = $processedData['data']['imageorient'];
				// hover effect
				$processedData['hoverEffect'] = FALSE;
				if (is_array($processedData['files'])) {
					foreach ($processedData['files'] as $file ) {
						if ($file->getProperties()['tx_t3sbootstrap_hover_effect'])	$processedData['hoverEffect'] = TRUE;


					}
				}
				$galleryUtility = GeneralUtility::makeInstance(GalleryHelper::class);
				// Gallery row with 25, 33, 50, 66, 75 or 100%
				$processedData = $galleryUtility->getGalleryRowWidth( $processedData );
				$processedData = $galleryUtility->getGalleryClasses( $processedData, $contentObjectConfiguration['settings.']['breakpoint'] );
			}
		} else {
			if ( $processedData['data']['assets'] || $processedData['data']['image'] || $processedData['data']['media'] ) {
				$processedData['addmedia']['figureclass'] .= $processedData['data']['image_zoom'] ? ' gallery' : '';
			}
		}

		/**
		 * Toasts
		 */
		if ( $processedData['data']['CType'] == 't3sbs_toast' ) {
			$processedData['animation'] = $flexconf['animation'] ? 'true' : 'false';
			$processedData['autohide'] = $flexconf['autohide'] ? 'true' : 'false';
			$processedData['delay'] = $flexconf['delay'];
			if ( $flexconf['placement'] == 'left' ) {
				$processedData['placement'] = 'position: absolute; top: 0; left: 0;';
			} elseif ( $flexconf['placement'] == 'right' ) {
				$processedData['placement'] = 'position: absolute; top: 0; right: 0;';
			} else {
				$processedData['placement'] = '';
			}
		}

		// if media
		if ( $processedData['data']['assets'] || $processedData['data']['image'] || $processedData['data']['media'] ) {
			$processedData['addmedia']['imgclass'] = $processedData['addmedia']['imgclass'] ?: 'img-fluid';
			$processedData['addmedia']['imgclass'] .= $processedData['data']['imageborder'] ? ' border' :'';
			$processedData['addmedia']['imgclass'] .= $processedData['data']['tx_t3sbootstrap_bordercolor'] && $processedData['data']['imageborder']
			 ? ' border-'.$processedData['data']['tx_t3sbootstrap_bordercolor'] : '';
			// lazyload
			if ( $extConf['lazyLoad'] ) {
				$processedData['addmedia']['imgclass'] .= $extConf['lazyLoad'] == 1 ? ' lazy' : '';
				$processedData['addmedia']['lazyload'] = $extConf['lazyLoad'];
				$processedData['lazyload'] = $extConf['lazyLoad'];
			}
			$figureclass = $processedData['addmedia']['figureclass'] ? $processedData['addmedia']['figureclass'].' '.$figureclass : $figureclass;
			$processedData['addmedia']['figureclass'] = $figureclass ? ' '.trim($figureclass) : '';
			$processedData['addmedia']['imagezoom'] = $processedData['data']['image_zoom'];
			$processedData['addmedia']['CType'] = $processedData['data']['CType'];
			$processedData['addmedia']['ratio'] = $processedData['data']['tx_t3sbootstrap_image_ratio'];
			$processedData['addmedia']['origImg'] = $processedData['data']['tx_t3sbootstrap_image_orig'];
		}

		// container class
		$processedData['data']['configuid'] = (int)$processorConfiguration['configuid'];

		$container = $defaultHelper->getContainerClass($processedData['data']);
		if ($container && $container != 'colPosContainer') {
			$processedData['containerPre'] = '<div class="'.$container.'">';
			$processedData['containerPost'] = '</div>';
			$processedData['container'] = $container;
		}

		if ($processedData['be_layout'] == 'OneCol' && !$container) {
			$pageContainer = self::getFrontendController()->page['tx_t3sbootstrap_container'] ? TRUE : FALSE;
			if (!$pageContainer && !$processedData['data']['tx_container_parent']) {
				$processedData['containerError'] = TRUE;
			}
		}

		// default header type
		switch ( $processedData['data']['CType'] ) {
			case 't3sbs_card':
				$processedData['header']['default'] = 4;
				break;
			case 't3sbs_mediaobject':
				$processedData['header']['default'] = 5;
				break;
			default:
				$processedData['header']['default'] = $processorConfiguration['defaultHeaderType'];
		}

		// content link
		if ( $processedData['data']['tx_t3sbootstrap_header_celink'] && $processedData['data']['header_link']
			|| $flexconf['bgwlink'] && $processedData['data']['header_link'] ) {

			if ( $processedData['data']['CType'] == 't3sbs_card' ) {
				if ($flexconf['button']['enable']) {
					$processedData['card']['button']['link'] = $processedData['data']['header_link'];
				}
			}

			if ( $parentCType != 'listGroup_wrapper' ) {
				$processedData['celink'] = $processedData['data']['header_link'];
				$processedData['class'] .= ' ce-link-content';
			}

			$processedData['data']['header_link'] = '';
			// no image zoom if ce-link (did not work)
			$processedData['data']['image_zoom'] = '';
			$processedData['addmedia']['imagezoom'] = '';
		}

		// animate css
		if ( ($processedData['data']['CType'] == 't3sbs_button'
			|| $processedData['data']['CType'] == 'modal'
			|| $processedData['data']['CType'] == 'button_group')
			&&	$flexconf['fixedPosition'] ) {
			// disable animateCss (conflict)
			$processedData['data']['tx_t3sbootstrap_animateCss'] = FALSE;
		}
		$processedData['dataAnimate'] = FALSE;
		$processedData['isAnimateCss'] = FALSE;
		if ($processedData['data']['tx_t3sbootstrap_animateCss'] && $extConf['animateCss'] ) {
			// add to class
			if( $processedData['data']['tx_t3sbootstrap_animateCssRepeat'] ) {
				$processedData['class'] .= ' animated bt_hidden ';
				// data-attribute
				$processedData['dataAnimate'] = ' data-vp-add-class='.$processedData['data']['tx_t3sbootstrap_animateCss'].'';
				$processedData['dataAnimate'] .= ' data-vp-repeat=true';
			} else {
				$processedData['class'] .= ' animated '.$processedData['data']['tx_t3sbootstrap_animateCss'];
			}
			// add to style
			if ($processedData['data']['tx_t3sbootstrap_animateCssDuration'] ) {
				$processedData['style'] .= ' animation-duration: '.$processedData['data']['tx_t3sbootstrap_animateCssDuration'].'s;';
			}
			if ($processedData['data']['tx_t3sbootstrap_animateCssDelay'] ) {
				$processedData['style'] .= ' animation-delay: '.$processedData['data']['tx_t3sbootstrap_animateCssDelay'].'s;';
			}
			$processedData['isAnimateCss'] = TRUE;
		}

		// child of autoLayout_row
		if ( $parentCType == 'autoLayout_row' ) {
			$processedData['newLine'] = $flexconf['newLine'] ? TRUE : FALSE;
			$processedData['class'] .= $classHelper->getAutoLayoutClass($flexconf);
		}

		// child of container
		if ( $parentCType == 'container' ) {
			$processedData['class'] .= $classHelper->getContainerClass($parentflexconf, $flexconf);
		}

		// extend flexforms with custom fields
		if ( is_array($flexconf['ffExtra']) ) {
			$processedData['ffExtra'] = $flexconf['ffExtra'];
		}

		# default margin-top for each content-element if no margin-top
		$hasMarginTop = strpos($processedData['class'], 'mt-') || strpos($processedData['class'], 'my-') || strpos($processedData['class'], 'm-');
		if ($processorConfiguration['contentMarginTop'] && $processedData['data']['colPos'] == 0 && $hasMarginTop == FALSE ) {
			$processedData['class'] .= $processorConfiguration['contentMarginTop'].' '.trim($processedData['class']);
		}

		# CSS-class for container only
		if ( $processedData['isTxContainer'] ) {
			$containerClass = $classHelper->getTxContainerClass($processedData['data'], $flexconf, $processedData['isVideo'], $extConf);
			$processedData['class'] .= $containerClass ? ' '.$containerClass : '';
		}

		$processedData['style'] .= ' '.$processedData['data']['tx_t3sbootstrap_extra_style'];

		$processedData['style'] = trim($processedData['style']);
		$processedData['class'] = trim($processedData['class']);

		return $processedData;
	}


	/**
	 * Returns the frontend controller
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController(): \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	{
		return $GLOBALS['TSFE'];
	}


	/**
	 * Returns an instance of the background image utility
	 *
	 * @return BackgroundImageUtility
	 */
	protected function getBackgroundImageUtility(): BackgroundImageUtility
	{
		return GeneralUtility::makeInstance(BackgroundImageUtility::class);
	}

}
