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

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);

        $application = self::createApplication();

        $tempDir = $this->getRandomRepoDirectory();

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
                'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',
                $tempDir
            ),
            $display
        );
    }
}
