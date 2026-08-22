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

#[AsCommand('t3sbootstrap:textpicToTextmedia', 'Migrate CType textpic to textmedia')]
class TextpicToTextmedia extends CommandBase
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
        $textpics = $contentQueryBuilder
             ->select('uid', 'image')
             ->from('tt_content')
             ->where(
                 $contentQueryBuilder->expr()->eq('CType', $contentQueryBuilder->createNamedParameter('textpic'))
             )
             ->executeQuery()
             ->fetchAllAssociative();

		$contentConnection = $this->connectionPool->getConnectionForTable('tt_content');
		$sysfileConnection = $this->connectionPool->getConnectionForTable('sys_file_reference');

		foreach ($textpics as $textpic) {

			$contentConnection->update(
			    'tt_content',
			    [
			        'assets' => $textpic['image'],
			        'image' => 0,
			        'CType' => 'textmedia',
			    ],
			    ['uid' => (int)$textpic['uid']]
			);

			$sysfileConnection->update(
			    'sys_file_reference',
			    ['fieldname' => 'assets'],
			    [
			        'uid_foreign' => (int)$textpic['uid'],
			        'tablenames' => 'tt_content',
			        'fieldname' => 'image',
			    ]
			);
		}

        return Command::SUCCESS;
    }

}
