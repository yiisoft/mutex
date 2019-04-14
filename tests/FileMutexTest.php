<?php
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yii\Mutex\Tests;

use Yii\Mutex\FileMutex;

/**
 * Class FileMutexTest.
 *
 * @group mutex
 */
class FileMutexTest extends \PHPUnit\Framework\TestCase
{
    use MutexTestTrait;

    /**
     * @throws \RuntimeException
     *
     * @return FileMutex
     */
    protected function createMutex()
    {
        return new FileMutex(sys_get_temp_dir());
    }

    /**
     * @dataProvider mutexDataProvider()
     *
     * @param string $mutexName
     */
    public function testDeleteLockFile($mutexName)
    {
        $mutex = $this->createMutex();

        $mutex->acquire($mutexName);
        $this->assertFileExists($mutex->getLockFilePath($mutexName));

        $mutex->release($mutexName);
        $this->assertFileNotExists($mutex->getLockFilePath($mutexName));
    }
}
