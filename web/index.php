<?php

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

/** @var DocumentManager $documentManager */
$documentManager = $container->get('doctrine.documentManager');
$workerRepository = $documentManager->getRepository(Worker::class);
$managerRepository = $documentManager->getRepository(Manager::class);

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->get("/", function () use ($app, $container, $workerRepository, $managerRepository) {
    $workers = $workerRepository->findAll();
    $managers = $managerRepository->findAll();

    return $app['twig']->render('index.twig', [
        'workers' => $workers,
        'managers' => $managers,
    ]);
});

$app->get("/start/{id}", function($id) use ($app, $container, $workerRepository) {
    /** @var Worker $worker */
    $worker = $workerRepository->find($id);
    $worker->task = true;
    $workerRepository->getDocumentManager()->persist($worker);
    $workerRepository->getDocumentManager()->flush();

    return $app->redirect("/");
});

$app->get("/stop/{id}", function($id) use ($app, $container, $workerRepository) {
    /** @var Worker $worker */
    $worker = $workerRepository->find($id);
    $worker->task = false;
    $workerRepository->getDocumentManager()->persist($worker);
    $workerRepository->getDocumentManager()->flush();

    return $app->redirect("/");
});

$app->get("/manager/{action}/{id}/{count}", function($action, $id, $count) use ($app, $managerRepository) {
    /** @var Manager $manager */
    $manager = $managerRepository->find($id);
    if ($action === 'sub') {
        $manager->countWorkers -= $count;
    } else {
        $manager->countWorkers += $count;
    }
    $managerRepository->getDocumentManager()->persist($manager);
    $managerRepository->getDocumentManager()->flush();

    return $app->redirect("/");
});

$app->get('/managers/json', function () use ($app, $managerRepository) {
    /** @var Manager[] $managers */
    $managers = $managerRepository->findAll();
    $data = [];
    foreach ($managers as $manager) {
        if ($manager->isOnline()) {
            $data[] = [
                'id' => $manager->id,
                'countWorkers' => $manager->countWorkers,
                'lastActivity' => $manager->lastActivity,
            ];
        }
    }

    return $app->json($data);
});

$app->get('/workers/json', function () use ($app, $workerRepository) {
    /** @var Worker[] $workers */
    $workers = $workerRepository->findAll();
    $data = [];
    foreach ($workers as $worker) {
        if ($worker->isOnline()) {
            $data[] = [
                'id' => $worker->id,
                'lastActiveTime' => $worker->lastActiveTime,
                'countRuns' => $worker->countRuns,
                'status' => $worker->status,
                'task' => $worker->task,
            ];
        }
    }

    return $app->json($data);
});

$app->run();
