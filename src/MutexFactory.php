<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

use RuntimeException;

abstract class MutexFactory implements MutexFactoryInterface
{
    final public function createAndAcquire(string $name): MutexInterface
    {
        $mutex = $this->create($name);
        if (!$mutex->acquire()) {
            throw new RuntimeException("Unable to acquire mutex \"$name\".");
        }
        return $mutex;
    }
}
