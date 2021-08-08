<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests\Mocks;

use Yiisoft\Mutex\MutexInterface;

final class MutexAcquired implements MutexInterface
{
    public function acquire(int $timeout = 0): bool
    {
        return false;
    }

    public function release(): void
    {
        // do nothing
    }
}
