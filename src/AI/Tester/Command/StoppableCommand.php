<?php

namespace AI\Tester\Command;

use AI\Tester\Console\Command;

abstract class StoppableCommand extends Command
{
    /**
     * @var bool
     */
    protected $stopSignal = false;

    public function stopCommand()
    {
        $this->stopSignal = true;
    }

    protected function initStopSignal()
    {
        pcntl_signal(SIGTERM, [$this, 'stopCommand']);
        pcntl_signal(SIGINT, [$this, 'stopCommand']);
    }

    protected function dispatchSignal()
    {
        pcntl_signal_dispatch();
    }
}
