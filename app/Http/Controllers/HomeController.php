<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;


use App\model\Reports;
use App\model\Communities;
use App\model\Periods;


class HomeController extends Controller
{
    public static $Type = '0';
    public static $againCheck = '0';
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        $viewitems = array();

        $viewitems = Communities::where('community_company_id', auth()->user()->company_id)
        ->where('name', '<>', 'Administration')
        ->where("name", "<>", "Grass Valley")
        ->get();

        $now = date("Y-m-d");

        $perioditems = Periods::orderBy('id', 'desc')->get();

        $last_item = $perioditems[0];

        $last_ending_date = date_create($last_item->ending);
        $now_date = date_create($now);


        $inital_date = array();
        $add_flag = 1;
        $report_id = "";

        $final_last = strtotime($last_item->ending);
        $final_last = strtotime("+7 day", $final_last);
        $now_string = strtotime($now);

        if($final_last < $now_string) {
            while($final_last < $now_string) {
                $final_first = strtotime("-6 day", $final_last);
                $starting = date('Y-m-d', $final_first);
                $starting_format = date("M d",$final_first);

                $ending = date('Y-m-d', $final_last);
                $ending_format = date("M d",$final_last);
                $final_last = strtotime("+7 day", $final_last);
                Periods::insert(array(
                    'starting' => $starting,
                    'ending' => $ending,
                    'caption' => $starting_format .' - '. $ending_format
                ));
            }

            $inital_date = Periods::orderBy('id', 'desc')->first();
            // $inital_date = $this->periodHasReport();

            $add_flag = 1;
        }
        else {
            $inital_date = $last_item;
            // $inital_date = $this->periodHasReport();
            $comid = auth()->user()->community_id;
            if(auth()->user()->community_id == 10) $comid = 1;

            $reports = Reports::where('period_id', $inital_date->id)
                        ->where('community_id', $comid)
                        ->first();

            if(!empty($reports)) {
                $report_id = $reports->id;
                $add_flag = 0;
            }
            else{
                $add_flag=1;
            }
        }
        $perioditems = Periods::orderBy('id', 'desc')->get();

