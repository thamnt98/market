<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name',
        'parent_id',
        'level',
        'display_name'
    ];

    public function childs()
    {
        return $this->hasMany('App\Models\Permission', 'parent_id', 'id');
    }
}
