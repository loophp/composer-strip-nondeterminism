<?php

declare(strict_types=1);

namespace test\loophp\ComposerStripNondeterminism\DataProvider\Command;

use Generator;
use test\loophp\ComposerStripNondeterminism\Util\CommandInvocation;

final class HashPathProvider
{
    /**
     * @return Generator<string, array{0: CommandInvocation}>
     */
    public function commandInvocation(): Generator
    {
        foreach (self::commandInvocations() as $commandInvocation) {
            yield $commandInvocation->style() => [
                $commandInvocation,
            ];
        }
    }

    /**
     * @return Generator<string, array{0: CommandInvocation}>
     */
    public static function simpleCommandInvocation(): Generator
    {
        foreach (self::commandInvocations() as $commandInvocation) {
            yield $commandInvocation->style() => [
                $commandInvocation,
            ];
        }
    }

    /**
     * @return array<int, CommandInvocation>
     */
    private static function commandInvocations(): array
    {
        return [
            CommandInvocation::usingWorkingDirectoryOption(),
        ];
    }
}
