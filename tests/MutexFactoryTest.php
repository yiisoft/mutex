<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Mutex\MutexInterface;
use Yiisoft\Mutex\Tests\Mocks\Mutex;
use Yiisoft\Mutex\Tests\Mocks\MutexAcquired;
use Yiisoft\Mutex\Tests\Mocks\MutexFactory;

final class MutexFactoryTest extends TestCase
{
    public function testCreateAndAcquire(): void
    {
        $factory = new MutexFactory(Mutex::class);
        $mutex = $factory->createAndAcquire('testCreateAndAcquire');

        $this->assertInstanceOf(MutexInterface::class, $mutex);

        $this->assertFalse($mutex->acquire());
        $mutex->release();

        $this->assertTrue($mutex->acquire());
        $mutex->release();
    }

    public function testCreateAndAcquireFailure(): void
    {
        $factory = new MutexFactory(MutexAcquired::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to acquire mutex "testCreateAndAcquireFailure".');

        $factory->createAndAcquire('testCreateAndAcquireFailure');
    }
}
