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

];
