<?php

declare(strict_types=1);

namespace loophp\ComposerStripNondeterminism\Service;

use loophp\ComposerStripNondeterminism\Iterator\SortedIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class PathHasher
{
    private function getHashForPath(string $path, string $context): string {
        $toHash = [
            'path' => str_replace($context, '', $path),
            'hash' => is_dir($path) ? hash('sha256', str_replace($context, '', $path)) : hash_file('sha256', $path),
            'mtime' => filemtime($path),
        ];

        return hash('sha256', json_encode($toHash));
    }

    public function hash(string $path): string
    {
        $path = realpath($path);

        if (is_file($path)) {
            return hash('sha256', $this->getHashForPath($path, dirname($path)));
        }

        $sortedFileIterator = new SortedIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $path,
                    RecursiveDirectoryIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::CHILD_FIRST
            )
        );

        $hash = '';

        /** @var \SplFileInfo $file */
        foreach ($sortedFileIterator as $file) {
            $hash = hash('sha256', sprintf('%s%s', $this->getHashForPath($file->getRealPath(), $path), $hash));
        }

        return ('' === $hash)
            ? hash('sha256', '')
            : $hash;
    }


}
