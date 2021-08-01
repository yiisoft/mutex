<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

/**
 * The Mutex allows mutual execution of concurrent processes in order to prevent "race conditions".
 *
 * This is achieved by using a "lock" mechanism. Each possibly concurrent thread cooperates by acquiring
 * a lock before accessing the corresponding data.
 *
 * Usage example:
 *
 * ```
 * $lock = $mutex->acquire($mutexName);
 * // ...
 * // business logic execution
 * // ...
 * $lock->release();
 * ```
 */
interface MutexInterface
{
    /**
     * Acquires a lock by name.
     *
     * @param string $name Name of the lock to be acquired. Must be unique.
     * @param int $timeout Time (in seconds) to wait for lock to be released. Defaults to zero meaning that method
     * will return false immediately in case lock was already acquired.
     *
     * @return MutexLockInterface
     * @throws MutexLockedException
     */
    public function acquire(string $name, int $timeout = 0): MutexLockInterface;
}
