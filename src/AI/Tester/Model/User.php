<?php

namespace AI\Tester\Model;

class User
{
    public $username;
    public $email;
    public $password;

    public function __construct(Param $param)
    {
        $param->buy;
    }
}
