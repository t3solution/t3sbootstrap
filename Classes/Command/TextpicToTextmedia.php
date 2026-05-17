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

#[AsCommand('t3sbootstrap:textpicToTextmedia', 'Migrate CType textpic to textmedia')]
class TextpicToTextmedia extends CommandBase
{
	
	public function __construct(
		private readonly ConnectionPool $connectionPool,
	) {}


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
		$contentQueryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $textpics = $contentQueryBuilder
             ->select('uid', 'image')
             ->from('tt_content')
             ->where(
                 $contentQueryBuilder->expr()->eq('CType', $contentQueryBuilder->createNamedParameter('textpic'))
             )
             ->executeQuery()
             ->fetchAllAssociative();

		$sysfileQueryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file_reference');

		foreach ($textpics as $textpic) {
		
			$contentQueryBuilder
			    ->update('tt_content')
			    ->where(
			        $contentQueryBuilder->expr()->eq('uid', $contentQueryBuilder->createNamedParameter($textpic['uid'], Connection::PARAM_INT)),
			    )
			    ->set('assets', $textpic['image'])
			    ->set('image', 0)
			    ->set('CType', 'textmedia')
			    ->executeStatement();
			    
			$sysfileQueryBuilder
			    ->update('sys_file_reference')
			    ->where(
			        $sysfileQueryBuilder->expr()->eq('uid_foreign', $sysfileQueryBuilder->createNamedParameter($textpic['uid'], Connection::PARAM_INT)),
			    )
			    ->set('fieldname', 'assets')
			    ->executeStatement();
		}

        return Command::SUCCESS;
    }

}
