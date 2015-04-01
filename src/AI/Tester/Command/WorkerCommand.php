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

class WorkerCommand extends Command
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

    /**
     * @var bool
     */
    protected $stopSignal = false;

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
            pcntl_signal_dispatch();
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
                $this->setWorkerStatus(Worker::STATUS_WORK);

                /** @var StrategyInterface $strategy */
                /** @var User $user */
                list($strategy, $user) = $this->strategyManager->getRandomStrategy();
                $this->logger->addDebug("Strategy for run", [$strategy, $user]);
                $strategy->run($user);
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
        pcntl_signal(SIGTERM, [$this, 'stopCommand']);
        pcntl_signal(SIGINT, [$this, 'stopCommand']);

        $this->logger = $this->getContainer()->get('logger.worker');
        $this->documentManager = $this->getDocumentManager();
        $this->workerRepository = $this->documentManager->getRepository(Worker::class);
        $this->strategyManager = $this->getStrategyManager();
        $this->worker = new Worker();
    }

    public function stopCommand()
    {
        $this->stopSignal = true;
    }

    /**
     * @param int $status
     */
    protected function setWorkerStatus($status)
    {
        $this->worker->updateLastActivity();
        $this->worker->status = $status;

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
