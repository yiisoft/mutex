<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

/**
 * The Mutex allows mutual execution of concurrent processes in order to prevent "race conditions".
 *
 * @see MutexInterface
 */
abstract class Mutex implements MutexInterface
{
    final public function __destruct()
    {
        if (!$this->isReleased()) {
            $this->release();
        }
    }
}
