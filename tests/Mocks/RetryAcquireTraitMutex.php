<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests\Mocks;

use Yiisoft\Mutex\MutexInterface;
use Yiisoft\Mutex\RetryAcquireTrait;

final class RetryAcquireTraitMutex implements MutexInterface
{
    use RetryAcquireTrait;
    private int $attemptsCounter = 0;

    public function __construct(private int $expectedAttempts)
    {
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
