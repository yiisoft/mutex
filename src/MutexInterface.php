<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

interface MutexInterface
{
    /**
     * @throws MutexLockedException
     */
    public function acquire(string $name, int $timeout = 0): void;

    public function release(string $name): void;
}
