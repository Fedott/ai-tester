<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;

interface StrategyInterface
{
    /**
     * @param User $user
     * @return bool
     */
    public function validForUser(User $user = null);

    /**
     * @param User $user
     * @throws StrategyException
     */
    public function run(User $user);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     */
    public function setPriority($priority);

    /**
     * @return string
     */
    public function getName();
}