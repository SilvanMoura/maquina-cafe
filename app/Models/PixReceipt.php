<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PixReceipt extends Model
{
    use HasFactory;

    protected $table = 'pix_receipts';

    protected $fillable = [
        'id',
        'external_reference',
        'pos_id',
        'status',
        'store_id',
        'valor',
        'id_payment',
        'transaction_id',
        'id_store_internal',
        'id_user_internal'
    ];

    //protected $table = 'payment_control';

    // Campos que podem ser preenchidos em massa
    //protected $fillable = [
    //    'external_reference',
    //    'pos_id',
    //    'status',
    //    'store_id',
    //    'transaction_amount',
    //    'id_payment',
    //   'transaction_id',
    //];

    // Definindo o tipo de dados das colunas (opcional)
    //protected $casts = [
    //    'transaction_amount' => 'decimal:2',
    //];
}
