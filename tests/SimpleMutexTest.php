<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Mutex\SimpleMutex;
use Yiisoft\Mutex\Tests\Mocks\Mutex;
use Yiisoft\Mutex\Tests\Mocks\MutexAcquired;
use Yiisoft\Mutex\Tests\Mocks\MutexFactory;
use Yiisoft\Mutex\Tests\Mocks\MutexReleased;

final class SimpleMutexTest extends TestCase
{
    public function testMutexAcquireTrue(): void
    {
        $mutexName = 'testMutexAcquireTrue';
        $mutex = new Mutex($mutexName);
        $simpleMutex = new SimpleMutex(new MutexFactory($mutex));

        $this->assertTrue($simpleMutex->acquire($mutexName));
        $this->assertFileExists($mutex->getFile());

        $simpleMutex->release($mutexName);
        $this->assertFileDoesNotExist($mutex->getFile());

        $this->assertTrue($simpleMutex->acquire($mutexName));
        $this->assertFileExists($mutex->getFile());

        $simpleMutex->release($mutexName);
        $this->assertFileDoesNotExist($mutex->getFile());
    }

    public function testMutexAcquireFalse(): void
    {
        $mutexName = 'testMutexAcquireFalse';
        $mutex = new MutexAcquired();
        $simpleMutex = new SimpleMutex(new MutexFactory($mutex));

        $this->assertFalse($simpleMutex->acquire($mutexName));
        $simpleMutex->release($mutexName);

        $this->assertFalse($simpleMutex->acquire($mutexName));
        $simpleMutex->release($mutexName);
    }

    public function testSafeReleaseIfMutexHasAlreadyBeenReleased(): void
    {
        $mutexName = 'testMutexReleased';
        $mutex = new MutexReleased($mutexName);
        $simpleMutex = new SimpleMutex(new MutexFactory($mutex));

        $this->assertTrue($simpleMutex->acquire($mutexName));
        $simpleMutex->release($mutexName);
        $simpleMutex->release($mutexName);
    }
}
