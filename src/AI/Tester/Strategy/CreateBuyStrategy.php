<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;
use DI\Annotation\Inject;
use Faker\Generator;
use AI\Tester\Client\API;

class CreateBuyStrategy implements StrategyInterface
{
    use StrategyTrait;

    /**
     * @Inject("faker")
     * @var Generator
     */
    protected $faker;

    public function validForUser(User $user)
    {
        if (!$user->registered) {
            return false;
        }

        return true;
    }

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
    }
}
