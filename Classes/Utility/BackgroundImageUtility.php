<?php

declare(strict_types=0);

namespace T3SBS\T3sbootstrap\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Resource\FileRepository;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class BackgroundImageUtility implements SingletonInterface
{

    public function __construct(
        private readonly ImageService $imageService,
        private readonly AssetCollector $assetCollector
    ) {}


    public function getJumbotronBgImage(
        int|string $uid,
        array $fileObjects=[],
        string $bgMediaQueries='2560,1920,1200,992,768,576',
        int $currentUid=0
    ): array {
        $imageUri_mobile = [];
        $css = '';
        $uid = $currentUid ? $currentUid : $uid;

        if (!empty($fileObjects)) {
            $file = $fileObjects[0];
            $image = $this->imageService->getImage((string)$file->getOriginalFile()->getUid(), $file->getOriginalFile(), true);
            $css = $this->generateCss('s'.$uid, $file, $image, [], $bgMediaQueries);
            $bgImages = $this->generateSrcsetImages($file, $image);
            $imageUri_mobile[] = $bgImages[576];
        }
        if ($css) {
            $this->assetCollector->addInlineStyleSheet('jumbotronBgImage-'.$uid, $css, [], ['priority' => true]);
        }

        return $imageUri_mobile;
    }


    public function getJumbotronBgSlider(
        int|string $uid,
        array $fileObjects=[],
        string $bgMediaQueries='2560,1920,1200,992,768,576',
        int $currentUid=0
    ): array  {

        $imageUri_mobile = [];
        $css = '';
        if (!empty($fileObjects)) {
            foreach ($fileObjects as $key=>$file) {
                $image = $this->imageService->getImage((string)$file->getOriginalFile()->getUid(), $file->getOriginalFile(), true);
                $css = $this->generateCss('s'.$currentUid.'-'.$key+1, $file, $image, [], $bgMediaQueries);
                $bgImages = $this->generateSrcsetImages($file, $image);
                $imageUri_mobile[$key] = $bgImages[576];
                if ($css) {
                    $this->assetCollector->addInlineStyleSheet('jumbotronBgSlider-'.$currentUid.'-'.$key+1, $css, [], ['priority' => true]);
                }
            }
        }

        return $imageUri_mobile;
   }


    public function getBgImage(
       int|string $uid,
       FileReference $file,
       string $bgMediaQueries='2560,1920,1200,992,768,576'
   ): void {
       $css = '';
       if ( !empty($file) && $file->getOriginalFile()->getType() === 2 ) {
           $image = $this->imageService->getImage((string)$file->getOriginalFile()->getUid(), $file->getOriginalFile(), true);
           $css = $this->generateCss('page-'.$uid, $file, $image, [], $bgMediaQueries);
           $bgImages = $this->generateSrcsetImages($file, $image);
       }
       if ( $css ) {
           $this->assetCollector->addInlineStyleSheet('bgImage-'.$uid, $css, [], ['priority' => true]);
       }
   }


    public function getTwoColumnBgImages(
        int|string $uid,
        array $flexconf=[],
        string $bgMediaQueries='1200,992,768,576'
    ): void {

        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $fileObjects = $fileRepository->findByRelation('tt_content', 'bgimages', $uid);

        $css = '';
        if ((int) $flexconf['bgimages'] === 1 && (int) $flexconf['bgimagePosition'] < 3) {
            // left or right
            $file = $fileObjects[0];
            $image = $this->imageService->getImage($file->getOriginalFile()->getUid(), $file->getOriginalFile(), 1);
            $css .= $this->generateCss('s'.$uid.'-'.$flexconf['bgimagePosition'], $file, $image, $flexconf, $bgMediaQueries);
        } 
        if ((int) $flexconf['bgimages'] === 2 && (int) $flexconf['bgimagePosition'] === 3) {
            // both
            foreach ($fileObjects as $fileKey=>$file) {
                $image = $this->imageService->getImage($file->getOriginalFile()->getUid(), $file->getOriginalFile(), 1);
                $css .= $this->generateCss('s'.$uid.'-'.$fileKey+1, $file, $image, $flexconf, $bgMediaQueries);
            }
        }
        if (!empty($css)) {
            $this->assetCollector->addInlineStyleSheet('twoColumnBgImages-'.$uid, $css, [], ['priority' => true]);
        }

    }


    public function getBgWrapperImage(
        int|string $uid,
        FileReference $file,
        array $flexconf,
        string $bgMediaQueries='2560,1920,1200,992,768,576'
    ): void {

        $image = $this->imageService->getImage($file->getOriginalFile()->getUid(), $file->getOriginalFile(), 1);

        if (!empty($flexconf['enableAutoheight'])) {
            if ($flexconf['addHeight']) {
                $inline = '"'.$uid.'":"'.$flexconf['addHeight'].'",';
                if ($inline) {
                    $this->assetCollector->addInlineJavaScript('addheight-'.$uid, $inline);
                }
            }
            $css = $this->generateCss('bg-img-'.$uid, $file, $image, $flexconf, $bgMediaQueries);
        } else {
            $css = $this->generateCss('s'.$uid, $file, $image, $flexconf, $bgMediaQueries);
        }

        if (!empty($css)) {
            $this->assetCollector->addInlineStyleSheet('bgWrapperImage-'.$uid, $css, [], ['priority' => true]);
        }

    }


    private function generateCss(
        string $uid,
        FileReference $file,
        File $image,
        array $flexconf=[],
        string $bgMediaQueries='2560,1920,1200,992,768,576'
    ): string {

        $imageRaster = !empty($flexconf['imageRaster']) ? 'url("/fileadmin/T3SB/Resources/Public/Images/raster.png"), ' : '';
        $processingInstructions = ['crop' => $file instanceof FileReference ? $file->getReferenceProperty('crop') : null];
        $cropVariantCollection = CropVariantCollection::create((string) $processingInstructions['crop']);

        $css = '';
        $mediaQueries = explode(',', $bgMediaQueries);
        $minWidth = (int)$mediaQueries[0];

        foreach ($mediaQueries as $querie) {
            $querie = (int)$querie;
            if ($querie === 576) {
                $cropVariant = 'mobile';
            } elseif ($querie === 768) {
                $cropVariant = 'tablet';
            } else {
                $cropVariant = 'default';
            }
            $cropArea = $cropVariantCollection->getCropArea($cropVariant);

            $processingInstructions = [
                'width' => (int)$querie,
                'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
            ];
            $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);

            $css .= '@media (max-width: '.$querie.'px) {';
            $css .= '#'.$uid.' {background-image:'.$imageRaster.' url("'.$this->imageService->getImageUri($processedImage).'") !important;}';
            $css .= '}';
            
            if ($minWidth === $querie) {
                $minQuerie = $querie +1;
                $css .= '@media (min-width: '.$minQuerie.'px) {';
                $css .= '#'.$uid.' {background-image:'.$imageRaster.' url("'.$this->imageService->getImageUri($processedImage).'") !important;}';
                $css .= '}';
            }
        }

        return $css;
    }


    private function generateSrcsetImages(FileReference $file, File $image): array 
    {
        $processingInstructions = ['crop' => $file instanceof FileReference ? $file->getReferenceProperty('crop') : null];
        $cropVariantCollection = CropVariantCollection::create((string) $processingInstructions['crop']);
        $cropVariant = 'mobile';
        $cropArea = $cropVariantCollection->getCropArea($cropVariant);
        $processingInstructions = [
            'width' => 576,
            'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
        ];
        $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
        $bgImages[576] = $this->imageService->getImageUri($processedImage);

        return $bgImages;
    }

}
