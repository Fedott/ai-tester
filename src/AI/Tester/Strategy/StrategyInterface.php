<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;

interface StrategyInterface
{
    public function validForUser(User $user);

    public function run(User $user);
}