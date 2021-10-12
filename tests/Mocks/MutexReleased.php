<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests\Mocks;

use Yiisoft\Mutex\MutexException;
use Yiisoft\Mutex\MutexInterface;

final class MutexReleased implements MutexInterface
{
    private ?string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function acquire(int $timeout = 0): bool
    {
        return $this->name !== null;
    }

    public function release(): void
    {
        if ($this->name === null) {
            throw new MutexException('Mutex has already been released.');
        }

        $this->name = null;
    }
}
