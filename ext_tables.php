<?php
defined('TYPO3_MODE') or die();

call_user_func(function () {

	if (TYPO3_MODE === 'BE') {

		if (version_compare(TYPO3_branch, '10.0', '>=')) {

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
				'T3sbootstrap',
				'web', // Make module a submodule of 'web'
				'm1', // Submodule key
				'', // Position
				[
					\T3SBS\T3sbootstrap\Controller\ConfigController::class => 'list, new, create, edit, update, delete, ',
				],
				[
					'access' => 'user,group',
					'icon'		 => 'EXT:t3sbootstrap/Resources/Public/Images/bootstrap-solid.svg',
					'labels' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_m1.xlf',
				]
			);

		} else {

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
				'T3SBS.T3sbootstrap',
				'web', // Make module a submodule of 'web'
				'm1', // Submodule key
				'', // Position
				[
					'Config' => 'list, new, create, edit, update, delete, ',
				],
				[
					'access' => 'user,group',
					'icon'		 => 'EXT:t3sbootstrap/Resources/Public/Images/bootstrap-solid.svg',
					'labels' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_m1.xlf',
				]
			);

		}

	}

	$menuheader = 198;

	// Add new page type:
	$GLOBALS['PAGES_TYPES'][$menuheader] = [
		'type' => 'web',
		'allowedTables' => '*',
	];

	// Add new page type as possible select item:
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
		'pages',
		'doktype',
		[
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang.xlf:menuheader_type',
			$menuheader,
			'content-header'
		],
		'2',
		'after'
	);

	// Add icon for new page type:
	\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
		$GLOBALS['TCA']['pages'],
		[
			'ctrl' => [
				'typeicon_classes' => [
					$menuheader => 'content-header',
				],
			],
		]
	);

	// Allow backend users to drag and drop the new page type:
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
		'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $menuheader . ')'
	);

	
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_t3sbootstrap_domain_model_config', 'EXT:t3sbootstrap/Resources/Private/Language/locallang_csh_tx_t3sbootstrap_domain_model_config.xlf');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3sbootstrap_domain_model_config');

});
