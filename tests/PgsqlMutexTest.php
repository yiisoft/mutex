<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yii\Mutex\Tests;

use yii\mutex\PgsqlMutex;

/**
 * Class PgsqlMutexTest.
 *
 * @group mutex
 * @group db
 * @group pgsql
 */
class PgsqlMutexTest
{
    use MutexTestTrait;

    /**
     * @return PgsqlMutex
     */
    protected function createMutex()
    {
        return new PgsqlMutex($this->getConnection());
    }

    private function getConnection()
    {
        // TODO: create MySQL connection here
    }
}
