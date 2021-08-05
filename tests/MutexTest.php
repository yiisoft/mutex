<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Mutex\MutexInterface;
use Yiisoft\Mutex\Tests\Mocks\Mutex;

final class MutexTest extends TestCase
{
    use MutexTestTrait;

    protected function createMutex(string $name): MutexInterface
    {
        return new Mutex($name);
    }
}
