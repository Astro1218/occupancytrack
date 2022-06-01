<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public $table = 'region';
    protected $fillable = [
        'r_id',
        's_code',
        'r_name'
    ];
    public $timestamps = false;
}
