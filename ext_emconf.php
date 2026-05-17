<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "t3sbootstrap".
 *
 * Auto generated 06-05-2026 12:02
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Bootstrap Components',
  'description' => 'Startup extension to use bootstrap 5 classes, components and more out of the box. Example and info: www.t3sbootstrap.de',
  'category' => 'templates',
  'version' => '5.3.42',
  'state' => 'stable',
  'uploadfolder' => false,
  'clearcacheonload' => false,
  'author' => 'Helmut Hackbarth',
  'author_email' => 'typo3@t3solution.de',
  'author_company' => 't3solution',
  'constraints' => 
  array (
    'depends' => 
    array (
      'php' => '8.2.0-8.5.99',
      'typo3' => '14.3.0-14.9.99',
      'container' => '3.2.2-3.99.99',
      't3sb_package' => '14.3.0-99.99.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
);

