<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class T3sbMigrateUpdateWizard implements UpgradeWizardInterface
{
	/**
	 * Return the identifier for this wizard
	 * This should be the same string as used in the ext_localconf class registration
	 */
	public function getIdentifier(): string
	{
	  return 't3sbMigrateUpdateWizard';
	}

	/**
	 * Return the speaking name of this wizard
	 */
	public function getTitle(): string
	{
	  return 'T3SBootstrap - Migrate to Bootstrap 5';
	}

	/**
	 * Return the description for this wizard
	 */
	public function getDescription(): string
	{
	  return 'Rename Bootstrap Utility Classes and change fieldname for t3sbs_carousel from image to assets';
	}

	/**
	 * Execute the update
	 *
	 * Called when a wizard reports that an update is necessary
	 */
	public function executeUpdate(): bool
	{
		$this->upgradeColumns();

 		return true;
	}

	/**
	 * Migrate Bootstrap Utility Classes from v4 to v5
	 */
	protected function upgradeColumns(): void
	{
		/* tx_t3sbootstrap_domain_model_config */
		$toRename = ['left', 'right', 'ml-', 'mr-', 'pl-', 'pr-'];
		$renameTo = ['start', 'end', 'ms-', 'me-', 'ps-', 'pe-'];
		$fields = ['page_titleclass', 'meta_class', 'navbar_class', 'jumbotron_class', 'breadcrumb_class', 'footer_class', 'expandedcontent_classtop', 'expandedcontent_classbottom', 'page_content_extra_class', 'body_extra_class', 'aside_extra_class', 'main_extra_class'];

		foreach ($fields as $field) {
			foreach ($toRename as $key=>$rename) {
				$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
				$queryBuilder = $connectionPool->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');
				$statements = $queryBuilder
						->select('uid', $field)
						->from('tx_t3sbootstrap_domain_model_config')
						->execute()
						->fetchAll();
				foreach ($statements as $statement) {
					$recordId = (int)$statement['uid'];
					$pos = strpos((string)$statement[$field], $rename);
					if ($pos === false) {
						// do nothing
					} else {
						$queryBuilder
							->update('tx_t3sbootstrap_domain_model_config')
							->where(
								$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($recordId, \PDO::PARAM_INT))
							)
							->set($field, str_replace($rename, $renameTo[$key], $statement[$field]))
							->execute();
					}
				}
			}
		}

		/* sys_file_reference */
		$fields = ['tx_t3sbootstrap_extra_class', 'tx_t3sbootstrap_extra_imgclass', 'tx_t3sbootstrap_description_align'];
		foreach ($fields as $field) {
			foreach ($toRename as $key=>$rename) {
				$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
				$queryBuilder = $connectionPool->getQueryBuilderForTable('sys_file_reference');
				$statements = $queryBuilder
					->select('uid', $field)
					->from('sys_file_reference')
					->execute()
					->fetchAll();
				foreach ($statements as $statement) {
					$recordId = (int)$statement['uid'];
					$pos = strpos((string)$statement[$field], $rename);
					if ($pos === false) {
						// do nothing
					} else {
						$queryBuilder
							->update('sys_file_reference')
							->where(
								$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($recordId, \PDO::PARAM_INT))
							)
							->set($field, str_replace($rename, $renameTo[$key], $statement[$field]))
							->execute();
					}
				}
			}
		}

		/* tt_content */
		$fields = ['tx_t3sbootstrap_extra_class', 'tx_t3sbootstrap_header_class'];
		foreach ($fields as $field) {
			foreach ($toRename as $key=>$rename) {
				$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
				$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
				$statements = $queryBuilder
						->select('uid', $field)
						->from('tt_content')
						->execute()
						->fetchAll();
				foreach ($statements as $statement) {
					$recordId = (int)$statement['uid'];
					$pos = strpos((string)$statement[$field], $rename);
					if ($pos === false) {
						// do nothing
					} else {
						$queryBuilder
							->update('tt_content')
							->where(
								$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($recordId, \PDO::PARAM_INT))
							)
							->set($field, str_replace($rename, $renameTo[$key], $statement[$field]))
							->execute();
					}
				}
			}
		}

		/* pages */
		$toRename = ['var(--'];
		$renameTo = ['var(--bs-'];
		$fields = ['tx_t3sbootstrap_titlecolor', 'tx_t3sbootstrap_subtitlecolor', 'tx_t3sbootstrap_navigationcolor', 'tx_t3sbootstrap_navigationactivecolor', 'tx_t3sbootstrap_navigationhover', 'tx_t3sbootstrap_navigationbgcolor'];
		foreach ($fields as $field) {
			foreach ($toRename as $key=>$rename) {
				$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
				$queryBuilder = $connectionPool->getQueryBuilderForTable('pages');
				$statements = $queryBuilder
					->select('uid', $field)
					->from('pages')
					->execute()
					->fetchAll();
				foreach ($statements as $statement) {
					$recordId = (int)$statement['uid'];
					$check = strpos((string)$statement[$field], 'var(--bs-');
					$pos = strpos((string)$statement[$field], $rename);
					if ($pos === false || $check === 0) {
						// do nothing
					} else {
						$queryBuilder
							->update('pages')
							->where(
								$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($recordId, \PDO::PARAM_INT))
							)
							->set($field, str_replace($rename, $renameTo[$key], $statement[$field]))
							->execute();
					}
				}
			}
		}

		/* t3sbs_carousel - move all media from image to asstes */
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$statements = $queryBuilder
			->select('uid','image')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('t3sbs_carousel'))
			)
			->execute()
			->fetchAll();

		foreach ($statements as $statement) {
			if ( $statement['image'] ) {

				$recordId = (int)$statement['uid'];
				$imageAmount = (int)$statement['image'];

				$queryBuilder
					->update('tt_content')
					->where(
						$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($recordId, \PDO::PARAM_INT))
					)
					->set('assets', $imageAmount)
					->set('image', 0)
					->execute();

				$queryBuilderSFR = $connectionPool->getQueryBuilderForTable('sys_file_reference');
				$fileStatements = $queryBuilderSFR
					->select('uid','fieldname')
					->from('sys_file_reference')
					->where(
						$queryBuilderSFR->expr()->eq('uid_foreign', $queryBuilderSFR->createNamedParameter($recordId, \PDO::PARAM_INT))
					)
					->execute()
					->fetchAll();

				foreach ($fileStatements as $fileStatement) {
					$fileId = (int)$fileStatement['uid'];
					$queryBuilderSFR
						->update('sys_file_reference')
						->where(
							$queryBuilderSFR->expr()->eq('uid', $queryBuilderSFR->createNamedParameter($fileId, \PDO::PARAM_INT))
						)
						->set('fieldname', 'assets')
						->execute();
				}
			}
		}

	}

	/**
	 * Is an update necessary?
	 *
	 * Is used to determine whether a wizard needs to be run.
	 * Check if data for migration exists.
	 */
	public function updateNecessary(): bool
	{
		$updateNeeded = false;
		// Check if the database table even exists
		if ( $this->checkIfWizardIsRequired() ) {
			$updateNeeded = true;
		}

		return $updateNeeded;
	}


	/**
	 * Returns an array of class names of prerequisite classes
	 *
	 * This way a wizard can define dependencies like "database up-to-date" or
	 * "reference index updated"
	 *
	 * @return string[]
	 */
	public function getPrerequisites(): array
	{
		return '';
	}


	/**
	 * Check for old Bootstrap Utility Classes
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function checkIfWizardIsRequired(): bool
	{

		$require = false;

		/* tx_t3sbootstrap_domain_model_config */
		$toRename = ['left', 'right', 'ml-', 'mr-', 'pl-', 'pr-'];
		$renameTo = ['start', 'end', 'ms-', 'me-', 'ps-', 'pe-'];
		$fields = ['page_titleclass', 'meta_class', 'navbar_class', 'jumbotron_class', 'breadcrumb_class', 'footer_class', 'expandedcontent_classtop', 'expandedcontent_classbottom', 'page_content_extra_class', 'body_extra_class', 'aside_extra_class', 'main_extra_class'];

		foreach ($fields as $field) {
			foreach ($toRename as $key=>$rename) {
				$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
				$queryBuilder = $connectionPool->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');
				$statements = $queryBuilder
					->select('uid', $field)
					->from('tx_t3sbootstrap_domain_model_config')
					->execute()
					->fetchAll();

				foreach ($statements as $statement) {
					$pos = strpos((string)$statement[$field], $rename);
					if ($pos === false) {
						// do nothing
					} else {
						$require = true;
					}
				}
			}
		}

		/* sys_file_reference */
		$toRename = ['left', 'right', 'ml-', 'mr-', 'pl-', 'pr-'];
		$renameTo = ['start', 'end', 'ms-', 'me-', 'ps-', 'pe-'];
		$fields = ['tx_t3sbootstrap_extra_class', 'tx_t3sbootstrap_extra_imgclass', 'tx_t3sbootstrap_description_align'];

		foreach ($fields as $field) {
			foreach ($toRename as $key=>$rename) {
				$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
				$queryBuilder = $connectionPool->getQueryBuilderForTable('sys_file_reference');
				$statements = $queryBuilder
						 ->select('uid', $field)
						 ->from('sys_file_reference')
						 ->execute()
						 ->fetchAll();
				foreach ($statements as $statement) {
					$pos = strpos((string)$statement[$field], $rename);
					if ($pos === false) {
						// do nothing
					} else {
						$require = true;
					}
				}
			}
		}

		/* pages */
		$toRename = ['var(--'];
		$renameTo = ['var(--bs-'];
		$fields = ['tx_t3sbootstrap_titlecolor', 'tx_t3sbootstrap_subtitlecolor', 'tx_t3sbootstrap_navigationcolor', 'tx_t3sbootstrap_navigationactivecolor', 'tx_t3sbootstrap_navigationhover', 'tx_t3sbootstrap_navigationbgcolor'];

		foreach ($fields as $field) {
			foreach ($toRename as $key=>$rename) {
				$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
				$queryBuilder = $connectionPool->getQueryBuilderForTable('pages');
				$statements = $queryBuilder
						 ->select('uid', $field)
						 ->from('pages')
						 ->execute()
						 ->fetchAll();
				foreach ($statements as $statement) {
					$check = strpos((string)$statement[$field], 'var(--bs-');
					$pos = strpos((string)$statement[$field], $rename);
					if ($pos === false || $check === 0) {
						// do nothing
					} else {
						$require = true;
					}
				}
			}
		}

		/* tt_content */
		$toRename = ['left', 'right', 'ml-', 'mr-', 'pl-', 'pr-'];
		$renameTo = ['start', 'end', 'ms-', 'me-', 'ps-', 'pe-'];
		$fields = ['tx_t3sbootstrap_extra_class', 'tx_t3sbootstrap_header_class'];

		foreach ($fields as $field) {
			foreach ($toRename as $key=>$rename) {
				$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
				$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
				$statements = $queryBuilder
						 ->select('uid', $field)
						 ->from('tt_content')
						 ->execute()
						 ->fetchAll();
				foreach ($statements as $statement) {
					$pos = strpos((string)$statement[$field], $rename);
					if ($pos === false) {
						// do nothing
					} else {
						$require = true;
					}
				}
			}
		}

		/* t3sbs_carousel - move all media from image to asstes */
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$statements = $queryBuilder
			->select('uid','image')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('t3sbs_carousel'))
			)
			->execute()
			->fetchAll();

		foreach ($statements as $statement) {
			if ( $statement['image'] ) {

				$require = true;
			}
		}

		return $require;
	}

}
