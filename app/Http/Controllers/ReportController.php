<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\model\Reports;
use App\model\Periods;
use App\model\Cencaps;
use App\model\Buildings;
use App\model\Moveouts;
use App\model\Inquiries;
use App\model\Communities;
use App\Console\Commands\importCsv;
use Exception;
class ReportController extends Controller
{
    public function chronjob(Request $request) {
        $output = null;
        try{

            $objKernel = new importCsv();
            $dt = new \DateTime('friday last week');
            $dt2 = new \DateTime('thursday');
            $output['last_week'] = '2022-05-06'; //$dt->format('Y-m-d');
            $output['friday_week'] = '2022-05-12'; //$dt2->format('Y-m-d');
            $output['success'] = true;
            $resultTotal = 0;
            $result = [];
            $page = 1;
            $id=0;
            $total = 0;
            $idsType = Array(5616,5604,5602);
            $check = true;
            //return $objKernel->create_csv();
            do{
                $data = $objKernel->tst($output['last_week'], $output['friday_week'], 100, $page);

                if(!empty($data)){
                    if($check === true){
                        foreach($data as $key => $value){
                            if ($value["Community"][0]["Name"] == "Prescott Lakes") {
                                $value["Community"][0]["Name"] = "Prescott";
                            }
                            switch($value['ActivityTypeId']){
                                case 5616:
                                    if($value['SaleStage'] == 'Tour'){
                                        $result['tour'][0] = isset($result['tour'][0]) ? $result['tour'][0] += 1 : 1;
                                        if(isset($result['tour'][$value["Community"][0]["Name"]])){
                                            $result['tour'][$value["Community"][0]["Name"]][0] += 1;
                                        }else{
                                            $result['tour'][$value["Community"][0]["Name"]][0] = 1;
                                        }
                                    }
                                    break;
                                case 5604:
                                    $result['inquiry'][0] = isset($result['inquiry'][0]) ? $result['inquiry'][0] += 1 : 1;
                                    if(isset($result['inquiry'][$value["Community"][0]["Name"]])){
                                        $result['inquiry'][$value["Community"][0]["Name"]][0] += 1;
                                    }else{
                                        $result['inquiry'][$value["Community"][0]["Name"]][0] = 1;
                                    }
                                    break;
                                case 5602:
                                    $result['deposit'][0] = isset($result['deposit'][0]) ? $result['deposit'][0] += 1 : 1;
                                    if(isset($result['deposit'][$value["Community"][0]["Name"]])){
                                        $result['deposit'][$value["Community"][0]["Name"]][0] += 1;
                                    }else{
                                        $result['deposit'][$value["Community"][0]["Name"]][0] = 1;
                                    }
                                    break;
                            }
                        }
                    }
                    $resultTotal = count($data);
                    $total += $resultTotal;
                    $page++;
                    //$result[$id++] = $data;
                }else{
                    $page--;
                    $id--;
                    $resultTotal = 0;
                }
                sleep(2);

            }while($resultTotal > 0 && $resultTotal <= 100);
            $output['total_register'] = $total;
            $output['total_page'] = $page;
            $output['data'] = $result;
        }catch(Exception $e){
            $output = $e->getMessage();
        }
        return $output;
    }

    static public function create(){
        $sql = "insert into reports ( `community_id`, `period_id`, `report_company_id` ) SELECT c.id community_id, ".
            " (SELECT id FROM periods WHERE 1 order by id desc limit 1) period_id, 1 ".
            " FROM communities c ".
            " WHERE c.create_census = 1 && c.community_company_id = 1";
        DB::insert($sql);
        return true;
    }

    static public function is_new_report() {
        $last_period = $result = DB::table("periods")
                            ->orderBy('id', 'DESC')
                            ->first();
        $result = DB::table("reports")
        ->where('period_id', '=', $last_period->id)
        ->where('report_company_id', '=', 1)
        ->first();

        if($result) {
            return FALSE;
        }
        return TRUE;
    }

    static public function get_report($period_id, $community_id, $report_company_id){
        $result = DB::table("reports")
        ->where('period_id', '=', $period_id)
        ->where('community_id', '=', $community_id)
        ->where('report_company_id', '=', $report_company_id)
        ->first();
        return $result;
    }

    static public function update_cron_report($report){
        $id = $report['id'];
        unset($report['id']);
        DB::table('reports')->where('id', $id)->update($report);
    }

    public function community_view(Request $request) {
        $date = $request->date;
        $community_id = $request->community_id;

        // $period_data = Periods::where('starting', '<=', $date)
        //                     ->where('ending', '>=', $date)
        //                     ->first();

        $period_data = $date;

        $add_flag = 1;
        $report_id = "";
        $report_data = array();

        if(!empty($period_data)) {

            $period_id = $period_data;
            $reportdata = Reports::where('period_id', $period_id)
                                ->where('community_id', $community_id)
                                ->where('report_company_id', auth()->user()->company_id)
                                ->first();

            if(!empty($reportdata)) {
                $add_flag = 0;
                $report_id = $reportdata->id;
            }
        }

        $community_flag = 1;
        if($community_id != auth()->user()->community_id){
            $community_flag = 0;
        }

        $result = array(
            'add_flag' => $add_flag,
            'report_id' => $report_id,
            'period_data' => $period_data,
            'user_data' => auth()->user(),
            'community_flag' => $community_flag
        );

        echo json_encode($result);
    }

    public function community_trend(Request $request) {
        $from = $request->from;
        $to = $request->to;

        $community_id = $request->community_id;

        // $from_period_data = Periods::where('starting', '<=', $from)
        //                     ->where('ending', '>=', $from)
        //                     ->first();
        $from_period_data = $from;
        // $to_period_data = Periods::where('starting', '<=', $to)
        //                 ->where('ending', '>=', $to)
        //                 ->first();
        $to_period_data = $to;

        $flag = 0;

        $report_data = array();

        if(!empty($from_period_data) && !empty($to_period_data)) {

            // $starting_date = date_create($from_period_data->starting);
            // $ending_date = date_create($to_period_data->ending);

            $period_data = Periods::where('id', '>=', $from)
                                ->where('id', '<=', $to)
                                ->get();

            for($i=0; $i<$period_data->count(); $i++) {
                $temp = $period_data[$i]->id;
                $reportdata = Reports::where('period_id', $period_data[$i]->id)
                                ->where('community_id', $community_id)
                                ->where('report_company_id', auth()->user()->company_id)
                                ->first();

                if(!empty($reportdata)) {
                    $flag = 1;
                    break;
                }

            }
        }

        $community_flag = 1;
        if($community_id != auth()->user()->community_id){
            $community_flag = 0;
        }

        $result = array(
            'flag' => $flag,
            'from_period_data' => $from_period_data,
            'to_period_data' => $to_period_data,
            'user_data' => auth()->user(),
            'community_flag' => $community_flag
        );

        echo json_encode($result);
    }

    public function company_view(Request $request) {
        $date = $request->date;

        $flag = 0;
        $period_data = $date;
        // $period_data = Periods::where('starting', '<=', $date)
        //                     ->where('ending', '>=', $date)
        //                     ->first();

        if(!empty($period_data)) {
            $period_id = $period_data;

            $report_data = Reports::where('period_id', $period_id)
                ->where('report_company_id', auth()->user()->company_id)
                ->get();

            if(!empty($report_data)) {
                $flag = 1;
            }
        }

        $result = array(
            'flag' => $flag,
            'period_data' => $period_data
        );

        echo json_encode($result);
    }

    public function company_trend(Request $request) {
        $from = $request->from;
        $to = $request->to;

        $from_period_data = $from;
        // $from_period_data = Periods::where('starting', '<=', $from)
        //                     ->where('ending', '>=', $from)
        //                     ->first();

        $to_period_data = $to;
        // $to_period_data = Periods::where('starting', '<=', $to)
        //                 ->where('ending', '>=', $to)
        //                 ->first();

        $flag = 0;

        if(!empty($from_period_data) && !empty($to_period_data)) {

            // $starting_date = date_create($from_period_data->starting);
            // $ending_date = date_create($to_period_data->ending);

            $period_data = Periods::where('id', '>=', $from_period_data)
                                ->where('id', '<=', $to_period_data)
                                ->get();

            for($i=0; $i<$period_data->count(); $i++) {

                $reportdata = Reports::where('period_id', $period_data[$i]->id)
                                ->where('report_company_id', auth()->user()->company_id)
                                ->first();

                if(!empty($reportdata)) {
                    $flag = 1;
                    break;
                }

            }
        }

        $result = array(
            'flag' => $flag,
            'from_period_data' => $from_period_data,
            'to_period_data' => $to_period_data
        );

        echo json_encode($result);
    }

