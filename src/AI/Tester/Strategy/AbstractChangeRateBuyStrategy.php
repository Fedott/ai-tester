<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\Buy;
use AI\Tester\Model\User;

abstract class AbstractChangeRateBuyStrategy extends AbstractStrategy
{
    /**
     * @param User $user
     * @return bool
     */
    public function validForUser(User $user = null)
    {
        if (null !== $user && $user->registered && $user->buysCount > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return Buy
     */
    public function run(User $user)
    {
        $this->logger->addInfo("Start strategy: {$this->getName()}");

        $this->processUserLogin($user);

        $buys = $this->processGetBuys($user);

        $randomBuy = $buys[array_rand($buys)];

        return $randomBuy;
    }
}
