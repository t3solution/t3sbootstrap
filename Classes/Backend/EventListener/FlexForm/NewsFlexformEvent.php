<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener\FlexForm;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
	identifier: 't3sbootstrap/newsFlexParsing',
	method: 'modifyDataStructure',
)]
final readonly class NewsFlexformEvent
{
	
	public function modifyDataStructure(AfterFlexFormDataStructureParsedEvent $event): void
	{
		$dataStructure = $event->getDataStructure();
		$identifier = $event->getIdentifier();

		$validKeys = ['news_pi1', 'news_newsliststicky', 'news_newsdetail', 'news_newsselectedlist'];
		if ($identifier['type'] === 'tca'
			&& $identifier['tableName'] === 'tt_content'
			&& in_array($identifier['dataStructureKey'], $validKeys, true)) {
			if (!empty($dataStructure['sheets']['template'])) {
				$file = GeneralUtility::getFileAbsFileName('EXT:t3sbootstrap/Resources/Private/Extensions/news/Configuration/FlexForms/News.xml');
				if (file_exists($file)) {
					$content = file_get_contents($file);
					if ($content) {
						ArrayUtility::mergeRecursiveWithOverrule($dataStructure['sheets'], GeneralUtility::xml2array($content));
					}
				}
			}
		}

		$event->setDataStructure($dataStructure);
	}
}
