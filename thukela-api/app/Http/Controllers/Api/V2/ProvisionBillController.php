<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use LaravelJsonApi\Core\Document\Error;
use LaravelJsonApi\Core\Responses\ErrorResponse;

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
                $eId = $arrayData['entity']['id'];
                $mEId = $arrayData['entity']['meters']['meter']['key1'];

                $sDate = isset($request['sDate']) && !empty($request['sDate']) ? $request['sDate'] : Carbon::now()->subDay(7)->format('Y-m-d');
                $eDate = isset($request['eDate']) && !empty($request['eDate']) ? $request['eDate'] : Carbon::now()->addDay(1)->format('Y-m-d');

                //$url2 = 'https://akim.pnpscada.com:441/getProvisionalBill.jsp?LOGIN='.$data['loginData']['fullogin'].'&PWD='.$password.'&key1='.$eId.'&startdate='.$sDate.'&enddate='.$eDate;
                $url2 = 'https://thukela-kadesh.pnpscada.com/getProvisionalBill.jsp?LOGIN='.$apiResponse[3].'&PWD='.$password.'&key1='.$eId.'&startdate='.$sDate.'&enddate='.$eDate;
                $result2 = $this->getCurl($url2,'xml');
                $xmlArray2 = simplexml_load_string($result2['response'], 'SimpleXMLElement', LIBXML_NOCDATA);
                $jsonData2 = json_encode($xmlArray2);
                $data['provisionalBill'] = json_decode($jsonData2, true);

                //$chartEId = $arrayData['entity'][0]['eid'];
                $chartEId = $arrayData['entity']['eid'];
                //$chartUrl = 'https://akim.pnpscada.com:441/getMeterAccountProfile.jsp?LOGIN='.$data['loginData']['fullogin'].'&PWD='.$password.'&eid='.$chartEId.'&start='.$sDate.'&end='.$eDate;
                $chartUrl = 'https://thukela-kadesh.pnpscada.com/getMeterAccountProfile.jsp?LOGIN='.$apiResponse[3].'&PWD='.$request->password.'&eid='.$chartEId.'&start='.$sDate.'&end='.$eDate;

                $chartResult = $this->getCurl($chartUrl,'xml');
                $chartXmlArray = simplexml_load_string($chartResult['response'], 'SimpleXMLElement', LIBXML_NOCDATA);
                $chartjsonData = json_encode($chartXmlArray);
                $chartData = json_decode($chartjsonData, true);
                $readingArr = $chartData['meter_account']['profile']['sample'];

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

}
