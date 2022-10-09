<?php
if (!getenv('TYPO3_PATH_COMPOSER_ROOT')) {
    putenv('TYPO3_PATH_COMPOSER_ROOT=' . dirname(dirname(__DIR__)));
    $_ENV['TYPO3_PATH_COMPOSER_ROOT'] = dirname(dirname(__DIR__));
}
if (!getenv('TYPO3_PATH_APP')) {
    putenv('TYPO3_PATH_APP=' . dirname(dirname(__DIR__)));
    $_ENV['TYPO3_PATH_APP'] = dirname(dirname(__DIR__));
}
if (!getenv('TYPO3_PATH_ROOT')) {
    putenv('TYPO3_PATH_ROOT=' . dirname(dirname(__DIR__)).'/public');
    $_ENV['TYPO3_PATH_ROOT'] = dirname(dirname(__DIR__)).'/public';
}
if (!getenv('TYPO3_PATH_WEB')) {
    putenv('TYPO3_PATH_WEB=' . dirname(dirname(__DIR__)).'/public');
    $_ENV['TYPO3_PATH_WEB'] = dirname(dirname(__DIR__)).'/public';
}
// TYPO3 is installed via composer. Flag this with a constant.
if (!defined('TYPO3_COMPOSER_MODE')) {
    define('TYPO3_COMPOSER_MODE', TRUE);
}
