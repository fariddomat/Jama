<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = ['customer_id', 'merchant_id', 'delivery_agent_id', 'from_address', 'to_address', 'delivery_time', 'otp', 'notes'];

    public static function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'merchant_id' => 'required|exists:users,id',
            'delivery_agent_id' => 'nullable|exists:users,id',
            'from_address' => 'nullable|string',
            'to_address' => 'nullable|string',
            'delivery_time' => 'nullable|date',
            'otp' => 'required|string|max:255|unique:orders,otp',
            'notes' => 'nullable|string',
        ];
    }

    protected $searchable = ['from_address', 'to_address', 'otp'];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'customer_id');
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

    public function items()
    {
        return $this->hasMany(Item::class, 'order_id');
    }

    public function getStatusAttribute()
    {
        $statuses = $this->items->pluck('status.name')->unique();
        if ($statuses->count() === 1) {
            return $statuses->first();
        }
        return 'pending';
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
