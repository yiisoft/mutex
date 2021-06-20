<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Mutex\MutexLock;
use Yiisoft\Mutex\MutexLockedException;
use Yiisoft\Mutex\Tests\Mocks\DumbMutex;

final class MutexTest extends TestCase
{
    protected function setUp(): void
    {
        DumbMutex::$locked = false;
    }

    /**
     * @dataProvider mutexNameDataProvider
     */
    public function testBase(string $mutexName): void
    {
        $mutex = $this->createMutex();

        $lock = $mutex->acquire($mutexName);
        $lock->release();

        // Acuire and release success
        $this->assertTrue(true);
    }

    /**
     * @dataProvider mutexNameDataProvider()
     */
    public function testMutexAlreadyLocked(string $mutexName): void
    {
        $mutexOne = $this->createMutex();
        $mutexTwo = $this->createMutex();

        $mutexOne->acquire($mutexName);

        $this->expectException(MutexLockedException::class);
        $mutexTwo->acquire($mutexName);
    }

    /**
     * @dataProvider mutexNameDataProvider()
     */
    public function testReleaseNotLocked(string $mutexName): void
    {
        $lock = new MutexLock($this->createMutex(), $mutexName);

        $this->expectException(RuntimeException::class);
        $lock->release();
    }

    public static function mutexNameDataProvider(): array
    {
        $utf = <<<'UTF'
        𝐘˛𝜄 ӏ𝕤 𝗮 𝔣𝖺𐑈𝝉, 𐑈ℯ𝔠ｕ𝒓𝗲, 𝝰𝞹𝒹 𝖊𝘧𝒇𝗶𝕔𝖎ⅇπτ Ｐ𝘏𝙿 𝖿г𝖺ｍ𝖾ｗσｒ𝐤.
        𝓕lе𝘅ӏᏏlе 𝞬𝖾𝘁 ϱ𝘳ɑ𝖌ｍ𝛼𝓉ͺ𝖼.
        𝑊ﮭ𝚛𝛞𝓼 𝔯𝕚𝕘һ𝞃 σ𝚞𝞽 ०𝒇 𝐭𝙝ҽ 𝗯𝘰𝘹.
        𝓗𝚊𝘀 𝓇𝖾𝙖𝐬ﻬ𝓃𝕒ᖯl𝔢 ꓒ𝘦քα𝗎l𝐭ꜱ.
        😱
        UTF;

        return [
            'simple name' => ['testname'],
            'long name' => ['Y' . str_repeat('iiiiiiiiii', 1000)],
            'UTF-8 garbage' => [$utf],
        ];
    }

    private function createMutex(): DumbMutex
    {
        return new DumbMutex();
    }
}
