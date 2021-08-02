<?php

namespace Yiisoft\Mutex;

final class Synchronizer
{
    private MutexFactoryInterface $mutexFactory;

    public function __construct(MutexFactoryInterface $mutexFactory)
    {
        $this->mutexFactory = $mutexFactory;
    }

    public function execute(string $name, callable $callback, int $timeout = 0)
    {
        $mutex = $this->mutexFactory->create($name);
        if (!$mutex->acquire($timeout)) {
            throw new \RuntimeException("Unable to execute synchronized \"$name\".");
        }
        $result = $callback();
        $mutex->release();
    }
}
