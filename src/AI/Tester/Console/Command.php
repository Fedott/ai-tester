<?php

namespace AI\Tester\Console;

use AI\Tester\Model\User;
use DI\Container;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->getContainer()->get('doctrine.documentManager');
    }

    /**
     * @return DocumentRepository
     */
    protected function getUserRepository()
    {
        return $this->getDocumentManager()->getRepository(User::class);
    }
}
