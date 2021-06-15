<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests\Mocks;

use Yiisoft\Mutex\Mutex;
use Yiisoft\Mutex\RetryAcquireTrait;

final class DumbMutex extends Mutex
{
    use RetryAcquireTrait;

    public int $attemptsCounter = 0;
    public static bool $locked = false;

    protected function acquireLock(string $name, int $timeout = 0): bool
    {
        return $this->retryAcquire($timeout, function () {
            $this->attemptsCounter++;
            if (!self::$locked) {
                self::$locked = true;

                return true;
            }

            return false;
        });
    }

    protected function releaseLock(string $name): bool
    {
        if (self::$locked) {
            self::$locked = false;

            return true;
        }

        return false;
    }
}
