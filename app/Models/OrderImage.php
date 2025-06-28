<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderImage extends Model
{

    use SoftDeletes;

    protected $fillable = ['order_id', 'path', 'type'];

    public static function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'path' => 'required|string|max:255',
            'type' => 'required|string|max:255'
        ];
    }

    protected $searchable = ['path', 'type'];
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id');
    }
}
