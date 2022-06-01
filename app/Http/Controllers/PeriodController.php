<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\model\Periods;

class PeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    static public function create()
    {
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
        }

        $inital_date = Periods::orderBy('id', 'desc')->first();

        return $inital_date['id'];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
