<?php
namespace T3SBS\T3sbootstrap\UserFunction;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Bootstrap condition (used in ../TSConfig/BackendLayouts/BootstrapCondition.ts)
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository;


class BootstrapCondition extends AbstractCondition
{

	/**
	 * Evaluate condition
	 *
	 * @param array $conditionParameters
	 * @return bool
	 */
	public function matchCondition(array $conditionParameters)
	{
		$result = FALSE;

		if ( $_GET['id'] && TYPO3_MODE == 'BE' ) {

			$pid = (int)$_GET['id'];
			$objectManager = GeneralUtility::makeInstance(ObjectManager::class);
			$configRepository = $objectManager->get(ConfigRepository::class);
			$config = $configRepository->findOneByPid($pid);

			if ( empty($config) ) {
				$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, (int)$_GET['id'])->get();

				// unset current page
				unset($rootLineArray[count($rootLineArray)-1]);

				foreach ($rootLineArray as $rootline) {
					$config = $configRepository->findOneByPid((int)$rootline['uid']);
					if ( !empty($config) ) break;
				}
			}
			if ( !empty($config) ) {

				if ( $config->getJumbotronEnable() ) {
					if ( $config->getFooterEnable() ) {
						// Content, Jumbotron & Footer
						if ( $config->getExpandedcontentEnabletop() ) {
							if ($conditionParameters[0] == 'AllandTop') {
									 $result = TRUE;
							}
						}
						if ( $config->getExpandedcontentEnablebottom() ) {
							if ($conditionParameters[0] == 'AllandBottom') {
									 $result = TRUE;
							}
						}
						if ( $config->getExpandedcontentEnabletop() && $config->getExpandedcontentEnablebottom() ) {
							if ($conditionParameters[0] == 'AllandTopBottom') {
									 $result = TRUE;
							}
						}
						if ( !$config->getExpandedcontentEnabletop() && !$config->getExpandedcontentEnablebottom() ) {
							if ($conditionParameters[0] == 'All') {
									 $result = TRUE;
							}
						}
					} else {
						// Content & Jumbotron
						if ($conditionParameters[0] == 'Jumbotron') {
								 $result = TRUE;
						}
						if ( $config->getExpandedcontentEnabletop() ) {
							if ($conditionParameters[0] == 'JumbotronandTop') {
									 $result = TRUE;
							}
						}
						if ( $config->getExpandedcontentEnablebottom() ) {
							if ($conditionParameters[0] == 'JumbotronandBottom') {
									 $result = TRUE;
							}
						}
						if ( $config->getExpandedcontentEnabletop() && $config->getExpandedcontentEnablebottom() ) {
							if ($conditionParameters[0] == 'JumbotronandTopBottom') {
									 $result = TRUE;
							}
						}
					}
				} else {
					if ( $config->getFooterEnable() ) {
						// Content & Footer
						if ($conditionParameters[0] == 'Footer') {
								 $result = TRUE;
						}
						if ( $config->getExpandedcontentEnabletop() ) {
							if ($conditionParameters[0] == 'FooterandTop') {
									 $result = TRUE;
							}
						}
						if ( $config->getExpandedcontentEnablebottom() ) {
							if ($conditionParameters[0] == 'FooterandBottom') {
									 $result = TRUE;
							}
						}

						if ( $config->getExpandedcontentEnabletop() && $config->getExpandedcontentEnablebottom() ) {
							if ($conditionParameters[0] == 'FooterandTopBottom') {
									 $result = TRUE;
							}
						}
					} else {
						// Content only (no Jumbotron & no Footer)
						if ($conditionParameters[0] == 'Content') {
								 $result = TRUE;
						}
						if ( $config->getExpandedcontentEnabletop() ) {
							if ($conditionParameters[0] == 'ContentandTop') {
									 $result = TRUE;
							}
						}
						if ( $config->getExpandedcontentEnablebottom() ) {
							if ($conditionParameters[0] == 'ContentandBottom') {
									 $result = TRUE;
							}
						}
						if ( $config->getExpandedcontentEnabletop() && $config->getExpandedcontentEnablebottom() ) {
							if ($conditionParameters[0] == 'ContentandTopBottom') {
									 $result = TRUE;
							}
						}
					}
				}
			}
		}

		return $result;
	}

}