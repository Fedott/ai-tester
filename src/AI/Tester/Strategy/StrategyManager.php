<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;
use AI\Tester\Util\Randomizer;
use DI\Annotation\Inject;
use Doctrine\ODM\MongoDB\DocumentManager;

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
     * @Inject("doctrine.documentManager")
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @param StrategyInterface[] $strategies
     */
    public function setStrategies(array $strategies)
    {
        $this->strategies = $strategies;
    }

    /**
     * @return array [StrategyInterface,User]
     */
    public function getRandomStrategy()
    {
        $user = $this->getRandomUser();

        return [$this->getRandomStrategyForUser($user), $user];
    }

    /**
     * @param User|null $user
     * @return StrategyInterface
     */
    public function getRandomStrategyForUser(User $user = null)
    {
        $this->prepareRandomizer($user);

        return $this->randomizer->getRandomVariant();
    }

    /**
     * @param User|null $user
     */
    protected function prepareRandomizer(User $user = null)
    {
        $this->randomizer->reset();

        foreach ($this->strategies as $strategy) {
            if ($strategy->validForUser($user)) {
                $this->randomizer->addVariant($strategy, $strategy->getPriority());
            }
        }
    }

    /**
     * @return User|null
     */
    protected function getRandomUser()
    {
        $this->randomizer->reset();

        $userRepository = $this->documentManager->getRepository(User::class);
        $users = $userRepository->findAll();

        $this->randomizer->addVariant(null, 1);
        foreach ($users as $user) {
            $this->randomizer->addVariant($user, 1);
        }

        $user = $this->randomizer->getRandomVariant();

        return $user;
    }
}
