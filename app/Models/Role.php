<?php

namespace App\Models;

use Laratrust\Models\Role as RoleModel;

class Role extends RoleModel
{
    public $guarded = [];
    public function scopeWhenSearch($query, $search)
    {
        return $query->when($search,function($q) use ($search){
            return $q->where('name','like',"%$search%");
        });
    }
    public function scopeWhereRoleNot($query, $role_name)
    {
        return $query->whereNotIn('name',(array)$role_name);

    }
}
