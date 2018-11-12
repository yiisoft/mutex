<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\mutex;

/**
 * DbMutex is the base class for classes, which relies on database while implementing mutex "lock" mechanism.
 *
 * @see Mutex
 */
abstract class DbMutex extends Mutex
{
    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * DbMutex constructor.
     * @param \PDO $connection
     * @param bool $autoRelease whether all locks acquired in this process (i.e. local locks) must be released automatically
     * before finishing script execution. Defaults to true. Setting this property to true means that all locks
     * acquired in this process must be released (regardless of errors or exceptions).
     */
    public function __construct(\PDO $connection, $autoRelease = true)
    {
        parent::__construct($autoRelease);
        $this->connection = $connection;
    }
}
