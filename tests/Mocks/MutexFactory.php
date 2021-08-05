<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests\Mocks;

use Yiisoft\Mutex\MutexInterface;

final class MutexFactory extends \Yiisoft\Mutex\MutexFactory
{
    private string $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function create(string $name): MutexInterface
    {
        return new $this->class($name);
    }
}
