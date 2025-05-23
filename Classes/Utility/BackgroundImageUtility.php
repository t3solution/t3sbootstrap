<?php

declare(strict_types=0);

namespace T3SBS\T3sbootstrap\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Core\Page\AssetCollector;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class BackgroundImageUtility implements SingletonInterface
{
    protected $imageService;

    public function __construct(
        ImageService $imageService
    ) {
        $this->imageService = $imageService;
    }

    /**
     * Writes a css file with the background images
     *
     * return mixed
     */
    public function getBgImage(
        int|string $uid,
        string $table='tt_content',
        bool $jumbotron=false,
        bool $bgColorOnly=false,
        array $flexconf=[],
        bool $body=false,
        int $currentUid=0,
        string $bgMediaQueries='2560,1920,1200,992,768,576'
    ) {
        $request = $GLOBALS['TYPO3_REQUEST'];
        $frontendController = $request->getAttribute('frontend.controller');
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $filesFromRepository = $fileRepository->findByRelation($table, 'assets', $uid);
        if (empty($filesFromRepository)) {
            $filesFromRepository = $fileRepository->findByRelation($table, 'media', $uid);
        }
        if (empty($filesFromRepository)) {
            $filesFromRepository = $fileRepository->findByRelation($table, 'bgimages', $uid);
        }

        $css = '';

        if (count($filesFromRepository) > 1 && $body == false) {
            if (!empty($flexconf['bgimagePosition']) && ($flexconf['bgimagePosition'] === 1 || $flexconf['bgimagePosition'] === 2)) {
                // bg-images in two-columns
                // in the case if two images available but only one is selected in the flexform
                $file = $filesFromRepository[0];
                $image = $this->imageService->getImage($file->getOriginalFile()->getUid(), $file->getOriginalFile(), 1);
                $bgImages = $this->generateSrcsetImages($file, $image);
                $imageUri_mobile = $bgImages[576];
                $css .= $this->generateCss('s'.$uid.'-'.$flexconf['bgimagePosition'], $file, $image, $flexconf, false, $bgMediaQueries);
            } else {
                // slider in jumbotron or two bg-images in two-columns
                if ($jumbotron === true) {
                    $uid = $frontendController->id;
                }
                foreach ($filesFromRepository as $fileKey=>$file) {
                    $fileKey = $fileKey+1;
                    $image[$fileKey] = $this->imageService->getImage((string)$file->getOriginalFile()->getUid(), $file->getOriginalFile(), true);
                    $bgImages[$fileKey] = $this->generateSrcsetImages($file, $image[$fileKey]);
                    $imageUri_mobile[$fileKey] = $bgImages[$fileKey][576];
                    $css .= $this->generateCss('s'.$uid.'-'.$fileKey, $file, $image[$fileKey], $flexconf, false, $bgMediaQueries);
                }
            }
        } else {
            // background-image
            if (!empty($filesFromRepository)) {
                $file = $filesFromRepository[0];
                $image = $this->imageService->getImage((string)$file->getOriginalFile()->getUid(), $file->getOriginalFile(), true);
                $uid = $currentUid ?: $uid;
                if (!empty($flexconf['bgimagePosition'])) {
                    $uid = $uid . '-' . $flexconf['bgimagePosition'];
                }
                if ($jumbotron) {
                    $css = $this->generateCss('s'.$uid, $file, $image, $flexconf, false, $bgMediaQueries);
                } elseif ($body) {
                    $css = $this->generateCss('page-'.$uid, $file, $image, $flexconf, true, $bgMediaQueries);
                } else {
                    if (!empty($flexconf['enableAutoheight'])) {
                        if ($flexconf['addHeight']) {
                            $inline = '"'.$uid.'":"'.$flexconf['addHeight'].'",';
                            if ($inline) {
                                GeneralUtility::makeInstance(AssetCollector::class)
                                  ->addInlineJavaScript('addheight-'.$uid, $inline);
                            }
                        }
                        $css = $this->generateCss('bg-img-'.$uid, $file, $image, $flexconf, false, $bgMediaQueries);
                    } else {
                        $css = $this->generateCss('s'.$uid, $file, $image, $flexconf, false, $bgMediaQueries);
                    }
                }
                $bgImages = $this->generateSrcsetImages($file, $image);
                $imageUri_mobile = $bgImages[576];
            } else {
                $imageUri_mobile = '';
                if ($bgColorOnly) {
                    if ($flexconf['enableAutoheight']) {
                        if ($flexconf['addHeight']) {
                            $inline = '"'.$uid.'":"'.$flexconf['addHeight'].'",';
                            if ($inline) {
                                GeneralUtility::makeInstance(AssetCollector::class)
                                  ->addInlineJavaScript('addheight-'.$uid, $inline);
                            }
                        }
                    }
                }
            }
        }
        if ($css) {
            GeneralUtility::makeInstance(AssetCollector::class)
             ->addInlineStyleSheet('bgimgutility-'.$uid, $css, [], ['priority' => true]);
        }

        return $imageUri_mobile;
    }


    /**
     * generate CSS
     */
    private function generateCss(
        string $uid,
        FileReference $file,
        File $image,
        array $flexconf=[],
        bool $body=false,
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
            if ($querie == 576) {
                $cropVariant = 'mobile';
            } elseif ($querie == 768) {
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

            if ($minWidth == $querie) {
                $minQuerie = $querie +1;
                $css .= '@media (min-width: '.$minQuerie.'px) {';
                $css .= '#'.$uid.' {background-image:'.$imageRaster.' url("'.$this->imageService->getImageUri($processedImage).'") !important;}';
                $css .= '}';
            }
        }

        return $css;
    }


    /**
     * generateSrcsetImages
     */
    private function generateSrcsetImages($file, $image): array
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