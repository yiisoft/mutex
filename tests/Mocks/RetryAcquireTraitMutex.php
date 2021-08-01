<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests\Mocks;

use Yiisoft\Mutex\Mutex;
use Yiisoft\Mutex\MutexInterface;
use Yiisoft\Mutex\RetryAcquireTrait;

final class RetryAcquireTraitMutex implements MutexInterface
{
    use RetryAcquireTrait;

    private int $expectedAttempts;
    private int $attemptsCounter = 0;

    public function __construct(int $expectedAttempts)
    {
        $this->expectedAttempts = $expectedAttempts;
    }

    public function acquire(int $timeout = 0): bool
    {
        return $this->retryAcquire(
            $timeout,
            function () {
                $this->attemptsCounter++;
                return $this->expectedAttempts === $this->attemptsCounter;
            }
        );
    }

    public function release(): void
    {
        // do nothing
    }
}
