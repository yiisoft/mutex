<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use RuntimeException;
use Yiisoft\Mutex\Tests\Mocks\Mutex;

use function md5;
use function microtime;

final class MutexTest extends TestCase
{
    public function testMutexAcquire(): void
    {
        $mutex = $this->createMutex('testMutexAcquire');

        $this->assertTrue($mutex->acquire());
        $mutex->release();
    }

    public function testThatMutexLockIsWorking(): void
    {
        $mutexOne = $this->createMutex('testThatMutexLockIsWorking');
        $mutexTwo = $this->createMutex('testThatMutexLockIsWorking');

        $this->assertTrue($mutexOne->acquire());
        $this->assertFalse($mutexTwo->acquire());
        $mutexOne->release();
        $mutexTwo->release();

        $this->assertTrue($mutexTwo->acquire());
        $mutexTwo->release();
    }

    public function testThatMutexLockIsWorkingOnTheSameComponent(): void
    {
        $mutex = $this->createMutex('testThatMutexLockIsWorkingOnTheSameComponent');

        $this->assertTrue($mutex->acquire());
        $this->assertFalse($mutex->acquire());

        $mutex->release();
        $mutex->release();
    }

    public function testTimeout(): void
    {
        $mutexOne = $this->createMutex(__METHOD__);
        $mutexTwo = $this->createMutex(__METHOD__);

        $this->assertTrue($mutexOne->acquire());
        $microtime = microtime(true);
        $this->assertFalse($mutexTwo->acquire(1));
        $diff = microtime(true) - $microtime;
        $this->assertTrue($diff >= 1 && $diff < 2);
        $mutexOne->release();
        $mutexTwo->release();
    }

    public function testDeleteLockFile(): void
    {
        $mutex = $this->createMutex('testDeleteLockFile');

        $mutex->acquire();
        $this->assertFileExists($mutex->getFile());

        $mutex->release();
        $this->assertFileDoesNotExist($mutex->getFile());
    }

    public function testDestruct(): void
    {
        $mutex = $this->createMutex('testDestruct');

        $this->assertTrue($mutex->acquire());

        $file = $mutex->getFile();
        $this->assertFileExists($file);

        unset($mutex);
        $this->assertFileDoesNotExist($file);
    }

    public function testReleaseFailure(): void
    {
        $mutexName = 'testReleaseFailure';
        $mutex = $this->createMutex($mutexName);
        $reflection = (new ReflectionObject($mutex))->getParentClass();
        $reflection->setStaticPropertyValue('currentProcessLocks', [md5(Mutex::class . $mutexName) => true]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Unable to release lock \"$mutexName\".");

        $mutex->release();
    }

    private function createMutex(string $name): Mutex
    {
        return new Mutex($name);
    }
}
