<?php
return [
    'app.url' => 'http://symfony.fextbuy.local',
    'db.host' => '127.0.0.1',
    'db.database' => 'ai-tester',
    'log.apiClient.path' => __DIR__.'/logs/apiClient.log',
    'log.apiClient.level' => \Monolog\Logger::DEBUG,
    'log.strategy.path' => __DIR__.'/logs/strategy.log',
    'log.strategy.level' => \Monolog\Logger::DEBUG,
    'doctrine.proxyDir' => __DIR__.'/cache/proxies',
    'doctrine.proxyNamespace' => 'Proxies',
    'doctrine.hydratorDir' => __DIR__.'/cache/hydrators',
    'doctrine.hydratorNamespace' => 'Hydrators',
    'doctrine.documentClassesPath' => __DIR__.'/src/AI/Tester/Model',

    'doctrine.documentManager' => DI\factory(function (DI\Container $c) {
        return \Doctrine\ODM\MongoDB\DocumentManager::create(
            $c->get('doctrine.connection'),
            $c->get('doctrine.configuration')
        );
    }),

    'doctrine.annotationDriver' => DI\factory(function(DI\Container $c) {
        \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::registerAnnotationClasses();
        return \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::create($c->get('doctrine.documentClassesPath'));
    }),

    'doctrine.connection' => DI\object(Doctrine\MongoDB\Connection::class)
        ->constructor(DI\link('db.host')),

    'doctrine.configuration' => DI\object(Doctrine\ODM\MongoDB\Configuration::class)
        ->method('setProxyDir', DI\link('doctrine.proxyDir'))
        ->method('setProxyNamespace', DI\link('doctrine.proxyNamespace'))
        ->method('setHydratorDir', DI\link('doctrine.hydratorDir'))
        ->method('setHydratorNamespace', DI\link('doctrine.hydratorNamespace'))
        ->method('setMetadataDriverImpl', DI\link('doctrine.annotationDriver'))
        ->method('setDefaultDB', DI\link('db.database')),

    'http.client' => DI\factory(function (\DI\Container $c) {
        return new \GuzzleHttp\Client([
            'base_url' => $c->get('app.url'),
            'defaults' => [
                'exceptions' => false,
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ],
        ]);
    }),

    'logger.apiClient' => DI\factory(function (\DI\Container $c) {
        return new \Monolog\Logger(
            "apiClient",
            [
                new \Monolog\Handler\StreamHandler(
                    $c->get('log.apiClient.path'),
                    $c->get('log.apiClient.level')
                )
            ]
        );
    }),

    'logger.strategy' => DI\factory(function (\DI\Container $c) {
        return new \Monolog\Logger(
            "strategy",
            [
                new \Monolog\Handler\StreamHandler(
                    $c->get('log.strategy.path'),
                    $c->get('log.strategy.level')
                )
            ]
        );
    }),

    'faker' => DI\factory(function() {
        return Faker\Factory::create();
    }),

    'strategy.manager' => DI\object(AI\Tester\Strategy\StrategyManager::class)
        ->method('setStrategies', DI\link('strategy.list')),

    'strategy.list' => DI\factory(function (\DI\Container $c) {
        return [
            $c->get(\AI\Tester\Strategy\CreateBuyStrategy::class),
            $c->get(\AI\Tester\Strategy\RateUpBuyStrategy::class),
            $c->get(\AI\Tester\Strategy\RateDownBuyStrategy::class),
            $c->get(\AI\Tester\Strategy\PurchaseBuyStrategy::class),
        ];
    }),
];