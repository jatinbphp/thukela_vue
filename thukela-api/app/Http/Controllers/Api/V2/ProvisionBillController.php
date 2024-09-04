<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Mail\sendAlert;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use LaravelJsonApi\Core\Document\Error;
use LaravelJsonApi\Core\Responses\ErrorResponse;
use Illuminate\Support\Facades\Log;
use App\Models\setting;

class ProvisionBillController extends Controller
{

    public function isLogggedIn($userName, $password)
    {
        if(empty($userName) || empty($password)){
            return 0;
        }
        $url = 'https://thukela-kadesh.pnpscada.com/getMemH.jsp?LOGIN=thukela.'.$userName.'&PWD='.$password;
        $result = $this->getCurl($url);
        $apiResponse = json_decode($result['response']);

        if(!empty($apiResponse) && $apiResponse[0] != 'error'){
            return 1;
        }
        return 0;
    }
    public function logIn(Request $request)
    {
        $success = 0;
        if($this->isLogggedIn($request->username,$request->password)){
            $success = 1;
        }
        $responceData['success'] = $success;
        return response()->json($responceData, 200);
    }

    public function getChartData(Request $request)
    {
        $data = [];
        if(!empty($request->username) && !empty($request->password)) {
            $userName = $request->username;
            $password = $request->password;
            $url = 'https://thukela-kadesh.pnpscada.com/getMemH.jsp?LOGIN=thukela.'.$userName.'&PWD='.$password;
            $result = $this->getCurl($url);
            $apiResponse = json_decode($result['response']);

            if(!empty($apiResponse) && $apiResponse[0] != 'error') {
                $url = 'https://thukela-kadesh.pnpscada.com/getEntitiesDetails.jsp?LOGIN='.$apiResponse[3].'&PWD='.$request->password;
                $result = $this->getCurl($url,'xml');
                $xmlArray = simplexml_load_string($result['response'], 'SimpleXMLElement', LIBXML_NOCDATA);
                $jsonData = json_encode($xmlArray);
                $arrayData = json_decode($jsonData, true);
                /*$eId = $arrayData['entity'][0]['id'];
                $mEId = $arrayData['entity'][0]['meters']['meter']['key1'];*/
                $eId = $arrayData['entity']['id']??0;

                $sDate = isset($request['sDate']) && !empty($request['sDate']) ? $request['sDate'] : Carbon::now()->subDay(7)->format('Y-m-d');
                $eDate = isset($request['eDate']) && !empty($request['eDate']) ? $request['eDate'] : Carbon::now()->addDay(1)->format('Y-m-d');

                //$url2 = 'https://akim.pnpscada.com:441/getProvisionalBill.jsp?LOGIN='.$data['loginData']['fullogin'].'&PWD='.$password.'&key1='.$eId.'&startdate='.$sDate.'&enddate='.$eDate;
                $url2 = 'https://thukela-kadesh.pnpscada.com/getProvisionalBill.jsp?LOGIN='.$apiResponse[3].'&PWD='.$password.'&key1='.$eId.'&startdate='.$sDate.'&enddate='.$eDate;
                $result2 = $this->getCurl($url2,'xml');
                $xmlArray2 = simplexml_load_string($result2['response'], 'SimpleXMLElement', LIBXML_NOCDATA);
                $jsonData2 = json_encode($xmlArray2);
                $data['provisionalBill'] = is_array($jsonData2) ? json_decode($jsonData2, true) : [];

                //$chartEId = $arrayData['entity'][0]['eid'];
                $chartEId = $arrayData['entity']['eid']??0;
                //$chartUrl = 'https://akim.pnpscada.com:441/getMeterAccountProfile.jsp?LOGIN='.$data['loginData']['fullogin'].'&PWD='.$password.'&eid='.$chartEId.'&start='.$sDate.'&end='.$eDate;
                $chartUrl = 'https://thukela-kadesh.pnpscada.com/getMeterAccountProfile.jsp?LOGIN='.$apiResponse[3].'&PWD='.$request->password.'&eid='.$chartEId.'&start='.$sDate.'&end='.$eDate;

                $chartResult = $this->getCurl($chartUrl,'xml');
                $chartXmlArray = simplexml_load_string($chartResult['response'], 'SimpleXMLElement', LIBXML_NOCDATA);
                $chartjsonData = json_encode($chartXmlArray);
                $chartData = json_decode($chartjsonData, true);
                $readingArr = $chartData['meter_account']['profile']['sample'] ?? [];

                $dateArr = [];
                $period = CarbonPeriod::create($sDate, $eDate);
                foreach ($period as $date) {
                    $dateArr[] =  $date->format('D').' '.$date->format('d/m/Y');
                }

                $chartTime = [];
                $readingData = [];

                for($i=0; $i<=23; $i++){
                    $time = $i.':00';
                    array_push($chartTime,date('H:i',strtotime($time)));
                    $time = $i.':30';
                    array_push($chartTime,date('H:i',strtotime($time)));
                }

                if(count($chartTime) > 0){
                    foreach ($chartTime as $tList){
                        $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                        $readingData[] = [
                            'label' => $tList,
                            /*'backgroundColor' => '#7F7F05',
                            'borderColor' => '#7F7F05',*/
                            'backgroundColor' => '#01b574',
                            'borderColor' => '#01b574',
                        ];
                    }
                }

                if(count($readingData) > 0){
                    foreach ($readingData as $key => $val){
                        if(count($readingArr) > 0){
                            $rData = [];
                            foreach ($readingArr as $list){
                                $readingDate = explode(' ',$list['date']);
                                $readingTime = date('i',strtotime($readingDate[1]));
                                $time = date('H:i', strtotime($readingDate[1]));
                                if($time == $val['label']){
                                    array_push($rData,number_format($list['P1'],2));
                                }
                            }
                            $readingData[$key]['data'] = $rData;
                        }
                    }
                }

                array_pop($dateArr);
                $data['readingData'] = $readingData;
                $data['chartDates'] = $dateArr;
            }
        }

        return response()->json($data, 200);
    }

