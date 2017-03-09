<?php

namespace RonteLtd\PushBundle\Logger;

/**
 * Class ApnsLogger
 * @package RonteLtd\PushBundle\Logger
 */
class ApnsLogger extends BaseLogger implements \ApnsPHP_Log_Interface
{
    protected $logFileName = 'apns';
}
