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

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
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

            if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) {
                $browser = 'Opera';
            } elseif (strpos($user_agent, 'Edge')) {
                $browser = 'Edge';
            } elseif (strpos($user_agent, 'Chrome')) {
                $browser = 'Chrome';
            } elseif (strpos($user_agent, 'Safari')) {
                $browser = 'Safari';
            } elseif (strpos($user_agent, 'Firefox')) {
                $browser = 'Firefox';
            } elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) {
                $browser = 'Internet Explorer';
            }

            return $str === $browser;
        });
    }

    protected function getColPosList(): ExpressionFunction
    {

    return new ExpressionFunction(
        'colPosList',
        static fn () => null, // Not implemented, we only use the evaluator
        static function ($arguments, $str) {

            $result = false;
            if ( !empty($arguments['page']['uid']) ) {
                $pid = $arguments['page']['uid'];

                $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
                $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');
                $result = $queryBuilder
                    ->select('jumbotron_enable', 'footer_enable', 'expandedcontent_enabletop', 'expandedcontent_enablebottom')
                    ->from('tx_t3sbootstrap_domain_model_config')
                    ->where(
                        $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT))
                    )
                    ->executeQuery();

                $config = $result->fetchAssociative();

                if (empty($config) && is_array($arguments['tree']->rootLineIds)) {
                    $rootLineIdsArray = array_reverse($arguments['tree']->rootLineIds);
                    unset($rootLineIdsArray[count($rootLineIdsArray)-1]);
                    unset($rootLineIdsArray[0]);
                    foreach ($rootLineIdsArray as $id) {

                        $result = $queryBuilder
                            ->select('jumbotron_enable', 'footer_enable', 'expandedcontent_enabletop', 'expandedcontent_enablebottom')
                                ->from('tx_t3sbootstrap_domain_model_config')
                            ->where(
                                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, Connection::PARAM_INT))
                            )
                            ->executeQuery();

                        $config = $result->fetchAssociative();
                        if (!empty($config)) {
                            break;
                        }
                    }
                }

                if (!empty($config)) {
                    if ( !empty($config['jumbotron_enable']) ) {
                        if ( !empty($config['footer_enable']) )  {
                            // Content, Jumbotron & Footer
                            if ( empty($config['expandedcontent_enabletop']) && empty($config['expandedcontent_enablebottom']) ) {
                                if ($str === 'All') {
                                    $result = true;
                                }
                            } else {
                                if ( !empty($config['expandedcontent_enabletop']) && !empty($config['expandedcontent_enablebottom']) ) {
                                    if ($str === 'AllandTopBottom') {
                                        $result = true;
                                    }
                                } else {
                                    if ( !empty($config['expandedcontent_enabletop']) ) {
                                        if ($str === 'AllandTop') {
                                            $result = true;
                                        }
                                    }
                                    if ( !empty($config['expandedcontent_enablebottom']) ) {
                                        if ($str === 'AllandBottom') {
                                            $result = true;
                                        }
                                    }
                                }
                            }
                        } else {
                            // Content & Jumbotron
                            if ( !$config['expandedcontent_enabletop'] && !$config['expandedcontent_enablebottom'] ) {
                                if ($str === 'Jumbotron') {
                                    $result = true;
                                }
                            } else {
                                if ( $config['expandedcontent_enabletop'] && $config['expandedcontent_enablebottom'] ) {
                                    if ($str === 'JumbotronandTopBottom') {
                                        $result = true;
                                    }
                                } else {

                                    if ( $config['expandedcontent_enabletop'] ) {
                                        if ($str === 'JumbotronandTop') {
                                            $result = true;
                                        }
                                    }
                                    if ($config['expandedcontent_enablebottom']) {
                                        if ($str === 'JumbotronandBottom') {
                                            $result = true;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if ($config['footer_enable']) {
                            // Content & Footer
                            if ( !$config['expandedcontent_enabletop'] && !$config['expandedcontent_enablebottom'] ) {
                                if ($str === 'Footer') {
                                    $result = true;
                                }
                            } else {
                                if ( $config['expandedcontent_enabletop'] && $config['expandedcontent_enablebottom'] ) {
                                    if ($str === 'FooterandTopBottom') {
                                        $result = true;
                                    }
                                } else {
                                    if ($config['expandedcontent_enabletop'] && $str === 'FooterandTop') {
                                        $result = true;
                                    }
                                    if ($config['expandedcontent_enablebottom'] && $str === 'FooterandBottom') {
                                        $result = true;
                                    }
                                }
                            }
                        } elseif ( !$config['expandedcontent_enabletop'] && !$config['expandedcontent_enablebottom'] ) {
                            if ($str === 'Content') {
                                $result = true;
                            }
                        } elseif ( $config['expandedcontent_enabletop'] && $config['expandedcontent_enablebottom'] ) {
                            if ($str === 'ContentandTopBottom') {
                                $result = true;
                            }
                        } else {
                            if ($config['expandedcontent_enabletop'] && $str === 'ContentandTop') {
                                $result = true;
                            }
                            if ($config['expandedcontent_enablebottom'] && $str === 'ContentandBottom') {
                                $result = true;
                            }
                        }
                    }
                }
            }

            return $result === true;
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
