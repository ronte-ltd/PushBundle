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
     * @param $gearmanServer
     */
    public function setGearmanServer($gearmanServer)
    {
        $this->gearmanServer = $gearmanServer;
    }

    /**
     * @param $gearmanPort
     */
    public function setGearmanPort($gearmanPort)
    {
        $this->gearmanPort = $gearmanPort;
    }

    /**
     * @param $deviceId
     * @param $text
     * @param array $extra
     * @param null $badge
     * @return bool
     */
    public function send($deviceId, $text, $extra = [], $badge = null)
    {
        return $this->apns->send($deviceId, $text, $extra, $badge);
    }

    /**
     * @param $deviceId
     * @param $text
     * @param array $extra
     * @param null $badge
     * @return bool
     */
    public function addPush($deviceId, $text, $extra = [], $badge = null)
    {
        $client = $this->createClient();

        try {
            $client->doBackground('sendPush', json_encode([
                'deviceId' => $deviceId,
                'text' => $text,
                'extra' => $extra,
                'badge' => $badge,

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
