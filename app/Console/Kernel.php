<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define os comandos Artisan para o aplicativo.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define a programação de tarefas do aplicativo.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Exemplo de tarefa agendada:
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Registra os comandos do console.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
