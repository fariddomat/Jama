<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{

    use SoftDeletes;

    protected $fillable = ['order_id', 'name', 'barcode', 'status_id'];

    public static function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|max:255',
            'status_id' => 'required|exists:statuses,id'
        ];
    }

    protected $searchable = ['name', 'barcode'];
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id');
    }
    public function status()
    {
        return $this->belongsTo(\App\Models\Status::class, 'status_id');
    }

    public function getCustomerNameAttribute()
    {
        return $this->order && $this->order->customer ? $this->order->customer->name : '—';
    }
}
