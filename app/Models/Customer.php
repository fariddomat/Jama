<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{

    use SoftDeletes;

    protected $fillable = ['name', 'mobile', 'mr_number', 'address'];

    public static function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:255',
            'address' => 'required|string',
            'mr_number' => 'required|string|max:255|unique:customers,mr_number',
        ];
    }

        public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    protected $searchable = ['name', 'mobile', 'address'];
}
