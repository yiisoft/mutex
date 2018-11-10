<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\mutex\tests\unit;

use yii\base\InvalidConfigException;
use yii\mutex\FileMutex;
use yiiunit\TestCase;

/**
 * Class FileMutexTest.
 *
 * @group mutex
 */
class FileMutexTest extends \PHPUnit\Framework\TestCase
{
    use MutexTestTrait;

    /**
     * @return FileMutex
     * @throws InvalidConfigException
     */
    protected function createMutex()
    {
        return \Yii::createObject([
            'class' => FileMutex::class,
            'mutexPath' => '@yiiunit/runtime/mutex',
        ]);
    }

    /**
     * @dataProvider mutexDataProvider()
     *
     * @param string $mutexName
     * @throws InvalidConfigException
     */
    public function testDeleteLockFile($mutexName)
    {
        $mutex = $this->createMutex();
        $fileName = $mutex->mutexPath . '/' . md5($mutexName) . '.lock';

        $mutex->acquire($mutexName);
        $this->assertFileExists($fileName);

        $mutex->release($mutexName);
        $this->assertFileNotExists($fileName);
    }
}
