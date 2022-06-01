<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\model\Reports;
use App\model\Communities;
use App\model\Periods;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile() {
        $userinfo = Auth::user();

        $result = DB::table('users')
            ->rightjoin('reports', 'reports.report_user', '=', 'users.id')
            ->leftjoin('communities', 'reports.community_id', '=', 'communities.id')
            ->where('users.id', '=', $userinfo->id)
            ->take('50')
            ->get();

        $userData = DB::table('users')
            ->leftjoin('communities', 'users.community_id', '=', 'communities.id')
            ->where('users.id', '=', $userinfo->id)
            ->first();

        $reportsData = DB::table('reports')
            ->leftjoin('communities', 'reports.community_id', '=', 'communities.id')
            ->leftjoin('periods', 'reports.period_id', '=', 'periods.id')
            ->leftjoin('users', 'reports.report_user', '=', 'users.id')
            ->orderBy('periods.id', 'DESC')
            ->where('report_user','=',$userinfo->id)
            ->where('reports.report_company_id', $userinfo->company_id)
            ->take(30)
            ->get(
                array(
                    'users.name as username',
                    'reports.*',
                    'periods.*',
                    'communities.*',
                    'reports.id as report_id'
                )
            )->toArray();

        if(isset($_POST['sortTypeagain1'])) {
            if($_POST['sortTypeagain1'] != 'null') {
                $GLOBALS['before1'] = $_POST['sortTypeagain1'];
            } else {
                $GLOBALS['before1'] = "null";
            }
        }

        $GLOBALS['field1'] = null;

        if(count($_POST) > 0) {
            if($_POST['type1'] == 'locationReport1') {
                $GLOBALS['field1'] = 'locationReport1';
            } else if($_POST['type1'] == 'dateofreport1') {
                $GLOBALS['field1'] = 'dateofreport1';
            } else if($_POST['type1'] == 'user1') {
                $GLOBALS['field1'] = 'user1';
            } else if($_POST['type1'] == 'timeoftheedit1') {
                $GLOBALS['field1'] = 'timeoftheedit1';
            } else if($_POST['type1'] == 'whatwasedit1') {
                $GLOBALS['field1'] = 'whatwasedit1';
            }
            usort($reportsData, array($this,'cmp2'));
        }
        $EditedData = ['Census and Capacity', 'Inquiries', 'Moveouts', 'Statistics', 'Move In/Out'];

        return view('profile', compact(
            'result',
            'userData',
            'reportsData',
            'EditedData'
        ));
    }
}
