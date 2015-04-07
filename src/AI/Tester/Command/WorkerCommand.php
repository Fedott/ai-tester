<?php

namespace AI\Tester\Command;

use AI\Tester\Console\Command;
use AI\Tester\Model\User;
use AI\Tester\Model\Worker;
use AI\Tester\Strategy\StrategyInterface;
use AI\Tester\Strategy\StrategyManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Monolog\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WorkerCommand extends StoppableCommand
{
    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var DocumentRepository
     */
    protected $workerRepository;

    /**
     * @var StrategyManager
     */
    protected $strategyManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Worker
     */
    protected $worker;

    protected function configure()
    {
        $this
            ->setName("ai:worker")
            ->setDescription("Start worker and wait command");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initEnvironment();

        $output->writeln("<info>Worker stated</info>");
        $this->logger->addInfo("Worker started");

        $this->setWorkerStatus(Worker::STATUS_READY);

        while (1) {
            $this->dispatchSignal();
            if ($this->stopSignal) {
                $this->logger->addDebug("Stopping signal");
                $output->writeln("Worker stopping");
                break;
            }

            $this->logger->debug("Circle", [time()]);
            $this->reloadWorker();

            if (null === $this->worker) {
                $this->logger->addWarning("Worker not found");
                break;
            }

            if ($this->worker->task) {
                $this->logger->addDebug("Task found");
                $this->setWorkerStatus(Worker::STATUS_WORK, true);

                /** @var StrategyInterface $strategy */
                /** @var User $user */
                list($strategy, $user) = $this->strategyManager->getRandomStrategy();
                $this->logger->addDebug("Strategy for run", [$strategy, $user]);
                try {
                    $strategy->run($user);
                } catch (\Exception $e) {
                    $this->logger->addError("Strategy thrown Exception", [$e->getMessage(), $e]);
                }
                $this->logger->addDebug("Strategy done");
            } else {
                $this->logger->addDebug("Wait task");
                $this->setWorkerStatus(Worker::STATUS_READY);
                sleep(1);
            }
        }

        $this->setWorkerStatus(Worker::STATUS_DOWN);
        $output->writeln("<info>Worker stopped</info>");
        $this->logger->addInfo("Worker stopped");
    }

    protected function initEnvironment()
    {
        $this->initStopSignal();

        $this->logger = $this->getContainer()->get('logger.worker');
        $this->documentManager = $this->getDocumentManager();
        $this->workerRepository = $this->documentManager->getRepository(Worker::class);
        $this->strategyManager = $this->getStrategyManager();
        $this->worker = new Worker();
    }

    /**
     * @param int $status
     * @param bool $countIncrement
     */
    protected function setWorkerStatus($status, $countIncrement = false)
    {
        $this->worker->updateLastActivity();
        $this->worker->status = $status;
        if ($countIncrement) {
            $this->worker->countRuns++;
        }

        $this->documentManager->persist($this->worker);
        $this->documentManager->flush();
    }

    protected function reloadWorker()
    {
        $workerId = $this->worker->id;
        $this->documentManager->clear(Worker::class);

        $this->worker = $this->workerRepository->find($workerId);
    }
}
