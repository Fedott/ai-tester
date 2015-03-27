<?php

namespace AI\Tester\Strategy;

use AI\Tester\Client\API;
use DI\Annotation\Inject;
use Monolog\Logger;

trait StrategyTrait
{
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
}