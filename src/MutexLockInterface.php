<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

interface MutexLockInterface
{
    public function release(): void;
}
