<?php

/**
 * PHP Version >=7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\Fixtures\Command
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Tests\Fixtures\Command;

use Openium\SymfonyToolKitBundle\Command\AbstractCommand;
use Psr\Log\LoggerInterface;
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
     * AbstractCommandTest constructor.
     *
     * @param LoggerInterface $logger
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }

    /**
     * configure
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function configure()
    {
        parent::configure();
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws InvalidArgumentException
     *
     * @return int|void|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
    }

    /**
     * writeMessage
     *
     * @param string $message
     *
     * @return void
     */
    public function writeMessage(string $message)
    {
        parent::writeMessage($message);
    }

    /**
     * getHasLog
     *
     * @return bool
     */
    public function getHasLog(): bool
    {
        return $this->hasLog;
    }
}
