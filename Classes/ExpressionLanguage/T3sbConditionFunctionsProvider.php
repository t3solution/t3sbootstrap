<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class T3sbConditionFunctionsProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            $this->getExtconf(),
            $this->getExtconfValue(),
            $this->getColPosList(),
            $this->getExtensionLoaded(),
        ];
    }

    /**
     * Returns the raw value of a setting, not just '1'/'0'. t3sbootstrap() normalises to
     * true/false, which is enough for switches, but a setting that is a choice - rteCodeBlock
     * holds the name of the Prism theme - needs the value itself. Deliberately a second
     * function: switching t3sbootstrap() to raw values would silently change existing queries
     * such as lazyLoad or imgCopyright, which can also be '2' or '3'.
     */
    protected function getExtconfValue(): ExpressionFunction
    {
        return new ExpressionFunction('t3sbootstrapValue', function ($str) {
            // Not implemented, we only use the evaluator
        }, function ($arguments, $str) {
            try {
                $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
            } catch (\Throwable) {
                return '';
            }

            return (string)($extConf[$str] ?? '');
        });
    }

    protected function getExtconf(): ExpressionFunction
    {
        return new ExpressionFunction('t3sbootstrap', function ($str) {
            // Not implemented
        }, function ($arguments, $str) {
            $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
            if ($str === 'extNews') {
                if (!empty($extConf[$str]) && ExtensionManagementUtility::isLoaded('news')) {
                    return '1';
                }
                return '0';
            }
            if (!empty($extConf[$str])) {
                return '1';
            }
            return '0';
        });
    }
    
    protected function getExtensionLoaded(): ExpressionFunction
    {
        return new ExpressionFunction('loaded', function () {
            // Not implemented, we only use the evaluator
        }, function ($arguments, $extKey) {
            return ExtensionManagementUtility::isLoaded($extKey);
        });
    }

    protected function getColPosList(): ExpressionFunction
    {
        return new ExpressionFunction('colPosList', function ($str) {
            // Not implemented
        }, function ($arguments, $str) {
            $result = false;

            if (!empty($arguments['page']['uid'])) {
                $pid = $this->resolveLivePageId($arguments['page']);
                $config = $this->getConfig($pid, $arguments);

                if (!empty($config)) {
                    $jumbotron = !empty($config['jumbotron_enable']);
                    $footer = !empty($config['footer_enable']);
                    $expandedcontent = !empty($config['expandedcontent_enabletop']);

                    if (empty($expandedcontent)) {
                        if (!empty($jumbotron) && !empty($footer) && $str === 'JF') {
                            $result = true;
                        } elseif (!empty($jumbotron) && empty($footer) && $str === 'J') {
                            $result = true;
                        } elseif (empty($jumbotron) && !empty($footer) && $str === 'F') {
                            $result = true;
                        } elseif (empty($jumbotron) && empty($footer) && $str === 'NONE') {
                            $result = true;
                        }
                    } else {
                        if (!empty($jumbotron) && !empty($footer) && $str === 'ALL') {
                            $result = true;
                        } elseif (empty($jumbotron) && empty($footer) && $str === 'E') {
                            $result = true;
                        } elseif (!empty($jumbotron) && empty($footer) && $str === 'JE') {
                            $result = true;
                        } elseif (empty($jumbotron) && !empty($footer) && $str === 'FE') {
                            $result = true;
                        }
                    }
                }
            }

            return $result;
        });
    }

    /**
     * Page id the config record belongs to - the LIVE one.
     *
     * In a workspace, FormEngine edits the version record and hands exactly that one
     * to the conditions: DatabaseEditRow reads it by its own uid, without a workspace
     * overlay, and BackendLayoutView::determinePageId() passes that uid on. The config
     * record, however, hangs on the live page and never has a version of its own - the
     * table is not workspace aware. Asking for the version's uid therefore finds
     * nothing, every colPosList() returns false and the page is left without a backend
     * layout. On a root page nothing can catch that: above the version sits only uid 0.
     */
    private function resolveLivePageId(array $page): int
    {
        $liveId = (int)($page['t3ver_oid'] ?? 0);

        return $liveId > 0 ? $liveId : (int)($page['uid'] ?? 0);
    }

    private function getConfig(int $pid, array $arguments): array
    {
        $config = $this->fetchConfig($pid);
        if (!empty($config)) {
            return $config;
        }

        foreach ($this->getParentPageIds($pid, $arguments) as $parentPageId) {
            $config = $this->fetchConfig($parentPageId);
            if (!empty($config)) {
                return $config;
            }
        }

        return [];
    }

    /**
     * Rootline of the current page, closest parent first, without the page itself.
     *
     * The variables differ per TYPO3 version: since v13 PageTsConfigFactory hands over
     * "fullRootLine" (page rows, root page first), v12 handed over a "tree" object carrying
     * rootLineIds. Nothing provides "tree" any more, so reading it alone left every page
     * without a config record of its own without a backend layout.
     *
     * @return int[]
     */
    private function getParentPageIds(int $pid, array $arguments): array
    {
        $pageIds = [];

        if (is_array($arguments['fullRootLine'] ?? null)) {
            foreach ($arguments['fullRootLine'] as $page) {
                $pageIds[] = (int)($page['uid'] ?? 0);
            }
            // Root page first, so the closest parent is at the end.
            $pageIds = array_reverse($pageIds);
        } elseif (is_array($arguments['tree']->rootLineIds ?? null)) {
            $pageIds = array_map('intval', array_values($arguments['tree']->rootLineIds));
            if ($pageIds !== [] && end($pageIds) === $pid) {
                $pageIds = array_reverse($pageIds);
            }
        }

        // The page itself was checked above, uid 0 is the tree root and has no record.
        return array_values(array_filter(
            $pageIds,
            static fn(int $pageId): bool => $pageId > 0 && $pageId !== $pid
        ));
    }

    private function fetchConfig(int $pid): array|false
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(HiddenRestriction::class));

        return $queryBuilder
            ->select('jumbotron_enable', 'footer_enable', 'expandedcontent_enabletop')
            ->from('tx_t3sbootstrap_domain_model_config')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT))
            )
            ->orderBy('uid')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();
    }
}
