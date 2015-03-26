<?php

namespace AI\Tester\Command;

use AI\Tester\Client\API;
use AI\Tester\Console\Command;
use AI\Tester\Model\User;
use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserActionCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName("ai:user:action")
            ->addArgument("username", InputArgument::REQUIRED)
            ->addArgument("action", InputArgument::REQUIRED)
            ->addArgument("data", InputArgument::IS_ARRAY, '', [])
            ->addOption("login", null, InputOption::VALUE_OPTIONAL, false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument("username");
        $action = $input->getArgument("action");
        $data = $input->getArgument("data");
        $login = (bool) $input->getOption("login");

        $apiClient = $this->getApiClient();

        $user = $this->findUserByUsername($username);
        if (!$user) {
            throw new Exception('User not found');
        }

        if (! method_exists($apiClient, $action)) {
            throw new Exception("Action not exists");
        }

        $parsedData = [];
        foreach ($data as $value) {
            if ($value == '{{user}}') {
                $value = $user;
            }
            $json = json_decode($value, true);
            if (null !== $json) {
                $value = $json;
            }

            $parsedData[] = $value;
        }

        if ($login) {
            $apiClient->login($user);
        }

        $result = call_user_func_array(
            [$apiClient, $action],
            $parsedData
        );

        $output->writeln("<info>Action result:</info>");
        $output->writeln(json_encode($result, JSON_PRETTY_PRINT));
    }
}
