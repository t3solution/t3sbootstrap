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

#[AsCommand('t3sbootstrap:imageToTextmedia', 'Migrate CType image to textmedia')]
class ImageToTextmedia extends CommandBase
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
        $images = $contentQueryBuilder
             ->select('uid', 'image')
             ->from('tt_content')
             ->where(
                 $contentQueryBuilder->expr()->eq('CType', $contentQueryBuilder->createNamedParameter('image'))
             )
             ->executeQuery()
             ->fetchAllAssociative();

		$contentConnection = $this->connectionPool->getConnectionForTable('tt_content');
		$sysfileConnection = $this->connectionPool->getConnectionForTable('sys_file_reference');

		foreach ($images as $image) {

			$contentConnection->update(
			    'tt_content',
			    [
			        'assets' => $image['image'],
			        'image' => 0,
			        'CType' => 'textmedia',
			    ],
			    ['uid' => (int)$image['uid']]
			);

			$sysfileConnection->update(
			    'sys_file_reference',
			    ['fieldname' => 'assets'],
			    [
			        'uid_foreign' => (int)$image['uid'],
			        'tablenames' => 'tt_content',
			        'fieldname' => 'image',
			    ]
			);
		}

        return Command::SUCCESS;
    }

}
