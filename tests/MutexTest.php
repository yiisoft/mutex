<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Mutex\Tests\Mocks\DumbMutex;

final class MutexTest extends TestCase
{
    use MutexTestTrait;

    protected function setUp(): void
    {
        DumbMutex::$locked = false;
    }

    private function createMutex(): DumbMutex
    {
        return new DumbMutex();
    }
}
