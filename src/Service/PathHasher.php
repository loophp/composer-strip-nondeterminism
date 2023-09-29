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
     * Should we hash the file permissions?
     */
    public const PERMS = 1;

    /**
     * Should we hash the file modification time?
     */
    public const MTIME = 2;

    public function __construct(
        private string $algo = 'sha256',
        private int $flags = self::PERMS
    ) {}

    private function getHashForPath(SplFileInfo $file, string $context, string $parent = ''): string {
        $toHash = [
            'hash' => match (true) {
                $file->isLink() && $file->isDir() => hash($this->algo, str_replace($context, '', $file->getLinkTarget())),
                $file->isLink(), $file->isFile() => hash_file($this->algo, $file->getRealPath()),
                $file->isDir() => hash($this->algo, str_replace($context, '', $file->getRealPath())),
            },
            'parent' => base64_encode($parent),
            'path' => str_replace($context, '', $file->getPathname()),
            'type' => $file->getType(),
        ];

        if ($this->flags & self::MTIME) {
            $toHash += ['mtime' => $file->getMTime()];
        }

        if ($this->flags & self::PERMS) {
            $toHash += ['perms' => $file->getPerms()];
        }

        ksort($toHash);

        return hash($this->algo, json_encode($toHash), true);
    }

    public function hash(string $path): string
    {
        $path = realpath($path);
        $hash = $this->getHashForPath(new SplFileInfo($path), $path);

        if (is_dir($path)) {
            $sortedFileIterator = new SortedIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        $path,
                        RecursiveDirectoryIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS
                    ),
                    RecursiveIteratorIterator::CHILD_FIRST
                )
            );

            /** @var \SplFileInfo $file */
            foreach ($sortedFileIterator as $splFile) {
                $hash = $this->getHashForPath($splFile, $path, $hash);
            }
        }

        return sprintf('%s-%s', $this->algo, base64_encode($hash));
    }


}
