<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ExpressionLanguage;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

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
            $this->extconf(),
            $this->browser(),
        ];
    }

	protected function extconf(): ExpressionFunction
	{
		return new ExpressionFunction('t3sbootstrap', function ($str) {
			// Not implemented
		}, function ($arguments, $str) {

			$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

			return $extConf[$str];

		});
	}


	protected function browser(): ExpressionFunction
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

}
