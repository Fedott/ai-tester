<?php

namespace AI\Tester\Strategy;

use AI\Tester\Model\User;

class RegisterUserStrategy extends AbstractStrategy
{
    /**
     * @var int
     */
    protected $priority = 1;

    /**
     * @return string
     */
    public function getName()
    {
        return 'Register user strategy';
    }

    /**
     * @param User $user
     * @return bool
     */
    public function validForUser(User $user)
    {
        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function run(User $user)
    {
        $newUser = new User();
        $newUser->username = $this->faker->userName;
        $newUser->email = $this->faker->email;
        $newUser->password = rand(12345678, 87654321);

        if (!$this->apiClient->register($user)) {
            $this->logger->addError("Register user failed");
            return false;
        }

        return true;
    }

}