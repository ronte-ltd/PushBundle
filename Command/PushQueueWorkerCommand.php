<?php

namespace RonteLtd\PushBundle\Command;

use RonteLtd\PushBundle\Pusher\Pusher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PushQueueWorkerCommand
 * @package RonteLtd\PushBundle\Command
 */
class PushQueueWorkerCommand extends Command
{
    /**
     * @var Pusher
     */
    private $pusher;

    /**
     * @var string
     */
    private $gearmanServer;

    /**
     * @var int
     */
    private $gearmanPort;

    /**
     * PushQueueWorkerCommand constructor.
     * @param string $gearmanServer
     * @param $gearmanPort
     */
    public function __construct($gearmanServer, $gearmanPort, $bgWorkerId)
    {
        $this->gearmanServer = $gearmanServer;
        $this->gearmanPort = $gearmanPort;
        $this->bgWorkerId = $bgWorkerId;

        parent::__construct();
    }

    /**
     * @param Pusher $pusher
     */
    public function setPusher(Pusher $pusher)
    {
        $this->pusher = $pusher;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $prefix = $this->bgWorkerId;
        $worker = new \GearmanWorker();
        $worker->addServer(
            $this->gearmanServer,
            $this->gearmanPort
        );
        $worker->addFunction($prefix . 'SendMobilePush', [$this, 'sendMobilePush']);

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
    public function sendMobilePush($job)
    {
        $workload = $job->workload();
        $data = json_decode($workload, true);
        $this->pusher->send($data['deviceId'], $data['text'], $data['payload'], $data['credentials']);
    }

    /**
     * Command name and description
     */
    protected function configure()
    {
        $this
            ->setName('push:worker:run')
            ->setDescription('Run push queue worker')
        ;
    }
}
