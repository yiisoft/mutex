<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

/**
 * Allows retrying acquire with a certain timeout.
 */
trait RetryAcquireTrait
{
    /**
     * @var int Number of milliseconds between each try until specified timeout times out.
     * By default, it is 50 milliseconds - it means that we may try to acquire lock up to 20 times per
     * second.
     */
    private int $retryDelay = 50;

    public function withRetryDelay(int $retryDelay): self
    {
        $new = clone $this;
        $new->retryDelay = $retryDelay;
        return $new;
    }

    private function retryAcquire(int $timeout, callable $callback): bool
    {
        $start = microtime(true);
        do {
            if ($callback()) {
                return true;
            }
            usleep($this->retryDelay * 1000);
        } while (microtime(true) - $start < $timeout);

        return false;
    }
}
