<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

use RuntimeException;

use function in_array;

/**
 * The Mutex component allows mutual execution of concurrent processes in order to prevent "race conditions".
 *
 * This is achieved by using a "lock" mechanism. Each possibly concurrent thread cooperates by acquiring
 * a lock before accessing the corresponding data.
 *
 * Usage example:
 *
 * ```
 * if ($mutex->acquire($mutexName)) {
 *     // business logic execution
 * } else {
 *     // execution is blocked!
 * }
 * ```
 *
 * This is a base class, which should be extended in order to implement the actual lock mechanism.
 */
abstract class Mutex implements MutexInterface
{
    /**
     * @var string[] Names of the locks acquired by the current PHP process.
     */
    private array $locks = [];

    /**
     * Mutex constructor.
     *
     * @param bool $autoRelease Whether all locks acquired in this process (i.e. local locks) must be released
     * automatically before finishing script execution. Defaults to true. Setting this property
     * to true means that all locks acquired in this process must be released (regardless of
     * errors or exceptions).
     */
    public function __construct(bool $autoRelease = true)
    {
        if ($autoRelease) {
            $locks = &$this->locks;
            register_shutdown_function(function () use (&$locks) {
                /**
                 * @var string $lock
                 */
                foreach ($locks as $lock) {
                    $this->release($lock);
                }
            });
        }
    }

    /**
     * Acquires a lock by name.
     *
     * @param string $name Name of the lock to be acquired. Must be unique.
     * @param int $timeout Time (in seconds) to wait for lock to be released. Defaults to zero meaning that method
     * will return false immediately in case lock was already acquired.
     *
     * @return MutexLockInterface
     */
    public function acquire(string $name, int $timeout = 0): MutexLockInterface
    {
        if (!in_array($name, $this->locks, true) && $this->acquireLock($name, $timeout)) {
            $this->locks[] = $name;

            return new MutexLock($this, $name);
        }

        throw new MutexLockedException();
    }

    /**
     * Releases acquired lock. This method will return false in case the lock was not found.
     *
     * @param string $name Name of the lock to be released. This lock must already exist.
     */
    public function release(string $name): void
    {
        if ($this->releaseLock($name)) {
            $index = array_search($name, $this->locks, true);
            if ($index !== false) {
                unset($this->locks[$index]);
            }

            return;
        }

        throw new RuntimeException();
    }

    /**
     * This method should be extended by a concrete Mutex implementations. Acquires lock by name.
     *
     * @param string $name Name of the lock to be acquired.
     * @param int $timeout Time (in seconds) to wait for the lock to be released.
     *
     * @return bool Acquiring result.
     */
    abstract protected function acquireLock(string $name, int $timeout = 0): bool;

    /**
     * This method should be extended by a concrete Mutex implementations. Releases lock by given name.
     *
     * @param string $name Name of the lock to be released.
     *
     * @return bool Release result.
     */
    abstract protected function releaseLock(string $name): bool;
}
