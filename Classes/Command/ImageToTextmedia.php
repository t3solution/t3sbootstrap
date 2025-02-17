<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ImageToTextmedia extends CommandBase
{
    /**
     * Defines the allowed options for this command
     *
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Migrate CType image to textmedia');
    }


    /**
     * Update all records
     *
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$contentQueryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
        $images = $contentQueryBuilder
             ->select('uid', 'image')
             ->from('tt_content')
             ->where(
                 $contentQueryBuilder->expr()->eq('CType', $contentQueryBuilder->createNamedParameter('image'))
             )
             ->executeQuery()
             ->fetchAllAssociative();

		$sysfileQueryBuilder = $connectionPool->getQueryBuilderForTable('sys_file_reference');

		foreach ($images as $image) {
		
			$contentQueryBuilder
			    ->update('tt_content')
			    ->where(
			        $contentQueryBuilder->expr()->eq('uid', $contentQueryBuilder->createNamedParameter($image['uid'], Connection::PARAM_INT)),
			    )
			    ->set('assets', $image['image'])
			    ->set('image', 0)
			    ->set('CType', 'textmedia')
			    ->executeStatement();
			    
			$sysfileQueryBuilder
			    ->update('sys_file_reference')
			    ->where(
			        $sysfileQueryBuilder->expr()->eq('uid_foreign', $sysfileQueryBuilder->createNamedParameter($image['uid'], Connection::PARAM_INT)),
			    )
			    ->set('fieldname', 'assets')
			    ->executeStatement();
		}

        return Command::SUCCESS;
    }

}
