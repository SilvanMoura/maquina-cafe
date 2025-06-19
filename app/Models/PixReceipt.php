<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PixReceipt extends Model
{
    use HasFactory;

    protected $table = 'pix_receipts';

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
}
