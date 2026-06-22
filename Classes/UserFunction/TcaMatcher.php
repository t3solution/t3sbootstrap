<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\UserFunction;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class TcaMatcher
{

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly FlexFormTools $flexFormTools,
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly FileRepository $fileRepository,
        private readonly SiteFinder $siteFinder,
    ) {}
    
    
    public function autoLayoutParent(array $arguments): bool
    {
        $parent = false;
        if (!empty($arguments['record']['tx_container_parent'][0])) {
            $uid = (int)$arguments['record']['tx_container_parent'][0];
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $result = $queryBuilder
                  ->select('*')
                  ->from('tt_content')
                  ->where(
                      $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                  )
                  ->executeQuery();
            $parent_rec = $result->fetchAssociative();

            if (!empty($parent_rec['CType']) && $parent_rec['CType'] === 'autoLayout_row') {
                $parent = true;
            }
        }

        return $parent;
    }


    /**
     * buttonParent
     */
    public function buttonParent(array $arguments): bool
    {
        $parent = true;
        if (!empty($arguments['record']['tx_container_parent'][0])) {
            $uid = (int)$arguments['record']['tx_container_parent'][0];
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $result = $queryBuilder
                  ->select('*')
                  ->from('tt_content')
                  ->where(
                      $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                  )
                  ->executeQuery();
            $parent_rec = $result->fetchAssociative();

            if (!empty($parent_rec['CType']) && $parent_rec['CType'] === 'button_group') {
                $parent = false;
            }
        }

        return $parent;
    }


    /**
     * buttonGroup
     */
    public function buttonGroup(array $arguments): bool
    {
        $group = false;
        if (!empty($arguments['record']['tx_container_parent'][0])) {
            $group = true;
        }

        return $group;
    }


    /**
     * noCardWrapperParent
     */
    public function noCardWrapperParent(array $arguments): bool
    {
        $parent = true;

        if (!empty($arguments['record']['tx_container_parent'][0])) {
            $uid = (int)$arguments['record']['tx_container_parent'][0];
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $result = $queryBuilder
                  ->select('CType')
                  ->from('tt_content')
                  ->where(
                      $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                  )
                  ->executeQuery();
            $parent_rec = $result->fetchAssociative();

            if (!empty($parent_rec['CType']) && $parent_rec['CType'] === 'card_wrapper') {
                $parent = false;
            }
        }

        return $parent;
    }


    /**
     * twoColumnsrParent
     */
    public function twoColumnsParent(array $arguments): bool
    {
        $parent = false;
        if (!empty($arguments['record']['tx_container_parent'][0])) {
            $uid = (int)$arguments['record']['tx_container_parent'][0];
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $result = $queryBuilder
                  ->select('*')
                  ->from('tt_content')
                  ->where(
                      $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                  )
                  ->executeQuery();
            $parent_rec = $result->fetchAssociative();

            if (!empty($parent_rec['CType']) && $parent_rec['CType'] === 'two_columns') {
                $parent = true;
            }
        }

        return $parent;
    }


    /**
     * container_1 ($_EXTCONF['container'] in tt_content.php)
     */
    public function container_1(array $arguments): bool
    {
        return true;
    }


    /**
     * container_0 ($_EXTCONF['container'] in tt_content.php)
     */
    public function container_0(array $arguments): bool
    {
        return false;
    }


    /**
     * spacing_1 ($_EXTCONF['spacing'] in tt_content.php)
     */
    public function spacing_1(array $arguments): bool
    {
        return true;
    }


    /**
     * ratio ($_EXTCONF['ratio'] in tt_content.php)
     */
    public function ratio_0(array $arguments): bool
    {
        return false;
    }


    /**
     * ratio ($_EXTCONF['ratio'] in tt_content.php)
     */
    public function ratio_1(array $arguments): bool
    {
        return true;
    }


    /**
     * spacing_0 ($_EXTCONF['spacing'] in tt_content.php)
     */
    public function spacing_0(array $arguments): bool
    {
        return false;
    }

    /**
     * color_1 ($_EXTCONF['color'] in tt_content.php)
     */
    public function color_1(array $arguments): bool
    {
        if (!empty($arguments['record']['CType']) && !empty($arguments['record']['CType'][0])) {
            return $arguments['record']['CType'][0] !== 'parallax_wrapper';
        }

        return true;
    }


    /**
     * color_0 ($_EXTCONF['color'] in tt_content.php)
     */
    public function color_0(array $arguments): bool
    {
        return false;
    }


    /**
     * is child of flex-container
     */
    public function flexContainerParent(array $arguments): bool
    {
        $parent = false;

        if (!empty($arguments['record']['tx_container_parent'][0])) {
            $uid = (int)$arguments['record']['tx_container_parent'][0];
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $result = $queryBuilder
                  ->select('*')
                  ->from('tt_content')
                  ->where(
                      $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                  )
                  ->executeQuery();
            $parent_rec = $result->fetchAssociative();

            if (!empty($parent_rec['tx_t3sbootstrap_flexform'])) {

                $parent_flexconf = $this->flexFormTools->convertFlexFormContentToArray($parent_rec['tx_t3sbootstrap_flexform']);

                if (!empty($parent_rec['CType']) && $parent_rec['CType'] === 'container' && $parent_flexconf['flexContainer']) {
                    $parent = true;
                }
            }
        }

        return $parent;
    }


    /**
     * isButton
     */
    public function isButton(array $arguments): bool
    {
        $button = false;

        if (!empty($arguments['record']['tx_container_parent'][0])) {
            $uid = (int)$arguments['record']['tx_container_parent'][0];
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $result = $queryBuilder
                  ->select('tx_t3sbootstrap_flexform')
                  ->from('tt_content')
                  ->where(
                      $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                  )
                  ->executeQuery();
            $parent_rec = $result->fetchAllAssociative();
            if (!empty($parent_rec)) {
                $flexconf = $this->flexFormTools->convertFlexFormContentToArray($parent_rec[0]['tx_t3sbootstrap_flexform']);
                if ($flexconf['appearance'] === 'button') {
                    $button = true;
                }
            }
        }

        return $button;
    }


    /**
     * isMenu
     */
    public function isMenu(array $arguments): bool
    {
        $menu = false;
        if (!empty($arguments['record']['CType']) && !empty($arguments['record']['CType'][0])) {
            if (substr($arguments['record']['CType'][0], 0, 4) === 'menu') {
                $menu = true;
            }
        }

        return $menu;
    }


    /**
     * animateCss
     */
    public function animateCss(array $arguments): bool
    {
        $animateCss = false;
        $extconf = $this->extensionConfiguration->get('t3sbootstrap');
        if ($extconf['animateCss']) {
            $animateCss = true;
        }

        return $animateCss;
    }


    /**
     * isYoutube
     */
    public function isYoutube(array $arguments): bool
    {
        $youtube = false;

        if (is_int($arguments['record']['uid'])) {
            $fileObjects = $this->fileRepository->findByRelation('tt_content', 'assets', $arguments['record']['uid']);
            $file = !empty($fileObjects[0]) ? $fileObjects[0] : false;

            if (!empty($file)) {
                if ($file->getType() === 4 && ($file->getMimeType() === 'video/youtube' || $file->getExtension() === 'youtube')) {
                    $youtube = true;
                }
            }
        }

        return $youtube;
    }


    /**
     * isVimeo
     */
    public function isVimeo(array $arguments): bool
    {
        $vimeo = false;

        if (is_int($arguments['record']['uid'])) {
            $fileObjects = $this->fileRepository->findByRelation('tt_content', 'assets', $arguments['record']['uid']);
            $file = !empty($fileObjects[0]) ? $fileObjects[0] : false;

            if (!empty($file)) {
                if ($file->getType() === 4 && ($file->getMimeType() === 'video/vimeo' || $file->getExtension() === 'vimeo')) {
                    $vimeo = true;
                }
            }
        }

        return $vimeo;
    }


    /**
     * isLocalVideo
     */
    public function isLocalVideo(array $arguments): bool
    {
        $video = false;

        if (is_int($arguments['record']['uid'])) {
            $fileObjects = $this->fileRepository->findByRelation('tt_content', 'assets', $arguments['record']['uid']);
            $file = !empty($fileObjects[0]) ? $fileObjects[0] : false;

            if (!empty($file)) {
                if ($file->getType() === 4) {
                    if (($file->getMimeType() === 'video/youtube' || $file->getExtension() === 'youtube')
                     || $file->getMimeType() === 'video/vimeo' || $file->getExtension() === 'vimeo') {
                        $video = false;
                    } else {
                        $video = true;
                    }
                }
            }
        }

        return $video;
    }


    /**
     * isNoMedia
     */
    public function isNoMedia(array $arguments): bool
    {
        $media = false;

        if (is_int($arguments['record']['uid'])) {
            $fileObjects = $this->fileRepository->findByRelation('tt_content', 'assets', $arguments['record']['uid']);
            $file = !empty($fileObjects[0]) ? $fileObjects[0] : false;

            if (!$file) {
                $media = true;
            } else {
                if ($file->getProperties()['hidden']) {
                    $media = true;
                }
            }
        }

        return $media;
    }


    /**
     * isImage
     */
    public function isImage(array $arguments): bool
    {
        $image = false;

        if (is_int($arguments['record']['uid'])) {
            $fileObjects = $this->fileRepository->findByRelation('tt_content', 'assets', $arguments['record']['uid']);
            $file = !empty($fileObjects[0]) ? $fileObjects[0] : false;
            if ($file) {
                if ($file->getType() === 2 && !$file->getProperties()['hidden']) {
                    $image = true;
                }
            }
        }

        return $image;
    }


    /**
     * allowReferences
     */
    public function allowReferences(array $arguments): bool
    {
        $cardWrapperReference = false;
        $extconf = $this->extensionConfiguration->get('t3sbootstrap');
 
        if ($extconf['allowReferences']) {
            $cardWrapperReference = true;
        }
    
        return $cardWrapperReference;
    }


    /**
     * toastContainerParent
     */
    public function toastContainerParent(array $arguments): bool
    {
        $parent = true;
        if (!empty($arguments['record']['tx_container_parent'][0])) {
            $uid = (int)$arguments['record']['tx_container_parent'][0];
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $result = $queryBuilder
                  ->select('*')
                  ->from('tt_content')
                  ->where(
                      $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                  )
                  ->executeQuery();
            $parent_rec = $result->fetchAssociative();
            if ($parent_rec['CType'] === 'toast_container') {
                $parent = false;
            }
        }

        return $parent;
    }


    /**
     * CType textmedia
     */
    public function textmedia(array $arguments): bool
    {
        $parent = false;
        if (!empty($arguments['record']['uid_foreign'])) {
            $uid = (int)$arguments['record']['uid_foreign'];
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $result = $queryBuilder
                  ->select('*')
                  ->from('tt_content')
                  ->where(
                      $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                  )
                  ->executeQuery();
            $contentElement = $result->fetchAssociative();
            if (!empty($contentElement['CType']) && $contentElement['CType'] === 'textmedia') {
                $parent = true;
            }
        }
    
        return $parent;
    }


    /**
     * scss condition
     */
    public function checkScssVisibility(array $arguments): bool
    {
        $show = FALSE;
    
        if (ExtensionManagementUtility::isLoaded('t3sbootstrap_builder')) {
            return $show;
        }
    
        $site = $this->siteFinder->getSiteByPageId($arguments['record']['pid']);
        $configuration = $site->getConfiguration();
        $settings = $configuration['settings'];
    
        if ($settings['bootstrap']['cdn']['customScss'] === TRUE) {
           $show = TRUE;
       }
    
        return $show;
    }


}
