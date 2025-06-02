<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    
    use SoftDeletes;

    protected $fillable = ['name', 'mobile', 'address'];

    public static function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:255',
            'address' => 'required|string'
        ];
    }

    protected $searchable = ['name', 'mobile', 'address'];
}