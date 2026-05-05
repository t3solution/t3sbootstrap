<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Resource\FileRepository;

class CollapsibleAccordion implements SingletonInterface
{
    public function __construct(
        private readonly FileRepository $fileRepository,
    ) {}

    public function getProcessedData(
        array $processedData,
        array $flexconf,
        array $parentflexconf
    ): array {
        $files = $this->fileRepository->findByRelation(
            'tt_content', 'assets', (int)$processedData['data']['uid']
        );

        $processedData['media']            = $files[0] ?? null;
        $processedData['appearance']       = $parentflexconf['appearance'] ?? '';
        $processedData['show']             = !empty($flexconf['active']) ? ' show' : '';
        $processedData['collapsed']        = !empty($flexconf['active']) ? '' : ' collapsed';
        $processedData['expanded']         = !empty($flexconf['active']) ? 'true' : 'false';
        $processedData['alwaysOpen']       = !empty($parentflexconf['alwaysOpen']) ? 'true' : 'false';
        $processedData['buttonstyle']      = $flexconf['style'] ?? 'primary';
        $processedData['collapsibleByPid'] = $flexconf['collapsibleByPid'] ?? '';

        return $processedData;
    }
}
