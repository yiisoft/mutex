<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Tests;

use Yiisoft\Mutex\Mutex;

trait MutexTestTrait
{
    abstract protected function createMutex(): Mutex;

    /**
     * @dataProvider mutexDataProvider()
     */
    public function testMutexAcquire(string $mutexName): void
    {
        $mutex = $this->createMutex();

        $this->assertTrue($mutex->acquire($mutexName));
        $this->assertTrue($mutex->release($mutexName));
    }

    /**
     * @dataProvider mutexDataProvider()
     */
    public function testThatMutexLockIsWorking(string $mutexName): void
    {
        $mutexOne = $this->createMutex();
        $mutexTwo = $this->createMutex();

        $this->assertTrue($mutexOne->acquire($mutexName));
        $this->assertFalse($mutexTwo->acquire($mutexName));
        $this->assertTrue($mutexOne->release($mutexName));
        $this->assertFalse($mutexTwo->release($mutexName));

        $this->assertTrue($mutexTwo->acquire($mutexName));
        $this->assertTrue($mutexTwo->release($mutexName));
    }

    /**
     * @dataProvider mutexDataProvider()
     */
    public function testThatMutexLockIsWorkingOnTheSameComponent(string $mutexName): void
    {
        $mutex = $this->createMutex();

        $this->assertTrue($mutex->acquire($mutexName));
        $this->assertFalse($mutex->acquire($mutexName));

        $this->assertTrue($mutex->release($mutexName));
        $this->assertFalse($mutex->release($mutexName));
    }

    public function testTimeout(): void
    {
        $mutexName = __FUNCTION__;
        $mutexOne = $this->createMutex();
        $mutexTwo = $this->createMutex();

        $this->assertTrue($mutexOne->acquire($mutexName));
        $microtime = microtime(true);
        $this->assertFalse($mutexTwo->acquire($mutexName, 1));
        $diff = microtime(true) - $microtime;
        $this->assertTrue($diff >= 1 && $diff < 2);
        $this->assertTrue($mutexOne->release($mutexName));
        $this->assertFalse($mutexTwo->release($mutexName));
    }

    public static function mutexDataProvider(): array
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
}
