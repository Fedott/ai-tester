<?php

namespace AI\Tester\Command;

use AI\Tester\Console\Command;
use AI\Tester\Model\User;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserInfoCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ai:user:info')
            ->setDescription('Show info about user')
            ->addArgument('username', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        /** @var User $user */
        $user = $this->getUserRepository()->findOneBy(array('username' => $username));

        if ($user) {
            $output->writeln(array(
                '<info>User found</info>',
                "Username: {$user->username}",
                "Email: {$user->email}",
                "Password: {$user->password}",
                sprintf("Registered: %s", $user->registered?"true":"false"),
            ));
        } else {
            $output->writeln("<error>User not found</error>");
        }
    }
}