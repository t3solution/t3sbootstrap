<?php
namespace T3SBS\T3sbootstrap\Utility;

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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;

class BackgroundImageUtility implements SingletonInterface
{

	/**
	 * Writes a css file with the background images
	 *
	 * @param	string	$uid
	 * @param	string	$table
	 * @param	bool	$jumbotron
	 * @param	bool	$bgColorOnly
	 * @param	array	$flexconf
	 * @param	bool	$body
	 * @param	int		$currentUid
	 * @param	bool	$webp
	 *
	 * @return array
	 */
	public function getBgImage($uid, $table='tt_content', $jumbotron=FALSE, $bgColorOnly=FALSE, $flexconf=[], $body=FALSE, $currentUid=0, $webp=FALSE)
	{

		$frontendController = $this->getFrontendController();
		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
		$filesFromRepository = $fileRepository->findByRelation($table, 'assets', $uid);
		if ( empty($filesFromRepository) ) {
			$filesFromRepository = $fileRepository->findByRelation($table, 'media', $uid);
		}

		if ( $webp ) {
			$jsModernizr = 'EXT:t3sbootstrap/Resources/Public/Contrib/Modernizr/modernizr.js';
			$jsModernizr = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($jsModernizr);
			$this->pageRenderer()->addJsFooterFile($jsModernizr);
		}

		if ( count($filesFromRepository) > 1 ) {
			if ( $flexconf['bgimagePosition'] == 1 || $flexconf['bgimagePosition'] == 2 ) {
				// bg-images in two-columns
				// in the case if two images available but only one is selected in the flexform
				$file = $filesFromRepository[0];
				$image = $this->imageService()->getImage($file->getOriginalFile()->getUid(), $file->getOriginalFile(), 1);
				$bgImages = $this->generateSrcsetImages($file, $image);
				$imageUri_mobile = $webp ? $bgImages[576].'.webp' : $bgImages[576];
				$css .= $this->generateCss('s'.$uid.'-'.$flexconf['bgimagePosition'], $file, $image, $webp, $flexconf);

			} else {
				// slider in jumbotron or two bg-images in two-columns
				$uid = $frontendController->id;
				foreach($filesFromRepository as $fileKey=>$file) {
					$fileKey = $fileKey+1;
					$image[$fileKey] = $this->imageService()->getImage($file->getOriginalFile()->getUid(), $file->getOriginalFile(), 1);
					$bgImages[$fileKey] = $this->generateSrcsetImages($file, $image[$fileKey]);
					$imageUri_mobile[$fileKey] = $webp ? $bgImages[$fileKey][576].'.webp' : $bgImages[$fileKey][576];
					$css .= $this->generateCss('s'.$uid.'-'.$fileKey, $file, $image[$fileKey], $webp, $flexconf);
				}
			}
		} else {
			// background-image
			if (!empty($filesFromRepository) ) {
				$file = $filesFromRepository[0];
				$image = $this->imageService()->getImage($file->getOriginalFile()->getUid(), $file->getOriginalFile(), 1);

				$uid = $currentUid ?: $uid;

				if ( $flexconf['bgimagePosition'] ) {
					$uid = $uid . '-' . $flexconf['bgimagePosition'];
				}
				if ($jumbotron) {
					$css = $this->generateCss('s'.$uid, $file, $image, $webp, $flexconf);
				} elseif ($body) {
					$css = $this->generateCss('page-'.$uid, $file, $image, $webp, $flexconf);					
				} else {
					if ( $flexconf['enableAutoheight'] ) {
						if ( $flexconf['addHeight'] ) {
							$this->pageRenderer()->addInlineSetting('ADDHEIGHT', $uid, $flexconf['addHeight']);
						}
						$this->pageRenderer()->addInlineSetting('ENABLEHEIGHT', $uid, $flexconf['enableAutoheight']);						
						$css = $this->generateCss('bg-img-'.$uid, $file, $image, $webp, $flexconf);
					} else {
						$css = $this->generateCss('s'.$uid, $file, $image, $webp, $flexconf);
					}
				}

				$bgImages = $this->generateSrcsetImages($file, $image);
				$imageUri_mobile = $webp ? $bgImages[576].'.webp' : $bgImages[576];

			} else {
				$imageUri_mobile = '';
				if ($bgColorOnly) {

					if ( $flexconf['enableAutoheight'] ) {
						if ( $flexconf['addHeight'] ) {
							$this->pageRenderer()->addInlineSetting('ADDHEIGHT', $uid, $flexconf['addHeight']);
						}
						$this->pageRenderer()->addInlineSetting('ENABLEHEIGHT', $uid, $flexconf['enableAutoheight']);
					}
				}
			}
		}

		$frontendController->additionalHeaderData['tx_t3sbootstrapt_styles_for_backgrouns_imagesr'] .= '<style>'.$css.'</style>';
		$jsFooterFile = 'EXT:t3sbootstrap/Resources/Public/Scripts/bgImageSize.js';
		$jsFooterFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($jsFooterFile);

		$this->pageRenderer()->addJsFooterFile($jsFooterFile);

		return $imageUri_mobile;
	}


