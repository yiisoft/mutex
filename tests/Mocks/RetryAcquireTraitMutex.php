<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests\Mocks;

use Yiisoft\Mutex\Mutex;
use Yiisoft\Mutex\RetryAcquireTrait;

final class RetryAcquireTraitMutex extends Mutex
{
    use RetryAcquireTrait;

    private int $expectedAttempts;
    private int $attemptsCounter = 0;

    public function __construct(int $expectedAttempts)
    {
        $this->expectedAttempts = $expectedAttempts;
        parent::__construct(false);
    }

    protected function acquireLock(string $name, int $timeout = 0): bool
    {
        return $this->retryAcquire(
            $timeout,
            function () {
                $this->attemptsCounter++;
                return $this->expectedAttempts === $this->attemptsCounter;
            }
        );
    }

    protected function releaseLock(string $name): bool
    {
        return true;
    }
}
