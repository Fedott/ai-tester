<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;
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

        if (null === $user) {
            $this->randomizer->addVariants($this->strategies, 'strategy');
        } else {
            foreach ($this->strategies as $strategy) {
                if ($strategy['strategy']->validForUser($user)) {
                    $this->randomizer->addVariant($strategy['strategy'], $strategy['priority']);
                }
            }
        }
    }
}
