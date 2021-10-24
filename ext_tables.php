<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use T3SBS\T3sbootstrap\Controller\ConfigController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

(function () {

	ExtensionUtility::registerModule(
		'T3sbootstrap',
		'web',
		'm1',
		'',
		[
			ConfigController::class => 'list, new, create, edit, update, delete, dashboard, constants ',
		],
		[
			'access' => 'user,group',
			'icon'		 => 'EXT:t3sbootstrap/Resources/Public/Images/bootstrap-solid.svg',
			'labels' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_m1.xlf',
		]
	);

	ExtensionManagementUtility::addLLrefForTCAdescr('tx_t3sbootstrap_domain_model_config', 'EXT:t3sbootstrap/Resources/Private/Language/locallang_csh_tx_t3sbootstrap_domain_model_config.xlf');
	ExtensionManagementUtility::allowTableOnStandardPages('tx_t3sbootstrap_domain_model_config');

})();
