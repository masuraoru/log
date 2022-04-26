<?php

namespace Masurao\Log;

class Logger
{
    private $writer;
    private $channel;

    public function __construct($writer, $channel)
    {
        $this->writer = $writer;
        $this->channel = $channel;
    }

    public function error($message, $data=null)
    {
        $this->log(LogPriority::ERROR, $message, $data);
    }

    public function warning($message, $data=null)
    {
        $this->log(LogPriority::WARNING, $message, $data);
    }

    public function info($message, $data=null)
    {
        $this->log(LogPriority::INFO, $message, $data);
    }

    public function debug($message, $data=null)
    {
        $this->log(LogPriority::DEBUG, $message, $data);
    }

    private function log($priority, $message, $data)
    {
        $this->writer->write($this->channel, $priority, $message, $data);
    }
}
