<?php
defined('TYPO3_MODE') or die();

call_user_func(function ($extKey) {

	if (TYPO3_MODE === 'BE') {

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
				'icon'		 => 'EXT:' . $extKey . '/Resources/Public/Images/bootstrap-solid.svg',
				'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_m1.xlf',
			]
		);
	}

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_t3sbootstrap_domain_model_config', 'EXT:t3sbootstrap/Resources/Private/Language/locallang_csh_tx_t3sbootstrap_domain_model_config.xlf');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3sbootstrap_domain_model_config');

}, $_EXTKEY);