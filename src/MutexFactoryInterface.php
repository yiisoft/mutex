<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

/**
 * Allows you to create a mutex instance.
 */
interface MutexFactoryInterface
{
    /**
     * Creates a mutex.
     *
     * @param string $name Name of the mutex to create.
     */
    public function create(string $name): MutexInterface;

    /**
     * Creates a mutex and acquires a lock.
     *
     * @param string $name Name of the mutex to create.
     * @thows RuntimeException If unable to acquire lock.
     *
     * @return MutexInterface
     */
    public function createAndAcquire(string $name): MutexInterface;
}
