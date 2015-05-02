<?php

use AI\Manager\Bootstrap;
use AI\Tester\Model\Manager;
use AI\Tester\Model\Worker;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Response;

if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
    return false;
}

require __DIR__.'/../vendor/autoload.php';


$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(__DIR__.'/../config.php');
$container = $builder->build();

$bootstrap = $container->get(Bootstrap::class);
$bootstrap->dispatch();
