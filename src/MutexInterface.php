<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

/**
 * The Mutex allows mutual execution of concurrent processes in order to prevent "race conditions".
 *
 * This is achieved by using a "lock" mechanism. Each possibly concurrent process cooperates by acquiring
 * a lock before accessing the corresponding data.
 *
 * Usage example:
 *
 * ```
 * $mutex = $mutexFactory->create();
 * if (!$mutex->acquire(1000)) {
 *     throw new \RuntimeException('Unable to acquire mutex.');
 * }
 *
 * // ...
 * // business logic execution
 * // ...
 *
 * $mutex->release();
 * ```
 */
interface MutexInterface
{
    /**
     * Acquires a lock.
     *
     * @param int $timeout Time (in seconds) to wait for lock to be released. Defaults to zero meaning that method
     * will return false immediately in case lock was already acquired.
     */
    public function acquire(int $timeout = 0): bool;

    /**
     * Releases a lock.
     */
    public function release(): void;
}
