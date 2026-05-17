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
use T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository;

#[AsCommand('t3sbootstrap:customScss', 'T3SB Custom Scss - write a custom scss file')]
final class CustomScss extends CommandBase
{

    public const BOOTSTRAPLATEST = '5.3.8';
    public const BOOTSWATCHURL   = 'https://bootswatch.com/5/';
    public const BASEDIR         = 'EXT:t3sb_package/Resources/';
    public const SCSSPATH        = self::BASEDIR.'Public/T3SB-Bootstrap/Bootstrap/scss/';
    public const VARIABLESPATH   = self::BASEDIR.'Public/T3SB-SCSS/';
    public const BOOTSTRAPPATH   = self::BASEDIR.'Public/T3SB-SCSS/Bootstrap/';

    
   public function __construct(
       private readonly SiteFinder $siteFinder,
       private readonly ConfigRepository $configRepository,
       private readonly PersistenceManager $persistenceManager,
       private readonly RequestFactory $requestFactory,
       private readonly FlashMessageService $flashMessageService,
   ) {
       parent::__construct();
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

            $bootstrapScssAbsPath = GeneralUtility::getFileAbsFileName(self::SCSSPATH);
            $uploadScssAbsPath = GeneralUtility::getFileAbsFileName(self::BOOTSTRAPPATH);

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
            $this->getBootstrapFiles($bootstrapVersion);

            $customFileName = 'custom-variables-'.$rootPageId.'.scss';
            $customFileNameOverride = 'custom-'.$rootPageId.'.scss';

            $this->writeCustomFile($settings['bootstrap']['cdn']['keepVariables'], $rootPageId, $customFileName, $settings, '_variables');
            $this->writeCustomFile($settings['bootstrap']['cdn']['keepVariables'], $rootPageId, $customFileNameOverride, $settings, '_bootswatch');

            $includeFileName = 'bootstrap-'.$rootPageId.'.scss';
            $includeFile = $uploadScssAbsPath.$includeFileName;

            if (!file_exists($includeFile)) {

               $customDir = self::VARIABLESPATH;

               $includeContent = '
@import "'.$customDir.'custom-variables-'.$rootPageId.'";
@import "'.self::BASEDIR.'Public/T3SB-Bootstrap/Bootstrap/scss/bootstrap";
@import "'.$customDir.'custom-'.$rootPageId.'";
            ';

                GeneralUtility::writeFile($includeFile, $includeContent);
            }

            $tempPath = GeneralUtility::getFileAbsFileName('typo3temp/assets/t3sbootstrap/css/');
            $this->deleteFilesFromDirectory($tempPath);

            $customFileName = 'bootstrap.scss';
            $customFile = $bootstrapScssAbsPath.$customFileName;
            $customContent = GeneralUtility::getURL($customFile);

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

            if (is_dir(GeneralUtility::getFileAbsFileName(self::BASEDIR.'Public/T3SB-Bootstrap/Bootstrap/scss/'))) {
                return Command::SUCCESS;
            }

            $this->addCustomMessage('Check the bootstrap version in the site set editor for validity!', 'ERROR');
            
            return Command::FAILURE;
        }

        $this->addCustomMessage('You have to activate SCSS in the Site Set!', 'ERROR');
        
        return Command::FAILURE;
   }


   private function writeCustomFile(bool $keepVariables, int $rootPageId, string $customFileName, array $settings, string $name): void
   {
         $bootstrapVariablesAbsPath = GeneralUtility::getFileAbsFileName(self::VARIABLESPATH);         
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

            $config = $this->configRepository->findOneBy(['pid' => $rootPageId]);
            if (!is_dir($bootstrapVariablesAbsPath)) {
               if (!mkdir($bootstrapVariablesAbsPath, 0755, true) && !is_dir($bootstrapVariablesAbsPath)) {
                     throw new \RuntimeException(sprintf('Directory "%s" was not created', $bootstrapVariablesAbsPath));
               }
            }
            $customContent = $name === '_variables' ? '// Overrides Bootstrap variables'.PHP_EOL.'// $enable-shadows: true;'.PHP_EOL.'// $enable-gradients: true;'.PHP_EOL.'// $enable-negative-margins: true;' : '// Your own SCSS';
            
            $bootswatch = $settings['bootstrap']['cdn']['bootswatch'];
            if (!empty($bootswatch)) {
               $customContent = @file_get_contents(self::BOOTSWATCHURL.strtolower($bootswatch).'/'.$name.'.scss');
               if ($name === '_variables') {
                  $customContent = str_replace(' !default', '', $customContent);
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


   private function getBootstrapFiles(string $bootstrapVersion): void
   {
      $localZipPath = GeneralUtility::getFileAbsFileName(self::BASEDIR.'Public/T3SB-Bootstrap/Bootstrap/');
      $localZipFile = GeneralUtility::getFileAbsFileName(self::BASEDIR.'Public/T3SB-Bootstrap/t3sb.zip');
      $extractTo = GeneralUtility::getFileAbsFileName(self::BASEDIR.'Public/T3SB-Bootstrap/Bootstrap/');

      if (is_dir($localZipPath)) {
         $this->rmDir($localZipPath);
      }
      if (!mkdir($localZipPath, 0755, true) && !is_dir($localZipPath)) {
         $this->addCustomMessage(sprintf('Directory "%s" was not created', $localZipPath), 'ERROR');
      }
      $zipFilename = 'v'.$bootstrapVersion.'.zip';
      $zipFilePath = 'https://github.com/twbs/bootstrap/archive/';
      $zipContent = $this->requestFactory->request($zipFilePath . $zipFilename)->getBody()->getContents();

      if (!empty($zipContent)) {
         GeneralUtility::writeFile($localZipFile, $zipContent);
         $zip = new \ZipArchive();
         if ($zip->open($localZipFile) === true) {
             $zip->extractTo($extractTo);
             $zip->close();
         } else {
             $this->addCustomMessage('Sorry ZIP creation failed at this time! Try again later.', 'ERROR');
         }

         $renameFrom = GeneralUtility::getFileAbsFileName(self::BASEDIR.'Public/T3SB-Bootstrap/Bootstrap/bootstrap-'.$bootstrapVersion.'/scss');
         $renameTo = GeneralUtility::getFileAbsFileName(self::BASEDIR.'Public/T3SB-Bootstrap/Bootstrap/scss');

         if (is_dir($renameFrom)) {
             rename($renameFrom, $renameTo);
         }

         $this->rmDir(GeneralUtility::getFileAbsFileName(self::BASEDIR . 'Public/T3SB-Bootstrap/Bootstrap/bootstrap-' . $bootstrapVersion));

         if (file_exists($localZipFile)) {
            unlink($localZipFile);
         }
      } else {
         $this->addCustomMessage('No content from GitHub archive!', 'ERROR');
      }
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
