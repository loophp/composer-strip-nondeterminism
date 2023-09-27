<?php

declare(strict_types=1);

namespace loophp\ComposerStripNondeterminism\Command;

use Composer\Command\BaseCommand;
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
            ->setDescription(
                <<<'EOF'
Calculate the a hash of a file or a directory (recursively)
EOF
            )
            ->addArgument('path', InputArgument::REQUIRED, 'Path to hash.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pathHasher = new PathHasher;

        $output->writeln(
            $pathHasher->hash($input->getArgument('path'))
        );

        return Command::SUCCESS;
    }
}
