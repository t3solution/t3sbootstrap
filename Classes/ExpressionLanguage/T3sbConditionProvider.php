<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ExpressionLanguage;

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;
use T3SBS\T3sbootstrap\ExpressionLanguage\T3sbConditionFunctionsProvider;


/**
 * Class T3sbConditionProvider
 *
 */
class T3sbConditionProvider extends AbstractProvider
{
	public function __construct()
	{
		$this->expressionLanguageProviders = [
			T3sbConditionFunctionsProvider::class
		];
	}
}