	/**
	 * generate CSS
	 *
	 * @return string $css
	 */
	private function generateCss( $uid, $file, $image, $webp, $flexconf=[] ) {

		$imageRaster = $flexconf['imageRaster'] ? 'url(/typo3conf/ext/t3sbootstrap/Resources/Public/Images/raster.png), ' : '';

		$processingInstructions = ['crop' => $file instanceof FileReference ? $file->getReferenceProperty('crop') : null];
		$cropVariantCollection = CropVariantCollection::create((string) $processingInstructions['crop']);

		$css = '';

		$mediaQueries = [2560,1920,1200,992,768,576];
		foreach ($mediaQueries as $querie) {
			if ($querie == 576) {
				$cropVariant = 'mobile';
			} elseif ($querie == 768) {
				$cropVariant = 'tablet';
			} else {
				$cropVariant = 'default';
			}
			$cropArea = $cropVariantCollection->getCropArea($cropVariant);

			$processingInstructions = [
				'width' => $querie,
				'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
			];

			$processedImage = $this->imageService()->applyProcessingInstructions($image, $processingInstructions);

			$css .= '@media (max-width: '.$querie.'px) {';
			if ($webp) {
				$css .= '.no-webp #'.$uid.' {background-image:'.$imageRaster.' url("'.$this->imageService()->getImageUri($processedImage).'");}';
				$css .= '.webp #'.$uid.' {background-image:'.$imageRaster.' url("'.$this->imageService()->getImageUri($processedImage).'.webp");}';
			} else {
				$css .= '#'.$uid.' {background-image:'.$imageRaster.' url("'.$this->imageService()->getImageUri($processedImage).'");}';
			}
			$css .= '}';

		}

		return $css;
	}


	/**
	 * generateSrcsetImages
	 *
	 * @return string $css
	 */
	private function generateSrcsetImages( $file, $image ) {

		$processingInstructions = ['crop' => $file instanceof FileReference ? $file->getReferenceProperty('crop') : null];
		$cropVariantCollection = CropVariantCollection::create((string) $processingInstructions['crop']);
		$cropVariant = 'mobile';
		$cropArea = $cropVariantCollection->getCropArea($cropVariant);
		$processingInstructions = [
			'width' => 576,
			'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
		];
		$processedImage = $this->imageService()->applyProcessingInstructions($image, $processingInstructions);
		$bgImages[576] = $this->imageService()->getImageUri($processedImage);

		return $bgImages;
	}


	/**
	 * Returns the ImageService
	 *
	 * @return ImageService
	 */
	protected function imageService()
	{
		$objectManager = GeneralUtility::makeInstance(ObjectManager::class);

		return $objectManager->get(ImageService::class);
	}


	/**
	 * Returns the Page Renderer
	 *
	 * @return PageRenderer
	 */
	protected function pageRenderer()
	{
		return GeneralUtility::makeInstance(PageRenderer::class);
	}


	/**
	 * Returns $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController()
	{
		return $GLOBALS['TSFE'];
	}


}
