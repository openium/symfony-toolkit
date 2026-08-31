<?php

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
 *
 * @deprecated since 7.0, will be removed in 8.0. The "--nl" option reimplements what Symfony's
 *             console component already provides natively: use the standard "-q"/"--quiet" flag
 *             and {@see OutputInterface::isQuiet()} instead of writeMessage()/prepareExecute().
 */
abstract class AbstractCommand extends Command
{
    protected SymfonyStyle $io;

    protected bool $hasLog;

    /**
     * AbstractCommand constructor.
     *
     *
     * @throws LogicException
     */
    public function __construct(protected LoggerInterface $logger, ?string $name = null)
    {
        parent::__construct($name);

        trigger_deprecation(
            'openium/symfony-toolkit',
            '7.0',
            'The "%s" class is deprecated and will be removed in 8.0, use the standard "-q"/"--quiet"'
            . ' console flag and "%s::isQuiet()" instead of the custom "--nl" option.',
            self::class,
            OutputInterface::class
        );
    }

    /**
     * configure
     *
     * @throws InvalidArgumentException
     */
    #[\Override]
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
