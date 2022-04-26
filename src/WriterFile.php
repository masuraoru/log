<?php

namespace Masurao\Log;

class WriterFile implements WriterInterface
{
    private $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function write($channel, $priority, $message, $data=null)
    {
        if ($data) {
            $data = ' ['.$this->normalize($data).']';
        }
        $string = '[' . date('Y-m-d H:i:s') . '] ['.LogPriority::getPriorityName($priority).'] ' . $message . $data . PHP_EOL;
        file_put_contents($this->filePath, $string, FILE_APPEND);
    }

    private function normalize($value)
    {
        if (is_scalar($value) || null === $value) {
            return $value;
        }

        $jsonFlags= JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;

        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        } elseif ($value instanceof \Traversable) {
            $value = @json_encode(iterator_to_array($value), $jsonFlags);
        } elseif (is_array($value)) {
            $value = @json_encode($value, $jsonFlags);
        } elseif (is_object($value) && !method_exists($value, '__toString')) {
            $value = sprintf('object(%s) %s', get_class($value), @json_encode($value));
        } elseif (is_resource($value)) {
            $value = sprintf('resource(%s)', get_resource_type($value));
        } elseif (!is_object($value)) {
            $value = gettype($value);
        }

        return (string) $value;
    }
}