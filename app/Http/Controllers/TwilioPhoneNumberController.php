<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Twilio\Rest\Client;

class TwilioPhoneNumberController extends Controller
{
    public function index($area_code)
    {  
        try {
            $res = [];
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
            
            $client = new Client($account_sid, $auth_token);
            $local = $client->availablePhoneNumbers("US")
                ->local
                ->read(["areaCode" => $area_code], 20);
            if($local)
            {
                $phone = [];
                foreach($local as $record)
                {
                    array_push($phone,$record->phoneNumber);
                }
                $res['success'] = true;
                $res['statusCode'] = 201;
                $res['data'] = $phone;
                $res['message'] = 'Successfully fetched';
            }
            else
            {
                $res['success'] = true;
                $res['statusCode'] = 201;
                $res['data'] = $local;
                $res['message'] = 'No record found with this area code';
            }
    
        } catch (Exception $e) {
            $res['success'] = false;
            $res['statusCode'] = 500;
            $res['data'] = null;
            $res['message'] = $e->getMessage();
        }

        return $res;
    }
}
