<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Buildings extends Model
{
    public $table = 'buildings';
    public $fillable = [
        'id',
        'name'
    ];
    public $timestamps = false;
}
