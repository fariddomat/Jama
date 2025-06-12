<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\HasRolesAndPermissions;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{

    use SoftDeletes;
    use HasRolesAndPermissions;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password','active', 'contact_number', 'address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function scopeWhereRole($query,$role_name)
    {
        return $query->whereHas('roles',function($q) use ($role_name){
            return $q->whereIn('name', (array)$role_name)
                    ->orWhereIn('id',(array)$role_name);
        });
    }
    public function scopeWhereRoleNot($query,$role_name)
    {
        return $query->whereHas('roles',function($q) use ($role_name){
            return $q->whereNotIn('name', (array)$role_name)
                    ->WhereNotIn('id',(array)$role_name);
        });
    }
    public function scopeWhenSearch($query,$search)
    {
        return $query->when($search,function($q) use ($search){
            return $q->where('name','like',"%$search%" );
        });
    }

    public function scopeWhenRole($query,$role_id)
    {
        return $query->when($role_id,function($q) use ($role_id){
            return $this->scopeWhereRole($q,$role_id);
        });
    }

     public function merchantOrders()
    {
        return $this->hasMany(Order::class, 'merchant_id');
    }

    // Orders assigned to the user as a delivery agent
    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'delivery_agent_id');
    }

}
