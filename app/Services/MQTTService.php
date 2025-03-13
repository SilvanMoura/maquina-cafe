<?php

namespace App\Services;

use Bluerhinos\phpMQTT;

class MQTTService
{
    private $mqtt;

    public function __construct()
    {
        $this->mqtt = new phpMQTT(env('MQTT_HOST', '192.168.0.77'), env('MQTT_PORT', 1883), uniqid());

    }

    public function connect()
    {
        if (!$this->mqtt->connect(true, null, 'username', 'password')) {
            exit(1);
        }
    }

    public function publish($topic, $message)
    {
        $this->mqtt->publish($topic, $message, 0);
    }

    public function subscribe($topic, callable $callback)
    {
        $this->mqtt->subscribe([$topic => ['qos' => 0, 'function' => $callback]], 0);
    }

    public function loop()
    {
        while ($this->mqtt->proc()) {
            // Permite que o script continue enquanto processa mensagens.
        }
    }

    public function disconnect()
    {
        $this->mqtt->close();
    }
}
