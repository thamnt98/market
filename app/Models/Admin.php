<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles;
    protected $fillable = ['email', 'phone_number', 'password', 'name', 'role', 'ib_id', 'admin_id', 'status', 'staff_commission', 'commission', 'super_manager_id'];


    public function commission()
    {
        return $this->belongsTo('App\Models\AdminCommission', 'admin_id', 'id');
    }
}
