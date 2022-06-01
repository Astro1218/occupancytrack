<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Moveouts extends Model
{
    public $table = 'moveouts';
    public $fillable = [
        'report_id',
        'description',
        'number',
        'moveout_company_id'
    ];
    public $timestamps = false;
}
