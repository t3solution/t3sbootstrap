<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class CardWrapper implements SingletonInterface
{
    
    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly FlexFormTools $flexFormTools,
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly FileRepository $fileRepository,
    ) {}
    
    
    /**
     * Returns the $processedData
     */
    public function getProcessedData(array $processedData, array $flexconf): array
    {
        // Ein Card-Wrapper, dessen FlexForm nie geoeffnet wurde, hat gar keine
        // Werte: tx_t3sbootstrap_flexform ist NULL, $flexconf entsprechend leer.
        // Der Schluessel wird deshalb einmal normalisiert, statt ihn an vier
        // Stellen direkt zu lesen - sonst warnt PHP 8 mit "Undefined array key",
        // und TYPO3s Error-Handler macht daraus eine Exception.
        $layout = (string)($flexconf['card_wrapper'] ?? '');

        $processedData['gutter'] = !empty($flexconf['gutter']) ? (int)$flexconf['gutter'] : 0;
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder->setRestrictions(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));
            
        $children = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($processedData['data']['uid'], Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($processedData['data']['sys_language_uid'], Connection::PARAM_INT))
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchAllAssociative();

        $processedData['colclass'] = !empty($flexconf['colclass']) ? $flexconf['colclass'] : '';
        $processedData['cropMaxCharacters'] = !empty($flexconf['cropMaxCharacters']) ? $flexconf['cropMaxCharacters'] : '';

        $extconf = $this->extensionConfiguration->get('t3sbootstrap');
        $processedData['contentBy'] = [];
        if (!empty($extconf['allowReferences'])) {
            $processedData = $this->getReferences($processedData, $flexconf);
        }

        if (count($children) || count($processedData['contentBy'])) {
            // Flipper defaults
            if ($layout === 'flipper') {
                switch (count($children)) {
                     case 1:
                        $processedData['flipper']['class'] = 'col-xs-12 col-sm-12 col-md-12';
                        $processedData['flipper']['width'] = 1294;
                    break;
                     case 2:
                        $processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-6';
                        $processedData['flipper']['width'] = 634;
                    break;
                     case 3:
                        $processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-4';
                        $processedData['flipper']['width'] = 414;
                    break;
                     case 4:
                        $processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-3';
                        $processedData['flipper']['width'] = 304;
                    break;
                     case 6:
                        $processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-2';
                        $processedData['flipper']['width'] = 175;
                    break;
                     default:
                        $processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-4';
                        $processedData['flipper']['width'] = 576;
                }
            }

            foreach ($children as $key=>$child) {
                $fileObjects = $this->fileRepository->findByRelation('tt_content', 'assets', $child['uid']);
                if (!empty($processedData['flipper']) && isset($processedData['flipper']['width'])) {
                    $flipperWidth = $processedData['flipper']['width'];
                } else {
                    $flipperWidth = 0;
                }
                $children[$key]['imgwidth'] = !empty($child['imagewidth']) ? $child['imagewidth'] : $flipperWidth;
                if (!empty($fileObjects)) {
                    if ($layout === 'flipper') {

                        $children[$key]['hFa'] = !empty($child['header_icon']) ? $child['header_icon'] : '';
                        
                        $children[$key]['file'] = $fileObjects;
                        $children[$key]['backheader'] = $child['tx_t3sbootstrap_cardheader'];
                        $children[$key]['header'] = $child['header'];
                    } else {
                        $children[$key]['file'] = $fileObjects[0];
                        $children[$key]['header'] = $child['header'];
                    }
                }
                $children[$key]['ratio'] = $child['tx_t3sbootstrap_image_ratio'] ?: '0';
                $children[$key]['uid'] = $child['uid'];
                $children[$key]['subheader'] = $child['subheader'];
                $children[$key]['header_link'] = $child['header_link'];
                $children[$key]['header_position'] = $child['header_position'] ? ' text-'.$child['header_position'] : '';
                $children[$key]['tx_t3sbootstrap_header_display'] = $child['tx_t3sbootstrap_header_display'];
                $children[$key]['tx_t3sbootstrap_header_class'] = $child['tx_t3sbootstrap_header_class'];
                $children[$key]['header_icon'] = !empty($child['header_icon']) ? $child['header_icon'] : '';
                $children[$key]['celink'] = $child['tx_t3sbootstrap_header_celink'];
                // a card without its own flexform stores NULL here
                $children[$key]['settings'] = !empty($child['tx_t3sbootstrap_flexform'])
                    ? $this->flexFormTools->convertFlexFormContentToArray((string)$child['tx_t3sbootstrap_flexform'])
                    : [];
            }
            $processedData['cards'] = $children;

            // swiperjs: all swiper options are read directly as {t3sbFlexform.*} in
            // Partials/Content/Assets/CardWrapper.fluid.html, only {navigation}/{pagination}
            // are evaluated as top level variables there
            if ($layout === 'slider') {
                $processedData['navigation'] = (int)!empty($flexconf['navigation']);
                $processedData['pagination'] = (int)!empty($flexconf['pagination']);
            }

            $processedData['visibleCards'] = !empty($flexconf['visibleCards']) ? (int)$flexconf['visibleCards'] : 3;
        }

        $processedData['card_wrapper_layout'] = $layout;

        return $processedData;
    }
    

    private function getReferences(array $processedData, array $flexconf): array
    {
        $contentByUid = [];
        $contentByPid = [];
        
        if (!empty($flexconf['contentByUid'])) {
            $uidContent = explode(',', $flexconf['contentByUid']);
            foreach ($uidContent as $uid) {
                $contentByUid[]['uid'] = (int) $uid;
            }
        }
        if (!empty($flexconf['contentByPid'])) {
            $contentByPidArr = explode(',', $flexconf['contentByPid']);
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $queryBuilder->setRestrictions(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));
            $contentByPid = $queryBuilder
                ->select('uid')
                ->from('tt_content')
                ->where(
                    $queryBuilder->expr()->in('tx_container_parent', $queryBuilder->createNamedParameter($contentByPidArr, Connection::PARAM_INT_ARRAY)),
                    $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($processedData['data']['sys_language_uid'], Connection::PARAM_INT))
                )
                ->orderBy('sorting')
                ->executeQuery()
                ->fetchAllAssociative();
        }

        $contentBy = [];
        if ( !empty($contentByUid) && !empty($contentByPid) ) {
            $contentBy = array_merge($contentByUid, $contentByPid);
        } elseif ( !empty($contentByUid) ) {
            $contentBy = $contentByUid;
        } elseif ( !empty($contentByPid) ) {
            $contentBy = $contentByPid;
        }
        $processedData['contentBy'] = $contentBy;
        
        return $processedData;
    }

}
