<?php

/**
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Tests
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Tests\Command;

use Openium\SymfonyToolKitBundle\Command\AbstractCommand;
use Openium\SymfonyToolKitBundle\Tests\Fixtures\Command\TestCommand;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class AbstractCommandTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Command
 */
class AbstractCommandTest extends TestCase
{
    public function testAbstractCommand(): void
    {
        $inputOption = new InputOption('nl', null, InputOption::VALUE_NONE, 'Disable log');
        $inputDef = new InputDefinition([$inputOption]);
        $input = new ArrayInput([], $inputDef);
        $input->setOption('nl', true);
        $output = new ConsoleOutput();
        $logger = $this->createMock(LoggerInterface::class);
        $command = new TestCommand($logger);
        $command->configure();
        $command->execute($input, $output);
        self::assertTrue($command instanceof AbstractCommand);
        self::assertFalse($command->getHasLog());
        $command->writeMessage('test');
    }

    public function testAbstractCommandWithLog(): void
    {
        $inputOption = new InputOption('nl', null, InputOption::VALUE_NONE, 'Disable log');
        $inputDef = new InputDefinition([$inputOption]);
        $input = new ArrayInput([], $inputDef);
        $output = new ConsoleOutput();
        $logger = $this->createMock(LoggerInterface::class);
        $command = new TestCommand($logger);
        $command->configure();
        $command->execute($input, $output);
        self::assertTrue($command instanceof AbstractCommand);
        self::assertTrue($command->getHasLog());
    }
}
