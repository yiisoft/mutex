<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Mutex\Tests\Mocks\DumbMutex;

final class RetryAcquireTraitTest extends TestCase
{
    protected function setUp(): void
    {
        DumbMutex::$locked = false;
    }

    public function testRetryAcquire(): void
    {
        $mutexName = __FUNCTION__;
        $mutexOne = $this->createMutex();
        $mutexTwo = $this->createMutex();

        $this->assertTrue($mutexOne->acquire($mutexName));
        $this->assertFalse($mutexTwo->acquire($mutexName, 1));

//        $this->assertSame(20, $mutexTwo->attemptsCounter);
    }

    private function createMutex(): DumbMutex
    {
        return new DumbMutex();
    }
}
