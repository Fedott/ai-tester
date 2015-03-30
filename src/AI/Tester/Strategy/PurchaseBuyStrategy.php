<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;

class PurchaseBuyStrategy extends AbstractStrategy
{
    /**
     * @var int
     */
    protected $priority = 40;

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

        $randomBuy = $buys[array_rand($buys)];

        $this->processPurchaseBuy($user, $randomBuy);
    }
}