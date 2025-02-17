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
class TextToTextmedia extends CommandBase
{
    /**
     * Defines the allowed options for this command
     *
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Migrate CType text to textmedia');
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
        $texts = $contentQueryBuilder
             ->select('uid',)
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
