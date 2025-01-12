<?php
	
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class BreadcrumbProvider extends AbstractPageTitleProvider
{

    /**
     * @var ConfigurationManagerInterface
     */
    protected ConfigurationManagerInterface $configurationManager;


	/**
	 * @param string $title
	 */
	public function setTitle(string $title): void
	{
		$configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
		$settings = $configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
			't3sbootstrap',
			'm1'
		);

		$this->title = '';
		$request = $GLOBALS['TYPO3_REQUEST'];
		$frontendController = $request->getAttribute('frontend.controller');
		$rootline = $frontendController->rootLine;
		foreach (array_reverse($rootline) as $key=>$titlePart) {
			if ($key !== 0) {
				$this->title .= $titlePart['title'];
				if ($key !== count($rootline)-1) {
					$this->title .= ' '.$settings['pageTitle']['separator'].' ';
				}
			}
		}
	}
}
