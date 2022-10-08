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

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Bootstrap Components',
  'description' => 'Startup extension to use bootstrap 5 classes, components and more out of the box. Example and info: www.t3sbootstrap.de',
  'category' => 'templates',
  'version' => '5.2.6',
  'state' => 'stable',
  'uploadfolder' => false,
  'clearcacheonload' => true,
  'author' => 'Helmut Hackbarth',
  'author_email' => 'typo3@t3solution.de',
  'author_company' => 't3solution',
  'constraints' => 
  array (
    'depends' => 
    array (
	  'php' => '7.4.1-8.99.99',
      'typo3' => '10.4.18-11.9.99',
      'container' => '2.0.5-2.99.99',
      'content_defender' => '3.2.3-3.99.99',
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
);

