<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    
    use SoftDeletes;

    protected $fillable = ['customer_id', 'merchant_id', 'delivery_agent_id', 'from_address', 'to_address', 'delivery_time'];

    public static function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'merchant_id' => 'required|exists:merchants,id',
            'delivery_agent_id' => 'required|exists:delivery_agents,id',
            'from_address' => 'required|string',
            'to_address' => 'required|string',
            'delivery_time' => 'required|date'
        ];
    }

    protected $searchable = ['from_address', 'to_address'];
    public function Customer()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'customer_id');
    }
    public function Merchant()
    {
        return $this->belongsTo(\App\Models\Merchant::class, 'merchant_id');
    }
    public function DeliveryAgent()
    {
        return $this->belongsTo(\App\Models\DeliveryAgent::class, 'delivery_agent_id');
    }
}