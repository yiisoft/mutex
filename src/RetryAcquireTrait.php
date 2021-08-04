<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

use Closure;

use function microtime;
use function usleep;

/**
 * Allows retrying acquire with a certain timeout.
 */
trait RetryAcquireTrait
{
    private int $retryDelay = 50;

    /**
     * Returns a new instance with the specified retry delay.
     *
     * @param int $retryDelay Number of milliseconds between each try until specified timeout times out.
     * By default, it is 50 milliseconds - it means that we may try to acquire lock up to 20 times per second.
     *
     * @return self
     */
    public function withRetryDelay(int $retryDelay): self
    {
        $new = clone $this;
        $new->retryDelay = $retryDelay;
        return $new;
    }

    private function retryAcquire(int $timeout, Closure $callback): bool
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
