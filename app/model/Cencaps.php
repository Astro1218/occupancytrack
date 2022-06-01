<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Cencaps extends Model
{
    public $table = 'cencaps';
    public $fillable = ['report_id', 'building_id', 'census', 'capacity', 'total_resident', 'cencaps_company_id'];
    public $timestamps = false;

    public function get_Building() {
        return $this->hasOne('App\model\Buildings', 'id', 'building_id');
    }
}