        return view('welcome', array(
            'viewitems' => $viewitems,
            'oneItem' => $inital_date,
            'periodItems' => $perioditems,
            'userData' => auth()->user(),
            'add_flag' => $add_flag,
            'report_id' => $report_id
        ));
    }

    public function periodHasReport(){
        $perioditems = Periods::orderBy('id', 'desc')->get();
        foreach($perioditems as $item){
            $reports = Reports::where('period_id', $item->id)->get();
            if(sizeof($reports) > 0){
                return $item;
            }
        }
    }

    public function usermanage(){

        if(isset($_GET['header']))
        {
            self::$Type = $_GET['header'];
            self::$againCheck = $_GET['direction'] == "ASC"? true: false;
        }

        $Communities = new Communities;

        $userData = Auth::user();

        if($userData->leveluser < 2) {
            $result = json_decode(
                DB::table('users')->leftJoin('communities', 'users.community_id', '=', 'communities.id')
                ->where(
                    [
                        'community_id' => $userData->community_id,
                        'company_id' => $userData->company_id
                    ]
                )
                ->get([ 'users.*'])
            );

        } else {
            if($userData->leveluser == 1) {
                $result = json_decode(DB::table('users')->where(
                    [
                        'community_id' => $userData->community_id,
                        'company_id' => $userData->company_id
                    ]
                )
                ->get([ 'users.*']));
            } else {
                $result = json_decode(DB::table('users')->get([ 'users.*']));
            }

        }

        foreach ($result as $key => $value) {
            $communities = $Communities->where(['id'=>$value->community_id])->get();
            $value->community = isset($communities[0]) ? json_decode($communities)[0]->name : null;
            if(auth()->user()->company_id != 1) {
                $value->State = json_decode($Communities->where(['id'=>$value->community_id])->get())[0]->State;
            } else {
                $value->State = '0';
            }

            if($value->leveluser > 2)
                $value->community = 'All';
        }

        $viewitems = $Communities->where('community_company_id', $userData->company_id)->get();

        $arr = ['success', 'danger', 'warning', 'primary'];
        $iNum = 0;

        usort($result, array($this,'cmp1'));

        if(isset($_GET['header']))
        {
            echo json_encode($result);
            return;
        }

        return view('main.usermanage',compact(
            'result',
            'iNum',
            'arr',
            'Communities',
            'userData',
            'viewitems'
        ));

    }

    public function reportmanage(){

        $userData = Auth::user();

        if(isset($_GET['header']))
        {
            $header = $_GET['header'];
            $direction = $_GET['direction'];
            $row_count = (int)$_GET['row_count'];
            switch($header){
                case "location":
                    $header = "t.community_id";
                    break;
                case "date":
                    $header = "t.period_id";
                    break;
                case "user":
                    $header = "t.username";
                    break;
                case "edit_time":
                    $header = "t.edit_time";
                    break;
                case "what_edit":
                    $header = "t.what_edit";
                    break;
            }
        }else{
            $header = "t.edit_time";
            $direction = "DESC";
            $row_count = 0;
        }

        $sql = "SELECT *  FROM (
                    SELECT
                        c.`name`,
                        u.`name` as username,
                        r.community_id,
                        r.period_id,
                        r.id as report_id,
                        p.caption,
                        r.report_company_id,
                        r.edit_time,
                        r.what_edit
                    FROM
                        reports AS r
                        LEFT JOIN communities AS c ON r.community_id = c.id
                        LEFT JOIN periods AS p ON r.period_id = p.id
                        LEFT JOIN users AS u ON r.report_user = u.id
                    ) AS t
                WHERE
                    t.report_company_id = $userData->company_id
                ORDER BY
                    $header $direction
                    limit $row_count, 30";
        $data = DB::select($sql);
        // $data = DB::table('reports')
        //         ->leftjoin('communities', 'reports.community_id', '=', 'communities.id')
        //         ->leftjoin('periods', 'reports.period_id', '=', 'periods.id')
        //         ->leftjoin('users', 'reports.report_user', '=', 'users.id')
        //         ->where('report_company_id', $userData->company_id)
        //         ->orderBy($header, $direction)
        //         ->offset($row_count)
        //         ->limit(30)
        //         ->get(
        //             array(
        //                 'users.name as username',
        //                 'reports.*',
        //                 'periods.*',
        //                 'communities.*',
        //                 'reports.id as report_id'
        //             )
        //         )->toArray();

        if(isset($_GET['header']))
        {
            echo json_encode($data);
            return;
        }

        return view('main.reportmanage', compact('data'));

    }

    private static function cmp($a, $b) {
        if($GLOBALS['field'] != null) {
            if($GLOBALS['field'] == 'locationReport1') {
                if($GLOBALS['before'] == 'locationReport1') {
                    return strcmp($b->name, $a->name);
                } else {
                    return strcmp($a->name, $b->name);
                }
            } else if($GLOBALS['field'] == 'dateofreport1') {
                if($GLOBALS['before'] == 'dateofreport1') {
                    return strcmp($b->period_id, $a->period_id);
                } else {
                    return strcmp($a->period_id, $b->period_id);
                }
            }
            else if($GLOBALS['field'] == 'user1') {
                if($GLOBALS['before'] == 'user1') {
                    return strcmp($b->username, $a->username);
                } else {
                    return strcmp($a->username, $b->username);
                }
            }
            else if($GLOBALS['field'] == 'timeoftheedit1') {
                if($GLOBALS['before'] == 'timeoftheedit1') {
                    return strcmp($b->edit_time, $a->edit_time);
                } else {
                    return strcmp($a->edit_time, $b->edit_time);
                }
            }
            else if($GLOBALS['field'] == 'whatwasedit1') {
                if($GLOBALS['before'] == 'whatwasedit1') {
                    return strcmp($b->whatedit, $a->whatedit);
                } else {
                    return strcmp($a->whatedit, $b->whatedit);
                }
            }
        } else {
            return strcmp($a->name, $b->name);
        }
    }

    private static function cmp1($a, $b) {
        if(self::$againCheck != 'true') {
            if(self::$Type == 'name') {
                return strcmp($a->name, $b->name);
            } else if(self::$Type == 'Community') {
                return strcmp($a->community, $b->community);
            } else if(self::$Type == 'Position') {
                return strcmp($a->position, $b->position);
            } else if(self::$Type == 'Status') {
                return strcmp($a->active, $b->active);
            } else if(self::$Type == 'CreatedDate') {
                return strcmp($a->created_date, $b->created_date);
            } else if(self::$Type == 'LastLogin') {
                return strcmp($a->last_login, $b->last_login);
            } else {
                return strcmp($a->name, $b->name);
            }
        } else {
            if(self::$Type == 'name') {
                return strcmp($b->name, $a->name);
            } else if(self::$Type == 'Community') {
                return strcmp($b->community, $a->community);
            } else if(self::$Type == 'Position') {
                return strcmp($b->position, $a->position);
            } else if(self::$Type == 'Status') {
                return strcmp($b->active, $a->active);
            } else if(self::$Type == 'CreatedDate') {
                return strcmp($b->created_date, $a->created_date);
            } else if(self::$Type == 'LastLogin') {
                return strcmp($b->last_login, $a->last_login);
            } else {
                return strcmp($b->name, $a->name);
            }
        }
    }

    private static function cmp2($a, $b) {
        if($GLOBALS['field1'] != null) {
            if($GLOBALS['field1'] == 'locationReport1') {
                if($GLOBALS['before1'] == 'locationReport1') {
                    return strcmp($b->name, $a->name);
                } else {
                    return strcmp($a->name, $b->name);
                }
            } else if($GLOBALS['field1'] == 'dateofreport1') {
                if($GLOBALS['before1'] == 'dateofreport1') {
                    return strcmp($b->period_id, $a->period_id);
                } else {
                    return strcmp($a->period_id, $b->period_id);
                }
            }
            else if($GLOBALS['field1'] == 'timeoftheedit1') {
                if($GLOBALS['before1'] == 'timeoftheedit1') {
                    return strcmp($b->edit_time, $a->edit_time);
                } else {
                    return strcmp($a->edit_time, $b->edit_time);
                }
            }
            else if($GLOBALS['field1'] == 'whatwasedit1') {
                if($GLOBALS['before1'] == 'whatwasedit1') {
                    return strcmp($b->whatedit, $a->whatedit);
                } else {
                    return strcmp($a->whatedit, $b->whatedit);
                }
            }
        } else {
            return strcmp($a->name, $b->name);
        }
    }
}
