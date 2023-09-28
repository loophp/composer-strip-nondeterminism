<?php

declare(strict_types=1);

namespace loophp\ComposerStripNondeterminism\Iterator;

use SplFileInfo;
use SplHeap;

final class SortedIterator extends SplHeap
{
    public function __construct(iterable $iterable)
    {
        foreach ($iterable as $item) {
            $this->insert($item);
        }
    }

    /**
     * @param SplFileInfo $left
     * @param SplFileInfo $right
     */
    public function compare($left, $right): int
    {
        return $right->getPathname() <=> $left->getPathname();
    }
}
