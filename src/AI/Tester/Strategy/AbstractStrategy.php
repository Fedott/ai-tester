<?php

namespace AI\Tester\Strategy;

use AI\Tester\Client\API;
use DI\Annotation\Inject;
use Faker\Generator;
use Monolog\Logger;

abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * @var int
     */
    protected $priority = 1;

    /**
     * @Inject
     * @var API
     */
    protected $apiClient;

    /**
     * @Inject("logger.strategy")
     * @var Logger
     */
    protected $logger;

    /**
     * @Inject("faker")
     * @var Generator
     */
    protected $faker;

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
}