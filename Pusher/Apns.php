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
     * @param $pushSound
     * @param $pushExpiry
     */
    public function __construct($pushSound, $pushExpiry)
    {
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
     * @param array $payload
     * @param array $credentials
     * @return bool
     */
    public function send(string $deviceId, string $text, array $payload, array $credentials)
    {
        try {
            $push = $this->createPush($credentials);
            $push->connect();

            $msg = $this->createMessage($deviceId, $text, $payload);

            $push->add($msg);
            $push->send();
            $push->disconnect();
        } catch (\ApnsPHP_Exception $e) {
            $this->logger->log($e->getMessage());
            return false;
        } catch (\InvalidArgumentException $e) {
            $this->logger->log($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @param array $credentials
     * @return \ApnsPHP_Push
     * @throws \ApnsPHP_Exception
     */
    public function createPush(array $credentials)
    {
        if (empty($credentials['certificate'])) {
            throw new \ApnsPHP_Exception('Certificate file path not specified!');
        }

        $environment = \ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION;
        $push = new \ApnsPHP_Push($environment, $credentials['certificate']);

        if (!empty($credentials['passPhrase'])) {
            $push->setProviderCertificatePassphrase($credentials['passPhrase']);
        }

        if (!empty($credentials['certificationAuthorityFile']) && is_file($credentials['certificationAuthorityFile'])) {
            $push->setRootCertificationAuthority($credentials['certificationAuthorityFile']);
        }

        $push->setLogger($this->logger);

        return $push;
    }

    /**
     * @param string $deviceId
     * @param string $text
     * @param array $payload
     * @return \ApnsPHP_Message
     */
    public function createMessage(string $deviceId, string $text, array $payload)
    {
        $this->payloadValidate($payload);

        $extra = $payload['extra'] ?? [];
        $badge = $payload['badge'] ?? null;
        $headers = $payload['headers'] ?? [];

        $messageBody = json_encode([
            'push' => [
                'header' => $headers,
                'body' => [
                    'message' => $text,
                ],
                'metaInfo' => [
                    'project' => $payload['project'],
                    'pushType' => $payload['pushType'] ,
                    'iconBadgeNumber' => $badge,
                ],
            ]
        ]);

        $msg = new \ApnsPHP_Message($deviceId);
        $msg->setSound($this->pushSound);
        $msg->setExpiry($this->pushExpiry);
        $msg->setText($messageBody);

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
     * Run queue.
     *
     * @param array $credentials
     */
    public function runQueue(array $credentials)
    {
        $push = $this->createPush($credentials);
        $push->connect();

        foreach ($this->messages as $message) {
            $push->add($message);
        }

        $push->send();
        $push->disconnect();
    }

    /**
     * @param array $payload
     */
    private function payloadValidate(array $payload)
    {
        if (!isset($payload['project'])
            || !in_array(gettype($payload['project']), ['string', 'integer'])
        ) {
            throw new \InvalidArgumentException('Insufficient "project" value in payload array');
        }

        if (!isset($payload['pushType']) || !is_numeric($payload['pushType'])) {
            throw new \InvalidArgumentException('Insufficient "(int)pushType" value in payload array');
        }
    }

}
