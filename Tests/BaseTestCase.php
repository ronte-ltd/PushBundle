<?php

namespace RonteLtd\PushBundle\Tests;

use RonteLtd\PushBundle\Logger\ApnsLogger;
use RonteLtd\PushBundle\Pusher\Apns;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object
     * @param string $methodName
     * @param array  $parameters
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Call protected/private property of a class.
     *
     * @param object &$object
     * @param string $propertyName
     *
     * @return mixed Method return.
     */
    public function invokeProperty(&$object, $propertyName)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * Create Apns instance
     * @return Apns
     */
    public function createApns()
    {
        $apns = new Apns(__DIR__ . '/apns/', 'dev', true, 12000);
        $apns->setLogger(new ApnsLogger(__DIR__ . '/log'));

        return $apns;
    }
}