    public function getchartinfodata(Request $request) {
        $cencaps = new Cencaps;
        $company_id = auth()->user()->company_id;

        $buildings = Buildings::where('building_company_id', $company_id)->get('name')->toArray();
        $fromDate = null;
        $toDate = null;

        if(isset($request->date)) {
            $date = $request->date;

            // $date_id = Periods::where('starting', '<=', $date)
            //                     ->where('ending', '>=', $date)
            //                     ->first();
            // $date_id = $date_id->id;
            $date_id = $date;

            if(isset($request->community_id)) {
                //$currentCensus = DB::select("SELECT * FROM reports AS re LEFT JOIN cencaps AS ce ON re.id = ce.report_id LEFT JOIN buildings AS bu ON ce.building_id = bu.id LEFT JOIN periods AS pe ON re.period_id = pe.id WHERE pe.id = '".$date_id."' AND re.report_company_id = '".$company_id."'");
                $community_id = $request->community_id;
                $currentCensus = DB::table('reports')
                                    ->select('*')
                                    ->leftJoin('cencaps', 'reports.id', '=', 'cencaps.report_id')
                                    ->leftJoin('buildings', 'buildings.id', '=', 'cencaps.building_id')
                                    ->leftJoin('periods', 'periods.id', '=', 'reports.period_id')
                                    ->where('reports.period_id', $date_id)
                                    ->where('reports.community_id', $community_id)
                                    ->where('reports.report_company_id', auth()->user()->company_id)
                                    ->get();
            }
            else {
                //$currentCensus = DB::select("SELECT * FROM reports AS re LEFT JOIN cencaps AS ce ON re.id = ce.report_id LEFT JOIN buildings AS bu ON ce.building_id = bu.id LEFT JOIN periods AS pe ON re.period_id = pe.id WHERE pe.id = '".$date_id."' AND re.report_company_id = '".$company_id."'");

                $currentCensus = DB::table('reports')
                                    ->select('*')
                                    ->leftJoin('cencaps', 'reports.id', '=', 'cencaps.report_id')
                                    ->leftJoin('buildings', 'buildings.id', '=', 'cencaps.building_id')
                                    ->leftJoin('periods', 'periods.id', '=', 'reports.period_id')
                                    ->where('reports.period_id', $date_id)
                                    ->where('reports.report_company_id', auth()->user()->company_id)
                                    ->get();
            }


        } else {
            $from = $request->from;
            $to = $request->to;

            if($from > $to) {
                $temp = $from;
                $from = $to;
                $to = $temp;
            }

            // $from_id = Periods::where('starting', '<=', $from)
            //                     ->where('ending', '>=', $from)
            //                     ->first();

            // $from_id = $from_id->id;
            $from_id = $from;

            // $to_id = Periods::where('starting', '<=', $to)
            //                 ->where('ending', '>=', $to)
            //                 ->first();

            // $to_id = $to_id->id;
            $to_id = $to;

            if(isset($request->community_id)) {
                //$currentCensus = DB::select("SELECT * FROM reports AS re LEFT JOIN cencaps AS ce ON re.id = ce.report_id LEFT JOIN buildings AS bu ON ce.building_id = bu.id LEFT JOIN periods AS pe ON re.period_id = pe.id WHERE pe.id >= '".$from_id."' AND pe.id <= '".$to_id."' AND re.report_company_id = '".$company_id."'");
                $community_id = $request->community_id;
                $currentCensus = DB::table('reports')
                                    ->select('*')
                                    ->leftJoin('cencaps', 'reports.id', '=', 'cencaps.report_id')
                                    ->leftJoin('buildings', 'buildings.id', '=', 'cencaps.building_id')
                                    ->leftJoin('periods', 'periods.id', '=', 'reports.period_id')
                                    ->where('reports.period_id', '>=', $from_id)
                                    ->where('reports.period_id', '<=', $to_id)
                                    ->where('reports.community_id', $community_id)
                                    ->where('reports.report_company_id', auth()->user()->company_id)
                                    ->get();
            }
            else {
                //$currentCensus = DB::select("SELECT * FROM reports AS re LEFT JOIN cencaps AS ce ON re.id = ce.report_id LEFT JOIN buildings AS bu ON ce.building_id = bu.id LEFT JOIN periods AS pe ON re.period_id = pe.id WHERE pe.id >= '".$from_id."' AND pe.id <= '".$to_id."' AND re.report_company_id = '".$company_id."'");
                $currentCensus = DB::table('reports')
                                    ->select('*')
                                    ->leftJoin('cencaps', 'reports.id', '=', 'cencaps.report_id')
                                    ->leftJoin('buildings', 'buildings.id', '=', 'cencaps.building_id')
                                    ->leftJoin('periods', 'periods.id', '=', 'reports.period_id')
                                    ->where('reports.period_id', '>=', $from_id)
                                    ->where('reports.period_id', '<=', $to_id)
                                    ->where('reports.report_company_id', auth()->user()->company_id)
                                    ->get();
            }


        }

        if(auth()->user()->company_id == 1) {
            $role = "m1";
        } else {
            $role = "m2";
        }

        return compact(
            'currentCensus',
            'buildings',
            'role'
        );
    }

