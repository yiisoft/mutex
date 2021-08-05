<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

use RuntimeException;

/**
 * Executes a callback in synchronized mode, i.e. only a single instance of the callback is executed at the same time:
 *
 * ```php
 * $newCount = $synchronizer->execute('critical_logic', function () {
 *     return $counter->increase();
 * }, 10);
 * ```
 */
final class Synchronizer
{
    private MutexFactoryInterface $mutexFactory;

    public function __construct(MutexFactoryInterface $mutexFactory)
    {
        $this->mutexFactory = $mutexFactory;
    }

    /**
     * Executes a PHP callable with a lock and returns the result.
     *
     * @param string $name Name of the mutex to acquire.
     * @param callable $callback PHP callable to execution.
     * @param int $timeout Time (in seconds) to wait for lock to be released. Defaults to zero meaning that
     * method {@see MutexInterface::acquire()} will return false immediately in case lock was already acquired.
     *
     * @throws RuntimeException If unable to acquire lock.
     *
     * @return mixed The result of the PHP callable execution.
     */
    public function execute(string $name, callable $callback, int $timeout = 0)
    {
        $mutex = $this->mutexFactory->createAndAcquire($name, $timeout);

        /** @var mixed $result */
        $result = $callback();

        $mutex->release();
        return $result;
    }
}
