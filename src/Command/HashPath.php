<?php

declare(strict_types=1);

namespace loophp\ComposerStripNondeterminism\Command;

use Composer\Command\BaseCommand;
use Composer\Console\Input\InputOption;
use loophp\ComposerStripNondeterminism\Service\PathHasher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class HashPath extends BaseCommand
{
    protected function configure(): void
    {
        $this
            ->setName('hash')
            ->setDescription('Calculate the a hash of a file or a directory (recursively)')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to hash.')
            ->addOption('algo', null, InputOption::VALUE_OPTIONAL, 'Hashing algorithm to use', 'sha256')
            ->addOption('disable-permissions', null, InputOption::VALUE_NONE, 'Disable hashing of the file permissions.')
            ->addOption('enable-mtime', null, InputOption::VALUE_NONE, 'Enable hashing of the file modification time (disabled by default because it does not produce a stable hash across Windows and Linux operating systems.)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $arguments = PathHasher::PERMS;

        if ($input->getOption('enable-mtime')) {
            $arguments |= PathHasher::MTIME;
        }

        if ($input->getOption('disable-permissions')) {
            $arguments ^= PathHasher::PERMS;
        }

        $pathHasher = new PathHasher(
            $input->getOption('algo'),
            $arguments
        );

        $output->writeln(
            $pathHasher->hash($input->getArgument('path'))
        );

        return Command::SUCCESS;
    }
}
