<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferPix extends Model
{
    use HasFactory;

    protected $table = 'transfer_pix';

    protected $fillable = [
        'valor',
        'nome_remetente',
        'cpf_remetente',
        'id_mercado_pago',
        'id_pix',
        'pos_id',
        'store_id',
        'status',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];
}
