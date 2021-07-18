<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'amount_money',
        'status',
        'order_number',
        'bank_name',
        'sign',
        'created_at',
        'updated_at',
        'type',
        'login',
        'usd'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
