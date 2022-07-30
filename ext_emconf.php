<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "t3sbootstrap".
 *
 * Auto generated 13-06-2021 10:05
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Bootstrap Components',
    'description' => 'Startup extension to use bootstrap 5 classes, components and more out of the box. Example and info: www.t3sbootstrap.de',
    'category' => 'templates',
    'author' => 'Helmut Hackbarth',
    'author_email' => 'typo3@t3solution.de',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author_company' => 't3solution',
    'version' => '5.1.21',
    'constraints' => [
      'depends' => [
        'typo3' => '10.4.18-11.9.99',
        'container' => '1.6.0-1.99.99',
      	'content_defender' => '3.2.2-3.99.99',
      ],
      'conflicts' => [
        'ws_scss' => '*',
        'dyncss' => '*',
        'gridelements' => '*',
      ],
    ],
    'autoload' => [
      'psr-4' => [
        'T3SBS\\T3sbootstrap\\' => 'Classes',
      ],
    ],
];
