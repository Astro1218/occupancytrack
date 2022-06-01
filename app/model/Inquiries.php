<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Inquiries extends Model
{
    public $table = 'inquiries';
    protected $fillable = [
        'report_id',
        'description',
        'number',
        'inquiry_company_id'
    ];
    public $timestamps = false;
}
