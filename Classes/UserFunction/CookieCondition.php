<?php
namespace T3SBS\T3sbootstrap\UserFunction;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use \TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition;

class CookieCondition extends AbstractCondition
{

	/**
	 * Evaluate condition
	 *
	 * @param array $conditionParameters
	 * @return bool
	 */
	public function matchCondition(array $conditionParameters)
	{
		$result = FALSE;

		if ($conditionParameters[0] == 'Opt-In-Allow') {
			if ($_COOKIE['cookieconsent_status'] == 'allow') {
				 $result = TRUE;
			}
		}

		if ($conditionParameters[0] == 'Opt-Out-Deny') {
			if ($_COOKIE['cookieconsent_status'] == 'deny') {
				 $result = TRUE;
			}
		}

		return $result;
	}
}