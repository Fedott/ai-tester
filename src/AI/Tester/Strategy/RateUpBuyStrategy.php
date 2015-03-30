<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;

class RateUpBuyStrategy extends AbstractChangeRateBuyStrategy
{
    /**
     * @var int
     */
    protected $priority = 1000;

    /**
     * @return string
     */
    public function getName()
    {
        return 'Rate up buy strategy';
    }

    /**
     * @param User $user
     * @return bool
     */
    public function run(User $user)
    {
        $randomBuy = parent::run($user);

        $this->processRateUpBuy($user, $randomBuy);
    }
}