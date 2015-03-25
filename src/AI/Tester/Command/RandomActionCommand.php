<?php

namespace AI\Tester\Command;

use AI\Tester\Console\Command;
use AI\Tester\Model\User;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RandomActionCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName("ai:random-action")
            ->setDescription('Random action on user')
            ->addArgument('user', InputArgument::REQUIRED, 'User id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $input->getArgument('user');
        $user = $this->getContainer()->make(User::class);

        
    }
}
