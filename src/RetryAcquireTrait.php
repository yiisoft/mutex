<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

use Closure;

/**
 * Trait RetryAcquireTrait.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
trait RetryAcquireTrait
{
    /**
     * @var int Number of milliseconds between each try in [[acquire()]] until specified timeout times out.
     * By default it is 50 milliseconds - it means that [[acquire()]] may try acquire lock up to 20 times per
     * second.
     */
    public int $retryDelay = 50;

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
