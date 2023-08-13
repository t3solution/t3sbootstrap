<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener\FlexForm;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;
use TYPO3\CMS\Core\Utility\ArrayUtility;

class NewsFlexformEvent
{
    public function __invoke(AfterFlexFormDataStructureParsedEvent $event): void
    {
        $dataStructure = $event->getDataStructure();
        $identifier = $event->getIdentifier();

        if ($identifier['type'] === 'tca' && $identifier['tableName'] === 'tt_content'
         && ($identifier['dataStructureKey'] === '*,news_pi1' || $identifier['dataStructureKey'] === '*,news_newsliststicky' || $identifier['dataStructureKey'] === '*,news_newsdetail')) {
            $file = GeneralUtility::getFileAbsFileName('EXT:t3sbootstrap/Resources/Private/Extensions/news/Configuration/FlexForms/News.xml');
            $content = file_get_contents($file);

            if ($content) {
                $extraDataStructure['sheets']['extraEntry'] = GeneralUtility::xml2array($content);
                ArrayUtility::mergeRecursiveWithOverrule($dataStructure, $extraDataStructure);
            }
        }

        $event->setDataStructure($dataStructure);
    }
}