    public function getdatatableinfodata(Request $request) {
        $periods = new Periods;

        $buildings = new Buildings;

        $flag = $request->flag;

        $Communities = new Communities;

        $communities_data = $Communities::where('community_company_id', auth()->user()->company_id)
        ->where("name", "<>", "Administration")
        ->where("name", "<>", "Grass Valley")
        ->get();

        // if($flag == 'westen_btn') {
        //     $communities_data = DB::table('communities')
        //         ->select('communities.*')
        //         ->join('region', 'communities.State', '=', 'region.s_code')
        //         ->where('region.r_name', 'west')
        //         ->where('community_company_id', auth()->user()->company_id)
        //         ->get();
        // }

        $main_date = $request->date;

        $temp = $periods->where('id', '<=', $main_date);
        $count = $temp->count();
        $skip = $count >= 4? ($count - 4): 0;
        $month_period_id_collection = $temp->orderBy('id', 'asc')->skip($skip)->take(4)->get();
        // $month_period_id_collection = $periods->where('starting', '<=', $main_date)->orderBy('starting', 'desc')->get()->take(4);

        $part_head_first_html = '';
        $part_head_second_html = '';

        for($i=0; $i<count($month_period_id_collection); $i++) {
            $part_head_first_html .= '<th style="text-align: center;">OCC</th>';
            $date_create = date_create($month_period_id_collection[$i]->ending);
            $date_create = date_format($date_create, "m/d/y");
            $part_head_second_html .= '<th style="text-align: center;">'. $date_create .'</th>';
        }

        $html = '';

        $html .= '<table id="main_report_table" class=" table-striped table-bordered complex-headers" style="width:100%;">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th style="text-align: center;">NAME OF</th>';
        $html .= '<th style="text-align: center;">INCR/</th>';
        $html .= '<th style="text-align: center;">MOVE</th>';
        $html .= '<th style="text-align: center;">MOVE</th>';
        $html .= $part_head_first_html;

        if(auth()->user()->company_id == 2) {
            $html .= '<th style="text-align: center;">SPEC</th>';
            $html .= '<th style="text-align: center;">M/O/</th>';
            $html .= '<th style="text-align: center;">GEN</th>';
        }
        else{
            $html .= '<th style="text-align: center;">YTD</th>';
            $html .= '<th style="text-align: center;">YTD</th>';
        }

        $html .= '<th style="text-align: center;">%</th>';
        $html .= '<th style="text-align: center;">TOTAL</th>';
        $html .= '<th style="text-align: center;">OPEN</th>';

        if(auth()->user()->company_id == 2) {
            $html .= '<th style="text-align: center;">TOTAL</th>';
            $html .= '<th style="text-align: center;">DI/DEP</th>';
            $html .= '<th style="text-align: center;">COMM</th>';
            $html .= '<th style="text-align: center;">WEEKS/</th>';
        }

        $html .= '<th style="text-align: center;">DATE</th>';

        if(auth()->user()->company_id == 2) {
            $html .= '<th style="text-align: center;">TOTAL</th>';
            $html .= '<th style="text-align: center;">PRIOR YE</th>';
            $html .= '<th style="text-align: center;">Y.T.D.</th>';
            $html .= '<th style="text-align: center;">CHANGE</th>';
        }

        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: center;">COMMUNITY</th>';
        $html .= '<th style="text-align: center; color:red;">(DECR)</th>';
        $html .= '<th style="text-align: center;">IN</th>';
        $html .= '<th style="text-align: center;">OUT</th>';
        $html .= $part_head_second_html;

        if(auth()->user()->company_id == 2) {
            $html .= '<th style="text-align: center;">DEP</th>';
            $html .= '<th style="text-align: center;">NOTICE</th>';
            $html .= '<th style="text-align: center;">DEP</th>';
        }
        else{
            $html .= '<th style="text-align: center;">MOVE IN</th>';
            $html .= '<th style="text-align: center;">MOVE OUT</th>';
        }

        $html .= '<th style="text-align: center;">OCC</th>';
        $html .= '<th style="text-align: center;">SUITES</th>';
        $html .= '<th style="text-align: center;">SUITES</th>';

        if(auth()->user()->company_id == 2) {
            $html .= '<th style="text-align: center;">RES</th>';
            $html .= '<th style="text-align: center;">CALL</th>';
            $html .= '<th style="text-align: center;">VISIT</th>';
            $html .= '<th style="text-align: center;">AT 100%</th>';
        }

        $html .= '<th style="text-align: center;">OPENED</th>';

        if(auth()->user()->company_id == 2) {
            $html .= '<th style="text-align: center;">MOVE OUTS</th>';
            $html .= '<th style="text-align: center;">OCC</th>';
            $html .= '<th style="text-align: center;">SUITES</th>';
            $html .= '<th style="text-align: center;">%</th>';
        }

        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        for($i=0; $i<count($communities_data); $i++) {

            $total_cencap_capacity = 0;
            $total_cencap_resident = 0;

            $total_inquery_comm = 0;
            $total_inquery_dep = 0;

            $total_moveout_spec = 0;
            $total_moveout_gen = 0;
            $total_moveout_mo = 0;

            $total_report_wtd_movein = 0;
            $total_report_wtd_moveout = 0;
            $total_report_ytd_movein = 0;
            $total_report_ytd_moveout = 0;

            $total_edit_time = '';

            $total_cencap_cencus_arr = array();

            $temp_html = '';

            for($j=0; $j<count($month_period_id_collection); $j++) {

                $reports = new Reports;

                $reportsData = $reports->where('community_id', $communities_data[$i]->id)
                                ->where('period_id', $month_period_id_collection[$j]->id)
                                ->where('report_company_id', auth()->user()->company_id)
                                ->first();

                $cencap_cencus = 0;

                if(!empty($reportsData) || $reportsData != null) {
                    $cencaps = new Cencaps;
                    $cencap_cencus = 0;
                    $cencapData = $cencaps->where('report_id', $reportsData->id)
                            ->where('cencaps_company_id', auth()->user()->company_id)
                            ->get();


                    for($l=0; $l<count($cencapData); $l++) {
                        $cencap_cencus += (int)$cencapData[$l]->census;
                    }
                }

                array_push($total_cencap_cencus_arr, $cencap_cencus);
            }

            $reports = new Reports;

            $reportsRecord = $reports->where('community_id', $communities_data[$i]->id)
                ->where('period_id', $month_period_id_collection[0]->id)
                ->where('report_company_id', auth()->user()->company_id)
                ->first();
            $total_cencap_capacity = 0;
            $total_cencap_resident = 0;

            $total_inquery_comm = 0;
            $total_inquery_dep = 0;

            $total_moveout_spec = 0;
            $total_moveout_gen = 0;
            $total_moveout_mo = 0;

            $total_report_wtd_movein = 0;
            $total_report_wtd_moveout = 0;
            $total_report_ytd_movein = 0;
            $total_report_ytd_moveout = 0;

            $total_move_out = 0;
            $total_prior_ye_occ = 0;
            if(!empty($reportsRecord)) {


                $cencaps = new Cencaps;
                $cencapData = $cencaps->where('report_id', $reportsRecord->id)
                                    ->where('cencaps_company_id', auth()->user()->company_id)
                                    ->get();

                for($l=0; $l<count($cencapData); $l++) {
                    $total_cencap_capacity += (int)$cencapData[$l]->capacity;
                    $total_cencap_resident += (int)$cencapData[$l]->total_resident;
                }

                /* inquiries table  */
                $inquiries = new Inquiries;

                $inqueryData_comm = $inquiries->where('report_id', $reportsRecord->id)
                            ->where('description', 'COMM VISITS')
                            ->where('inquiry_company_id', auth()->user()->company_id)
                            ->get();

                for($l=0; $l<count($inqueryData_comm); $l++) {
                    $total_inquery_comm += $inqueryData_comm[$l]->number;
                }

                ////////////////

                $inqueryData_dep = $inquiries->where('report_id', $reportsRecord->id)
                            ->where('description', 'DI/DEP CALLS')
                            ->where('inquiry_company_id', auth()->user()->company_id)
                            ->get();

                for($l=0; $l<count($inqueryData_dep); $l++) {
                    $total_inquery_dep += $inqueryData_dep[$l]->number;
                }

                /* moveout table  */
                $moveouts = new Moveouts;
                $moveoutData_spec = $moveouts->where('report_id', $reportsRecord->id)
                            ->where('description', 'SPEC DEP')
                            ->where('moveout_company_id', auth()->user()->company_id)
                            ->get();

                for($l=0; $l<count($moveoutData_spec); $l++) {
                    $total_moveout_spec += $moveoutData_spec[$l]->number;
                }

                $moveoutData_mo = $moveouts->where('report_id', $reportsRecord->id)
                        ->where('description', 'M/O NOTICE')
                        ->where('moveout_company_id', auth()->user()->company_id)
                        ->get();

                for($l=0; $l<count($moveoutData_mo); $l++) {
                    $total_moveout_mo += $moveoutData_mo[$l]->number;
                }

                $moveoutData_gen = $moveouts->where('report_id', $reportsRecord->id)
                        ->where('description', 'GEN DEP')
                        ->where('moveout_company_id', auth()->user()->company_id)
                        ->get();

                for($l=0; $l<count($moveoutData_gen); $l++) {
                    $total_moveout_gen += $moveoutData_gen[$l]->number;
                }


                /* report table  */

                $total_report_wtd_movein = $reportsRecord->wtd_movein;
                $total_report_wtd_moveout = $reportsRecord->wtd_moveout;
                $total_report_ytd_movein = $reportsRecord->ytd_movein;
                $total_report_ytd_moveout = $reportsRecord->ytd_moveout;
                $total_move_out = $reportsRecord->total_moveout;
                $total_prior_ye_occ = $reportsRecord->prior_ye_occ;

                $total_edit_time = $reportsRecord->edit_time;
            }

            $temp_html .= '<tr>';

            $temp_html .= '<td>'.$communities_data[$i]->name.'</td>';

            $total_report_incr = $total_report_wtd_movein - $total_report_wtd_moveout;

            if($total_report_incr >= 0) {
                $temp_html .= '<td style="text-align: center;">'.$total_report_incr.'</td>';
            }
            else {
                $total_report_incr = (int)$total_report_incr;
                $total_report_incr = abs($total_report_incr);

                $total_report_incr = '('.$total_report_incr.')';
                $temp_html .= '<td style="text-align: center; color: red;">'.$total_report_incr.'</td>';
            }



            $temp_html .= '<td style="text-align: center;">'.$total_report_wtd_movein.'</td>';
            $temp_html .= '<td style="text-align: center;">'.$total_report_wtd_moveout.'</td>';

            for($j=0; $j<count($total_cencap_cencus_arr); $j++) {
                $temp_html .= '<td style="text-align: center;">'.$total_cencap_cencus_arr[$j].'</td>';
            }

            if(auth()->user()->company_id == 2) {
                $temp_html .= '<td style="text-align: center;">'.$total_moveout_spec.'</td>';
                $temp_html .= '<td style="text-align: center;">'.$total_moveout_mo.'</td>';
                $temp_html .= '<td style="text-align: center;">'.$total_moveout_gen.'</td>';
            }
            else {
                $temp_html .= '<td style="text-align: center;">'.$total_report_ytd_movein.'</td>';
                $temp_html .= '<td style="text-align: center;">'.$total_report_ytd_moveout.'</td>';
            }

            if($total_cencap_capacity == 0) {
                $total_cencap_percent = '0.00%';
            }
            else {
                $total_cencap_percent = number_format(100 * $total_cencap_cencus_arr[0] / $total_cencap_capacity, '2', '.', "").'%';
            }

            if($flag == 'full_percent_btn') {
                if($total_cencap_capacity == 0) {
                    continue;
                }else{
                    $temp = floor(100 * $total_cencap_cencus_arr[0] / $total_cencap_capacity);
                    if( $temp < 100){
                        continue;
                    }
                }

            }

            $temp_html .= '<td style="text-align: center;">'. $total_cencap_percent . '</td>';
            $temp_html .= '<td style="text-align: center;">'.$total_cencap_capacity.'</td>';

            $total_cencap_open_unit = $total_cencap_capacity - $total_cencap_cencus_arr[0];
            $temp_html .= '<td style="text-align: center;">'.$total_cencap_open_unit.'</td>';

            if(auth()->user()->company_id == 2) {
                $temp_html .= '<td style="text-align: center;">'.$total_cencap_resident.'</td>';
                $temp_html .= '<td style="text-align: center;">'.$total_inquery_dep.'</td>';
                $temp_html .= '<td style="text-align: center;">'.$total_inquery_comm.'</td>';

                $week_report_data = Reports::where('period_id', $month_period_id_collection[0]->id)
                            ->where('community_id', $communities_data[$i]->id)
                            ->where('report_company_id', auth()->user()->company_id)
                            ->first();

                if(empty($week_report_data)) {
                    $temp_html .= '<td style="text-align: center;"></td>';
                }
                else {
                    $week_report_id = $week_report_data->id;

                    $week_real_data = Reports::where('id', '>=', $week_report_id)
                            ->where('community_id', $communities_data[$i]->id)
                            ->where('report_company_id', auth()->user()->company_id)
                            ->orderBy('id')
                            ->get();


                    if(empty($week_real_data)) {
                        $temp_html .= '<td style="text-align: center;"></td>';
                    }
                    else {
                        $report_week = 0;
                        for($m=0; $m<count($week_real_data); $m++) {
                            $week_cencaps_data = $week_real_data[$m]->get_Cencaps->first();

                            $week_census = $week_cencaps_data->census;
                            $week_capacity = $week_cencaps_data->capacity;

                            if($week_census == $week_capacity) {
                                $report_week++;
                            }
                            else {
                                break;
                            }
                        }

                        $temp_html .= '<td style="text-align: center;">'.$report_week.'</td>';
                    }
                }

            }

            if($flag == 'more_year_btn') {

                // communities open more than 2 year -
                // replace by communities that have decrease -
                // only show communities that have inc/decr table negative

                if($total_report_wtd_movein - $total_report_wtd_moveout >= 0) {
                    continue;
                }
                else {

                }
                // if($communities_data[$i]->open_date == '0000-00-00' || $communities_data[$i]->open_date == '') {
                //     continue;
                // }
                // else {
                //     $now_year = date("Y");

                //     $edit_time_arr = explode('-', $communities_data[$i]->open_date);
                //     $edit_time_year = (int)$edit_time_arr[0];

                //     if((int)($now_year - $edit_time_year) < 2) {
                //         continue;
                //     }
                // }
            }


            if($flag == 'less_year_btn') {
                // communities have less than 2 year -
                // replace by communities that have increase -
                // only show communities that have inc/decr table positive

                if($total_report_incr <= 0) {
                    continue;
                }
                else {
                }
                // if($communities_data[$i]->open_date == '0000-00-00' || $communities_data[$i]->open_date == '') {
                //     continue;
                // }
                // else {
                //     $now_year = date("Y");

                //     $edit_time_arr = explode('-', $communities_data[$i]->open_date);
                //     $edit_time_year = (int)$edit_time_arr[0];

                //     if((int)($now_year - $edit_time_year) > 2) {
                //         continue;
                //     }

                // }
            }


            if($flag == 'pre_btn') {
                // western Region-
                // with communities not at 100% -
                // display all the ones that have less than 100% at the occ%% table

                if($total_cencap_capacity == 0) {
                }
                else {
                    // if($total_cencap_percent != '100.00%'){
                        $temp = floor(100 * $total_cencap_cencus_arr[0] / $total_cencap_capacity);
                        if( $temp >= 100){
                            continue;
                        }
                    // }
                }

            }

            if($flag == 'westen_btn') {
            // Pre open - With open unit -
            // will only show the one that have open unit table bigger than 0
                if($total_cencap_open_unit > 0){

                }else{
                    continue;
                }

                // $communities_data = DB::table('communities')
                //     ->select('communities.*')
                //     ->join('region', 'communities.State', '=', 'region.s_code')
                //     ->where('region.r_name', 'west')
                //     ->where('community_company_id', auth()->user()->company_id)
                //     ->get();
            }
            $temp_html .= '<td style="text-align: center;">'.$communities_data[$i]->open_date.'</td>';

            if(auth()->user()->company_id == 2) {
                $temp_html .= '<td style="text-align: center;">'.$total_move_out.'</td>';
                $temp_html .= '<td style="text-align: center;">'.$total_prior_ye_occ.'</td>';
                $temp_html .= '<td style="text-align: center;">'. ($total_cencap_cencus_arr[0] - $total_prior_ye_occ) .'</td>';
                if($total_cencap_capacity == 0) {
                    $temp_html .= '<td style="text-align: center;">0.00%</td>';
                }
                else {
                    $temp_html .= '<td style="text-align: center;">'. number_format(100 * (($total_cencap_cencus_arr[0] - $total_prior_ye_occ) / $total_cencap_capacity), '2', '.', "").'%' .'</td>';
                }
            }

            $temp_html .= '</tr>';


            $html .= $temp_html;
        }

        $html .= '</tbody>';
        $html .= '</table>';

        echo $html;
    }


