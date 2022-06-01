<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Cencaps;
use DB;
use APP\Console\Log;
class CencapController extends Controller
{

    static public function insert_row($data){
        $sql = "insert into cencaps ( `report_id`, `building_id`, `census`, `capacity`, `total_resident`, `cencaps_company_id` ) VALUES ('".
        $data['report_id']."', '".
        $data['building_id']."', '".
        $data['census']."', '".
        $data['capacity']."', '".
        $data['total_resident']."', '".
        $data['cencaps_company_id']."');";

        DB::insert($sql);
        return true;
    }
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
    public function create()
    {
        //
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
