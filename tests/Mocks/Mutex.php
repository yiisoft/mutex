<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests\Mocks;

use function clearstatcache;
use function fclose;
use function fileinode;
use function flock;
use function fstat;
use function md5;
use function sys_get_temp_dir;
use function unlink;

final class Mutex extends \Yiisoft\Mutex\Mutex
{
    private string $file;

    /**
     * @var resource
     */
    private $lockResource;

    public function __construct(string $name)
    {
        $this->file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5($name) . '.lock';
        parent::__construct(self::class, $name);
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function acquireLock(int $timeout = 0): bool
    {
        $resource = fopen($this->file, 'wb+');

        if ($resource === false) {
            return false;
        }

        if (!flock($resource, LOCK_EX | LOCK_NB)) {
            fclose($resource);
            return false;
        }

        if (DIRECTORY_SEPARATOR !== '\\' && fstat($resource)['ino'] !== fileinode($this->file)) {
            clearstatcache(true, $this->file);
            flock($resource, LOCK_UN);
            fclose($resource);
            return false;
        }

        $this->lockResource = $resource;
        return true;
    }

    public function releaseLock(): bool
    {
        if ($this->lockResource === null) {
            return false;
        }

        if (DIRECTORY_SEPARATOR === '\\') {
            flock($this->lockResource, LOCK_UN);
            fclose($this->lockResource);
            unlink($this->file);
        } else {
            unlink($this->file);
            flock($this->lockResource, LOCK_UN);
            fclose($this->lockResource);
        }

        $this->lockResource = null;
        return true;
    }
}
