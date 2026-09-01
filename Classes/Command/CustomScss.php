<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use T3SBS\T3sbootstrap\Domain\Model\Config;
use T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository;
use T3SBS\T3sbootstrap\Service\AssetPathService;

#[AsCommand('t3sbootstrap:customScss', 'T3SB Custom Scss - write a custom scss file')]
final class CustomScss extends CommandBase
{

    public const BOOTSTRAPLATEST = '5.3.8';
    public const BOOTSWATCHURL   = 'https://bootswatch.com/5/';


   public function __construct(
       private readonly SiteFinder $siteFinder,
       private readonly ConfigRepository $configRepository,
       private readonly PersistenceManager $persistenceManager,
       private readonly RequestFactory $requestFactory,
       private readonly FlashMessageService $flashMessageService,
       private readonly AssetPathService $assetPathService,
   ) {
       parent::__construct();
   }


   /**
    * Generated: the bootstrap sources extracted from the GitHub archive
    *
    */
   private function getScssPath(): string
   {
      return $this->assetPathService->getPath('T3SB-Bootstrap/Bootstrap/scss');
   }


   /**
    * Editable sources: custom-variables-<uid>.scss and custom-<uid>.scss
    *
    */
   private function getVariablesPath(): string
   {
      return $this->assetPathService->getScssPath();
   }


   /**
    * Generated: the include file bootstrap-<uid>.scss
    *
    */
   private function getBootstrapPath(): string
   {
      return $this->assetPathService->getPath('T3SB-SCSS/Bootstrap');
   }
    
    
    /**
     * Defines the allowed options for this command
     *
     */
   protected function configure(): void
   {
   $this
      ->setHelp('This command accepts arguments')
      ->addArgument(
            'rootPageId',
            InputArgument::REQUIRED,
            'Root page ID',
      );
   }
        

