<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = ['customer_id', 'merchant_id', 'delivery_agent_id', 'status_id', 'from_address', 'to_address', 'delivery_time', 'otp', 'notes'];

    public static function rules($id = null)
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'merchant_id' => 'required|exists:users,id',
            'delivery_agent_id' => 'nullable|exists:users,id',
            'status_id' => 'required|exists:statuses,id',
            'from_address' => 'nullable|string',
            'to_address' => 'nullable|string',
            'delivery_time' => 'nullable|date',
            'otp' => 'required|string|max:255|unique:orders,otp,' . ($id ? $id : 'NULL'),
            'notes' => 'nullable|string',
        ];
    }

    protected $searchable = ['from_address', 'to_address', 'otp'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getCustomerNameAttribute()
    {
        return $this && $this->customer ? $this->customer->name : '—';
    }

    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function deliveryAgent()
    {
        return $this->belongsTo(User::class, 'delivery_agent_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function getStatussAttribute()
    {
        return $this->status->name;

    }


    public function items()
    {
        return $this->hasMany(Item::class, 'order_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->otp)) {
                $order->otp = Str::random(10); // Generate a unique OTP
            }
        });
    }
}
