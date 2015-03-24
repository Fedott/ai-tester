<?php

namespace AI\Tester\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InfoCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ai:info')
            ->setDescription('Show info about AI');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>INFO</info>");
    }
}