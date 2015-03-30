<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;

class CreateBuyStrategy extends AbstractStrategy
{
    /**
     * @var int
     */
    protected $priority = 50;

    /**
     * @return string
     */
    public function getName()
    {
        return 'Create buy strategy';
    }

    /**
     * @param User $user
     * @return bool
     */
    public function validForUser(User $user)
    {
        if (!$user->registered) {
            return false;
        }

        return true;
    }

    /**
     * @param User $user
     */
    public function run(User $user)
    {
        $this->logger->addInfo("Start strategy: {$this->getName()}");

        $this->processUserLogin($user);

        $buy = [
            'target' => $this->faker->word,
            'price' => $this->faker->randomNumber(),
        ];

        $this->processCreateBuy($buy);
    }
}