    /**
     * Update all records
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
         if (!extension_loaded('zip')) {
            $this->addCustomMessage('The PHP extension “zip” must be loaded.', 'ERROR');
         }

         if ( !is_numeric($input->getArgument('rootPageId')) ) {
            $this->addCustomMessage('Root page ID should be a number!', 'ERROR');
         }

         $rootPageId = (int) $input->getArgument('rootPageId');
         $configuration = $this->siteFinder->getSiteByPageId($rootPageId)->getConfiguration();
         $settings = $configuration['settings'];

         if (empty($settings['bootstrap']['cdn']['bootstrap'])) {
            $this->addCustomMessage('The optional site set “T3S Bootstrap – VERSION” should be integrated.', 'ERROR');
         }

         $isSiteroot = BackendUtility::getRecord('pages', $rootPageId, 'is_siteroot')['is_siteroot'];
         if (empty($isSiteroot)) {
            $this->addCustomMessage('Your selection is not a root page.', 'ERROR');
         }

         if ($settings['bootstrap']['cdn']['customScss'] === true && $settings['bootstrap']['cdn']['enable'] === false) {

            $bootstrapScssAbsPath = $this->getScssPath();
            $uploadScssAbsPath = $this->getBootstrapPath();

            if (!is_dir($bootstrapScssAbsPath)) {
                if (!mkdir($bootstrapScssAbsPath, 0755, true) && !is_dir($bootstrapScssAbsPath)) {
                    $this->addCustomMessage(sprintf('Directory "%s" for SCSS files was not created.', $bootstrapScssAbsPath), 'ERROR');
                }
            }

            if (!is_dir($uploadScssAbsPath)) {
                if (!mkdir($uploadScssAbsPath, 0755, true) && !is_dir($uploadScssAbsPath)) {
                    $this->addCustomMessage(sprintf('Directory "%s" was not created', $uploadScssAbsPath), 'ERROR');
                }
            }

            $bootstrapVersion = str_starts_with($settings['bootstrap']['cdn']['bootstrap'], '5.') ? $settings['bootstrap']['cdn']['bootstrap'] : self::BOOTSTRAPLATEST;

            // The sources are no longer deleted before the download, so their mere
            // presence says nothing about this run - ask getBootstrapFiles() itself.
            if (!$this->getBootstrapFiles($bootstrapVersion)) {
                $this->addCustomMessage('Check the bootstrap version in the site set editor for validity!', 'ERROR');
                $output->writeln('<error>Check the bootstrap version in the site set editor for validity!</error>');

                return Command::FAILURE;
            }

            $customFileName = 'custom-variables-'.$rootPageId.'.scss';
            $customFileNameOverride = 'custom-'.$rootPageId.'.scss';

            $this->writeCustomFile($settings['bootstrap']['cdn']['keepVariables'], $rootPageId, $customFileName, $settings, '_variables');
            $this->writeCustomFile($settings['bootstrap']['cdn']['keepVariables'], $rootPageId, $customFileNameOverride, $settings, '_bootswatch');

            $includeFileName = 'bootstrap-'.$rootPageId.'.scss';
            $includeFile = $uploadScssAbsPath.$includeFileName;

            // Always rewritten, never only when missing: a file left over from an
            // older version still carries the import paths of that version, and a
            // stale import kills the compile step.
            // Paths are relative to $includeFile in T3SB-SCSS/Bootstrap/ - an
            // absolute server path would be baked in and, if it ever fails to
            // resolve, scssphp writes it verbatim into the public css.
            $includeContent = '
@import "../custom-variables-'.$rootPageId.'";
@import "../../T3SB-Bootstrap/Bootstrap/scss/bootstrap";
@import "../custom-'.$rootPageId.'";
            ';

            GeneralUtility::writeFile($includeFile, $includeContent);

            $tempPath = GeneralUtility::getFileAbsFileName('typo3temp/assets/t3sbootstrap/css/');
            $this->deleteFilesFromDirectory($tempPath);

            $customFileName = 'bootstrap.scss';
            $customFile = $bootstrapScssAbsPath.$customFileName;
            // getURL() returns false when the file is not there - writeFile() would
            // get that false under strict_types
            $customContent = GeneralUtility::getURL($customFile);

            if (!is_string($customContent) || $customContent === '') {
                $this->addCustomMessage(sprintf('"%s" could not be read.', $customFile), 'ERROR');
                $output->writeln(sprintf('<error>"%s" could not be read.</error>', $customFile));

                return Command::FAILURE;
            }

            if ( !empty($settings['optimize']) ) {
                // if site set bootstrap-optimize is set
                foreach ($settings['optimize'] as $component=>$import) {
                    if (!$import && $customContent) {
                        $find = '@import "'.$component.'";';
                        $replace = '// @import "'.$component.'";';
                        $customContent = str_replace($find, $replace, $customContent);
                    }
                }
            }
   
            GeneralUtility::writeFile($customFile, $customContent);

            return Command::SUCCESS;
        }

        $this->addCustomMessage('You have to activate SCSS in the Site Set!', 'ERROR');
        
        return Command::FAILURE;
   }


   private function writeCustomFile(bool $keepVariables, int $rootPageId, string $customFileName, array $settings, string $name): void
   {
         // Checked up front, not further down: below the existing file is copied
         // away and deleted, and bailing out after that would leave the include
         // file pointing at a scss file that is no longer there.
         $config = $this->configRepository->findOneBy(['pid' => $rootPageId]);

         if ($keepVariables === false && !$config instanceof Config) {
            throw new \RuntimeException(
               sprintf(
                  'No t3sbootstrap configuration record found on page %d - open the t3sbootstrap module on that page once and save it.',
                  $rootPageId
               ),
               1756400001
            );
         }

         $bootstrapVariablesAbsPath = $this->getVariablesPath();
         // delete all files with timestamp except the last 30 (true)
         $this->deleteFilesFromDirectory($bootstrapVariablesAbsPath, true);

         $customFile = $bootstrapVariablesAbsPath.$customFileName;

         if (file_exists($customFile)) {
             $copyFile = $bootstrapVariablesAbsPath.'_'.time().'-'.$customFileName;
             if (!copy($customFile, $copyFile)) {
                 $this->addCustomMessage('Copy of "Write Custom File" faild', 'ERROR');
             }
             if ($keepVariables === false) {
                 unlink($customFile);
             }
         }

         if (!file_exists($customFile) && $keepVariables === false) {

            if (!is_dir($bootstrapVariablesAbsPath)) {
               if (!mkdir($bootstrapVariablesAbsPath, 0755, true) && !is_dir($bootstrapVariablesAbsPath)) {
                     throw new \RuntimeException(sprintf('Directory "%s" was not created', $bootstrapVariablesAbsPath));
               }
            }
            $customContent = $name === '_variables' ? '// Overrides Bootstrap variables'.PHP_EOL.'// $enable-shadows: true;'.PHP_EOL.'// $enable-gradients: true;'.PHP_EOL.'// $enable-negative-margins: true;' : '// Your own SCSS';

            $bootswatch = $settings['bootstrap']['cdn']['bootswatch'] ?? '';
            if (!empty($bootswatch)) {
               // A dead Bootswatch server used to put "false" into the database and
               // into str_replace() - keep the default content instead.
               $bootswatchContent = @file_get_contents(self::BOOTSWATCHURL.strtolower($bootswatch).'/'.$name.'.scss');

               if (is_string($bootswatchContent) && $bootswatchContent !== '') {
                  $customContent = $name === '_variables'
                     ? str_replace(' !default', '', $bootswatchContent)
                     : $bootswatchContent;
               } else {
                  $this->addCustomMessage(
                     sprintf('Could not load the Bootswatch theme "%s" - the default content was used instead.', $bootswatch),
                     'ERROR'
                  );
               }
            }
            if ($name === '_variables') {
               $config->setCustomVariablesScss($customContent);
            } else {
               $config->setCustomScss($customContent);
            }

            $this->configRepository->update($config);
            $this->persistenceManager->persistAll();

             GeneralUtility::writeFile($customFile, $customContent);
         }
     }


    private function deleteFilesFromDirectory(string $directory, bool $onlyunderlined=false): void
    {
        if (is_dir($directory)) {
            if ($dh = opendir($directory)) {
               $n = 1;
               while (($file = readdir($dh)) !== false) {
                  if (!in_array($file,['.','..'])) {
                     if ($onlyunderlined === true) {
                        if (str_starts_with($file, '_')) {
                           $n++;
                           if ($n > 30) {
                              unlink($directory.$file);
                           }
                        }
                     } else {
                        unlink($directory.$file);
                     }
                  }
               }
               closedir($dh);
            }
        }
    }


   private function getBootstrapFiles(string $bootstrapVersion): bool
   {
      $t3sbBootstrapPath = $this->assetPathService->getPath('T3SB-Bootstrap');
      $localZipPath = $t3sbBootstrapPath.'Bootstrap/';
      $localZipFile = $t3sbBootstrapPath.'t3sb.zip';

      // Extract into a staging directory and only swap it in when everything went
      // through. Removing the sources up front left the installation without them
      // whenever the version was wrong or GitHub was unreachable - and the SCSS
      // compiler then took the whole frontend down with a missing @import.
      $stagingPath = $t3sbBootstrapPath.'Bootstrap.tmp/';

      if (is_dir($stagingPath)) {
         $this->rmDir($stagingPath);
      }
      if (!mkdir($stagingPath, 0755, true) && !is_dir($stagingPath)) {
         $this->addCustomMessage(sprintf('Directory "%s" was not created', $stagingPath), 'ERROR');

         return false;
      }

      $zipFilename = 'v'.$bootstrapVersion.'.zip';
      $zipFilePath = 'https://github.com/twbs/bootstrap/archive/';

      try {
         $zipContent = $this->requestFactory->request($zipFilePath . $zipFilename)->getBody()->getContents();
      } catch (\Throwable $e) {
         $this->rmDir($stagingPath);
         $this->addCustomMessage(
            sprintf('Could not download Bootstrap %s - the existing sources were kept: %s', $bootstrapVersion, $e->getMessage()),
            'ERROR'
         );

         return false;
      }

      if (empty($zipContent)) {
         $this->rmDir($stagingPath);
         $this->addCustomMessage('No content from GitHub archive! The existing sources were kept.', 'ERROR');

         return false;
      }

      GeneralUtility::writeFile($localZipFile, $zipContent);
      $zip = new \ZipArchive();

      if ($zip->open($localZipFile) === true) {
          $zip->extractTo($stagingPath);
          $zip->close();
      } else {
          $this->rmDir($stagingPath);
          if (file_exists($localZipFile)) {
             unlink($localZipFile);
          }
          $this->addCustomMessage('Sorry ZIP creation failed at this time! The existing sources were kept.', 'ERROR');

          return false;
      }

      if (file_exists($localZipFile)) {
         unlink($localZipFile);
      }

      $renameFrom = $stagingPath.'bootstrap-'.$bootstrapVersion.'/scss';
      $renameTo = $stagingPath.'scss';

      if (!is_dir($renameFrom)) {
         $this->rmDir($stagingPath);
         $this->addCustomMessage(
            sprintf('The archive did not contain "bootstrap-%s/scss" - check the Bootstrap version in the Site Set. The existing sources were kept.', $bootstrapVersion),
            'ERROR'
         );

         return false;
      }

      if (!@rename($renameFrom, $renameTo)) {
         $this->rmDir($stagingPath);
         $this->addCustomMessage(sprintf('Directory "%s" was not created', $renameTo), 'ERROR');

         return false;
      }

      $this->rmDir($stagingPath . 'bootstrap-' . $bootstrapVersion);

      // The new sources are complete. Park the old ones instead of deleting them
      // right away - if the rename below fails, both directories would be gone.
      $backupPath = rtrim($localZipPath, '/').'.old/';

      if (is_dir($backupPath)) {
         $this->rmDir($backupPath);
      }
      if (is_dir($localZipPath) && !@rename($localZipPath, $backupPath)) {
         $this->rmDir($stagingPath);
         $this->addCustomMessage(sprintf('Directory "%s" could not be replaced', $localZipPath), 'ERROR');

         return false;
      }

      if (!@rename($stagingPath, $localZipPath)) {
         // put the old sources back, then report the failure
         if (is_dir($backupPath)) {
            @rename($backupPath, $localZipPath);
         }
         $this->rmDir($stagingPath);
         $this->addCustomMessage(sprintf('Directory "%s" was not created', $localZipPath), 'ERROR');

         return false;
      }

      $this->rmDir($backupPath);

      return true;
   }


   private function addCustomMessage(string $text, string $header): void
   {
      $message = GeneralUtility::makeInstance(
         FlashMessage::class,
         $text,
         $header,
         ContextualFeedbackSeverity::ERROR
      );
      
      $defaultFlashMessageQueue = $this->flashMessageService->getMessageQueueByIdentifier();
      $defaultFlashMessageQueue->enqueue($message);
   }
    
}
