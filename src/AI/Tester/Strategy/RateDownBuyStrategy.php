<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;

class RateDownBuyStrategy extends AbstractStrategy
{
    /**
     * @var int
     */
    protected $priority = 300;

    /**
     * @return string
     */
    public function getName()
    {
        return 'Rate down buy strategy';
    }

    /**
     * @param User $user
     * @return bool
     */
    public function validForUser(User $user)
    {
        if ($user->registered && $user->buysCount > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function run(User $user)
    {
        if (!$this->apiClient->login($user)) {
            $this->logger->addError("User login failed");
            return false;
        }
        $this->logger->addInfo("User login success");

        $buys = $this->apiClient->getBuys();
        if (false === $buys) {
            $this->logger->addError("Get buys failed", [$user]);
            return false;
        }
        $this->logger->addInfo("Get buy success");

        $randomBuy = $buys[array_rand($buys)];

        if (!$this->apiClient->rateDownBuy($randomBuy)) {
            $this->logger->addError("Rate down buy failed", [$user, $randomBuy]);
            return false;
        }
        $this->logger->addInfo("Rate down buy success");

        return true;
    }
}