<?php

namespace Masurao\Log;

interface WriterInterface
{
    public function write($channel, $priority, $message, $data);
}