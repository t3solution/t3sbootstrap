<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;

#[AsCommand('t3sbootstrap:textToTextmedia', 'Migrate CType text to textmedia')]
class TextToTextmedia extends CommandBase
{
    
    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {
        parent::__construct();
    }
    
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
		$contentQueryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
		$contentQueryBuilder->getRestrictions()->removeAll()
			->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $texts = $contentQueryBuilder
             ->select('uid')
             ->from('tt_content')
             ->where(
                 $contentQueryBuilder->expr()->eq('CType', $contentQueryBuilder->createNamedParameter('text'))
             )
             ->executeQuery()
             ->fetchAllAssociative();


		$contentConnection = $this->connectionPool->getConnectionForTable('tt_content');

		foreach ($texts as $text) {

			$contentConnection->update(
			    'tt_content',
			    ['CType' => 'textmedia'],
			    ['uid' => (int)$text['uid']]
			);
		}

        return Command::SUCCESS;
    }

}
