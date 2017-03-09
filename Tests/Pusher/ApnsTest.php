<?php

namespace RonteLtd\PushBundle\Tests\Pusher;

use RonteLtd\PushBundle\Tests\BaseTestCase;

class ApnsTest extends BaseTestCase
{
    protected function setUp()
    {
        $this->apns = $this->createApns();
    }

    public function testCreatePushException()
    {
        $this->expectException('\ApnsPHP_Exception');
        $this->apns->createPush();
    }

    public function testCreateMessageException()
    {
        $this->expectException('\ApnsPHP_Message_Exception');
        $this->apns->createMessage('invalid_device_id', 'test_text');
    }

    public function testSendFalse()
    {
        $result = $this->apns->send('invalid_device_id', 'test_text');

        $this->assertFalse($result);
    }
}

