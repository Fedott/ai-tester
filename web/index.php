<?php

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Response;

if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
    return false;
}

require __DIR__.'/../vendor/autoload.php';


$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(__DIR__.'/../config.php');
$container = $builder->build();

/** @var DocumentManager $documentManager */
$documentManager = $container->get('doctrine.documentManager');
$workerRepository = $documentManager->getRepository(\AI\Tester\Model\Worker::class);

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->get("/", function () use ($app, $container, $workerRepository) {
    /** @var \AI\Tester\Model\Worker[] $workers */
    $workers = $workerRepository->findAll();

    return $app['twig']->render('index.twig', [
        'workers' => $workers,
    ]);
});

$app->get("/start/{id}", function($id) use ($app, $container, $workerRepository) {
    /** @var \AI\Tester\Model\Worker $worker */
    $worker = $workerRepository->find($id);
    $worker->task = true;
    $workerRepository->getDocumentManager()->persist($worker);
    $workerRepository->getDocumentManager()->flush();

    return $app->redirect("/");
});

$app->get("/stop/{id}", function($id) use ($app, $container, $workerRepository) {
    /** @var \AI\Tester\Model\Worker $worker */
    $worker = $workerRepository->find($id);
    $worker->task = false;
    $workerRepository->getDocumentManager()->persist($worker);
    $workerRepository->getDocumentManager()->flush();

    return $app->redirect("/");
});

$app->run();
