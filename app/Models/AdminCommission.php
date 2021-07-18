<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminCommission extends Model
{
    protected $fillable = ['admin_id', 'us_stock_commission', 'forex_commission', 'other_commission', 'staff_us_stock_commission', 'staff_forex_commission', 'staff_other_commission'];
}
