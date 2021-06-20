<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

final class MutexLock implements MutexLockInterface
{
    private Mutex $mutex;
    private string $name;

    public function __construct(Mutex $mutex, string $name)
    {
        $this->mutex = $mutex;
        $this->name = $name;
    }

    public function release(): void
    {
        $this->mutex->release($this->name);
    }
}
