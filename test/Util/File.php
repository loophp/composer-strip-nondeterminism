<?php

declare(strict_types=1);

namespace test\loophp\ComposerStripNondeterminism\Util;

use BadMethodCallException;

use function is_string;

final class File
{
    private $contents;
    private $path;
    private $exists;

    private function __construct(
        string $path,
        bool $exists,
        ?string $contents
    ) {
        $this->path = $path;
        $this->exists = $exists;
        $this->contents = $contents;
    }

    public function contents(): string
    {
        if (false === $this->exists || null === $this->contents) {
            throw new BadMethodCallException(sprintf(
                'File at "%s" did not exist or was not readable at the time of creation.',
                $this->path
            ));
        }

        return $this->contents;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public static function fromPath(string $path): self
    {
        if (!file_exists($path)) {
            return new self(
                $path,
                false,
                null
            );
        }

        $contents = file_get_contents($path);

        if (!is_string($contents)) {
            return new self(
                $path,
                true,
                null
            );
        }

        return new self(
            $path,
            true,
            $contents
        );
    }

    public function path(): string
    {
        return $this->path;
    }
}
