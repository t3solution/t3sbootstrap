<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use T3SBS\T3sbootstrap\Service\ConfigTransferService;
use T3SBS\T3sbootstrap\Service\SystemTransferService;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsCommand('t3sbootstrap:configExport', 'Write the complete t3sbootstrap configuration into a json file')]
final class ConfigExport extends CommandBase
{
    public function __construct(
        private readonly ConfigTransferService $configTransferService,
        private readonly SystemTransferService $systemTransferService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp(
            'The same export the backend module offers, for a schedule or a deployment script.'
            . PHP_EOL . PHP_EOL
            . 'The file holds the configuration record with the own scss, the extension'
            . ' configuration, the t3sbootstrap sets and settings of every site, and an'
            . ' inventory of this installation - versions and installed extensions - so the'
            . ' target can tell what it is missing. Without --file it is written to'
            . ' var/transfer/ with a timestamp in its name.' . PHP_EOL
            . 'Keep in mind that this is a text file in the clear - it carries no passwords,'
            . ' but it does describe the appearance and the extension inventory of the'
            . ' installation.'
        );

        $this->addOption(
            'file',
            'f',
            InputOption::VALUE_REQUIRED,
            'Target file. A relative path is taken from the project root.'
        );

        $this->addOption(
            'pid',
            'p',
            InputOption::VALUE_REQUIRED,
            'Export only the configuration record of this page. Without it every record of the'
            . ' installation is written, which is a backup - an import needs a single page.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pid = (int)$input->getOption('pid');

        try {
            $payload = $this->configTransferService->export($pid > 0 ? $pid : null);
            $payload += $this->systemTransferService->export();
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $json = (string)json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $file = $this->resolveTargetFile((string)$input->getOption('file'), $pid);

        GeneralUtility::mkdir_deep(dirname($file));

        if (!GeneralUtility::writeFile($file, $json, true)) {
            $io->error('Could not write ' . $file);

            return Command::FAILURE;
        }

        $pages = [];
        foreach ((array)($payload['records'] ?? []) as $record) {
            $pages[(int)(((array)($record['sourcePage'] ?? []))['uid'] ?? 0)] = true;
        }

        if (count($pages) > 1) {
            $io->note(sprintf(
                'The file holds the configurations of %d pages and cannot be imported into a'
                . ' single page - it is a backup of the whole installation. Use --pid to export'
                . ' one root page for a transfer.',
                count($pages)
            ));
        }

        $io->success(sprintf(
            '%d record(s), %d extension configuration value(s), %d site(s) and %d installed'
            . ' extension(s) written to %s',
            count($payload['records'] ?? []),
            count($payload['extensionConfiguration'] ?? []),
            count($payload['sites'] ?? []),
            count($payload['environment']['extensions'] ?? []),
            $file
        ));

        return Command::SUCCESS;
    }

    /**
     * An absolute path is taken as it is, a relative one from the project root, and no
     * option at all lands in var/transfer/ - outside the public directory, because the
     * file describes the whole installation.
     */
    private function resolveTargetFile(string $file, int $pid): string
    {
        if ($file === '') {
            return Environment::getVarPath() . '/transfer/'
                . sprintf('t3sbootstrap-config-%s-pid%d.json', date('Ymd-His'), $pid);
        }

        $file = str_replace('\\', '/', $file);

        return str_starts_with($file, '/') ? $file : Environment::getProjectPath() . '/' . ltrim($file, '/');
    }
}
