<?php

namespace RonteLtd\PushBundle\Tests\Logger;

use RonteLtd\PushBundle\Logger\ApnsLogger;
use RonteLtd\PushBundle\Tests\BaseTestCase;

class ApnsLoggerTest extends BaseTestCase
{
    /**
     * @return ApnsLogger
     */
    public function testApnsLoggerConstruct()
    {
        $logger = new ApnsLogger(__DIR__ . '/../log');

        $this->assertEquals('apns', $this->invokeProperty($logger, 'logFileName'));
        $this->assertEquals(__DIR__ . '/../log/ronte_ltd_pusher', $this->invokeProperty($logger, 'logDir'));

        return $logger;
    }

    /**
     * @depends testApnsLoggerConstruct
     * @param ApnsLogger $logger
     */
    public function testLog(ApnsLogger $logger)
    {
        $fileName = __DIR__ . '/../log/ronte_ltd_pusher/apns.log';
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $message = 'test Message';
        $logger->log($message);

        $content = file_get_contents($fileName);
        $this->assertEquals(date('[Y-m-d H:i:s]') . ' ' . $message . "\n", $content);
    }
}

