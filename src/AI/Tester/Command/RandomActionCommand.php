<?php

namespace AI\Tester\Command;

use AI\Tester\Console\Command;
use AI\Tester\Logger\ConsoleOutputHandler;
use AI\Tester\Model\User;
use AI\Tester\Strategy\CreateBuyStrategy;
use Exception;
use Monolog\Logger;
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

        /** @var User|null $user */
        $user = $this->getUserRepository()->findOneBy(array('username' => $username));
        if (!$user) {
            throw new Exception('User not found');
        }

        /** @var Logger $logger */
        $logger = $this->getContainer()->get('logger.strategy');
        $logger->pushHandler(new ConsoleOutputHandler($output));

        /** @var CreateBuyStrategy $strategy */
        $strategy = $this->getContainer()->get(CreateBuyStrategy::class);
        if ($strategy->validForUser($user)) {
            $strategy->run($user);
        }
    }
}
