<?php

namespace RonteLtd\PushBundle\Logger;

/**
 * Class BaseLogger
 * @package RonteLtd\PushBundle\Logger
 */
abstract class BaseLogger
{
    /**
     * @var string
     */
    protected $logDir;

    /**
     * @var string
     */
    protected $logFileName;

    /**
     * BaseLogger constructor.
     * @param $logDir
     */
    public function __construct($logDir)
    {
        $this->logDir = $logDir. '/ronte_ltd_pusher';
    }

    /**
     * @param $message
     */
    public function log($message)
    {
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir);
        }

        $file = fopen($this->logDir . '/' . $this->logFileName . '.log', 'ab');

        if (flock($file, LOCK_EX)) {
            $message = date('[Y-m-d H:i:s]') . ' ' . $message . "\n";
            fwrite($file, $message);
            flock($file, LOCK_UN);
        }

        fclose($file);
    }
}
