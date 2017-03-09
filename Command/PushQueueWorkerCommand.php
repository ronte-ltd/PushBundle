<?php

namespace RonteLtd\PushBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PushQueueWorkerCommand
 * @package RonteLtd\PushBundle\Command
 */
class PushQueueWorkerCommand extends Command implements ContainerAwareInterface
{
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->container;
        $worker = new \GearmanWorker();
        $worker->addServer(
            $container->getParameter('ronte_push.gearman_server'),
            $container->getParameter('ronte_push.gearman_port')
        );
        $worker->addFunction('sendPush', [$this, 'sendPush']);

        while (1) {
            $worker->work();
            if ($worker->returnCode() != GEARMAN_SUCCESS) {
                break;
            }
        }
        return true;
    }

    /**
     * @param $job
     */
    public function sendPush($job)
    {
        $workload = $job->workload();
        $data = json_decode($workload, true);
        $this->container
            ->get('ronte.pusher')
            ->send($data['deviceId'], $data['text'], $data['extra'], $data['badge']);
    }

    /**
     * Command name and description
     */
    protected function configure()
    {
        $this
            ->setName('ronte_ltd:push:worker:run')
            ->setDescription('Run push queue worker');
    }
}
