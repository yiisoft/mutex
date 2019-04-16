<?php
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yii\Mutex\Tests\Mocks;

use Yii\Mutex\Mutex;
use Yii\Mutex\RetryAcquireTrait;

/**
 * Class DumbMutex.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class DumbMutex extends Mutex
{
    use RetryAcquireTrait;

    public $attemptsCounter = 0;
    public static $locked = false;

    /**
     * {@inheritdoc}
     */
    protected function acquireLock(string $name, int $timeout = 0): bool
    {
        return $this->retryAcquire($timeout, function () {
            $this->attemptsCounter++;
            if (!static::$locked) {
                static::$locked = true;

                return true;
            }

            return false;
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function releaseLock(string $name): bool
    {
        if (static::$locked) {
            static::$locked = false;

            return true;
        }

        return false;
    }
}