    public function getNotificationData(Request $request)
    {
        $data = [];
        if($this->isLogggedIn($request->username,$request->password)){
            $url = 'http://ronmarkapp.co.za/admin/notifications.php';
            $result = $this->getCurl($url, 'json');
            $notifications = json_decode($result['response']);
            $data['notifications'] = $notifications->data;
        }
        return response()->json($data, 200);
    }

    public function sendContactUs(Request $request){
        $response = [];
        if($this->isLogggedIn($request->username,$request->password)) {
            $url = 'http://ronmarkapp.co.za/admin/contactus.php';
            $data = [
                'buildingname' => $request['buildingname'],
                'flatnumber' => $request['flatnumber'],
                'phonenumber' => $request['phonenumber'],
                'message' => $request['message'],
            ];
            $result = $this->postCurl($url, $data);
            $response = json_decode($result['response']);
            return response()->json($response, 200);
        }
        return response()->json($response, 401);

    }

    public function getCurl($url,$type = 'json'){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/'.$type,
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $data['status'] = 0;
            $data['error'] = $err;
        } else {
            $data['status'] = 1;
            $data['response'] = $response;
        }
        return $data;
    }

    public function postCurl($url, $contactData){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($contactData),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $data['status'] = 0;
            $data['error'] = $err;
        } else {
            $data['status'] = 1;
            $data['response'] = $response;
        }
        return $data;
    }

    public function sendEmail(){
        Log::info('Send MAil function start');
        $userArr = [
    	   //'AppTest1'=>'App1',
    	   //'AppTest2'=>'App2',
    	   'App_Test_4'=>'thukelaApp',
    	   'App_Test_5'=>'thukelaApp',
    	   'App_Test_6'=>'thukelaApp',
    	   'App_Test_7'=>'thukelaApp',
    	   'App_Test_8'=>'thukelaApp',
    	   'App_Test_9'=>'thukelaApp',
    	   'App_Test_10'=>'thukelaApp'
        ];
        $password = 'thukelaApp';

        foreach ($userArr as $key => $list){
            Log::info('Send MAil function running for==>'.$key);

            $url = 'https://thukela-kadesh.pnpscada.com/getMemH.jsp?LOGIN=thukela.'.$key.'&PWD='.$list;
            $result = $this->getCurl($url);
            $apiResponse = json_decode($result['response']);

            /*$member_url = 'https://thukela-kadesh.pnpscada.com/getLogins.jsp?LOGIN=thukela.'.$key.'&PWD='.$list.'&aperm=Y&iperm=Y&iperm100k=N&eperm=Y&entcid=Y&entkey1=Y&entkey2=Y';
            $member_result = $this->getCurl($member_url);

            return $member_result;

            return $member_apiResponse = json_decode($member_result['response']);*/

            if(!empty($apiResponse) && $apiResponse[0] != 'error'){
                $url1 = 'https://thukela-kadesh.pnpscada.com/getEntitiesDetails.jsp?LOGIN='.$apiResponse[3].'&PWD='.$list;
                $result1 = $this->getCurl($url1,'xml');
                $xmlArray = simplexml_load_string($result1['response'], 'SimpleXMLElement', LIBXML_NOCDATA);
                $jsonData = json_encode($xmlArray);

                if(!empty($jsonData)){
                    $arrayData = json_decode($jsonData, true);
                    $eId = isset($arrayData['entity']['id']) && !empty($arrayData['entity']['id']) ? $arrayData['entity']['id'] : 0;

                    Log::info('Send Mail function entity id==>'.$eId);

                    if($eId != 0){
                        /*---------Current Week Code---------*/
                        $sDate = Carbon::now()->subDay(7)->format('Y-m-d');
                        $eDate = Carbon::now()->addDay(1)->format('Y-m-d');

                        $url2 = 'https://thukela-kadesh.pnpscada.com/getProvisionalBill.jsp?LOGIN='.$apiResponse[3].'&PWD='.$list.'&key1='.$eId.'&startdate='.$sDate.'&enddate='.$eDate;
                        $result2 = $this->getCurl($url2,'xml');
                        $xmlArray2 = simplexml_load_string($result2['response'], 'SimpleXMLElement', LIBXML_NOCDATA);
                        $jsonData2 = json_encode($xmlArray2);
                        $currentData = json_decode($jsonData2, true);

                        if(!empty($currentData['readings'])){
                            $currentS1 = $currentData['readings']['mr']['start']['E1'];
                            $currentE1 = $currentData['readings']['mr']['end']['E1'];
                            $currentConsumption = floatval($currentE1) - floatval($currentS1);

                            /*---------Previous Week Code---------*/
                            $preEDate = Carbon::parse($sDate)->subDay(1)->format('Y-m-d');
                            $preSDate = Carbon::parse($preEDate)->subDay(7)->format('Y-m-d');

                            $url3 = 'https://thukela-kadesh.pnpscada.com/getProvisionalBill.jsp?LOGIN='.$apiResponse[3].'&PWD='.$list.'&key1='.$eId.'&startdate='.$preSDate.'&enddate='.$preEDate;
                            $result3 = $this->getCurl($url3,'xml');
                            $xmlArray3 = simplexml_load_string($result3['response'], 'SimpleXMLElement', LIBXML_NOCDATA);
                            $jsonData3 = json_encode($xmlArray3);
                            $previousData = json_decode($jsonData3, true);
                            $previousS1 = $previousData['readings']['mr']['start']['E1'];
                            $previousE1 = $previousData['readings']['mr']['end']['E1'];
                            $previousConsumption = floatval($previousE1) - floatval($previousS1);

                             Log::info('Send Mail function currentConsumption==>'.$currentConsumption);
                             Log::info('Send Mail function previousConsumption==>'.$previousConsumption);

                            if($currentConsumption > $previousConsumption){
                                $percentage = (($currentConsumption - $previousConsumption) / $previousConsumption) * 100;

                                 Log::info('Send Mail function percentage==>'.$percentage);

                                //if(round($percentage) > 30){
                                try{
                                    $setting = Setting::where('name', 'email_alerts_percentage')->first();
                                    $comparePercentag = ($setting && isset($setting->value) && $setting->value) ? $setting->value : 30; 
                                    Log::info('Send Mail function comparePercentag==>'.$comparePercentag);
                                    if(round($percentage) > $comparePercentag){
                                        //send mail
                                        $data['username'] = $apiResponse[3];
                                        $data['entity_id'] = $eId;
                                        $data['previousConsumption'] = $previousConsumption;
                                        $data['currentConsumption'] = $currentConsumption;
                                        $data['percentage'] = $percentage;

                                        try {
                                            // Mail::to('webmaster@portalthukelametering.co.za')
                                            //     ->cc(['notifications@thukelametering.co.za'])
                                            //     ->send(new sendAlert($data));
                                            Log::info('Mail sent successfully.');
                                        } catch (\Exception $e) {
                                            Log::error('Failed to send mail: ' . $e->getMessage());
                                        }

                                        // @Mail::to('webmaster@portalthukelametering.co.za')->cc(['notifications@thukelametering.co.za','mukund.h.php@gmail.com'])->send(new sendAlert($data));
                                        
                                        Log::info('Mail Send Success==>'.$apiResponse[3]);
                                    }else{
                                        Log::info("Percentage is lower then $comparePercentag% for ==>".$apiResponse[3]);
                                    }
                                }catch(\Exception $e){
                                    Log::info('Mail Send Error==>'.$e->getMessage());
                                }                        
                            }else{
                                Log::info('currentConsumption is lower then previousConsumption');
                            }
                        }
                    }
                }
            }
        }
        return 'Mail send successfully';
    }
    public function send_simple_mail(){
           $data['username'] = 'Test User';
                            $data['entity_id'] = '123';
                            $data['previousConsumption'] = 100;
                            $data['currentConsumption'] = 300;
                            $data['percentage'] = 3;
       @Mail::to('mukund.h.php@gmail.com')->send(new sendAlert($data));
    }

    public function setSetting(Request $request) {
        $response = [];
        if($request->username == 'ThukelaApp' && $request->password == 'Thukela12' && $this->isLogggedIn($request->username,$request->password)) {
            if($request->email_alerts_percentage){
                $data = [
                    'name' => 'email_alerts_percentage',
                    'value' => $request->email_alerts_percentage,
                ];
                if($isExists = Setting::where('name', 'email_alerts_percentage')->exists()){
                    Setting::where('name', 'email_alerts_percentage')->update(['value' => $request->email_alerts_percentage]);
                }else{
                    Setting::create($data);
                }
            }
            $response['type'] = 'success';
            return response()->json($response, 200);
        }
        $response['type'] = 'failed';
        return response()->json($response, 401);

    }

    public function getSetting(Request $request) {
        if($request->username == 'ThukelaApp' && $request->password == 'Thukela12' && $this->isLogggedIn($request->username,$request->password)) {
            $data['settingData'] = Setting::pluck('value', 'name')->toArray();
            return response()->json($data, 200);
        }
        $data['type'] = 'failed';
        return response()->json($data, 401);
    }
}
