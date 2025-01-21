<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class SendCreditNotification implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message; // Mensagem a ser enviada
    public $pulses;  // Número de pulsos
    protected $deviceId; // ID do dispositivo

    /**
     * Cria uma nova instância do evento.
     *
     * @param string $deviceId
     * @param string $message
     * @param int $pulses
     */
    public function __construct(string $deviceId, string $message, int $pulses)
    {
        $this->deviceId = $deviceId;
        $this->message = $message;
        $this->pulses = $pulses;
    }

    /**
     * Define o canal de transmissão.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        // Canal dinâmico baseado no deviceId
        return new Channel('app.' . $this->deviceId);
    }

    /**
     * Dados que serão transmitidos.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'pulsos' => $this->pulses,
        ];
    }
}
