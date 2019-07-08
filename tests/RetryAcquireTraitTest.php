<?php
namespace Yiisoft\Mutex\Tests;

use Yiisoft\Mutex\Tests\Mocks\DumbMutex;

/**
 * Class RetryAcquireTraitTest.
 *
 * @group mutex
 */
class RetryAcquireTraitTest extends \PHPUnit\Framework\TestCase
{
    public function testRetryAcquire()
    {
        $mutexName = __FUNCTION__;
        $mutexOne = $this->createMutex();
        $mutexTwo = $this->createMutex();

        $this->assertTrue($mutexOne->acquire($mutexName));
        $this->assertFalse($mutexTwo->acquire($mutexName, 1));

        $this->assertSame(20, $mutexTwo->attemptsCounter);
    }

    /**
     * @return DumbMutex
     */
    private function createMutex(): DumbMutex
    {
        return new DumbMutex();
    }
}
