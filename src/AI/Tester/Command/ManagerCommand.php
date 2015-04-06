<?php

namespace AI\Tester\Command;

use AI\Tester\Model\Manager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Monolog\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ManagerCommand extends StoppableCommand
{
    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var DocumentRepository
     */
    protected $managerRepository;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Process[]
     */
    protected $workers = [];

    protected function configure()
    {
        $this
            ->setName("ai:manager")
            ->setDescription("Start manager for workers");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Manager start");

        $this->initEnvironment();

        $this->activityUpdate();

        while (1) {
            $this->dispatchSignal();
            if ($this->stopSignal) {
                $output->writeln("<info>Manager stopping</info>");

                $this->syncWorkerCount(0);

                $output->writeln("All workers stopped");
                break;
            }

            $this->activityUpdate();

            $this->monitorWorkers();

            $this->reloadManager();
            $this->syncWorkerCount($this->manager->countWorkers);

            sleep(1);
        }

        $output->writeln("Manager stopped");
    }

    protected function initEnvironment()
    {
        $this->initStopSignal();

        $this->logger = $this->getContainer()->get('logger.worker');
        $this->documentManager = $this->getDocumentManager();
        $this->managerRepository = $this->documentManager->getRepository(Manager::class);
        $this->manager = new Manager();
    }

    protected function monitorWorkers()
    {
        foreach ($this->workers as $num => $processWorker) {
            if (!$processWorker->isRunning()) {
                $this->logger->addError("WORKER PROCESS BROKEN", [$processWorker->getOutput(), $processWorker->getErrorOutput()]);
                unset ($this->workers[$num]);
            }
        }
    }

    /**
     * @param int $count
     */
    protected function syncWorkerCount($count)
    {
        if ($count >= 0 && count($this->workers) !== $count) {
            $diff = count($this->workers) - $count;
            if ($diff > 0) {
                for (;$diff !== 0; $diff--) {
                    $workerProcess = array_pop($this->workers);
                    $workerProcess->stop(0, SIGTERM);
                }
            } else {
                for (;$diff !== 0; $diff++) {
                    $workerProcess = new Process("./bin/ai ai:worker");
                    $workerProcess->start();
                    $this->workers[] = $workerProcess;
                }
            }
        }
    }

    protected function activityUpdate()
    {
        $this->manager->updateLastActivity();
        $this->documentManager->persist($this->manager);
        $this->documentManager->flush();
    }

    protected function reloadManager()
    {
        $managerId = $this->manager->id;
        $this->documentManager->clear(Manager::class);

        $this->manager = $this->managerRepository->find($managerId);
    }
}
