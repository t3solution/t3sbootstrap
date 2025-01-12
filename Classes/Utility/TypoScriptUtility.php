<?php

declare(strict_types = 1);

namespace T3SBS\T3sbootstrap\Utility;

use Psr\Http\Message\ServerRequestInterface;

/**
 * TypoScriptUtility
 */
class TypoScriptUtility
{
	public static function getSetup(ServerRequestInterface $request): array
	{
		return $request->getAttribute('frontend.typoscript')->getSetupArray();
	}

	public static function getConstants(ServerRequestInterface $request): array
	{
		return $request->getAttribute('frontend.typoscript')->getFlatSettings();
	}

}
