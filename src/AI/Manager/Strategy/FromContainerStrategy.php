<?php


namespace AI\Manager\Strategy;


use DI\Annotation\Inject;
use DI\Container;
use League\Route\Strategy\StrategyInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class FromContainerStrategy implements StrategyInterface
{
    /**
     * @Inject
     * @var Container
     */
    protected $container;

    /**
     * @param array $controllerSection
     * @param array $vars - named wildcard segments of the matched route
     * @return Response
     */
    public function dispatch($controllerSection, array $vars)
    {
        $controller = $this->container->get($controllerSection[0]);
        $response = $controller->$controllerSection[1];

        if ($response instanceof Response) {
            return $response;
        }
        throw new RuntimeException(
            'When using the Request -> Response Strategy your controller must ' .
            'return an instance of [Symfony\Component\HttpFoundation\Response]'
        );
    }
}
