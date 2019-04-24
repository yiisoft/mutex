<?php
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Mutex;

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
abstract class Mutex
{
    /**
     * @var string[] names of the locks acquired by the current PHP process.
     */
    private $locks = [];

    /**
     * Mutex constructor.
     *
     * @param bool $autoRelease whether all locks acquired in this process (i.e. local locks) must be released
     *                          automatically before finishing script execution. Defaults to true. Setting this property
     *                          to true means that all locks acquired in this process must be released (regardless of
     *                          errors or exceptions).
     */
    public function __construct(bool $autoRelease = true)
    {
        if ($autoRelease) {
            $locks = &$this->locks;
            register_shutdown_function(function () use (&$locks) {
                foreach ($locks as $lock) {
                    $this->release($lock);
                }
            });
        }
    }

    /**
     * Acquires a lock by name.
     *
     * @param string $name    name of the lock to be acquired. Must be unique.
     * @param int    $timeout time (in seconds) to wait for lock to be released. Defaults to zero meaning that method
     *                        will return false immediately in case lock was already acquired.
     *
     * @return bool lock acquiring result.
     */
    public function acquire(string $name, int $timeout = 0): bool
    {
        if (!in_array($name, $this->locks, true) && $this->acquireLock($name, $timeout)) {
            $this->locks[] = $name;

            return true;
        }

        return false;
    }

    /**
     * Releases acquired lock. This method will return false in case the lock was not found.
     *
     * @param string $name of the lock to be released. This lock must already exist.
     *
     * @return bool lock release result: false in case named lock was not found..
     */
    public function release(string $name): bool
    {
        if ($this->releaseLock($name)) {
            $index = array_search($name, $this->locks);
            if ($index !== false) {
                unset($this->locks[$index]);
            }

            return true;
        }

        return false;
    }

    /**
     * This method should be extended by a concrete Mutex implementations. Acquires lock by name.
     *
     * @param string $name    of the lock to be acquired.
     * @param int    $timeout time (in seconds) to wait for the lock to be released.
     *
     * @return bool acquiring result.
     */
    abstract protected function acquireLock(string $name, int $timeout = 0): bool;

    /**
     * This method should be extended by a concrete Mutex implementations. Releases lock by given name.
     *
     * @param string $name of the lock to be released.
     *
     * @return bool release result.
     */
    abstract protected function releaseLock(string $name): bool;
}
