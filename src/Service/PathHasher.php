<?php

declare(strict_types=1);

namespace loophp\ComposerStripNondeterminism\Service;

use FilesystemIterator;
use loophp\ComposerStripNondeterminism\Iterator\SortedIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class PathHasher
{
    /**
     * Should we take in account file permissions?
     */
    public const PERMS = 1;

    public function __construct(private int $flags = self::PERMS) {}

    private function getHashForPath(SplFileInfo $file, string $context): string {
        $toHash = [
            'hash' => match (true) {
                $file->isLink() && $file->isDir() => hash('sha256', str_replace($context, '', $file->getLinkTarget())),
                $file->isLink(), $file->isFile() => hash_file('sha256', $file->getRealPath()),
                $file->isDir() => hash('sha256', str_replace($context, '', $file->getRealPath())),
            },
            'path' => str_replace($context, '', $file->getPathname()),
            'type' => $file->getType(),
        ];

        if ($this->flags & self::PERMS) {
            $toHash += ['perms' => $file->getPerms()];
        }

        ksort($toHash);

        return hash('sha256', json_encode($toHash));
    }

    public function hash(string $path): string
    {
        $path = realpath($path);

        if (is_file($path)) {
            return hash('sha256', $this->getHashForPath(new SplFileInfo($path), dirname($path)));
        }

        $sortedFileIterator = new SortedIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $path,
                    RecursiveDirectoryIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS
                ),
                RecursiveIteratorIterator::CHILD_FIRST
            )
        );

        $hash = '';

        /** @var \SplFileInfo $file */
        foreach ($sortedFileIterator as $splFile) {
            $hash = hash('sha256', sprintf('%s%s', $this->getHashForPath($splFile, $path), $hash));
        }

        return ('' === $hash)
            ? hash('sha256', '')
            : $hash;
    }


}
