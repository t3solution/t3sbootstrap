<?php
declare(strict_types=1);

use T3SBS\T3sbootstrap\ExpressionLanguage\T3sbConditionProvider;

defined('TYPO3') or die ('Access denied.');

/*
 * This file is part of the EXT:t3sbootstrap
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
return [
	'typoscript' => [
		T3sbConditionProvider::class
	]
];
