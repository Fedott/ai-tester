<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;

class RateDownBuyStrategy extends AbstractChangeRateBuyStrategy
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
    public function run(User $user)
    {
        $randomBuy = parent::run($user);

        $this->processRateDownBuy($user, $randomBuy);
    }
}