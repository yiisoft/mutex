<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use Yiisoft\Mutex\Tests\Mocks\DumbMutex;

/**
 * Class MutexTest
 */
class MutexTest extends \PHPUnit\Framework\TestCase
{
    use MutexTestTrait;

    /**
     * @return DumbMutex
     */
    private function createMutex(): DumbMutex
    {
        return new DumbMutex();
    }
}
