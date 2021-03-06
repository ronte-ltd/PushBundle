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
     * @var string
     */
    protected $bgWorkerId;

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
     * @param string $workerId
     */
    public function setBgWorkerId(string $workerId)
    {
        $this->bgWorkerId = $workerId;
    }

    /**
     * @param string $deviceId
     * @param string|array $text
     * @param array $payload
     * @param array $credentials
     * @return bool
     */
    public function send(string $deviceId, $text, array $payload, array $credentials)
    {
        return $this->apns->send($deviceId, $text, $payload, $credentials);
    }

    /**
     * @param string $deviceId
     * @param string|array $text
     * @param array $payload
     * @param array $credentials
     * @return bool
     */
    public function addPush(string $deviceId, $text, array $payload , array $credentials)
    {
        $client = $this->createClient();
        $prefix = str_replace(' ', '', $this->bgWorkerId);

        try {
            $client->doBackground($prefix. 'SendMobilePush', json_encode([
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
