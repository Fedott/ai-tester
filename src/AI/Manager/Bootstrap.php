<?php

namespace AI\Manager;

use AI\Manager\Controller\ManagerController;
use AI\Manager\Strategy\FromContainerStrategy;
use DI\Annotation\Inject;
use FastRoute\Dispatcher;
use League\Route\RouteCollection;
use Symfony\Component\HttpFoundation\Request;

class Bootstrap
{
    /**
     * @Inject
     * @var RouteCollection
     */
    protected $router;

    /**
     * @Inject
     * @var FromContainerStrategy
     */
    protected $strategy;

    /**
     * @var bool
     */
    protected $initialized = false;

    public function init()
    {
        $this->initRoutes();
    }

    protected function initRoutes()
    {
        $this->router->get('/', ManagerController::class . '::index', $this->strategy);
        $this->router->get('/start/{id}', ManagerController::class . '::start', $this->strategy);
        $this->router->get('/stop/{id}', ManagerController::class . '::stop', $this->strategy);
        $this->router->get(
            '/manager/{action}/{id}/{count}',
            ManagerController::class . '::changeCountWorkers',
            $this->strategy
        );
        $this->router->get('/managers/json', ManagerController::class . '::managers', $this->strategy);
        $this->router->get('/workers/json', ManagerController::class . '::workers', $this->strategy);
    }

    public function dispatch()
    {
        if (!$this->initialized) {
            $this->init();
        }

        $dispatcher = $this->router->getDispatcher();

        $request = $this->initRequest();

        $response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

        $response->send();
    }

    /**
     * @return Request
     */
    protected function initRequest()
    {
        $request = Request::createFromGlobals();
        return $request;
    }
}
