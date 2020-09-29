<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ExpressionLanguage;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository;


/**
 * Class T3sbConditionFunctionsProvider
 * @internal
 */
class T3sbConditionFunctionsProvider implements ExpressionFunctionProviderInterface {


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

			return $extConf[$str];

		});
	}


	protected function getBrowser(): ExpressionFunction
	{
	  return new ExpressionFunction('browser', function ($str) {
		 // Not implemented
	  }, function ($arguments, $str) {

		$user_agent = GeneralUtility::getIndpEnv('HTTP_USER_AGENT');
		 $browser = 'Other';

		 if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) $browser = 'Opera';
		 elseif (strpos($user_agent, 'Edge')) $browser = 'Edge';
		 elseif (strpos($user_agent, 'Chrome')) $browser = 'Chrome';
		 elseif (strpos($user_agent, 'Safari')) $browser = 'Safari';
		 elseif (strpos($user_agent, 'Firefox')) $browser = 'Firefox';
		 elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) $browser = 'Internet Explorer';

		 return $str === $browser;
	  });

    }


	protected function getColPosList(): ExpressionFunction
	{
		return new ExpressionFunction('colPosList', function ($str) {
			// Not implemented
		}, function ($arguments, $str) {

			$result = FALSE;
	
			if ( $_GET['id'] && TYPO3_MODE == 'BE' ) {
	
				$pid = (int)$_GET['id'];
				$objectManager = GeneralUtility::makeInstance(ObjectManager::class);
				$configRepository = $objectManager->get(ConfigRepository::class);
				$config = $configRepository->findOneByPid($pid);

				if ( empty($config) ) {
					$rootLineIdsArray = array_reverse($arguments['tree']->rootLineIds);
					unset($rootLineIdsArray[count($rootLineIdsArray)-1]);
					unset($rootLineIdsArray[0]);

					foreach ($rootLineIdsArray as $id) {
						$config = $configRepository->findOneByPid($id);
						if ( !empty($config) ) break;
					}
				}

				if ( !empty($config) ) {

					if ( $config->getJumbotronEnable() ) {
						if ( $config->getFooterEnable() ) {
							// Content, Jumbotron & Footer
							if ( $config->getExpandedcontentEnabletop() ) {
								if ($str == 'AllandTop') {
										 $result = TRUE;
								}
							}
							if ( $config->getExpandedcontentEnablebottom() ) {
								if ($str == 'AllandBottom') {
										 $result = TRUE;
								}
							}
							if ( $config->getExpandedcontentEnabletop() && $config->getExpandedcontentEnablebottom() ) {
								if ($str == 'AllandTopBottom') {
										 $result = TRUE;
								}
							}
							if ( !$config->getExpandedcontentEnabletop() && !$config->getExpandedcontentEnablebottom() ) {
								if ($str == 'All') {
										 $result = TRUE;
								}
							}
						} else {
							// Content & Jumbotron
							if ($str == 'Jumbotron') {
									 $result = TRUE;
							}
							if ( $config->getExpandedcontentEnabletop() ) {
								if ($str == 'JumbotronandTop') {
										 $result = TRUE;
								}
							}
							if ( $config->getExpandedcontentEnablebottom() ) {
								if ($str == 'JumbotronandBottom') {
										 $result = TRUE;
								}
							}
							if ( $config->getExpandedcontentEnabletop() && $config->getExpandedcontentEnablebottom() ) {
								if ($str == 'JumbotronandTopBottom') {
										 $result = TRUE;
								}
							}
						}
					} else {
						if ( $config->getFooterEnable() ) {
							// Content & Footer
							if ($str == 'Footer') {
									 $result = TRUE;
							}
							if ( $config->getExpandedcontentEnabletop() ) {
								if ($str == 'FooterandTop') {
										 $result = TRUE;
								}
							}
							if ( $config->getExpandedcontentEnablebottom() ) {
								if ($str == 'FooterandBottom') {
										 $result = TRUE;
								}
							}
	
							if ( $config->getExpandedcontentEnabletop() && $config->getExpandedcontentEnablebottom() ) {
								if ($str == 'FooterandTopBottom') {
										 $result = TRUE;
								}
							}
						} else {
							// Content only (no Jumbotron & no Footer)
							if ($str == 'Content') {
									 $result = TRUE;
							}
							if ( $config->getExpandedcontentEnabletop() ) {
								if ($str == 'ContentandTop') {
										 $result = TRUE;
								}
							}
							if ( $config->getExpandedcontentEnablebottom() ) {
								if ($str == 'ContentandBottom') {
										 $result = TRUE;
								}
							}
							if ( $config->getExpandedcontentEnabletop() && $config->getExpandedcontentEnablebottom() ) {
								if ($str == 'ContentandTopBottom') {
										 $result = TRUE;
								}
							}
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
