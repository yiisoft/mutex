<?php

declare(strict_types=1);

namespace Yiisoft\Mutex;

/**
 * Allows you to create a mutex instance.
 */
interface MutexFactoryInterface
{
    public function create(): MutexInterface;
}
