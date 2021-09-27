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

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Bootstrap Components',
  'description' => 'Startup extension to use bootstrap 5 classes, components and more out of the box. Example and info: www.t3sbootstrap.de',
  'category' => 'templates',
  'author' => 'Helmut Hackbarth',
  'author_email' => 'typo3@t3solution.de',
  'state' => 'stable',
  'uploadfolder' => true,
  'clearCacheOnLoad' => true,
  'version' => '5.0.1',
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '10.4.18-11.99.99',
      'container' => '1.3.1-1.99.99',
      'content_defender' => '3.1.0-3.99.99',
    ),
    'conflicts' => 
    array (
      'ws_scss' => '*',
      'dyncss' => '*',
      'gridelements' => '*',
    ),
    'suggests' => 
    array (
    ),
  ),
  'autoload' => 
  array (
    'psr-4' => 
    array (
      'T3SBS\\T3sbootstrap\\' => 'Classes',
    ),
  ),
  'clearcacheonload' => true,
  'author_company' => NULL,
);

