<?php

namespace Aangaraa\Pay\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'aangaraa_pay_transactions';

    protected $fillable = [
        'transaction_id',
        'app_key',
        'amount',
        'currency',
        'phone_number',
        'description',
        'operator',
        'status',
        'provider_reference',
        'payment_url',
        'pay_token',
        'error_message',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'float',
        'metadata' => 'array'
    ];
}
