<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener\FlexForm;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class FlexformEvent
{
    public function __invoke(AfterFlexFormDataStructureParsedEvent $event): void
    {
        $extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            't3sbootstrap',
            'm1'
        );

        if (array_key_exists('flexformExtend', $extconf) && $extconf['flexformExtend'] === '1') {
            $dataStructure = $event->getDataStructure();
            $identifier = $event->getIdentifier();

            if (!empty($settings['sitepackage'])) {
                $ffPath = 'EXT:t3sb_package/T3SB/FlexForms/';
            } else {
                $ffPath = '/fileadmin/T3SB/FlexForms/';
            }

            foreach ($GLOBALS['TCA']['tt_content']['columns']['tx_t3sbootstrap_flexform']['config']['ds'] as $key=>$flexForm) {
                $flexForms[$key] = substr($flexForm, 46, -4);
            }

            if (array_key_exists($identifier['dataStructureKey'], $flexForms)) {
                if ($identifier['type'] === 'tca' && $identifier['tableName'] === 'tt_content'
                && $identifier['fieldName'] === 'tx_t3sbootstrap_flexform' && $identifier['dataStructureKey']) { 
 					$file = GeneralUtility::getFileAbsFileName($ffPath.$flexForms[$identifier['dataStructureKey']].'.xml');
                    if (file_exists($file)) {
                        $content = @file_get_contents($file);
                        if ($content) {
                            $dataStructure['sheets']['extraEntry'] = GeneralUtility::xml2array($content);

                            $extraDataStructure['sheets']['extraEntry'] = GeneralUtility::xml2array($content);
                            ArrayUtility::mergeRecursiveWithOverrule($dataStructure, $extraDataStructure);
                        }
                    }
                }

                $event->setDataStructure($dataStructure);
            }
        }
    }
}
