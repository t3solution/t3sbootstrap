<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use T3SBS\T3sbootstrap\Service\AnchorService;
use TYPO3\CMS\Core\Core\Bootstrap;

#[AsCommand(
    't3sbootstrap:anchor',
    'T3SB Speaking ID - generate the anchor of content elements from their header'
)]
final class Anchor extends CommandBase
{
    public function __construct(private readonly AnchorService $anchorService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp(
            'Without options the command shows what it would do and changes nothing.' . PHP_EOL . PHP_EOL
            . 'Mode "empty" (default) fills elements that have no anchor - the same thing that'
            . ' happens when such an element is opened and saved.' . PHP_EOL
            . 'Mode "rebuild" also rewrites every anchor that no longer matches its own header.'
            . ' That is the case after copying: the copy keeps the anchor of its source. It is'
            . ' also the case for an anchor somebody typed on purpose - those are overwritten'
            . ' too, they cannot be told apart. Existing links to the old anchor stop working,'
            . ' so read the dry run before adding --execute.' . PHP_EOL
            . 'Mode "clear" empties every anchor. Use it after switching the extension option'
            . ' "Speaking ID" off: the option only hides the field, it does not remove values'
            . ' that are already stored, and the frontend keeps rendering their anchor span.'
        );

        $this->addOption(
            'mode',
            'm',
            InputOption::VALUE_REQUIRED,
            'empty (only elements without an anchor), rebuild (also anchors that do not match their header) or clear (empty all)',
            AnchorService::MODE_EMPTY
        );
        $this->addOption('execute', null, InputOption::VALUE_NONE, 'Write the changes. Without it nothing is touched.');
        $this->addOption('pid', 'p', InputOption::VALUE_REQUIRED, 'Limit to one page', '0');
        $this->addOption('uid', 'u', InputOption::VALUE_REQUIRED, 'Limit to one content element', '0');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Bootstrap::initializeBackendAuthentication();
        $io = new SymfonyStyle($input, $output);

        $mode = (string)$input->getOption('mode');
        $modes = [AnchorService::MODE_EMPTY, AnchorService::MODE_REBUILD, AnchorService::MODE_CLEAR];
        if (!in_array($mode, $modes, true)) {
            $io->error(sprintf('Unknown mode "%s". Use "empty", "rebuild" or "clear".', $mode));

            return self::FAILURE;
        }

        $changes = $this->anchorService->collect(
            $mode,
            (int)$input->getOption('pid'),
            (int)$input->getOption('uid')
        );

        if ($changes === []) {
            $io->success('Nothing to do - every anchor is where it belongs.');

            return self::SUCCESS;
        }

        $io->table(
            ['uid', 'pid', 'lang', 'header', 'anchor now', 'anchor new'],
            array_map(
                static fn(array $change): array => [
                    $change['uid'],
                    $change['pid'],
                    $change['lang'],
                    mb_strimwidth($change['header'], 0, 40, '…'),
                    $change['old'] === '' ? '-' : $change['old'],
                    $change['new'] === '' ? '-' : $change['new'],
                ],
                $changes
            )
        );

        if (!$input->getOption('execute')) {
            $io->note(sprintf('%d element(s) would change. Add --execute to write them.', count($changes)));

            return self::SUCCESS;
        }

        $written = $this->anchorService->apply($changes);
        $io->success(sprintf('%d element(s) updated. Flush the frontend caches afterwards.', $written));

        return self::SUCCESS;
    }
}
