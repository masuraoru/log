<?php

namespace Masurao\Log;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;

class WriterRemote implements WriterInterface
{
    private $apiUrl;
    private $token;

    public function __construct($apiUrl, $token)
    {
        $this->apiUrl = $apiUrl;
        $this->token = $token;
    }

    public function write($channel, $priority, $message, $data=null)
    {
        $client = new Client();
        try {
            $client->post($this->apiUrl, [
                RequestOptions::JSON => [
                    'token' => $this->token,
                    'channel'=>$channel,
                    'type'=>$priority,
                    'message'=>$message,
                    'data'=>$this->normalize($data)
                ],'timeout' => 2,'connect_timeout' => 2
            ]);
        } catch (BadResponseException $e) {
            trigger_error(
                "unable to send log to remote endpoint; " .
                "message = {$e->getMessage()}; " .
                "httpStatusCode = {$e->getResponse()->getStatusCode()}; " .
                "exception class = " . get_class($e),
                E_USER_WARNING
            );
        } catch (\Exception $e) {
            trigger_error(
                "unable to send log to remote endpoint; " .
                "message = {$e->getMessage()}; " .
                "code = {$e->getCode()}; " .
                "exception class = " . get_class($e),
                E_USER_WARNING
            );
        }
    }

    private function normalize($value)
    {

        if ($value === null) {
            return null;
        }
        if (is_scalar($value)) {
            return [$value];
        }
        if ($value instanceof \DateTime) {
            return [$value->format('Y-m-d H:i:s')];
        }
        if ($value instanceof \Traversable) {
            return iterator_to_array($value);
        }
        if (is_array($value)) {
            return $value;
        }
        if (is_object($value) && !method_exists($value, '__toArray')) {
            return [
                'object('.get_class($value).')'=>@json_encode($value)
            ];
        }
        if (is_resource($value)) {
            return [
                'resource('.get_resource_type($value).')'
            ];
        }
        if (!is_object($value)) {
            return [
                gettype($value)
            ];
        }
        return [];
    }
}