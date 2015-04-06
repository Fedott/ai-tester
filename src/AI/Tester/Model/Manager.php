<?php

namespace AI\Tester\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class Manager
{
    /**
     * @ODM\Id
     * @var string
     */
    public $id;

    /**
     * @ODM\Int
     * @var int
     */
    public $lastActivity;

    /**
     * @ODM\Increment
     * @var string
     */
    public $countWorkers;

    /**
     * @return bool
     */
    public function isOnline()
    {
        return time() - $this->lastActivity < 3;
    }

    public function updateLastActivity()
    {
        $this->lastActivity = time();
    }
}
