<?php
namespace App\Services;

use Bluerhinos\phpMQTT;

class MQTTService
{
    private $mqtt;

    public function __construct()
    {
        // Aqui instanciamos o phpMQTT
        //$this->mqtt = new phpMQTT(env('MQTT_HOST', 'broker.hivemq.com'), env('MQTT_PORT', 1883), uniqid());
        $host = gethostbyname(env('MQTT_HOST', 'broker.hivemq.com'));
        $this->mqtt = new phpMQTT($host, env('MQTT_PORT', 1883), uniqid());
    }
    //test.mosquitto.org - testes
    //broker.hivemq.com - deploy
    public function connect()
    {
        if (!$this->mqtt->connect(true, null)) {
            exit(1);  // Se não conseguir conectar, o script termina.
        }
    }

    public function publish($topic, $message)
    {
        // Publicação no tópico MQTT
        $this->mqtt->publish($topic, $message, 0);
    }

    public function subscribe($topic, callable $callback)
    {
        // Inscreve-se no tópico e define o callback
        $this->mqtt->subscribe([$topic => ['qos' => 0, 'function' => $callback]], 0);
    }

    public function loop()
    {
        // Processa as mensagens enquanto o script estiver em execução
        while ($this->mqtt->proc()) {
            // Continua o processamento de mensagens
        }
    }

    public function loopFor($seconds = 2)
{
    $start = time();
    while (time() - $start < $seconds) {
        $this->mqtt->proc();
        usleep(100000); // 100ms
    }
}


    public function disconnect()
    {
        // Desconecta o cliente MQTT
        $this->mqtt->close();
    }
}
