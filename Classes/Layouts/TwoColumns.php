<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Layouts;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;

class TwoColumns implements SingletonInterface
{
    
    public function __construct(
        private readonly Gutters $gutters,
        private readonly Grid $grid,
        private readonly BackgroundImageUtility $backgroundImageUtility,
    ) {}
    

    public function getProcessedData(
        array $processedData, 
        array $flexconf, 
        string $bgMediaQueries='2560,1920,1200,992,768,576'
    ): array
    {
        $processedData = $this->gutters->getGutters($processedData, $flexconf);
        $processedData = $this->grid->getGrid($processedData, $flexconf);

        $processedData['style'] .= !empty($flexconf['colHeight']) ? ' min-height: '.$flexconf['colHeight'].'px;' : '';
        $processedData['verticalAlign'] = !empty($flexconf['colHeight'])
             && !empty($flexconf['verticalAlign']) ? ' d-flex align-items-' . $flexconf['verticalAlign'] : '';
        $processedData['equalHeight'] = !empty($flexconf['equalHeight']) ? ' d-flex align-items-stretch' : '';
        $processedData['bgimages'] = '';
        $processedData['bgimagePosition'] = '';
        $processedData['bgimageSize'] = '';
		$processedData['files'] = !empty($processedData['files']) ? $processedData['files'] : '';

        if (!empty($flexconf['bgimages'])) {
            $this->backgroundImageUtility
                ->getTwoColumnBgImages(
                    $processedData['data']['uid'],
                    $flexconf,
                    $bgMediaQueries
            );
            $processedData['bgimagePosition'] = $flexconf['bgimagePosition'];
            $processedData['bgimageSize'] = !empty($flexconf['bgimageSize']) ? $flexconf['bgimageSize'] : 'cover';
            $processedData['class'] .= ' col-image';
        }

        return $processedData;
    }

}
