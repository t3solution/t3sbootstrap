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

#[AsCommand('t3sbootstrap:textToTextmedia', 'Migrate CType text to textmedia')]
class TextToTextmedia extends CommandBase
{
    
    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {}
    
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
		$contentQueryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $texts = $contentQueryBuilder
             ->select('uid')
             ->from('tt_content')
             ->where(
                 $contentQueryBuilder->expr()->eq('CType', $contentQueryBuilder->createNamedParameter('text'))
             )
             ->executeQuery()
             ->fetchAllAssociative();


		foreach ($texts as $text) {
		
			$contentQueryBuilder
			    ->update('tt_content')
			    ->where(
			        $contentQueryBuilder->expr()->eq('uid', $contentQueryBuilder->createNamedParameter($text['uid'], Connection::PARAM_INT)),
			    )
			    ->set('CType', 'textmedia')
			    ->executeStatement();
		}

        return Command::SUCCESS;
    }

}