    public function community_view_chronjob(Request $request) {

        // $date = '2022/03/12';//$request->date;
        // $period_data = Periods::where('starting', '<=', $date)
        //                      ->where('ending', '>=', $date)
        //                      ->first();
        $period_data = Periods::orderBy('ending', 'desc')
                             ->first();
        $period_id = $period_data->id;
        $community_id = $request->community_id;

//        $report_data = Reports::where('period_id', $period_id)
//                            //->where('community_id', $community_id)
//                            //->where('report_company_id', 1)
//                            ->first();
//        $statistics = Reports::where('period_id', $period_id)
//                            ->get();
        $statistics = DB::table('reports')
            ->where('period_id', $period_id)
//            ->join('communities', 'reports.community_id', '=', 'communities.id')
//            ->select('communities.*')
            ->get();
//        $statistics = Reports::where('period_id', '>=', 500)
//                            ->get();
        $statistc_result = array(
            'editable' => $statistics->sum('editable'),
            'unqualified' => $statistics->sum('unqualified'),
            'tours' => $statistics->sum('tours'),
            'deposits' => $statistics->sum('deposits'),
            'wtd_movein' => $statistics->sum('wtd_movein'),
            'wtd_moveout' => $statistics->sum('wtd_moveout'),
            'ytd_movein' => $statistics->sum('ytd_movein'),
            'ytd_moveout' => $statistics->sum('ytd_moveout'),
            );

        // if(!empty($report_data)) {
        //     $report_id = $report_data->id;
        //     $week_data = Reports::where('id', '>=', $report_id)
        //                     //->where('community_id', $community_id)
        //                     ->get();
        // }
        // else {
        //     $week_data = array();
        // }

        $community_data = Communities::where('community_company_id', 1)
        ->where('name', '<>', 'Administration')
        ->where("name", "<>", "Grass Valley")
        ->get();

        //$perioditems = Periods::orderBy('id', 'desc')->get();


        return array(
            'statistics' => $statistc_result,
            //'community_data' => $community_data,
            //'periodItems' => $perioditems,
            'period_data' => $period_data,
            //'community_id' => $community_id,
            'week_data' => $statistics
        );
    }

