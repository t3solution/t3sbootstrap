<?php

use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [

	'cssJsIcon' => [
		 'provider' => BitmapIconProvider::class,
		 'source' => 'EXT:t3sbootstrap/Resources/Public/Icons/Register/css-javascript.png',
	],

	'bootstraplogo' => [
		'provider' => SvgIconProvider::class,
		'source' => 'EXT:t3sbootstrap/Resources/Public/Icons/Extension.svg',
	],
	
	'tx-myextension-svgicon' => [
		// Icon provider class
		'provider' => SvgIconProvider::class,
		// The source SVG for the SvgIconProvider
		'source' => 'EXT:my_extension/Resources/Public/Icons/mysvg.svg',
	],

];
