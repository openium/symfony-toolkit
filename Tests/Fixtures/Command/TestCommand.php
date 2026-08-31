<?php

namespace Openium\SymfonyToolKitBundle\Tests\Fixtures\Command;

use Openium\SymfonyToolKitBundle\Command\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'test:abstract-command')]
class TestCommand extends AbstractCommand
{
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecute($input, $output);
        $this->writeMessage('hello world');
        return Command::SUCCESS;
    }
}
