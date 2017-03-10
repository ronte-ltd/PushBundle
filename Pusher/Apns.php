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
     * @var bool
     */
    private $pushSound;

    /**
     * @var int
     */
    private $pushExpiry;

    /**
     * Apns constructor.
     * @param $certificatesDir
     * @param $pushEnv
     * @param $pushSound
     * @param $pushExpiry
     */
    public function __construct($certificatesDir, $pushEnv, $pushSound, $pushExpiry)
    {
        $this->certificatesDir = $certificatesDir;
        $this->pushEnv = $pushEnv;
        $this->pushSound = $pushSound;
        $this->pushExpiry = $pushExpiry;
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
     * @return bool
     */
    public function send($deviceId, $text, $extra = [], $badge = null)
    {
        try {
            $push = $this->createPush();
            $push->connect();

            $msg = $this->createMessage($deviceId, $text, $extra, $badge);

            $push->add($msg);
            $push->send();
            $push->disconnect();
        } catch (\ApnsPHP_Exception $e) {
            $this->logger->log($e->getMessage());

            return false;
        }

        return true;
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
        $push->setLogger($this->logger);

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
        $msg->setSound($this->pushSound);
        $msg->setExpiry($this->pushExpiry);
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
