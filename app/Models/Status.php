<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    
    use SoftDeletes;

    protected $fillable = ['name'];

    public static function rules()
    {
        return [
            'name' => 'required|string|max:255'
        ];
    }

    protected $searchable = ['name'];
}