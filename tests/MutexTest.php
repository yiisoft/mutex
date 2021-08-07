<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Mutex\MutexInterface;
use Yiisoft\Mutex\Tests\Mocks\Mutex;

final class MutexTest extends TestCase
{
    use MutexTestTrait;

    public function testDestruct(): void
    {
        $mutex = $this->createMutex('testDestruct');

        $this->assertTrue($mutex->acquire());

        $file = $mutex->getFile();
        $this->assertFileExists($file);

        unset($mutex);
        $this->assertFileDoesNotExist($file);
    }

    protected function createMutex(string $name): MutexInterface
    {
        return new Mutex($name);
    }
}
