<?php

declare(strict_types=1);

namespace loophp\ComposerStripNondeterminism\Command;

use Composer\Command\BaseCommand;
use Composer\Console\Input\InputOption;
use Loophp\PathHasher\NAR;
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
            ->addOption('algo', null, InputOption::VALUE_OPTIONAL, 'Hashing algorithm to use', 'sha256');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pathHasher = new NAR($input->getOption('algo'));

        $output->writeln($pathHasher->hash($input->getArgument('path')));

        return Command::SUCCESS;
    }
}
