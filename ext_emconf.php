<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "t3sbootstrap".
 *
 * Auto generated 10-01-2019 08:34
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
	'title' => 'Bootstrap Components',
	'description' => 'Startup extension to use bootstrap 4 classes, components and more out of the box. Example and info: www.t3sbootstrap.de',
	'category' => 'templates',
	'author' => 'Helmut Hackbarth',
	'author_email' => 'typo3@t3solution.de',
	'state' => 'stable',
	'uploadfolder' => true,
	'createDirs' => '',
	'clearCacheOnLoad' => true,
	'version' => '4.6.1',
	'constraints' => [
	'depends' => [
		'typo3' => '10.4.10-11.99.99',
		'container' => '1.2.0-1.99.99',
		'content_defender' => '3.1.0-3.99.99'
	],
	'conflicts' => [],
	],
	'autoload' => [
		'psr-4' => ['T3SBS\\T3sbootstrap\\' => 'Classes']
	],
	'clearcacheonload' => true,
	'author_company' => NULL,
];