    public function community_view_goto(Request $request) {
        if(!empty($request->date)) {
            $date = $request->date;
            $period_data = Periods::where('id', '<=', $date)
                                ->where('id', '>=', $date)
                                ->first();
            // $period_data = Periods::where('starting', '<=', $date)
            //                     ->where('ending', '>=', $date)
            //                     ->first();
            $period_id = $period_data->id;
            $community_id = $request->community_id;

            $report_data = Reports::where('period_id', $period_id)
                                ->where('community_id', $community_id)
                                ->where('report_company_id', auth()->user()->company_id)
                                ->first();

            if(!empty($report_data)) {
                $report_id = $report_data->id;
                $week_data = Reports::where('id', '>=', $report_id)
                                ->where('community_id', $community_id)
                                ->get();
            }
            else {
                $week_data = array();
            }


        }
        else {
            $report_id = $request->report_id;

            $report_data = Reports::find($report_id);
            $period_id = $report_data->period_id;
            $community_id = $report_data->community_id;
            $period_data = Periods::find($period_id);

            $week_data = Reports::where('id', '>=', $report_id)
                            ->where('community_id', $community_id)
                            ->get();
        }

        $user_data = auth()->user();
        $community_data = Communities::where('community_company_id', auth()->user()->company_id)
        ->where('name', '<>', 'Administration')
        ->where("name", "<>", "Grass Valley")
        ->get();

        $perioditems = Periods::orderBy('id', 'desc')->get();


        return view('community_viewreports', array(
            'report_data' => $report_data,
            'user_data' => $user_data,
            'community_data' => $community_data,
            'periodItems' => $perioditems,
            'period_data' => $period_data,
            'community_id' => $community_id,
            'week_data' => $week_data
        ));

    }

    public function community_trend_goto(Request $request) {
        $community_id = $request->community_id;
        $from = $request->from;
        $to = $request->to;

        $user_data = auth()->user();

        $period_data = Periods::where('id', '>=', $from)
                        ->where('id', '<=', $to)
                        ->get();

        // $period_data = Periods::where('starting', '>=', $from)
        //                 ->where('starting', '<=', $to)
        //                 ->get();

        $report_data = array();
        for($i=0; $i<count($period_data); $i++) {
            $report = Reports::where('community_id', $community_id)
                            ->where('period_id', $period_data[$i]->id)
                            ->first();

            if(!empty($report)){
                array_push($report_data, $report);
            }
        }

        // $week_data = Reports::where('id', '>=', $report_id)
        //                         ->where('community_id', $community_id)
        //                         ->get();

        $building_data = array();
        foreach($report_data as $report) {
            foreach($report->get_Cencaps as $cencaps) {
                $flag = 0;
                for($i=0; $i<count($building_data); $i++) {
                    if($building_data[$i] == $cencaps->get_Building->name) {
                        $flag =1;
                        break;
                    }
                }

                if($flag == 0) {
                    array_push($building_data, $cencaps->get_Building->name);
                }
            }
        }

        $building_db_data = Buildings::where('building_company_id', $user_data->company_id)->get();
        $order_array = [];
        for($i=0; $i<sizeof($building_data); $i++){
            foreach($building_db_data as $temp){
                if($temp['name'] == $building_data[$i]){
                    array_push($order_array, $temp['id']);
                }
            }
        }
        for($i=0; $i<sizeof($order_array); $i++){
            for($j=0; $j<sizeof($order_array); $j++){
                if($order_array[$i] < $order_array[$j]){
                    $temp = $order_array[$i];
                    $order_array[$i] = $order_array[$j];
                    $order_array[$j] = $temp;

                    $temp = $building_data[$i];
                    $building_data[$i] = $building_data[$j];
                    $building_data[$j] = $temp;
                }
            }
        }
        
        // var_dump($report_data);
        // die();

        return view('community_trendreports', array(
            'building_data' => $building_data,
            'report_data' => $report_data,
            'period_data' => $period_data,
            'user_data' => $user_data,
            'from' => $from,
            'to' => $to,
            'community_id' => $community_id
        ));
    }

    public function company_view_goto(Request $request) {
        $date = $request->date;

        $period_data = Periods::where('id', '<=', $date)
                            ->where('id', '>=', $date)
                            ->first();

        // $period_data = Periods::where('starting', '<=', $date)
        //                     ->where('ending', '>=', $date)
        //                     ->first();

        $period_id = $period_data->id;
        $report_data = Reports::where('period_id', $period_id)
                            ->where('report_company_id', auth()->user()->company_id)
                            ->distinct()
                            ->get();

        $community_data = Communities::where("name", "<>", "Grass Valley")
        ->where('name', '<>', 'Administration')
        ->get();
        $user_data = auth()->user();

        $building_data = array();
        foreach($report_data as $report) {
            foreach($report->get_Cencaps as $cencaps) {
                $flag = 0;
                for($i=0; $i<count($building_data); $i++) {
                    if($building_data[$i] == $cencaps->get_Building->name) {
                        $flag =1;
                        break;
                    }
                }

                if($flag == 0) {
                    array_push($building_data, $cencaps->get_Building->name);
                }
            }
        }

        $building_db_data = Buildings::where('building_company_id', $user_data->company_id)->get();
        $order_array = [];
        for($i=0; $i<sizeof($building_data); $i++){
            foreach($building_db_data as $temp){
                if($temp['name'] == $building_data[$i]){
                    array_push($order_array, $temp['id']);
                }
            }
        }
        for($i=0; $i<sizeof($order_array); $i++){
            for($j=0; $j<sizeof($order_array); $j++){
                if($order_array[$i] < $order_array[$j]){
                    $temp = $order_array[$i];
                    $order_array[$i] = $order_array[$j];
                    $order_array[$j] = $temp;

                    $temp = $building_data[$i];
                    $building_data[$i] = $building_data[$j];
                    $building_data[$j] = $temp;
                }
            }
        }
        
        return view('company_viewreports', array(
            'building_data' => $building_data,
            'report_data' => $report_data,
            'period_data' => $period_data,
            'user_data' => $user_data,
            'community_data' => $community_data
        ));
    }

    public function company_trend_goto(Request $request) {
        $from = $request->from;
        $to = $request->to;

        $user_data = auth()->user();

        $period_data = Periods::where('id', '>=', $from)
                        ->where('id', '<=', $to)
                        ->get();
        // $period_data = Periods::where('starting', '>=', $from)
        //                 ->where('starting', '<=', $to)
        //                 ->get();

        $report_data = array();
        for($i=0; $i<count($period_data); $i++) {
            $report = Reports::where('period_id', $period_data[$i]->id)
                            ->where('report_company_id', auth()->user()->company_id)
                            ->get();

            for($j=0; $j<$report->count(); $j++) {
                array_push($report_data, $report[$j]);
            }
        }

        $building_data = array();
        foreach($report_data as $report) {
            foreach($report->get_Cencaps as $cencaps) {
                $flag = 0;
                for($i=0; $i<count($building_data); $i++) {
                    if($building_data[$i] == $cencaps->get_Building->name) {
                        $flag =1;
                        break;
                    }
                }

                if($flag == 0) {
                    array_push($building_data, $cencaps->get_Building->name);
                }
            }
        }
        $building_db_data = Buildings::where('building_company_id', $user_data->company_id)->get();
        $order_array = [];
        for($i=0; $i<sizeof($building_data); $i++){
            foreach($building_db_data as $temp){
                if($temp['name'] == $building_data[$i]){
                    array_push($order_array, $temp['id']);
                }
            }
        }
        for($i=0; $i<sizeof($order_array); $i++){
            for($j=0; $j<sizeof($order_array); $j++){
                if($order_array[$i] < $order_array[$j]){
                    $temp = $order_array[$i];
                    $order_array[$i] = $order_array[$j];
                    $order_array[$j] = $temp;

                    $temp = $building_data[$i];
                    $building_data[$i] = $building_data[$j];
                    $building_data[$j] = $temp;
                }
            }
        }

        return view('company_trendreports', array(
            'building_data' => $building_data,
            'report_data' => $report_data,
            'period_data' => $period_data,
            'user_data' => $user_data,
        ));
    }

    public function community_view_add(Request $request) {
        if(!empty($request->date)) {

            $date = $request->date;
            $period_data = Periods::where('id', '<=', $date)
                                ->where('id', '>=', $date)
                                ->first();
            // $period_data = Periods::where('starting', '<=', $date)
            //                     ->where('ending', '>=', $date)
            //                     ->first();

            $period_id = $period_data->id;
            $community_id = $request->community_id;

            $report_data = Reports::where('period_id', $period_id)
                                ->where('community_id', $community_id)
                                ->where('report_company_id', auth()->user()->company_id)
                                ->first();
        }
        else {
            $period_id = $request->period_id;
            $community_id = $request->community_id;
            $period_data = Periods::find($period_id);
            $report_data = array();
        }

        $user_data = auth()->user();
        $community_data = Communities::where('community_company_id', auth()->user()->company_id)
        ->where('name', '<>', 'Administration')
        ->where("name", "<>", "Grass Valley")
        ->get();

        $building_data = Buildings::where('building_company_id', $user_data->company_id)->get();

        $perioditems = Periods::orderBy('id', 'desc')->get();

        return view('addaction', array(
            'period_id' => $period_id,
            'community_id' => $community_id,
            'community_data' => $community_data,
            'period_data' => $period_data,
            'user_data' => $user_data,
            'building_data' => $building_data,
            'report_data' => $report_data,
            'periodItems' => $perioditems,
        ));
    }

