<?php
/**
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Command
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class AbstractCommand
 *
 * @package Openium\SymfonyToolKitBundle\Command
 */
abstract class AbstractCommand extends Command
{
    protected SymfonyStyle $io;

    protected bool $hasLog;

    /**
     * AbstractCommand constructor.
     *
     * @param string|null $name
     *
     * @throws LogicException
     */
    public function __construct(protected LoggerInterface $logger, string $name = null)
    {
        parent::__construct($name);
    }

    /**
     * configure
     *
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->addOption('nl', null, InputOption::VALUE_NONE, 'Disable log');
    }

    /**
     * execute
     *
     * @throws InvalidArgumentException
     */
    protected function prepareExecute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->hasLog = $input->getOption('nl') === false;
    }

    /**
     * writeMessage
     */
    protected function writeMessage(string $message): void
    {
        if ($this->hasLog) {
            $this->io->writeln($message);
        }
    }
}
