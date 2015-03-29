<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;
use AI\Tester\Util\Randomizer;
use DI\Annotation\Inject;

class StrategyManager
{
    /**
     * @var StrategyInterface[]
     */
    protected $strategies;

    /**
     * @Inject
     * @var Randomizer
     */
    protected $randomizer;

    /**
     * @param StrategyInterface[] $strategies
     */
    public function setStrategies(array $strategies)
    {
        $this->strategies = $strategies;
    }

    /**
     * @return StrategyInterface
     */
    public function getRandomStrategyWithPriorities()
    {
        $this->prepareRandomizer();

        return $this->randomizer->getRandomVariant();
    }

    /**
     * @param User $user
     * @return StrategyInterface
     */
    public function getRandomStrategyForUser(User $user)
    {
        $this->prepareRandomizer($user);

        return $this->randomizer->getRandomVariant();
    }

    protected function prepareRandomizer(User $user = null)
    {
        $this->randomizer->reset();

        foreach ($this->strategies as $strategy) {
            if (null !== $user && $strategy->validForUser($user)) {
                $this->randomizer->addVariant($strategy, $strategy->getPriority());
            } else {
                $this->randomizer->addVariant($strategy, $strategy->getPriority());
            }
        }
    }
}
