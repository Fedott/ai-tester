<?php

namespace AI\Tester\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class User
{
    /**
     * @ODM\Id
     *
     * @var string
     */
    public $id;

    /**
     * @ODM\String
     *
     * @var string
     */
    public $username;

    /**
     * @ODM\String
     *
     * @var string
     */
    public $email;

    /**
     * @ODM\String
     *
     * @var string
     */
    public $password;

    /**
     * @ODM\Boolean
     *
     * @var bool
     */
    public $registered = false;
}
