<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

/**
 * Represents a mutex lock.
 */
interface MutexLockInterface
{
    /**
     * Releases a lock.
     */
    public function release(): void;
}
