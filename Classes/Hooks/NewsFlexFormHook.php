<?php
namespace T3SBS\T3sbootstrap\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class NewsFlexFormHook
{

	public function parseDataStructureByIdentifierPostProcess(array $dataStructure, array $identifier): array
	{

	 if ($identifier['type'] === 'tca' && $identifier['tableName'] === 'tt_content'
	 	 && ($identifier['dataStructureKey'] === 'news_pi1,list' || $identifier['dataStructureKey'] === '*,news_pi1'
	 	  || $identifier['dataStructureKey'] === '*,news_newsdetail') ) {
		$file = Environment::getPublicPath() . '/typo3conf/ext/t3sbootstrap/Resources/Private/Extensions/news/Configuration/FlexForms/News.xml';
		$content = file_get_contents($file);
		if ($content) {
			$dataStructure['sheets']['extraEntry'] = GeneralUtility::xml2array($content);
		}
	}
	return $dataStructure;
	}
}
