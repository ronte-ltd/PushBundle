<?php

namespace RonteLtd\PushBundle\Pusher;

use RonteLtd\PushBundle\Logger\ApnsLogger;

/**
 * Class Apns
 * @package RonteLtd\PushBundle\Pusher
 */
class Apns
{
    /**
     * @var array
     */
    private $messages = [];

    /**
     * Base certificates path
     * @var string
     */
    private $certificatesDir;

    /**
     * @var string
     */
    private $pushEnv;

    /**
     * @var ApnsLogger
     */
    private $logger;

    /**
     * @param $certificatesDir
     */
    public function setCertificatesDir($certificatesDir)
    {
        $this->certificatesDir = $certificatesDir;
    }

    /**
     * @param $pushEnv
     */
    public function setPushEnv($pushEnv)
    {
        $this->pushEnv = $pushEnv;
    }

    /**
     * @param ApnsLogger $logger
     */
    public function setLogger(ApnsLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Send message
     * @param $deviceId
     * @param $text
     * @param array $extra
     * @param null $badge
     * @return bool|int
     */
    public function send($deviceId, $text, $extra = [], $badge = null)
    {
        try {
            $push = $this->createPush();
            $push->setLogger($this->logger);
            $push->connect();

            $msg = $this->createMessage($deviceId, $text, $extra, $badge);

            $push->add($msg);
            $push->send();
            $push->disconnect();
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage());

            return false;
        }

        return 1;
    }

    /**
     * @return \ApnsPHP_Push
     */
    public function createPush()
    {
        $env = $this->pushEnv;
        $environment = \ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION;
        $push = new \ApnsPHP_Push(
            $environment,
            $this->certificatesDir . $env . '/server_certificates_bundle_sandbox.pem'
        );
        $push->setRootCertificationAuthority(
            $this->certificatesDir . $env . '/entrust_root_certification_authority.pem'
        );

        return $push;
    }

    /**
     * @param $deviceId
     * @param $text
     * @param array $extra
     * @param null $badge
     * @return \ApnsPHP_Message
     */
    public function createMessage($deviceId, $text, $extra = [], $badge = null)
    {
        $msg = new \ApnsPHP_Message($deviceId);
        $msg->setSound(true);
        $msg->setExpiry(12000);
        $msg->setText($text);

        if (is_numeric($badge)) {
            $msg->setBadge($badge);
        }

        foreach ($extra as $key => $val) {
            $msg->setCustomProperty($key, $val);
        }

        return $msg;
    }

    /**
     * Add message to queue
     * @param \ApnsPHP_Message $message
     */
    public function addMessage(\ApnsPHP_Message $message)
    {
        $this->messages[] = $message;
    }

    /**
     * Run queue
     */
    public function runQueue()
    {
        $push = $this->createPush();
        $push->connect();

        foreach ($this->messages as $message) {
            $push->add($message);
        }

        $push->send();
        $push->disconnect();
    }

}
