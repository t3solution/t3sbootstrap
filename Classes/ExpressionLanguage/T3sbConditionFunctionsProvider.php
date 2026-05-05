<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ExpressionLanguage;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Http\NormalizedParams;

class T3sbConditionFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            $this->getExtconf(),
            $this->getBrowser(),
            $this->getColPosList(),
            $this->getExtensionLoaded(),
        ];
    }

    protected function getExtconf(): ExpressionFunction
    {
        return new ExpressionFunction('t3sbootstrap', function ($str) {
            // Not implemented
        }, function ($arguments, $str) {
            $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

            if ($str === 'extNews') {	
                if ( !empty($extConf[$str]) && ExtensionManagementUtility::isLoaded('news') ) {
                    return '1';
                }

                return '0';
            }

            if ( !empty($extConf[$str]) ) {
                return '1';
            }

            return '0';
        });
    }

    protected function getBrowser(): ExpressionFunction
    {
        return new ExpressionFunction('browser', function ($str) {
            // Not implemented
        }, function ($arguments, $str) {
            $user_agent = GeneralUtility::getIndpEnv('HTTP_USER_AGENT');
            $browser = 'Other';

            if (str_contains($user_agent, 'Opera') || str_contains($user_agent, 'OPR/')) {
                $browser = 'Opera';
            } elseif (str_contains($user_agent, 'Edge')) {
                $browser = 'Edge';
            } elseif (str_contains($user_agent, 'Chrome')) {
                $browser = 'Chrome';
            } elseif (str_contains($user_agent, 'Safari')) {
                $browser = 'Safari';
            } elseif (str_contains($user_agent, 'Firefox')) {
                $browser = 'Firefox';
            } elseif (str_contains($user_agent, 'MSIE') || str_contains($user_agent, 'Trident/7')) {
                $browser = 'Internet Explorer';
            }

            return $str === $browser;
        });
    }


    protected function getColPosList(): ExpressionFunction
    {

        return new ExpressionFunction('colPosList', function ($str) {
            // Not implemented
        }, function ($arguments, $str) {

            $result = false;
            if ( !empty($arguments['page']['uid']) ) {
                $pid = $arguments['page']['uid'];
                $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
                $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');
                $config = $queryBuilder
                    ->select('jumbotron_enable', 'footer_enable', 'expandedcontent_enabletop')
                    ->from('tx_t3sbootstrap_domain_model_config')
                    ->where(
                        $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT))
                    )
                    ->setMaxResults(1)
                    ->executeQuery()
                    ->fetchAssociative();

                if (empty($config) && is_array($arguments['tree']->rootLineIds)) {
                    $rootLineIdsArray = array_reverse($arguments['tree']->rootLineIds);
                    array_pop($rootLineIdsArray);
                    array_shift($rootLineIdsArray);
                    foreach ($rootLineIdsArray as $id) {
                        $config = $queryBuilder
                            ->select('jumbotron_enable', 'footer_enable', 'expandedcontent_enabletop')
                                ->from('tx_t3sbootstrap_domain_model_config')
                            ->where(
                                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, Connection::PARAM_INT))
                            )
                            ->setMaxResults(1)
                            ->executeQuery()
                            ->fetchAssociative();

                        if (!empty($config)) {
                            break;
                        }
                    }
                }

                $jumbotron = !empty($config['jumbotron_enable']) ? $config['jumbotron_enable'] : 0;
                $footer = !empty($config['footer_enable']) ? $config['footer_enable'] : 0;
                $expandedcontent = !empty($config['expandedcontent_enabletop']) ? $config['expandedcontent_enabletop'] : 0;

                if (empty($expandedcontent)) {
            
                    if ( !empty($jumbotron) && !empty($footer) )  {
                        if ($str === 'JF') {
                            $result = true;
                        }
                    }
                    if ( !empty($jumbotron) && empty($footer) )  {
                        if ($str === 'J') {
                            $result = true;
                        }
                    }
                    if ( empty($jumbotron) && !empty($footer) )  {
                        if ($str === 'F') {
                            $result = true;
                        }
                    }
                    if ( empty($jumbotron) && empty($footer) )  {
                        if ($str === 'NONE') {
                            $result = true;
                        }
                    }
            
                } else {
            
                    if ( !empty($jumbotron) && !empty($footer) )  {
                        if ($str === 'ALL') {
                            $result = true;
                        }
                    }
            
                    if ( empty($jumbotron) && empty($footer) )  {
                        if ($str === 'E') {
                            $result = true;
                        }
                    }
                    
                    if ( !empty($jumbotron) && empty($footer) )  {
                        if ($str === 'JE') {
                            $result = true;
                        }
                    }
            
                    if ( empty($jumbotron) && !empty($footer) )  {
                        if ($str === 'FE') {
                            $result = true;
                        }
                    }
                }
            }

            return $result;
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
    
}
