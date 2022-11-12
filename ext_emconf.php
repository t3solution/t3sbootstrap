$EM_CONF[$_EXTKEY] = [
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
  [
    'depends' => 
    [
      'php' => '7.4.1-8.99.99',
      'typo3' => '10.4.18-11.9.99',
      'container' => '2.0.5-2.99.99',
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
