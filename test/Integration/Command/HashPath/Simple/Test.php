<?php

declare(strict_types=1);

namespace test\loophp\ComposerStripNondeterminism\Integration\Command\HashPath\Simple;

use Symfony\Component\Console;
use test\loophp\ComposerStripNondeterminism\Integration\Command\HashPath\AbstractTestCase;
use test\loophp\ComposerStripNondeterminism\Util\CommandInvocation;

/**
 * @internal
 *
 * @coversNothing
 */
final class Test extends AbstractTestCase
{
    /**
     * @dataProvider \test\loophp\ComposerStripNondeterminism\DataProvider\Command\HashPathProvider::simpleCommandInvocation
     */
    public function testSucceeds(
        CommandInvocation $commandInvocation
    ): void {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture'
        );

        $initialState = $scenario->initialState();

        $application = self::createApplication();

        $tempDir = __DIR__ . '/fixture';

        $input = new Console\Input\ArrayInput(
            $scenario->consoleParameters() + [
                'path' => $tempDir
            ]
        );
        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run($input, $output);
        self::assertExitCodeSame(0, $exitCode);
        $display = $output->fetch();
        self::assertStringContainsString(
            sprintf(
                '64ee22b28a92d5a5ad62eaded6b0a63d8cea03592d3e7cdbf475026224261352',
                $tempDir
            ),
            $display
        );
    }
}