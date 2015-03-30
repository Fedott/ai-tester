<?php

namespace AI\Tester\Strategy;

use AI\Tester\Client\API;
use AI\Tester\Model\Buy;
use AI\Tester\Model\User;
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

    /**
     * @param User $user
     * @throws StrategyException
     */
    protected function processUserLogin(User $user)
    {
        if (!$this->apiClient->login($user)) {
            $this->logger->addError("User login failed");
            throw new StrategyException("User login failed");
        }
        $this->logger->addInfo("User login success");
    }

    /**
     * @param User $user
     * @return Buy[]|bool
     * @throws StrategyException
     */
    protected function processGetBuys(User $user)
    {
        $buys = $this->apiClient->getBuys();
        if (false === $buys) {
            $this->logger->addError("Get buys failed", [$user]);
            throw new StrategyException("Get buys failed");
        }
        $this->logger->addInfo("Get buy success");

        return $buys;
    }

    /**
     * @param User $user
     * @param Buy $buy
     * @throws StrategyException
     */
    protected function processPurchaseBuy(User $user, Buy $buy)
    {
        if (!$this->apiClient->purchaseBuy($buy)) {
            $this->logger->addError("Purchase buy failed", [$user, $buy]);
            throw new StrategyException("Purchase buy failed");
        }
        $this->logger->addInfo("Purchase buy success");
    }

    /**
     * @param $buy
     * @throws StrategyException
     */
    protected function processCreateBuy($buy)
    {
        if (!$this->apiClient->createBuy($buy)) {
            $this->logger->addInfo("Create buy failed", [$buy]);
            throw new StrategyException("Create buy failed");
        }
        $this->logger->addInfo("Create buy success");
    }

    /**
     * @param User $user
     * @param Buy $buy
     * @throws StrategyException
     */
    protected function processRateUpBuy(User $user, Buy $buy)
    {
        if (!$this->apiClient->rateUpBuy($buy)) {
            $this->logger->addError("Rate up buy failed", [$user, $buy]);
            throw new StrategyException("Rate up buy failed");
        }
        $this->logger->addInfo("Rate up buy success");
    }

    /**
     * @param User $user
     * @param Buy $buy
     * @throws StrategyException
     */
    protected function processRateDownBuy(User $user, Buy $buy)
    {
        if (!$this->apiClient->rateDownBuy($buy)) {
            $this->logger->addError("Rate down buy failed", [$user, $buy]);
            throw new StrategyException("Rate down buy failed");
        }
        $this->logger->addInfo("Rate down buy success");
    }
}