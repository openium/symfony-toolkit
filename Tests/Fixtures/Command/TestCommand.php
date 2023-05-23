<?php
/**
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\Fixtures\Command
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Tests\Fixtures\Command;

use Openium\SymfonyToolKitBundle\Command\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Fixtures\Command
 */
class TestCommand extends AbstractCommand
{
    /**
     * configure
     *
     * @throws InvalidArgumentException
     */
    public function configure(): void
    {
        parent::configure();
    }

    /**
     * execute
     *
     * @throws InvalidArgumentException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::prepareExecute($input, $output);
        return Command::SUCCESS;
    }

    /**
     * writeMessage
     */
    public function writeMessage(string $message): void
    {
        parent::writeMessage($message);
    }

    /**
     * getHasLog
     */
    public function getHasLog(): bool
    {
        return $this->hasLog;
    }
}