    public function community_view_edit(Request $request) {
        $community_data = Communities::where('community_company_id', auth()->user()->company_id)
        ->where('name', '<>', 'Administration')
        ->where("name", "<>", "Grass Valley")
        ->get();
        $user_data = auth()->user();
        $building_data = Buildings::where('building_company_id', $user_data->company_id)->get();

        if(!empty($request->date)) {

            $date = $request->date;
            $period_data = Periods::where('id', '<=', $date)
                                ->where('id', '>=', $date)
                                ->first();

            // $period_data = Periods::where('starting', '<=', $date)
            //                     ->where('ending', '>=', $date)
            //                     ->first();

            $period_id = $period_data->id;
            $community_id = $request->community_id;

            $report_data = Reports::where('period_id', $period_id)
                                ->where('community_id', $community_id)
                                ->where('report_company_id', auth()->user()->company_id)
                                ->first();

            if(!empty($report_data)) {
                $report_id = $report_data->id;
                $whatedit = $report_data->whatedit;

                $cencaps_data = Cencaps::where('report_id', $report_id)->where('cencaps_company_id', $user_data->company_id)->get();
                $inquiry_data = Inquiries::where('report_id', $report_id)->where('inquiry_company_id', $user_data->company_id)->get();
                $moveout_data = Moveouts::where('report_id', $report_id)->where('moveout_company_id', $user_data->company_id)->where('description', '<>', 'M/O NOTICE')->get();

                $mo_notice = Moveouts::where('report_id', $report_id)->where('moveout_company_id', $user_data->company_id)->where('description', 'M/O NOTICE')->first();
            }
            else {
                $report_id = "";
                $whatedit = "";
                $cencaps_data = array();
                $inquiry_data = array();
                $moveout_data = array();

                $mo_notice = "";
            }

        }
        else {
            $report_id = $request->report_id;
            $report_data = Reports::find($report_id);
            $community_id = $report_data->community_id;
            $whatedit = $report_data->whatedit;
            $period_id = $report_data->period_id;
            $period_data = Periods::find($period_id);

            $cencaps_data = Cencaps::where('report_id', $report_id)->where('cencaps_company_id', $user_data->company_id)->get();
            $inquiry_data = Inquiries::where('report_id', $report_id)->where('inquiry_company_id', $user_data->company_id)->get();
            $moveout_data = Moveouts::where('report_id', $report_id)->where('moveout_company_id', $user_data->company_id)->where('description', '<>', 'M/O NOTICE')->get();

            $mo_notice = Moveouts::where('report_id', $report_id)->where('moveout_company_id', $user_data->company_id)->where('description', 'M/O NOTICE')->first();
        }

        if(!empty($mo_notice)) {
            $mo_notice = $mo_notice->number;
        }
        else {
            $mo_notice = "";
        }

        // dd($cencaps_data);
        $perioditems = Periods::orderBy('id', 'desc')->get();

        return view('editaction', array(
            'report_id' => $report_id,
            'period_id' => $period_id,
            'community_id' => $community_id,
            'community_data' => $community_data,
            'period_data' => $period_data,
            'user_data' => $user_data,
            'building_data' => $building_data,
            'cencaps_data' => $cencaps_data,
            'inquiry_data' => $inquiry_data,
            'moveout_data' => $moveout_data,
            'mo_notice' => $mo_notice,
            'report_data' => $report_data,
            'whatedit' => $whatedit,
            'periodItems' => $perioditems,

        ));
    }

