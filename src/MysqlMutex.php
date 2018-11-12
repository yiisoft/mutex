<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\mutex;

/**
 * MysqlMutex implements mutex "lock" mechanism via MySQL locks.
 *
 * @see Mutex
 */
class MysqlMutex extends DbMutex
{
    public function __construct(\PDO $connection, $autoRelease = true)
    {
        $driverName = $connection->getAttribute(\PDO::ATTR_DRIVER_NAME);
        if ($driverName !== 'mysql') {
            throw new \InvalidArgumentException('MySQL connection instance should be passed. Got ' . $driverName . '.');
        }

        parent::__construct($connection, $autoRelease);
    }

    /**
     * Acquires lock by given name.
     * @param string $name of the lock to be acquired.
     * @param int $timeout time (in seconds) to wait for lock to become released.
     * @return bool acquiring result.
     * @see http://dev.mysql.com/doc/refman/5.0/en/miscellaneous-functions.html#function_get-lock
     */
    protected function acquireLock($name, $timeout = 0)
    {
        $statement = $this->connection->prepare('SELECT GET_LOCK(:name, :timeout)');
        $statement->bindValue(':name', $this->hashLockName($name));
        $statement->bindValue(':timeout', $timeout);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Releases lock by given name.
     * @param string $name of the lock to be released.
     * @return bool release result.
     * @see http://dev.mysql.com/doc/refman/5.0/en/miscellaneous-functions.html#function_release-lock
     */
    protected function releaseLock($name)
    {
        $statement = $this->connection->prepare('SELECT RELEASE_LOCK(:name)');
        $statement->bindValue(':name', $this->hashLockName($name));
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Generate hash for lock name to avoid exceeding lock name length limit.
     *
     * @param string $name
     * @return string
     * @since 2.0.16
     * @see https://github.com/yiisoft/yii2/pull/16836
     */
    protected function hashLockName($name)
    {
        return sha1($name);
    }
}
