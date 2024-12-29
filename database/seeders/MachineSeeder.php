<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;

class MachineSeeder extends Seeder
{
    public function run()
    {
        Machine::create([
            'name' => 'Máquina 1',
            'description' => 'Máquina de Café - Recepção',
            'pix_key' => 'maq001@meunegocio.com',
        ]);
    }
}
