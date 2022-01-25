<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Parser;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * ParserInterface
 */
interface ParserInterface
{
	public function supports(string $extension): bool;

	public function compile(string $file, array $settings): string;
}