    public function add_report(Request $request){

        // $request->validate([
        //     'cencaps_num' => [
        //         'required',
        //         function ($attribute, $value, $fail) {
        //             if ($value == 0) {
        //                 dd($value);
        //                 $fail('OCC items is empty.');
        //             }
        //         },
        //     ],
        //     'inquiries_num' => [
        //         'required',
        //         function ($attribute, $value, $fail) {
        //             if ($value == 0) {
        //                 $fail('Inquiries items is empty.');
        //             }
        //         },
        //     ],
        //     'moveouts_num' => [
        //         'required',
        //         function ($attribute, $value, $fail) {
        //             if ($value == 0) {
        //                 $fail('Move items is empty.');
        //             }
        //         },
        //     ],

        // ]);

        $company_id = auth()->user()->company_id;

        if($company_id == 1) {
            $validate_data = array(
                'unqualified' => 'required | integer',
                'tours' => 'required | integer',
                'deposits' => 'required | integer',
                'wtd_movein' => 'required | integer',
                'wtd_moveout' => 'required | integer',
                'ytd_movein' => 'required | integer',
                'total_moveout' => 'required | integer',
                'prior_ye_occ' => 'required | integer'
            );

            $cencaps_count = $request->cencaps_num;

            for($i=1; $i<=$cencaps_count; $i++) {

                $temp = array(
                    'building_name_'.$i => 'required | integer',
                    'census_'.$i => 'required | integer',
                    'capacity_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $inquiry_count = $request->inquiries_num;

            for($i=1; $i<=$inquiry_count; $i++) {
                $temp = array(
                    'inquiry_type_'.$i => 'required',
                    'inquiry_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $moveout_count = $request->moveouts_num;

            for($i=1; $i<=$moveout_count; $i++) {
                $temp = array(
                    'move_type_'.$i => 'required',
                    'move_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $request->validate($validate_data);

            $saveData = array(
                'community_id' => $request->community_id,
                'period_id' => $request->period_id,
                'unqualified' => $request->unqualified,
                'tours' => $request->tours,
                'deposits' => $request->deposits,
                'wtd_movein' => $request->wtd_movein,
                'wtd_moveout' => $request->wtd_moveout,
                'ytd_movein' => $request->ytd_movein,
                'ytd_moveout' => $request->ytd_moveout,
                'total_moveout' => $request->total_moveout,
                'prior_ye_occ' => $request->prior_ye_occ,
                'report_user' => auth()->user()->id,
                'whatedit' => '',
                'edit_time' => date("Y-m-d"),
                'report_company_id' => $company_id
            );

            if(Reports::create($saveData)) {

                $rId = Reports::orderBy('id', 'desc')->get();
                $rId = $rId[0]->id;

                for($i=1; $i<=$cencaps_count; $i++) {
                    Cencaps::create(array(
                        'report_id' => $rId,
                        'building_id' => $request->input('building_name_'.$i),
                        'census' => $request->input('census_'.$i),
                        'capacity' => $request->input('capacity_'.$i),
                        'total_resident' => 0,
                        'cencaps_company_id' => $company_id
                    ));
                }

                for($i=1; $i<=$inquiry_count; $i++) {
                    Inquiries::create(array(
                        'report_id' => $rId,
                        'description' => $request->input('inquiry_type_'.$i),
                        'number' => $request->input('inquiry_'.$i),
                        'inquiry_company_id' => $company_id
                    ));
                }

                for($i=1; $i<=$moveout_count; $i++) {
                    Moveouts::create(array(
                        'report_id' => $rId,
                        'description' => $request->input('move_type_'.$i),
                        'number' => $request->input('move_'.$i),
                        'moveout_company_id' => $company_id
                    ));
                }



                // $period_id = $request->period_id;

                // $new_period = Periods::orderBy('id', 'desc')->first();

                // $ending = $new_period->ending;
                // $ending_date = strtotime($ending);

                // $new_start_date = strtotime("+1 day", $ending_date);
                // $new_end_date = strtotime("+7 day", $ending_date);

                // $new_start_date_value = date_format("Y-m-d",$new_start_date);
                // $new_start_date_caption = date_format("M d",$new_start_date);

                // $new_end_date_value = date_format("Y-m-d",$new_end_date);
                // $new_end_date_caption = date_format("M d",$new_end_date);

                // Periods::create(array(
                //     'starting' => $new_start_date_value,
                //     'ending' => $new_end_date_value,
                //     'caption' => $new_start_date_caption .' - '. $new_end_date_caption
                // ));

                $request->session()->flash('alert', 'Created Successful');
                return redirect()->route('home');
            }
        }
        else {
            $validate_data = array(
                'wtd_movein' => 'required | integer',
                'wtd_moveout' => 'required | integer',
                'total_moveout' => 'required | integer',
                'prior_ye_occ' => 'required | integer'
            );

            $cencaps_count = $request->cencaps_num;

            for($i=1; $i<=$cencaps_count; $i++) {

                $temp = array(
                    'building_name_'.$i => 'required | integer',
                    'census_'.$i => 'required | integer',
                    'capacity_'.$i => 'required | integer',
                    'total_resident_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $inquiry_count = $request->inquiries_num;

            for($i=1; $i<=$inquiry_count; $i++) {
                $temp = array(
                    'inquiry_type_'.$i => 'required',
                    'inquiry_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $moveout_count = $request->moveouts_num;

            for($i=1; $i<=$moveout_count; $i++) {
                $temp = array(
                    'move_type_'.$i => 'required',
                    'move_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $request->validate($validate_data);

            $saveData = array(
                'community_id' => $request->community_id,
                'period_id' => $request->period_id,
                'unqualified' => '0',
                'tours' => '0',
                'deposits' => '0',
                'wtd_movein' => $request->wtd_movein,
                'wtd_moveout' => $request->wtd_moveout,
                'ytd_movein' => '0',
                'ytd_moveout' => '0',
                'total_moveout' => $request->total_moveout,
                'prior_ye_occ' => $request->prior_ye_occ,
                'report_user' => auth()->user()->id,
                'whatedit' => '',
                'edit_time' => date("Y-m-d"),
                'report_company_id' => $company_id
            );

            if(Reports::create($saveData)) {

                $rId = Reports::orderBy('id', 'desc')->get();
                $rId = $rId[0]->id;

                $cencaps_count = $request->cencaps_num;

                for($i=1; $i<=$cencaps_count; $i++) {
                    Cencaps::create(array(
                        'report_id' => $rId,
                        'building_id' => $request->input('building_name_'.$i),
                        'census' => $request->input('census_'.$i),
                        'capacity' => $request->input('capacity_'.$i),
                        'total_resident' => $request->input('total_resident_'.$i),
                        'cencaps_company_id' => $company_id
                    ));
                }

                $inquiry_count = $request->inquiries_num;

                for($i=1; $i<=$inquiry_count; $i++) {
                    Inquiries::create(array(
                        'report_id' => $rId,
                        'description' => $request->input('inquiry_type_'.$i),
                        'number' => $request->input('inquiry_'.$i),
                        'inquiry_company_id' => $company_id
                    ));
                }

                $moveout_count = $request->moveouts_num;

                for($i=1; $i<=$moveout_count; $i++) {
                    Moveouts::create(array(
                        'report_id' => $rId,
                        'description' => $request->input('move_type_'.$i),
                        'number' => $request->input('move_'.$i),
                        'moveout_company_id' => $company_id
                    ));
                }

                Moveouts::create(array(
                    'report_id' => $rId,
                    'description' => 'M/O NOTICE',
                    'number' => $request->input('mo_notice'),
                    'moveout_company_id' => $company_id
                ));

                // $period_id = $request->period_id;

                // $new_period = Periods::orderBy('id', 'desc')->first();

                // $ending = $new_period->ending;
                // $ending_date = strtotime($ending);

                // $new_start_date = strtotime("+1 day", $ending_date);
                // $new_end_date = strtotime("+7 day", $ending_date);

                // $new_start_date_value = date_format("Y-m-d",$new_start_date);
                // $new_start_date_caption = date_format("M d",$new_start_date);

                // $new_end_date_value = date_format("Y-m-d",$new_end_date);
                // $new_end_date_caption = date_format("M d",$new_end_date);

                // Periods::create(array(
                //     'starting' => $new_start_date_value,
                //     'ending' => $new_end_date_value,
                //     'caption' => $new_start_date_caption .' - '. $new_end_date_caption
                // ));

                $request->session()->flash('alert', 'Created Successful');
                return redirect()->route('home');
            }
        }
    }

    public function update_report(Request $request){
        // $request->validate([
        //     'cencaps_num' => [
        //         'required',
        //         function ($attribute, $value, $fail) {

        //             if ($value == 0) {
        //                 $fail('OCC items is empty.');
        //             }
        //         },
        //     ],
        //     'inquiries_num' => [
        //         'required',
        //         function ($attribute, $value, $fail) {
        //             if ($value == 0) {
        //                 $fail('Inquiries items is empty.');
        //             }
        //         },
        //     ],
        //     'moveouts_num' => [
        //         'required',
        //         function ($attribute, $value, $fail) {
        //             if ($value == 0) {
        //                 $fail('Move items is empty.');
        //             }
        //         },
        //     ]
        // ]);

        $company_id = auth()->user()->company_id;
        $report_id = $request->report_id;

        if($company_id == 1) {

            $validate_data = array(
                'unqualified' => 'required | integer',
                'tours' => 'required | integer',
                'deposits' => 'required | integer',
                'wtd_movein' => 'required | integer',
                'wtd_moveout' => 'required | integer',
                'ytd_movein' => 'required | integer',
                'total_moveout' => 'required | integer',
                'prior_ye_occ' => 'required | integer'
            );

            $cencaps_count = $request->cencaps_num;

            for($i=1; $i<=$cencaps_count; $i++) {

                $temp = array(
                    'building_name_'.$i => 'required | integer',
                    'census_'.$i => 'required | integer',
                    'capacity_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $inquiry_count = $request->inquiries_num;

            for($i=1; $i<=$inquiry_count; $i++) {
                $temp = array(
                    'inquiry_type_'.$i => 'required',
                    'inquiry_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $moveout_count = $request->moveouts_num;

            for($i=1; $i<=$moveout_count; $i++) {
                $temp = array(
                    'move_type_'.$i => 'required',
                    'move_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $request->validate($validate_data);

            $whatedit = $request->whatedit;
            $whatedit_id_arr = explode(',', $whatedit);

            for($i=0; $i<count($whatedit_id_arr); $i++) {

                $min_id = $i;
                $temp = 0;
                for($j=$i+1; $j<count($whatedit_id_arr); $j++) {
                    if($whatedit_id_arr[$j] < $whatedit_id_arr[$min_id]) {
                        $min_id = $j;
                    }
                }

                if($whatedit_id_arr[$i] > $whatedit_id_arr[$min_id]) {
                    $temp = $whatedit_id_arr[$i];
                    $whatedit_id_arr[$i]  = $whatedit_id_arr[$min_id];
                    $whatedit_id_arr[$min_id] = $temp;
                }
            }
            $what_edit ='';

            for($i=0; $i<count($whatedit_id_arr); $i++) {
                if($i == 0) {
                    if($whatedit_id_arr[$i] == 1) {
                        $what_edit .= 'Census and Capacity';
                    }
                    else if($whatedit_id_arr[$i] == 2) {
                        $what_edit .= 'Inquiry';
                    }
                    else if($whatedit_id_arr[$i] == 3) {
                        if(auth()->user()->company_id == 1) {
                            $what_edit .= 'Moveouts';
                        }
                        else{
                            $what_edit .= 'Deposits';
                        }
                    }
                    else if($whatedit_id_arr[$i] == 4) {
                        $what_edit .= 'Statistics';
                    }
                    else if($whatedit_id_arr[$i] == 5) {
                        $what_edit .= 'Move In/Out';
                    }
                    else if($whatedit_id_arr[$i] == 6){
                        $what_edit .= 'Other';
                    }
                }
                else {
                    if($whatedit_id_arr[$i] == 1) {
                        $what_edit .= ', ';
                        $what_edit .= 'Census and Capacity';
                    }
                    else if($whatedit_id_arr[$i] == 2) {
                        $what_edit .= ', ';
                        $what_edit .= 'Inquiry';
                    }
                    else if($whatedit_id_arr[$i] == 3) {
                        $what_edit .= ', ';
                        if(auth()->user()->company_id == 1) {
                            $what_edit .= 'Moveouts';
                        }
                        else{
                            $what_edit .= 'Deposits';
                        }
                    }
                    else if($whatedit_id_arr[$i] == 4) {
                        $what_edit .= ', ';
                        $what_edit .= 'Statistics';
                    }
                    else if($whatedit_id_arr[$i] == 5) {
                        $what_edit .= ', ';
                        $what_edit .= 'Move In/Out';
                    }
                    else if($whatedit_id_arr[$i] == 6){
                        $what_edit .= ', ';
                        $what_edit .= 'Other';
                    }
                }
            }



            $saveData = array(
                'community_id' => $request->community_id,
                'period_id' => $request->period_id,
                'unqualified' => $request->unqualified,
                'tours' => $request->tours,
                'deposits' => $request->deposits,
                'wtd_movein' => $request->wtd_movein,
                'wtd_moveout' => $request->wtd_moveout,
                'ytd_movein' => $request->ytd_movein,
                'ytd_moveout' => $request->ytd_moveout,
                'total_moveout' => $request->total_moveout,
                'prior_ye_occ' => $request->prior_ye_occ,
                'report_user' => auth()->user()->id,
                'what_edit' => $what_edit,
                'edit_time' => date("Y-m-d"),
                'report_company_id' => $company_id
            );



            $report = Reports::find($report_id);

            if($report->update($saveData)) {

                Cencaps::where('report_id', $report_id)->delete();
                $cencaps_count = $request->cencaps_num;

                for($i=1; $i<=$cencaps_count; $i++) {
                    Cencaps::create(array(
                        'report_id' => $report_id,
                        'building_id' => $request->input('building_name_'.$i),
                        'census' => $request->input('census_'.$i),
                        'capacity' => $request->input('capacity_'.$i),
                        'total_resident' => 0,
                        'cencaps_company_id' => $company_id
                    ));
                }


                Inquiries::where('report_id', $report_id)->delete();
                $inquiry_count = $request->inquiries_num;

                for($i=1; $i<=$inquiry_count; $i++) {
                    Inquiries::create(array(
                        'report_id' => $report_id,
                        'description' => $request->input('inquiry_type_'.$i),
                        'number' => $request->input('inquiry_'.$i),
                        'inquiry_company_id' => $company_id
                    ));
                }

                Moveouts::where('report_id', $report_id)->delete();
                $moveout_count = $request->moveouts_num;

                for($i=1; $i<=$moveout_count; $i++) {
                    Moveouts::create(array(
                        'report_id' => $report_id,
                        'description' => $request->input('move_type_'.$i),
                        'number' => $request->input('move_'.$i),
                        'moveout_company_id' => $company_id
                    ));
                }

                $request->session()->flash('alert', 'Updated Successful');
                return redirect()->route('home');
            }
        }
        else {
            $validate_data = array(
                'wtd_movein' => 'required | integer',
                'wtd_moveout' => 'required | integer',
                'total_moveout' => 'required | integer',
                'prior_ye_occ' => 'required | integer'
            );

            $cencaps_count = $request->cencaps_num;

            for($i=1; $i<=$cencaps_count; $i++) {

                $temp = array(
                    'building_name_'.$i => 'required | integer',
                    'census_'.$i => 'required | integer',
                    'capacity_'.$i => 'required | integer',
                    'total_resident_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $inquiry_count = $request->inquiries_num;

            for($i=1; $i<=$inquiry_count; $i++) {
                $temp = array(
                    'inquiry_type_'.$i => 'required',
                    'inquiry_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $moveout_count = $request->moveouts_num;

            for($i=1; $i<=$moveout_count; $i++) {
                $temp = array(
                    'move_type_'.$i => 'required',
                    'move_'.$i => 'required | integer'
                );

                $validate_data = array_merge($validate_data, $temp);
            }

            $request->validate($validate_data);

            $whatedit = $request->whatedit;
            $whatedit_id_arr = explode(',', $whatedit);

            for($i=0; $i<count($whatedit_id_arr); $i++) {

                $min_id = $i;
                $temp = 0;
                for($j=$i+1; $j<count($whatedit_id_arr); $j++) {
                    if($whatedit_id_arr[$j] < $whatedit_id_arr[$min_id]) {
                        $min_id = $j;
                    }
                }

                if($whatedit_id_arr[$i] > $whatedit_id_arr[$min_id]) {
                    $temp = $whatedit_id_arr[$i];
                    $whatedit_id_arr[$i]  = $whatedit_id_arr[$min_id];
                    $whatedit_id_arr[$min_id] = $temp;
                }
            }
            $what_edit ='';

            for($i=0; $i<count($whatedit_id_arr); $i++) {
                if($i == 0) {
                    if($whatedit_id_arr[$i] == 1) {
                        $what_edit .= 'Census and Capacity';
                    }
                    else if($whatedit_id_arr[$i] == 2) {
                        $what_edit .= 'Inquiry';
                    }
                    else if($whatedit_id_arr[$i] == 3) {
                        if(auth()->user()->company_id == 1) {
                            $what_edit .= 'Moveouts';
                        }
                        else{
                            $what_edit .= 'Deposits';
                        }
                    }
                    else if($whatedit_id_arr[$i] == 4) {
                        $what_edit .= 'Statistics';
                    }
                    else if($whatedit_id_arr[$i] == 5) {
                        $what_edit .= 'Move In/Out';
                    }
                    else if($whatedit_id_arr[$i] == 6){
                        $what_edit .= 'Other';
                    }
                }
                else {
                    if($whatedit_id_arr[$i] == 1) {
                        $what_edit .= ', ';
                        $what_edit .= 'Census and Capacity';
                    }
                    else if($whatedit_id_arr[$i] == 2) {
                        $what_edit .= ', ';
                        $what_edit .= 'Inquiry';
                    }
                    else if($whatedit_id_arr[$i] == 3) {
                        $what_edit .= ', ';
                        if(auth()->user()->company_id == 1) {
                            $what_edit .= 'Moveouts';
                        }
                        else{
                            $what_edit .= 'Deposits';
                        }
                    }
                    else if($whatedit_id_arr[$i] == 4) {
                        $what_edit .= ', ';
                        $what_edit .= 'Statistics';
                    }
                    else if($whatedit_id_arr[$i] == 5) {
                        $what_edit .= ', ';
                        $what_edit .= 'Move In/Out';
                    }
                    else if($whatedit_id_arr[$i] == 6){
                        $what_edit .= ', ';
                        $what_edit .= 'Other';
                    }
                }
            }

            $saveData = array(
                'community_id' => $request->community_id,
                'period_id' => $request->period_id,
                'unqualified' => '0',
                'tours' => '0',
                'deposits' => '0',
                'wtd_movein' => $request->wtd_movein,
                'wtd_moveout' => $request->wtd_moveout,
                'ytd_movein' => '0',
                'ytd_moveout' => '0',
                'total_moveout' => $request->total_moveout,
                'prior_ye_occ' => $request->prior_ye_occ,
                'report_user' => auth()->user()->id,
                'what_edit' => $what_edit,
                'edit_time' => date("Y-m-d"),
                'report_company_id' => $company_id
            );

            $report = Reports::find($report_id);

            if($report->update($saveData)) {

                Cencaps::where('report_id', $report_id)->delete();
                $cencaps_count = $request->cencaps_num;

                for($i=1; $i<=$cencaps_count; $i++) {
                    Cencaps::create(array(
                        'report_id' => $report_id,
                        'building_id' => $request->input('building_name_'.$i),
                        'census' => $request->input('census_'.$i),
                        'capacity' => $request->input('capacity_'.$i),
                        'total_resident' => $request->input('total_resident_'.$i),
                        'cencaps_company_id' => $company_id
                    ));
                }


                Inquiries::where('report_id', $report_id)->delete();
                $inquiry_count = $request->inquiries_num;

                for($i=1; $i<=$inquiry_count; $i++) {
                    Inquiries::create(array(
                        'report_id' => $report_id,
                        'description' => $request->input('inquiry_type_'.$i),
                        'number' => $request->input('inquiry_'.$i),
                        'inquiry_company_id' => $company_id
                    ));
                }

                Moveouts::where('report_id', $report_id)->delete();
                $moveout_count = $request->moveouts_num;

                for($i=1; $i<=$moveout_count; $i++) {
                    Moveouts::create(array(
                        'report_id' => $report_id,
                        'description' => $request->input('move_type_'.$i),
                        'number' => $request->input('move_'.$i),
                        'moveout_company_id' => $company_id
                    ));
                }

                Moveouts::create(array(
                    'report_id' => $report_id,
                    'description' => 'M/O NOTICE',
                    'number' => $request->input('mo_notice'),
                    'moveout_company_id' => $company_id
                ));

                $request->session()->flash('alert', 'Created Successful');
                return redirect()->route('home');
            }
        }
    }

}
