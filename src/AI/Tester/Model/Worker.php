<?php

namespace AI\Tester\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class Worker
{
    const STATUS_READY = 1;
    const STATUS_WORK = 2;
    const STATUS_DOWN = 3;

    /**
     * @ODM\Id
     * @var string
     */
    public $id;

    /**
     * @ODM\Int
     *
     * @var int
     */
    public $status;

    /**
     * @ODM\Int
     *
     * @var int
     */
    public $lastActiveTime;

    /**
     * @ODM\String
     *
     * @var string
     */
    public $lastStrategy;

    /**
     * @ODM\Boolean
     *
     * @var bool
     */
    public $task = false;

    public function updateLastActivity()
    {
        $this->lastActiveTime = time();
    }
}
