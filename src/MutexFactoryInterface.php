<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

/**
 * Allows you to create a mutex instance.
 */
interface MutexFactoryInterface
{
    /**
     * @param string $name Name of the mutex to create.
     */
    public function create(string $name): MutexInterface;
}
