<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use Yiisoft\Mutex\Mutex;
use Yiisoft\Mutex\MutexLockedException;

trait MutexTestTrait
{
    abstract protected function createMutex(): Mutex;

    /**
     * @dataProvider mutexDataProvider()
     */
    public function testThatMutexLockIsWorking(string $mutexName): void
    {
        $mutexOne = $this->createMutex();
        $mutexTwo = $this->createMutex();

        $mutexOne->acquire($mutexName);

        $this->expectException(MutexLockedException::class);
        $mutexTwo->acquire($mutexName);
    }

    public function testTimeout(): void
    {
        $mutexName = __FUNCTION__;
        $mutexOne = $this->createMutex();
        $mutexTwo = $this->createMutex();

        $lock = $mutexOne->acquire($mutexName);

        $microtime = microtime(true);
        $this->assertFalse($mutexTwo->acquire($mutexName, 1));
        $diff = microtime(true) - $microtime;
        $this->assertTrue($diff >= 1 && $diff < 2);
        $this->assertTrue($mutexOne->release($mutexName));
        $this->assertFalse($mutexTwo->release($mutexName));
    }
}
