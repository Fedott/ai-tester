<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\Buy;
use AI\Tester\Model\User;
use AI\Tester\Util\Randomizer;
use DI\Annotation\Inject;

class PurchaseBuyStrategy extends AbstractStrategy
{
    /**
     * @var int
     */
    protected $priority = 40;

    /**
     * @Inject
     * @var Randomizer
     */
    protected $randomizer;

    /**
     * @return string
     */
    public function getName()
    {
        return 'Purchase buy strategy';
    }

    /**
     * @param User $user
     * @return bool
     */
    public function validForUser(User $user)
    {
        if ($user->registered && $user->buysCount > 3) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @throws StrategyException
     */
    public function run(User $user)
    {
        $this->logger->addInfo("Start strategy: {$this->getName()}");

        $this->processUserLogin($user);

        $buys = $this->processGetBuys($user);

        $randomBuy = $this->getRandomBuy($buys);

        $this->processPurchaseBuy($user, $randomBuy);
    }

    /**
     * @param Buy[] $buys
     * @return Buy
     */
    protected function getRandomBuy($buys)
    {
        $this->prepareRandomizer($buys);

        return $this->randomizer->getRandomVariant();
    }

    /**
     * @param Buy[] $buys
     */
    protected function prepareRandomizer(array $buys)
    {
        $this->randomizer->reset();
        foreach ($buys as $buy) {
            $this->randomizer->addVariant($buy, $buy->rating);
        }
    }
}