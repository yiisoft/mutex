<?php

namespace Yiisoft\Mutex\Tests\Mocks;

use Yiisoft\Mutex\Mutex;
use Yiisoft\Mutex\RetryAcquireTrait;

/**
 * Class DumbMutex
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
