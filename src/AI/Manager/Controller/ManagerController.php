<?php


namespace AI\Manager\Controller;

use AI\Tester\Model\Manager;
use AI\Tester\Model\Worker;
use DI\Annotation\Inject;
use DI\Container;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class ManagerController
{
    /**
     * @Inject
     * @var Container
     */
    protected $container;

    /**
     * @Inject
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var DocumentRepository
     */
    protected $workerRepository;

    /**
     * @var DocumentRepository
     */
    protected $managerRepository;

    /**
     * @Inject
     * @var Twig_Environment
     */
    protected $twig;

    public function __construct()
    {
        $this->workerRepository = $this->documentManager->getRepository(Worker::class);
        $this->managerRepository = $this->documentManager->getRepository(Manager::class);
    }

    public function indexAction()
    {
        $workers = $this->workerRepository->findAll();
        $managers = $this->managerRepository->findAll();

        return new Response(
            $this->twig->render('index.twig', [
                'workers' => $workers,
                'managers' => $managers,
            ])
        );
    }

    public function startAction($id)
    {
        $worker = $this->workerRepository->find($id);
        $worker->task = true;
        $this->workerRepository->getDocumentManager()->persist($worker);
        $this->workerRepository->getDocumentManager()->flush();

        return new RedirectResponse('/');
    }

    public function stopAction($id)
    {
        $worker = $this->workerRepository->find($id);
        $worker->task = false;
        $this->workerRepository->getDocumentManager()->persist($worker);
        $this->workerRepository->getDocumentManager()->flush();

        return new RedirectResponse('/');
    }

    public function changeCountWorkersAction($action, $id, $count)
    {
        $manager = $this->managerRepository->find($id);
        if ($action === 'sub') {
            $manager->countWorkers -= $count;
        } else {
            $manager->countWorkers += $count;
        }
        $this->managerRepository->getDocumentManager()->persist($manager);
        $this->managerRepository->getDocumentManager()->flush();

        return new RedirectResponse('/');
    }

    public function managersAction()
    {
        /** @var Manager[] $managers */
        $managers = $this->managerRepository->findAll();
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

        return new JsonResponse($data);
    }

    public function workersAction()
    {
        /** @var Worker[] $workers */
        $workers = $this->workerRepository->findAll();
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

        return new JsonResponse($data);
    }
}