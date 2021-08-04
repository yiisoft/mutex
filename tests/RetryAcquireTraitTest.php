<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

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
}
