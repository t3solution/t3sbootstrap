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
     * Liefert den rohen Wert einer Einstellung, nicht nur '1'/'0'.
     *
     * t3sbootstrap() normalisiert auf wahr/falsch und reicht damit fuer
     * Schalter. Sobald eine Einstellung eine Auswahl ist - rteCodeBlock haelt
     * den Namen des Prism-Themes - braucht die Bedingung den Wert selbst.
     *
     * Bewusst eine zweite Funktion: t3sbootstrap() auf den Rohwert
     * umzustellen wuerde bestehende Abfragen still veraendern, etwa bei
     * lazyLoad oder imgCopyright, die auch '2' oder '3' annehmen koennen.
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
                $pid = (int)$arguments['page']['uid'];
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

    private function getConfig(int $pid, array $arguments): array
    {
        $config = $this->fetchConfig($pid);

        if (empty($config)
            && isset($arguments['tree'])
            && is_array($arguments['tree']->rootLineIds ?? null)
        ) {
            // rootLineIds is handed over sorted root page first, current page last.
            // Normalize to "closest parent first" independently of that sorting.
            $rootLineIdsArray = array_values($arguments['tree']->rootLineIds);
            if ($rootLineIdsArray !== [] && (int)end($rootLineIdsArray) === $pid) {
                $rootLineIdsArray = array_reverse($rootLineIdsArray);
            }
            // Only the current page is skipped (already checked above), the root page is kept.
            $rootLineIdsArray = array_filter(
                $rootLineIdsArray,
                static fn($id): bool => (int)$id !== $pid
            );

            foreach ($rootLineIdsArray as $id) {
                $config = $this->fetchConfig((int)$id);
                if (!empty($config)) {
                    break;
                }
            }
        }

        return is_array($config) ? $config : [];
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
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();
    }
}
