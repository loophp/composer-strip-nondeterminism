<?php

declare(strict_types=1);

namespace loophp\ComposerStripNondeterminism\Service;

use Composer\Composer;
use Composer\Package\Loader\ArrayLoader;
use Composer\Util\SyncHelper;

final class TouchUtils
{

    public function touch(string $path, ?int $mtime = null, ?int $atime = null): bool
    {
        $return = true;

        /** @var \SplFileInfo $file */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
            $return = $return && touch($file->getRealPath(), $mtime, $atime);
        }

        return $return;
    }
}
