<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use ErrorException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Mutex\Synchronizer;
use Yiisoft\Mutex\Tests\Mocks\Mutex;
use Yiisoft\Mutex\Tests\Mocks\MutexFactory;

use function file_exists;

final class SynchronizerTest extends TestCase
{
    private Mutex $mutex;
    private Synchronizer $synchronizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mutex = new Mutex('SynchronizerTest');
        $this->synchronizer = new Synchronizer(new MutexFactory($this->mutex));
    }

    public function testExecute(): void
    {
        $result = $this->synchronizer->execute('testExecute', function (): bool {
            return file_exists($this->mutex->getFile());
        });

        $this->assertTrue($result);
        $this->assertFileDoesNotExist($this->mutex->getFile());
    }

    public function testExecuteExceptionThrown(): void
    {
        $result = false;

        try {
            $this->synchronizer->execute('testExecuteExceptionThrown', function () use (&$result) {
                $result = file_exists($this->mutex->getFile());
                throw new RuntimeException('Some error.');
            });
        } catch (RuntimeException $e) {
            $this->assertSame('Some error.', $e->getMessage());
        } finally {
            $this->assertTrue($result);
            $this->assertFileDoesNotExist($this->mutex->getFile());
        }
    }

    public function testExecuteWithErrorCapture(): void
    {
        $result = false;

        $exceptionMessage = null;
        try {
            $this->synchronizer->execute('testExecuteExceptionThrown', function () use (&$result) {
                $result = file_exists($this->mutex->getFile());
                return $undefined;
            });
        } catch (ErrorException $e) {
            $exceptionMessage = $e->getMessage();
        }

        $this->assertSame(
            PHP_VERSION_ID >= 80000 ? 'Undefined variable $undefined' : 'Undefined variable: undefined',
            $exceptionMessage,
        );
        $this->assertTrue($result);
        $this->assertFileDoesNotExist($this->mutex->getFile());
    }
}
