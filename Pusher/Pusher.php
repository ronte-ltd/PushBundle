<?php

namespace RonteLtd\PushBundle\Pusher;
use Symfony\Component\Debug\Exception\ContextErrorException;

/**
 * Class Pusher
 * @package RonteLtd\PushBundle\Pusher
 */
class Pusher
{
    /**
     * @var Apns
     */
    protected $apns;

    /**
     * @var string
     */
    protected $gearmanServer;

    /**
     * @var string
     */
    protected $gearmanPort;

    /**
     * @param Apns $apns
     */
    public function setApns(Apns $apns)
    {
        $this->apns = $apns;
    }

    /**
     * @param string $gearmanServer
     */
    public function setGearmanServer(string $gearmanServer)
    {
        $this->gearmanServer = $gearmanServer;
    }

    /**
     * @param int $gearmanPort
     */
    public function setGearmanPort(int $gearmanPort)
    {
        $this->gearmanPort = $gearmanPort;
    }

    /**
     * @param $deviceId
     * @param $text
     * @param array $payload
     * @return bool
     */
    public function send(string $deviceId, string $text, array $payload)
    {
        return $this->apns->send($deviceId, $text, $payload);
    }

    /**
     * @param $deviceId
     * @param $text
     * @param array $payload
     * @return bool
     */
    public function addPush(string $deviceId, string $text, array $payload)
    {
        $client = $this->createClient();

        try {
            $client->doBackground('sendPush', json_encode([
                'deviceId' => $deviceId,
                'text' => $text,
                'payload' => $payload,
            ]));
        } catch (ContextErrorException $e) {
            return false;
        }

        return true;
    }

    /**
     * @return \GearmanClient
     */
    private function createClient()
    {
        $client = new \GearmanClient();
        $client->addServer(
            $this->gearmanServer,
            $this->gearmanPort
        );

        return $client;
    }
}
