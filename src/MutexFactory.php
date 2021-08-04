<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

use RuntimeException;

/**
 * Creates a mutex instance {@see MutexFactoryInterface}.
 */
abstract class MutexFactory implements MutexFactoryInterface
{
    final public function createAndAcquire(string $name, int $timeout = 0): MutexInterface
    {
        $mutex = $this->create($name);

        if (!$mutex->acquire($timeout)) {
            throw new RuntimeException("Unable to acquire mutex \"$name\".");
        }

        return $mutex;
    }
}
