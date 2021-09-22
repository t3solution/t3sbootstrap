<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addStaticFile('t3sbootstrap', 'Configuration/TypoScript', 'Bootstrap Components');
