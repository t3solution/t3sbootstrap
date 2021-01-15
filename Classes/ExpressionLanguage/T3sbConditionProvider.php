<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ExpressionLanguage;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

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
