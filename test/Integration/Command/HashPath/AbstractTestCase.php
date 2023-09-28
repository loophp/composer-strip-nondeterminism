<?php

declare(strict_types=1);

namespace test\loophp\ComposerStripNondeterminism\Integration\Command\HashPath;

use Composer\Console\Application;
use Error;
use InvalidArgumentException;
use loophp\ComposerStripNondeterminism\Plugin as ComposerPlugin;
use PHPUnit\Framework;
use RuntimeException;
use Symfony\Component\Console;
use Symfony\Component\Filesystem;
use test\loophp\ComposerStripNondeterminism\Util\CommandInvocation;
use test\loophp\ComposerStripNondeterminism\Util\Directory;
use test\loophp\ComposerStripNondeterminism\Util\Scenario;
use test\loophp\ComposerStripNondeterminism\Util\State;

use function is_string;

use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;

/**
 * @internal
 *
 * @coversNothing
 */
abstract class AbstractTestCase extends Framework\TestCase
{
    private $currentWorkingDirectory;

    final protected function setUp(): void
    {
        self::fileSystem()->remove(self::temporaryDirectory());
        self::fileSystem()->remove(self::temporaryRepoDirectory());

        $currentWorkingDirectory = getcwd();

        if (false === $currentWorkingDirectory) {
            throw new RuntimeException('Unable to determine current working directory.');
        }

        $this->currentWorkingDirectory = $currentWorkingDirectory;
    }

    final protected function tearDown(): void
    {
        // self::fileSystem()->remove(self::temporaryDirectory());
        // self::fileSystem()->remove(self::temporaryRepoDirectory());

        chdir($this->currentWorkingDirectory);
    }

    public function getRandomRepoDirectory(): string
    {
        $directory = realpath($this->temporaryRepoDirectory());

        if (false === $directory) {
            throw new Error('Unable to determine temporary repo directory.');
        }

        return $directory;
    }

    public static function temporaryRepoDirectory(): string
    {
        return __DIR__ . '/../../../../.build/test/repo';
    }

    final protected static function assertExitCodeSame(
        int $expected,
        int $actual
    ): void {
        self::assertSame($expected, $actual, sprintf(
            'Failed asserting that exit code %d is identical to %d.',
            $actual,
            $expected
        ));
    }

    final protected static function createApplication(): Application
    {
        $application = new Application();

        foreach ((new ComposerPlugin())->getCommands() as $command) {
            $application->add($command);
        }

        $application->setAutoExit(false);

        return $application;
    }

    final protected static function createScenario(
        CommandInvocation $commandInvocation,
        string $fixtureDirectory
    ): Scenario {
        if (!is_dir($fixtureDirectory)) {
            throw new InvalidArgumentException(sprintf(
                'Fixture directory "%s" does not exist',
                $fixtureDirectory
            ));
        }

        $scenario = Scenario::fromCommandInvocationAndInitialState(
            $commandInvocation,
            State::fromDirectory(Directory::fromPath($fixtureDirectory))
        );

        if ($commandInvocation->is(CommandInvocation::inCurrentWorkingDirectory())) {
            chdir($scenario->directory()->path());
        }

        return $scenario;
    }

    private static function fileSystem(): Filesystem\Filesystem
    {
        return new Filesystem\Filesystem();
    }

    private static function temporaryDirectory(): string
    {
        return __DIR__ . '/../../../../.build/test/';
    }
}
