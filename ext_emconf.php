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

$EM_CONF[$_EXTKEY] = [
   'title' => 'Bootstrap Components',
   'description' => 'Startup extension to use bootstrap 5 classes, components and more out of the box. Example and info: [www.t3sbootstrap.de](https://www.t3sbootstrap.de)',
   'category' => 'templates',
   'version' => '5.3.50',
   'state' => 'stable',
   'author' => 'Helmut Hackbarth',
   'author_email' => 'typo3@t3solution.de',
   'author_company' => 't3solution',
   'constraints' => [
     'depends' => [
       'php' => '8.2.0-8.5.99',
       'typo3' => '14.3.0-14.99.99',
       'fluid_styled_content' => '14.3.0-14.99.99',
       'rte_ckeditor' => '14.3.0-14.99.99',
       'container' => '4.1.0-4.99.99',
     ],
     'conflicts' => [],
     'suggests' => [],
   ],
   'autoload' => [
        'psr-4' => [
            'T3sbs\\T3sbootstrap\\' => 'Classes/',
        ],
    ],
];
