<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferPix extends Model
{
    use HasFactory;

    protected $table = 'transfer_pix';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'external_reference',
        'pos_id',
        'status',
        'store_id',
        'transaction_amount',
        'id_payment',
        'transaction_id',
        'receipt_id',
    ];

    // Definindo o tipo de dados das colunas (opcional)
    protected $casts = [
        'transaction_amount' => 'decimal:2',
    ];
}
