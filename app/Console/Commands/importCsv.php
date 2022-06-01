<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CencapController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\MoveoutController;
use App\Console\Log;

use Illuminate\Support\Facades\Config;

use Exception;

class controlException extends Exception
{
}

class importCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importCsv:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily import yardi csv files.';

    public static $rest_api = [
        'portal_id' => '172',
        'list_id' => '122958',
        'individual_id' => '6896302',
        'ocp_key_first' => 'aef260265798416ea85eebadcbbd6fb9',
        'ocp_key_second' => 'aef260265798416ea85eebadcbbd6fb9',
        //'ocp_key_first' => '28b5b8e407ef463289219956bac6a6f9',
        //'ocp_key_second' => '4348dc1c32f749aebf3e6c47ca40d6f7',
        'restapi_domain' => 'https://api2.enquiresolutions.com',
        'search_api' => 'https://api2.enquiresolutions.com/v3/Search/?PortalId=',
        'case_api' => 'https://api2.enquiresolutions.com/v3/Case/?PortalId=',
        'activity_api' => 'https://api2.enquiresolutions.com/v3/Activity?PortalId=',
        'call_api' => 'http://perfilages.com/hgreport/create.php?call='
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function tst($_last_friday, $_friday, $size, $page){
        $urlCall = self::$rest_api['activity_api'] . self::$rest_api['portal_id'] . "&PageSize={$size}&DateUpdatedStart={$_last_friday}&DateUpdatedEnd={$_friday}&PageNumber={$page}";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlCall,
            CURLOPT_TIMEOUT => 600,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Content-Type: application/json; charset=utf-8",
                "Ocp-Apim-Subscription-Key: " . self::$rest_api['ocp_key_first']
            ),
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $httpCode = curl_getinfo($curl , CURLINFO_HTTP_CODE);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return json_decode($response, true);
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //$this->create_csv();
        $this->read_csv();
    }

    public function read_csv() {

        Log::info(base_path());
        $working_directory = base_path("public/yardi/");
        $processed_directory = base_path("public/processed/");
        $import_files = glob($working_directory . "*.csv");
        Log::info("step 1");
        if ($import_files) {
          foreach ($import_files as $file) {
            Log::info("step 2");
            $message_log = array();
            $message_log[] = 'Beginning Processing.';
            $warning = false;
            $error = false;

            $input_file_location = $working_directory;

            $_period_id = 0;
            if (isset($_GET['period_id']) && (int) $_GET['period_id'] > 0) {
              $_period_id = $_GET['period_id'];
            }

            $_new_report = 0;
            if (isset($_GET['new_report']) && (int) $_GET['new_report'] > 0) {
              $_new_report = 1;
            }
            Log::info("step 3");
            try {
              Log::info("step 4");
              if (!file_exists($file)) {
                throw new controlException('File not found: ' . $file);
              }
              if (!($inputfile = fopen($file, 'r'))) {
                throw new controlException('File could not be opened. ' . $file);
              }
              Log::info("step 5");
              $message_log[] = $file . ' found and open for processing.';

              // try{
              //     $result = report::lock_all_reports();
              // } catch(Exception $e ){
              //     throw new controlException('Unable to lock existing reports. ' . $e->getMessage());
              // }
              // if(!$result and $result !== 0){
              //     throw new controlException('Failure when locking reports, no exception.');
              // }
              // $message_log[] = 'Previous reports locked.';

              try {
                Log::info("step 6");
                if ($_period_id > 0) {
                  $period_id = $_period_id;
                } else {
                  $period_id = PeriodController::create();
                }
              } catch (Exception $e) {
                throw new controlException('Unable to create a new period. ' . $e->getMessage());
              }
              Log::info("step 7");
              $message_log[] = 'Period ' . $period_id . ' created.';

              try {
                /*
                            if ($_period_id = 0) {
                                report::generate_new_reports($period_id);
                            } else {
                                report::generate_new_reports();
                            }
                            */
                if ($_new_report == 0) {
                  if ($_period_id > 0) {
                    ReportController::create($period_id);
                  } else {
                    Log::info("step 8");
                    ReportController::create();
                  }
                }
              } catch (Exception $e) {
                throw new controlException('Unable to create reports. ' . $e->getMessage());
              }
              Log::info("step 9");
              $message_log[] = 'New reports created.';

              $message_log[] = 'Beginning File Process.';

              //get the headings in order
              //This section is not actually used for anything. It was previously used in case the order of the columns changed.
              $fileheading = trim(fgets($inputfile));
              $headings = explode(',', $fileheading);
              Log::info("step 10");
              $heading_list = array('Property Name', 'Report Section', 'Data Type', 'Data Count', 'Care Level');

              //read the rest of the file
              $communities = array();
              $processed_lines = 0;
              Log::info("step 11");
              while (!feof($inputfile)) {
                $processed_lines++;
                $record = fgets($inputfile);
                if ($record == false)
                  continue;
                $parsed = explode(',', $record);

                $community = $parsed[0];
                $section = $parsed[1];
                $type = $parsed[2];
                $count = intval($parsed[3]);
                $building = trim($parsed[4]);

                if ($count > 0) {

                  if ($section == 'Capacity') {
                    $communities[$community][$section][$building] = $count;
                  } elseif ($section == 'Census') {
                    $communities[$community][$section][$building] = $count;
                  } elseif ($section == 'Statistics') {
                    if (isset($communities[$community][$section][$type])) {
                      $communities[$community][$section][$type] += $count;
                    } else {
                      $communities[$community][$section][$type] = $count;
                    }
                  } elseif ($section == 'Inquiries') {
                    if (empty($type)) {
                      $type = 'Empty';
                    }

                    if (isset($communities[$community][$section][$type])) {
                      $communities[$community][$section][$type] += $count;
                    } else {
                      $communities[$community][$section][$type] = $count;
                    }
                  } elseif ($section == 'MoveOuts') {
                    if (empty($type)) {
                      $type = 'Empty';
                    }

                    if (isset($communities[$community][$section][$type])) {
                      $communities[$community][$section][$type] += $count;
                    } else {
                      $communities[$community][$section][$type] = $count;
                    }
                  }
                }
              }
              Log::info("step 12");
              $message_log[] = 'Processed ' . $processed_lines . ' records.';

              fclose($inputfile);

              $message_log[] = 'Closed File.';

              $message_log[] = 'Processing community data.';
              //get building ID map
              $building_map = BuildingController::get_map();

              //process each community
              $community_map = CommunityController::get_map();
              Log::info("step 13");

              foreach (array_keys($communities) as $community) {

                $message_log[] = 'Processing ' . $community;

                Log::info('Processing ' . $community);

                if (isset($community_map[$community])) {
                  $community_id = $community_map[$community];

                  $report = new ReportController();

                  $result = $report->get_report($period_id, $community_id, 1);
                  if ($result === false) {

                    $warning = true;
                    $message_log[] = 'Warning: Unable to retrieve a report for ' . $community . '.';

                    Log::info('Warning: Unable to retrieve a report for ' . $community . '.');
                  } else {
                    $result = (array) $result;
                    $report_id = $result['id'];
                    //cencap
                    $censusdata = $communities[$community]['Census'];
                    $capacitydata = $communities[$community]['Capacity'];
                    if (is_array($capacitydata)) {
                      foreach (array_keys($capacitydata) as $building_name) {
                        Log::info("$building_name");
                        $building_id = $building_map[$building_name];
                        if (isset($censusdata[$building_name]))
                          $census = $censusdata[$building_name];
                        else
                          $census = 0;
                        if (isset($capacitydata[$building_name]))
                          $capacity = $capacitydata[$building_name];
                        else
                          $capacity = 0;

                        $data = array(
                          'report_id' => $report_id,
                          'building_id' => $building_id,
                          'census' => $census,
                          'capacity' => $capacity,
                          'total_resident' => 0,
                          'cencaps_company_id' => 1
                        );

                        CencapController::insert_row($data);
                      }
                    }
                    Log::info("debug7");

                    //inquiry
                    if (isset($communities[$community]['Inquiries'])) {
                      $inquirydata = $communities[$community]['Inquiries'];
                      if (is_array($inquirydata)) {
                        foreach ($inquirydata as $description => $number) {
                          $data = array(
                            'report_id' => $report_id,
                            'description' => $description,
                            'number' => $number,
                            'inquiry_company_id' => 1
                          );
                          InquiryController::insert_row($data);
                        }
                      }
                    }
                    Log::info("debug8");


                    //moveout
                    if (isset($communities[$community]['MoveOuts'])) {
                      Log::info("move1");
                      $moveoutdata = $communities[$community]['MoveOuts'];
                      Log::info("move2");
                      if (is_array($moveoutdata) && count($moveoutdata) > 0) {
                        Log::info("move3");
                        foreach ($moveoutdata as $description => $number) {

                          Log::info("move4");
                          Log::info($report_id);
                          Log::info($description);
                          Log::info($number);
                          $data = array(
                            'report_id' => $report_id,
                            'description' => $description,
                            'number' => $number,
                            'moveout_company_id' => 1
                          );
                          Log::info("move5");
                          MoveoutController::insert_row($data);
                          Log::info("move6");
                        }
                      }
                    }

                    Log::info("debug9");


                    //stats
                    if ($_new_report == 0) {

                      if (isset($communities[$community]['Statistics'])) {
                        $statdata = $communities[$community]['Statistics'];

                        if (isset($statdata['WTD Deposits']))
                          $result['deposits'] = $statdata['WTD Deposits'];

                        if (isset($statdata['WTD Tours']))
                          $result['tours'] = $statdata['WTD Tours'];

                        if (isset($statdata['WTD Not Qual Inq']))
                          $result['unqualified'] = $statdata['WTD Not Qual Inq'];

                        if (isset($statdata['WTD Move-Ins']))
                          $result['wtd_movein'] = $statdata['WTD Move-Ins'];

                        if (isset($statdata['WTD Move-Outs']))
                          $result['wtd_moveout'] = $statdata['WTD Move-Outs'];

                        if (isset($statdata['YTD Move-Ins']))
                          $result['ytd_movein'] = $statdata['YTD Move-Ins'];

                        if (isset($statdata['YTD Move-Outs']))
                          $result['ytd_moveout'] = $statdata['YTD Move-Outs'];
                      }
                    }
                    //save it all
                    Log::info("debug10");
                    $report->update_cron_report($result);

                    Log::info('Completed ' . $community . ' report.');
                  }
                }
              }
              $message_log[] = 'Report processing complete.';

              Log::info("step 14");

              if (!(rename($file, $processed_directory . time() . "-" . basename($file)))) {
                throw new controlException('File was processed, but unable to move.');
              }
              Log::info("step 15");
              $message_log[] = 'Processed file was archived.';
            } catch (controlException $e) {
              $error = true;
              $message_log[] = 'FAILURE: ' . $e->getMessage();
            } catch (Exception $e) {
              $error = true;
              $message_log[] = 'FAILURE: ' . $e->getMessage();
            }

            Log::info("step 16");
            $sendfrom = 'From: OT@the-tech-portal.com';
            // $emailto = array('daniel@the-tech-portal.com', 'daemon@the-tech-portal.com');
            // $emailto = array('denisbr@gmail.com');

            $body = '';
            if (!$error) {
              Log::info("step 17");
              $subject = 'Yardi file processed';
              if ($warning) {
                $subject .= ' WITH WARNINGS';
              }

              // Log::info($_SERVER['HTTP_HOST']);
              // if(isset($_SERVER['HTTP_HOST'])){
              //     $body = 'http://' . $_SERVER['HTTP_HOST'] . '/company_report.php?period_id=' . urlencode($period_id) . "\n\n";
              // }
              // else{
              //     $body = 'http://occupancytrack.com/company_report.php?period_id=' . urlencode($period_id) . "\n\n";
              // }

              // Log::info($_SERVER['SERVER_NAME']);

              $emailto[] = 'globalhunter727@gmail.com';
            } else {

              Log::info("step 23");
              $subject = 'ERROR PROCESSING YARDI FILE';
            }

            Log::info("step 24");
            $body .= join("\n", $message_log);

            foreach ($emailto as $to) {
              Log::info("step 25");
              mail($to, $subject, $body, $sendfrom);
              Log::info("step 26");
            }
          }
        }

    }

    public function PrintVar($_var){
        echo "<pre>";
        print_r($_var);
        echo "</pre>";
    }
    public function create_csv()
    {
        $create_path = base_path("public/yardi/");
        $create_name = 'zclients.csv';
        $create_path2 = base_path("public/");
        $create_name2 = 'all.csv';

        $countDataTypes = array();
        $dataSource = array();
        $dataSourceLeadStatus = array();
        $dataSourceTourOn = array();
        $dataSourceTourOn2 = array();
        $dataSourceDeposit = array();
        $obj = array();


        $dt = new \DateTime('friday last week');
        $dt2 = new \DateTime('thursday');
        $output['last_week'] = $dt->format('Y-m-d');
        $output['friday_week'] = $dt2->format('Y-m-d');
        $page = 1;
        $resultTotal = 0;
        $result = [];
        do{
            $data = $this->tst($output['last_week'], $output['friday_week'], 100, $page);
            if(!empty($data)){

                foreach($data as $key => $value){
                    if ($value["Community"][0]["Name"] == "Prescott Lakes") {
                        $value["Community"][0]["Name"] = "Prescott";
                    }
                    switch($value['ActivityTypeId']){
                        case 5616:
                            $result['tour'][0] = isset($result['tour'][0]) ? $result['tour'][0] += 1 : 1;
                            if(isset($dataSourceTourOn2[$value["Community"][0]["Name"]])){
                                $dataSourceTourOn2[$value["Community"][0]["Name"]][0] += 1;
                            }else{
                                $dataSourceTourOn2[$value["Community"][0]["Name"]][0] = 1;
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
                            if(isset($dataSourceDeposit[$value["Community"][0]["Name"]])){
                                $dataSourceDeposit[$value["Community"][0]["Name"]][0] += 1;
                            }else{
                                $dataSourceDeposit[$value["Community"][0]["Name"]][0] = 1;
                            }
                            break;
                    }
                }
                $resultTotal = count($data);
                $page++;
            }else{
                $resultTotal = 0;
            }
            sleep(2);

        }while($resultTotal > 0 && $resultTotal <= 100);
        $this->PrintVar($page);
        $this->PrintVar($dataSourceTourOn2);
        $this->PrintVar($dataSourceDeposit);

        // $allList = $this->callCurl(self::$rest_api['activity_api'] . self::$rest_api['portal_id']);
        // if (count($allList) > 0) {
        //     Log::info('Read from ACTIVITY API');
        //     foreach ($allList as $list) {
        //         if(isset($list["DateCreated"]) && strtotime('friday last week') <= strtotime($list["DateCreated"])){
        //             $regex = "/.*[Tt]our*/m";
        //             preg_match_all($regex,$list['ActivityType'],$matches,PREG_SET_ORDER,0);
        //             if (count($matches) > 0) {
        //                 if ($list["Community"][0]["Name"] == "Prescott Lakes") {
        //                     $list["Community"][0]["Name"] = "Prescott";
        //                 }
        //                 if(isset($dataSourceTourOn2[$list["Community"][0]["Name"]])){
        //                     $dataSourceTourOn2[$list["Community"][0]["Name"]][0] += 1;
        //                 }else{
        //                     $dataSourceTourOn2[$list["Community"][0]["Name"]][0] = 1;
        //                 }
        //             }

        //             $regex_deposit = "/.*[Dd]eposit*/m";
        //             preg_match_all($regex_deposit,$list['ActivityType'],$matches_deposit,PREG_SET_ORDER,0);
        //             if (count($matches_deposit) > 0) {
        //                 if ($list["Community"][0]["Name"] == "Prescott Lakes") {
        //                     $list["Community"][0]["Name"] = "Prescott";
        //                 }
        //                 if(isset($dataSourceDeposit[$list["Community"][0]["Name"]])){
        //                     $dataSourceDeposit[$list["Community"][0]["Name"]][0] += 1;
        //                 }else{
        //                     $dataSourceDeposit[$list["Community"][0]["Name"]][0] = 1;
        //                 }
        //             }
        //         }

        //     }
        // }

        $allList = $this->callCurl(self::$rest_api['case_api'] . self::$rest_api['portal_id'] . '&IndividualId=' . self::$rest_api['individual_id'] . '&CaseNumber=DateCompleted');
        if (count($allList) > 0) {
            if (isset($allList["cases"][0])) {
                Log::info('Read from case_api');
                foreach ($allList["cases"][0]["Individuals"] as $_c) {
                    foreach ($_c['activities'] as $c) {
                        if ($c['SaleStage'] == 'Inquiry')  {
                            if(strtotime('friday last week') <= strtotime($c["DateCreated"])){

                                $data = $this->getInfo('Market Source', $_c["properties"]);
                                if (!empty($data)) {
                                    if ($c["Community"][0]["Name"] == "Prescott Lakes") {
                                        $c["Community"][0]["Name"] = "Prescott";
                                    }

                                    if(isset($dataSource[$c["Community"][0]["Name"]][$data])){
                                        $dataSource[$c["Community"][0]["Name"]][$data] += 1;
                                    }else{
                                        $dataSource[$c["Community"][0]["Name"]][$data] = 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $allList = $this->callCurl(self::$rest_api['search_api'] . self::$rest_api['portal_id'] . '&ListId=' . self::$rest_api['list_id'] . '&q=created');
        if (count($allList) > 0 && isset($allList["result"]["individuals"])) {
            Log::info('Read from SEARCH API');
            foreach ($allList["result"]["individuals"] as $c) {
                if(strtotime('friday last week') <= strtotime($c["created"])){

                    // $data = $this->getData('Market Source', $c["properties"]);
                    // Log::info('Market Source');
                    // if($data){
                    //     if ($c["community"][0]["Name"] == "Prescott Lakes") {
                    //         $c["community"][0]["Name"] = "Prescott";
                    //     }
                    //     if(isset($dataSource[$c["community"][0]["Name"]][$data])){
                    //         $dataSource[$c["community"][0]["Name"]][$data] += 1;
                    //     }else{
                    //         $dataSource[$c["community"][0]["Name"]][$data] = 1;
                    //     }
                    // }

                    $data = $this->getInfoUnqualified('Lost Lead Reason', $c["properties"]);
                    if(!empty($data)){

                        if ($c["community"][0]["Name"] == "Prescott Lakes") {
                            $c["community"][0]["Name"] = "Prescott";
                        }
                        if(isset($dataSourceLeadStatus[$c["community"][0]["Name"]][$data])){
                            $dataSourceLeadStatus[$c["community"][0]["Name"]][$data] += 1;
                        }else{
                            $dataSourceLeadStatus[$c["community"][0]["Name"]][$data] = 1;
                        }
                    }

                    // $data =  $this->getData('Tour On', $c["properties"]);
                    // if($data){
                    //     if ($c["community"][0]["Name"] == "Prescott Lakes") {
                    //         $c["community"][0]["Name"] = "Prescott";
                    //     }
                    //     if(isset($dataSourceTourOn2[$c["community"][0]["Name"]][$data])){
                    //         $dataSourceTourOn2[$c["community"][0]["Name"]][$data] += 1;
                    //     }else{
                    //         $dataSourceTourOn2[$c["community"][0]["Name"]][$data] = 1;
                    //     }
                    // }

                    // $data =  $this->getData('Deposit On', $c["properties"]);
                    // if($data){
                    //     if ($c["community"][0]["Name"] == "Prescott Lakes") {
                    //         $c["community"][0]["Name"] = "Prescott";
                    //     }
                    //     if(isset($dataSourceDeposit[$c["community"][0]["Name"]][$data])){
                    //         $dataSourceDeposit[$c["community"][0]["Name"]][$data] += 1;
                    //     }else{
                    //         $dataSourceDeposit[$c["community"][0]["Name"]][$data] = 1;
                    //     }
                    // }
                }
            }
        }
        $this->PrintVar($dataSource);
        $this->PrintVar($dataSourceLeadStatus);
        $this->PrintVar($dataSourceTourOn2);
        $this->PrintVar($dataSourceDeposit);

        if(!empty($dataSource)){
            Log::info("INQ");
            foreach($dataSource as $key => $var){
                foreach ($var as $_k => $_v) {
                    $obj[] = array(
                      'Property' => $key,               # Property da tabela
                      'Report' => "Inquiries",                                # Report Section é fixo Inquiries
                      'CareLeve' => '',//$care,                                    # Community da tabela ID: 41531
                      // 'DataType' => key($var),                                    # Data type da tabela ID: 41622 conhecido como Market Source
                      'DataType' => $_k,                                    # Data type da tabela ID: 41622 conhecido como Market Source
                      // 'DataCount' => $var[key($var)]                                       # Quantidade de data iguais na tabela.
                      'DataCount' => $_v                                       # Quantidade de data iguais na tabela.
                    );
                }
            }
        }

        if(!empty($dataSourceLeadStatus)){
            Log::info("UNQ");
            foreach($dataSourceLeadStatus as $key => $var){
                foreach ($var as $_k => $_v) {
                    $obj[] = array(
                      'Property' => $key,               # Property da tabela
                      'Report' => "Statistics",                                # Report Section é fixo Inquiries
                      'CareLeve' => $_k,//$care,                                    # Community da tabela ID: 41531
                      // 'DataType' => key($var),                                    # Data type da tabela ID: 41622 conhecido como Market Source
                      'DataType' => 'WTD Not Qual Inq',                                    # Data type da tabela ID: 41622 conhecido como Market Source
                      // 'DataCount' => $var[key($var)]                                       # Quantidade de data iguais na tabela.
                      'DataCount' => $_v                                       # Quantidade de data iguais na tabela.
                    );
                }
            }
        }

        if(!empty($dataSourceTourOn2)){
            Log::info("TOUR");
            foreach($dataSourceTourOn2 as $key => $var){
                foreach ($var as $_k => $_v) {
                    $obj[] = array(
                      'Property' => $key,               # Property da tabela
                      'Report' => "Statistics",                                # Report Section é fixo Inquiries
                      'CareLeve' => 'Cottage',//$care,                                    # Community da tabela ID: 41531
                      // 'DataType' => key($var),                                    # Data type da tabela ID: 41622 conhecido como Market Source
                      'DataType' => 'WTD Tours',                                    # Data type da tabela ID: 41622 conhecido como Market Source
                      // 'DataCount' => $var[key($var)]                                       # Quantidade de data iguais na tabela.
                      'DataCount' => $_v                                       # Quantidade de data iguais na tabela.
                    );
                }
            }
        }

        if(!empty($dataSourceDeposit)){
            Log::info("DEPOSIT");
            foreach($dataSourceDeposit as $key => $var){
                foreach ($var as $_k => $_v) {
                    $obj[] = array(
                      'Property' => $key,               # Property da tabela
                      'Report' => "Statistics",                                # Report Section é fixo Inquiries
                      'CareLeve' => 'Cottage',//$care,                                    # Community da tabela ID: 41531
                      // 'DataType' => key($var),                                    # Data type da tabela ID: 41622 conhecido como Market Source
                      'DataType' => 'WTD Deposits',                                    # Data type da tabela ID: 41622 conhecido como Market Source
                      // 'DataCount' => $var[key($var)]                                       # Quantidade de data iguais na tabela.
                      'DataCount' => $_v                                       # Quantidade de data iguais na tabela.
                    );
                }
            }
        }

        $fp = fopen($create_path . $create_name, 'w');
        fputs($fp, implode(',', array(
            "Property name",
            'Report Section',
            'Data type',
            'Data count',
            'Care level'
        ))."\n");

	    $result_data = 'Property name,Report Section,Data type,Data count,Care level\n' ;
        if (is_array($obj)) {
            foreach ($obj as $obj_value) {
                fputs($fp, implode(',', array(
                    $obj_value["Property"],
                    $obj_value["Report"],
                    $obj_value["DataType"],
                    $obj_value["DataCount"],
                    $obj_value["CareLeve"]
                ))."\n");
            }
        }
        fclose($fp);

        $fp2 = fopen($create_path2 . $create_name2, 'w');
        fputs($fp2, implode(',', array(
            "Property name",
            'Report Section',
            'Data type',
            'Data count',
            'Care level'
        ))."\n");

	    $result_data2 = 'Property name,Report Section,Data type,Data count,Care level\n' ;
        if (is_array($obj)) {
            foreach ($obj as $obj_value) {
                fputs($fp2, implode(',', array(
                    $obj_value["Property"],
                    $obj_value["Report"],
                    $obj_value["DataType"],
                    $obj_value["DataCount"],
                    $obj_value["CareLeve"]
                ))."\n");
            }
        }
        fclose($fp2);
    }

    public function getData($label, $array)
    {
        $value = '';
        if (!empty($array)) {
            foreach ($array as $json) {
                if ($json['label'] == $label) {
                    if(isset($json['Value'])) {
                        if($json['Value']) {
                            $value = $json["Value"];
                        }
                    }
                }
            }
        }
        return $value;
    }

    public function getInfoUnqualified($label, $array)
    {
        $result = "";
        if (!empty($array)) {
            foreach ($array as $json) {
                if (
                    $json['label'] == $label && isset($json['value']) &&
                    $json['value'] == 'Financially Unqualified'
                ) {
                    $result = $json['value'];

                }
            }
        }
        return $result;
    }

    public function callCurl($url) {
        $curl = curl_init();
        $allList = array();
        //$url = self::$rest_api['call_api'].urlencode($url);
        // $response = file_get_contents($url);
        // $allList = json_decode($response, true);
        // return $allList;

        // $json = file_get_contents(base_path("public/yardi/") . "1.json");
        // $json_data = json_decode($json, true);
        // return $json_data;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 600,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_FOLLOWLOCATION, true,
            CURLOPT_HTTPHEADER => array(
                "Access-Control-Allow-Origin : *",
                //'Authorization : Bearer ' . self::$rest_api['ocp_key_first'],
                "cache-control : no-cache",
                "content-type : application/json",
                "Ocp-Apim-Subscription-Key : " . self::$rest_api['ocp_key_first'],
                "Origin : " . self::$rest_api['restapi_domain']
            ),
        ));
        //curl_setopt($curl, CURLOPT_SSLVERSION , 3);
        curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $httpCode = curl_getinfo($curl , CURLINFO_HTTP_CODE);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        // $fp = fopen(base_path("public/yardi/") . "1.txt", 'w');
        // fwrite($fp, $response);
        // fclose($fp);
        if ($err || $response == "") {
            $response = curl_error($curl);
            curl_close($curl);
        } else {
            curl_close($curl);
            $allList = json_decode($response, true);
        }
        return $allList;
    }
}
