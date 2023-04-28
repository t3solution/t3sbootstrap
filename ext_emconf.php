<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "t3sbootstrap".
 *
 * Auto generated 28-09-2022 12:19
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF['t3sbootstrap'] = [
  'title' => 'Bootstrap Components',
  'description' => 'Startup extension to use bootstrap 5 classes, components and more out of the box. Example and info: www.t3sbootstrap.de',
  'category' => 'templates',
  'version' => '5.2.11',
  'state' => 'stable',
  'clearCacheOnLoad' => true,
  'author' => 'Helmut Hackbarth',
  'author_email' => 'typo3@t3solution.de',
  'author_company' => 't3solution',
  'constraints' => 
  [
    'depends' => 
    [
      'php' => '8.0.0-8.99.99',
      'typo3' => '11.5.0-11.5.99',
      'container' => '2.1.2-2.99.99',
      'content_defender' => '3.2.3-3.99.99',
    ],
    'conflicts' => 
    [
      'ws_scss' => '*',
      'dyncss' => '*',
      'gridelements' => '*',
    ],
    'suggests' => 
    [
    ],
  ],
];

