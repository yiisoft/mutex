<?php
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yii\Mutex\Tests;

use Yii\Mutex\MysqlMutex;

/**
 * Class MysqlMutexTest.
 *
 * @group mutex
 * @group db
 * @group mysql
 */
class MysqlMutexTest
{
    use MutexTestTrait;

    /**
     * @return MysqlMutex
     */
    protected function createMutex()
    {
        return new MysqlMutex($this->getConnection());
    }

    private function getConnection()
    {
        // TODO: create MySQL connection here
    }
}
