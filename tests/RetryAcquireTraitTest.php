<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Mutex\Tests\Mocks\RetryAcquireTraitMutex;

final class RetryAcquireTraitTest extends TestCase
{
    public function testRetryAcquire(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->markTestSkipped('RetryAcquireTrait use the "usleep()" function. On Windows, this function may not work correctly.');
        }

        $mutex = new RetryAcquireTraitMutex(20);
        $mutex->acquire('test', 1);

        // Test do not throw exception: 20 attempts in 1 second
        $this->assertTrue(true);
    }
}
