<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;

class CreateBuyStrategy extends AbstractStrategy
{
    /**
     * @var int
     */
    protected $priority = 100;

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
     * @return bool
     */
    public function run(User $user)
    {
        $result = $this->apiClient->login($user);
        $this->logger->addInfo("User Login", [$result]);

        $buy = [
            'target' => $this->faker->word,
            'price' => $this->faker->randomNumber(),
        ];

        $result = $this->apiClient->createBuy($buy);
        $this->logger->addInfo("Create buy", [$result]);

        return true;
    }
}
