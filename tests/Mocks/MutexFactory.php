<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests\Mocks;

use Yiisoft\Mutex\MutexInterface;

use function is_string;

final class MutexFactory extends \Yiisoft\Mutex\MutexFactory
{
    /**
     * @param MutexInterface|string $classOrObject
     */
    public function __construct(private $classOrObject)
    {
    }

    public function create(string $name): MutexInterface
    {
        return is_string($this->classOrObject) ? new $this->classOrObject($name) : $this->classOrObject;
    }
}
