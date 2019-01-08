<?php

/**
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Command
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractCommand
 *
 * @package Openium\SymfonyToolKitBundle\Command
 */
abstract class AbstractCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $hasLog;

    /**
     * AbstractCommand constructor.
     *
     * @param LoggerInterface $logger
     * @param string|null $name
     *
     * @throws LogicException
     */
    public function __construct(LoggerInterface $logger, string $name = null)
    {
        parent::__construct($name);
        $this->logger = $logger;
    }

    /**
     * configure
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption('nl', null, InputOption::VALUE_NONE, 'Disable log');
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->hasLog = $input->getOption('nl') === false;
    }

    /**
     * writeMessage
     *
     * @param string $message
     *
     * @return void
     */
    protected function writeMessage(string $message)
    {
        if ($this->hasLog) {
            $this->output->writeln($message);
        }
    }
}
