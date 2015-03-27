<?php

namespace AI\Tester\Logger;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputHandler extends AbstractHandler
{
    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(OutputInterface $output, $level = Logger::DEBUG, $bubble = true)
    {
        $this->output = $output;
        parent::__construct($level, $bubble);
    }

    public function handle(array $record)
    {
        $this->output->writeln(sprintf("[%s] :: %s", $record['datetime']->format("Y-m-d H:i:s"), $record['message']));
    }
}
