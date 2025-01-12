<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'hora_transacao',
        'apelido',
        'nome_vendedor',
        'id_vendedor',
        'id_transacao',
        'valor_transacao',
        'status_transacao',
        'nome_comprador',
        'id_comprador',
    ];
}
