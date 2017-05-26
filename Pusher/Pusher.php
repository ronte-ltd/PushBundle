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
     * @param string $deviceId
     * @param string $text
     * @param array $payload
     * @param array $credentials
     * @return bool
     */
    public function send(string $deviceId, string $text, array $payload, array $credentials)
    {
        return $this->apns->send($deviceId, $text, $payload, $credentials);
    }

    /**
     * @param $deviceId
     * @param $text
     * @param array $payload
     * @return bool
     */
    public function addPush(string $deviceId, string $text, array $payload , array $credentials)
    {
        $client = $this->createClient();

        try {
            $client->doBackground('sendPush', json_encode([
                'deviceId' => $deviceId,
                'text' => $text,
                'payload' => $payload,
                'credentials' => $credentials,
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
