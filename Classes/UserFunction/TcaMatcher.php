<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\UserFunction;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
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
            $queryBuilder->getRestrictions()->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
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
            $queryBuilder->getRestrictions()->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
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
            $queryBuilder->getRestrictions()->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
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
            $queryBuilder->getRestrictions()->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
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
            $queryBuilder->getRestrictions()->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
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

                if (!empty($parent_rec['CType']) && $parent_rec['CType'] === 'container' && !empty($parent_flexconf['flexContainer'])) {
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
            $queryBuilder->getRestrictions()->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $result = $queryBuilder
                  ->select('tx_t3sbootstrap_flexform')
                  ->from('tt_content')
                  ->where(
                      $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                  )
                  ->executeQuery();
            $parent_rec = $result->fetchAllAssociative();
            // the row may exist while the flexform column is NULL or empty -
            // convertFlexFormContentToArray() is typed string and would fatal
            if (!empty($parent_rec[0]['tx_t3sbootstrap_flexform'])) {
                $flexconf = $this->flexFormTools->convertFlexFormContentToArray((string)$parent_rec[0]['tx_t3sbootstrap_flexform']);
                if (!empty($flexconf['appearance']) && $flexconf['appearance'] === 'button') {
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
     * The vertical header only makes sense inside the background wrapper, and only
     * while its flexform option "headerInside" is on - the heading is taken out of
     * the flow and centred against the wrapper, which needs the wrapper around it.
     *
     * The flexform value is read from the record FormEngine hands over, not from the
     * database: "headerInside" reloads the form on change, and that reload carries
     * the current form values without saving them. The database is only the fallback
     * for the shapes where the field has not been processed yet.
     */
    public function headerInside(array $arguments): bool
    {
        $record = $arguments['record'] ?? [];

        $cType = $record['CType'] ?? '';
        $cType = is_array($cType) ? (string)($cType[0] ?? '') : (string)$cType;
        if ($cType !== 'background_wrapper') {
            return false;
        }

        $flexform = $record['tx_t3sbootstrap_flexform'] ?? null;

        // FormEngine hands the processed structure over, the database the raw XML.
        if (is_array($flexform)) {
            return !empty($flexform['data']['sDEF']['lDEF']['headerInside']['vDEF']);
        }

        if (is_string($flexform) && $flexform !== '') {
            $flexconf = $this->flexFormTools->convertFlexFormContentToArray($flexform);
            return !empty($flexconf['headerInside']);
        }

        // Nothing usable in the row - a saved record still has the value in the table.
        // A new one has uid "NEW..." and no flexform yet, so it stays hidden.
        if (!is_int($record['uid'] ?? null)) {
            return false;
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $row = $queryBuilder
            ->select('tx_t3sbootstrap_flexform')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($record['uid'], Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        if (empty($row['tx_t3sbootstrap_flexform'])) {
            return false;
        }

        $flexconf = $this->flexFormTools->convertFlexFormContentToArray((string)$row['tx_t3sbootstrap_flexform']);

        return !empty($flexconf['headerInside']);
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
            $queryBuilder->getRestrictions()->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $result = $queryBuilder
                  ->select('*')
                  ->from('tt_content')
                  ->where(
                      $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                  )
                  ->executeQuery();
            $parent_rec = $result->fetchAssociative();
            if (!empty($parent_rec['CType']) && $parent_rec['CType'] === 'toast_container') {
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
            $queryBuilder->getRestrictions()->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
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
    
        try {
            $site = $this->siteFinder->getSiteByPageId($arguments['record']['pid']);
        } catch (SiteNotFoundException) {
            return $show;
        }
        $configuration = $site->getConfiguration();
        $settings = $configuration['settings'];
    
        if ($settings['bootstrap']['cdn']['customScss'] === TRUE) {
           $show = TRUE;
       }
    
        return $show;
    }
    
    
    public function iconpackLoaded(array $arguments): bool
    {
        $show = FALSE;
    
        if (ExtensionManagementUtility::isLoaded('iconpack')) {
            $show = TRUE;
        }

        return $show;
    }
    


}
