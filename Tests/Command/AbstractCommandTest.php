<?php

namespace Openium\SymfonyToolKitBundle\Tests\Command;

use Openium\SymfonyToolKitBundle\Tests\Fixtures\Command\TestCommand;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Tester\CommandTester;

class AbstractCommandTest extends TestCase
{
    private function createCommand(): TestCommand
    {
        return new TestCommand($this->createStub(LoggerInterface::class));
    }

    public function testConstructorTriggersDeprecation(): void
    {
        // given
        $deprecations = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$deprecations): bool {
            $deprecations[] = $errstr;
            return true;
        }, \E_USER_DEPRECATED);

        // when
        $this->createCommand();
        restore_error_handler();

        // then
        self::assertCount(1, $deprecations);
        self::assertStringContainsString('AbstractCommand" class is deprecated', $deprecations[0]);
    }

    public function testWriteMessageOutputsByDefault(): void
    {
        // given
        $tester = new CommandTester($this->createCommand());
        // when
        $tester->execute([]);
        // then
        self::assertStringContainsString('hello world', $tester->getDisplay());
    }

    public function testWriteMessageIsSilencedWithNlOption(): void
    {
        // given
        $tester = new CommandTester($this->createCommand());
        // when
        $tester->execute(['--nl' => true]);
        // then
        self::assertStringNotContainsString('hello world', $tester->getDisplay());
    }
}
