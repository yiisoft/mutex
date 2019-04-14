<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yii\Mutex\Tests;

use Yii\Mutex\Tests\Mocks\DumbMutex;

/**
 * Class RetryAcquireTraitTest.
 *
 * @group mutex
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
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
    private function createMutex()
    {
        return new DumbMutex();
    }
}
