<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Mutex\Tests\Mocks\RetryAcquireTraitMutex;

final class RetryAcquireTraitTest extends TestCase
{
    public function testRetryAcquireSuccess(): void
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->markTestSkipped('usleep() is not reliable on Windows.');
        }

        // 2s to acquire mutex
        $mutex = (new RetryAcquireTraitMutex(2))
            ->withRetryDelay(1000);

        $this->assertTrue($mutex->acquire(2));
    }

    public function testRetryAcquireFailure(): void
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->markTestSkipped('usleep() is not reliable on Windows.');
        }

        // 2s to acquire mutex
        $mutex = (new RetryAcquireTraitMutex(2))
            ->withRetryDelay(1000);

        $this->assertFalse($mutex->acquire(1));
    }

    public function testRetryAcquireThrowExceptionWithNotPositiveRetryDelay(): void
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->markTestSkipped('usleep() is not reliable on Windows.');
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Retry delay value must be a positive number greater than zero, "0" is received.',
        );

        (new RetryAcquireTraitMutex(2))->withRetryDelay(0);
    }

    public function testImmutability(): void
    {
        $mutex = new RetryAcquireTraitMutex(2);

        $this->assertNotSame($mutex, $mutex->withRetryDelay(1000));
    }
}
