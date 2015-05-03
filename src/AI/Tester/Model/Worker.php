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

    protected static $onlineStatuses = [
        self::STATUS_READY,
        self::STATUS_WORK
    ];

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
    public $task = true;

    /**
     * @ODM\Increment
     *
     * @var int
     */
    public $countRuns = 0;

    public function updateLastActivity()
    {
        $this->lastActiveTime = time();
    }

    public function isOnline()
    {
        if (!in_array($this->status, static::$onlineStatuses)
            || time() - $this->lastActiveTime > 30
        ) {
            return false;
        }

        return true;
    }
}
