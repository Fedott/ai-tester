<?php

namespace AI\Tester\Command;

use AI\Tester\Client\API;
use AI\Tester\Console\Command;
use AI\Tester\Model\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
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
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        $user = $this->getUserRepository()->findOneBy(array('username' => $username));
        if (!$user) {
            throw new Exception('User not found');
        }

        /** @var API $apiClient */
        $apiClient = $this->getContainer()->get(API::class);

//        $result = $apiClient->register($user);
//        $output->writeln(sprintf("Register failed: %s", $result?"true":"false"));

        $result = $apiClient->login($user);
        $output->writeln(sprintf("Login failed: %s", $result?"true":"false"));
    }
}
