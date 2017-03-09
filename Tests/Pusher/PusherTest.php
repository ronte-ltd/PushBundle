<?php

namespace RonteLtd\PushBundle\Tests\Pusher;

use RonteLtd\PushBundle\Pusher\Pusher;
use RonteLtd\PushBundle\Tests\BaseTestCase;

class PusherTest extends BaseTestCase
{
    /**
     * @var Pusher
     */
    private $pusher;

    /**
     * Create Pusher instance
     * @return Pusher
     */
    public function createPusher()
    {
        $pusher = new Pusher();
        $pusher->setApns($this->createApns());
        $pusher->setGearmanServer('127.0.0.1');
        $pusher->setGearmanPort('4730');

        return $pusher;
    }

    protected function setUp()
    {
        $this->pusher = $this->createPusher();
    }

    public function testCreateClientClass()
    {
        $client = $this->invokeMethod($this->pusher, 'createClient');
        $this->assertInstanceOf('\GearmanClient', $client);
    }

    public function testSendReturnFalse()
    {
        $result = $this->pusher->send('invalid_token', 'test_message');
        $this->assertFalse($result);
    }

    public function testAddPushReturnFalse()
    {
        $result = $this->pusher->addPush('invalid_token', 'test_message');
        $this->assertTrue($result);
    }
}

