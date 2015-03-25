<?php

namespace AI\Tester\Command;

use AI\Tester\Console\Command;
use AI\Tester\Model\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ai:user:create')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('email', InputArgument::OPTIONAL, 'Email default username@email.com')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password default random');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $email = $input->getArgument('email')?:$username."@email.com";
        $password = $input->getArgument('password')?:rand(10000000, 99999999);

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = $password;

        $dm = $this->getDocumentManager();
        $dm->persist($user);
        $dm->flush();

        $output->writeln("<info>User created</info>");
        $output->writeln("Username: $username");
        $output->writeln("Email: $email");
        $output->writeln("Password: $password");
    }
}