<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Twilio\Rest\Client;
use App\Models\User;

class TwilioPhoneNumberController extends Controller
{
    public function index(Request $req ,$area_code)
    {  
        try {
            $res = [];
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            
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

    public function phoneNumberPurchase(Request $req){
        try {
            $res = [];
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $user_sid = $req->header('sid');
            $client = new Client($account_sid, $auth_token);
            $incoming_phone_number = $client->incomingPhoneNumbers->create(["phoneNumber" => $req->phone_number]);
            if(!$incoming_phone_number)
            {
                $res['success'] = false;
                $res['statusCode'] = 300;
                $res['data'] = null;
                $res['message'] = 'Failed';
            }
            $user['psid'] = $incoming_phone_number->sid;
            User::where('sid',$req->header('twilio_sid'))->update(['phone_number'=>$req->phone_number]);
            $user = User::where('sid',$req->header('twilio_sid'))->first();
            $res['success'] = true;
            $res['statusCode'] = 201;
            $res['data'] = $user;
            $res['message'] = 'Phone number purchased successfully';
        }
        catch (Exception $e) {
            $res['success'] = false;
            $res['statusCode'] = 500;
            $res['data'] = null;
            $res['message'] = $e->getMessage();
        }
        return $res;
                                    
    }

    public function transferPhoneNumber($pSid , $aSid)
    {
        try {
            $res = [];
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $client = new Client($account_sid, $auth_token);
            $number = $client->incomingPhoneNumbers($pSid)->update(array(
                "accountSid" => $aSid,
                "sms_fallback_url" => "https://www.apiexplorer.com/api/messageHook"
            ));
            if($number)
            {
                return true;
            }
            return false;
        }
        catch (Exception $e) {
           return $e->getMessage();
        }
    }
}
