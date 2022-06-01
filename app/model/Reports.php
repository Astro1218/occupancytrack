<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    public $table = 'reports';
    
    public $fillable = ['id', 'community_id', 'period_id', 'unqualified', 'tours', 'deposits', 'wtd_movein', 'wtd_moveout', 'ytd_movein', 'ytd_moveout', 'total_moveout', 'prior_ye_occ', 'report_user', 'what_edit', 'edit_time', 'report_company_id'];

    public $timestamps = false;

    public function get_Community() {
        return $this->hasOne('App\model\Communities', 'id', 'community_id');   
    }

    public function get_Periods() {
        return $this->hasOne('App\model\Periods', 'id', 'period_id');   
    }
    
    public function get_Cencaps() {
        return $this->hasMany('App\model\Cencaps','report_id','id');
    }

    public function get_Inquiries() {
        return $this->hasMany('App\model\Inquiries','report_id','id');
    }

    public function get_Moveouts() {
        return $this->hasMany('App\model\Moveouts','report_id','id');
    }
}
