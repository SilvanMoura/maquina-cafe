<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Bluerhinos\phpMQTT;
use Illuminate\Support\Facades\Log;


class MqttSubscriber extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe to MQTT topics';

    public function handle()
    {
        $server = env('MQTT_HOST', '192.168.0.77');
        $port = env('MQTT_PORT', 1883);
        $username = env('MQTT_USERNAME', '');
        $password = env('MQTT_PASSWORD', '');
        $client_id = 'LaravelSubscriber_' . uniqid();

        $mqtt = new phpMQTT($server, $port, $client_id);

        if (!$mqtt->connect(true, null, $username, $password)) {
            $this->error("Não foi possível conectar ao servidor MQTT.");
            return;
        }

        $mqtt->subscribe(['devices/registration' => ['qos' => 0, 'function' => [$this, 'processMessage']]], 0);

        while ($mqtt->proc()) {
            // Aguarda mensagens
        }

        $mqtt->close();
    }

    public function processMessage($topic, $message)
    {
        // Logando a mensagem no terminal
        $this->info("Mensagem recebida no tópico {$topic}: {$message}");

        // Salvando em um arquivo de log
        Log::info("Mensagem MQTT", [
            'topic' => $topic,
            'message' => $message,
        ]);

        // Se desejar, processar a mensagem para lógica adicional
    }
}
