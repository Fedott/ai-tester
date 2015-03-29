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
     * @return bool
     */
    public function run(User $user)
    {
        $this->logger->addInfo("Start strategy: {$this->getName()}");

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

        if (!$this->apiClient->purchaseBuy($randomBuy)) {
            $this->logger->addError("Purchase buy failed", [$user, $randomBuy]);
            return false;
        }
        $this->logger->addInfo("Purchase buy success");

        return true;
    }
}