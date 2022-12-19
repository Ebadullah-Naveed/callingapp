<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Twilio\Rest\Client;

class TwilioSMSController extends Controller
{
    public function index(Request $req)
    {
        $res = [];
        $receiverNumber = $req->phone_number;
        $message = $req->message ?? null;
        if(!$receiverNumber)
        {
            $res['success'] = false;
            $res['statusCode'] = 201;
            $res['data'] = null;
            $res['message'] = "Receiver Number is required";
        }
        try {
  
            $user_sid = $req->header('twilio_sid');
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = $req->header('twilio_number');
            $account_sid = getenv("TWILIO_SID");
            $client = new Client($account_sid, $auth_token,$user_sid);
            $message = $client->messages->create($receiverNumber, [
                'from' => $twilio_number, 
                'body' => $message]);
           
            $res['success'] = true;
            $res['statusCode'] = 200;   
            $res['data'] = $message;
            $res['message'] = $message->status;
  
        } catch (Exception $e) {
            $res['success'] = false;
            $res['statusCode'] = 500;
            $res['data'] = null;
            $res['message'] = $e->getMessage();
        }

        return $res;
    }

    public function retrieveMessage(Request $req)
    {
        $res = [];
        try {
            $mes = [];
            $user_sid = $req->header('twilio_sid');
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = $req->header('twilio_number');
            $account_sid = getenv("TWILIO_SID");
            $client = new Client($account_sid, $auth_token,$user_sid);
            $messages = $client->messages->read([]);
            $arrCheck = [];
            foreach($messages as $key=>$message)
            {
                if($message->from != $twilio_number)
                {
                    if(in_array($message->from,$arrCheck) == false)
                    {
                        $mes[] = ['body'=>$message->body,'from'=>$message->from];
                    }
                }
                array_push($arrCheck,$message->from);
            }
            $res['success'] = true;
            $res['statusCode'] = 200;
            $res['data'] = $mes;
            $res['message'] = "SMS retrieve successfully";
  
        } catch (Exception $e) {
            $res['success'] = false;
            $res['statusCode'] = 500;
            $res['data'] = null;
            $res['message'] = $e->getMessage();
        }

        return $res;
    }

    public function userChatHistory(Request $req)
    {
        $res = [];
        try {
            $mes = [];
            $mes2 = [];
            $user_sid = $req->header('twilio_sid');
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = $req->header('twilio_number');
            $account_sid = getenv("TWILIO_SID");
            $client = new Client($account_sid, $auth_token,$user_sid);
            $messages = $client->messages
                   ->read([
                              "from" => $req->from_number,
                              "to" => $twilio_number
                          ]
                   );

            foreach($messages as $key=>$message)
            {
                $mes[] = ['message'=>$message->body,'from'=>$message->from];
            }
            $messages2 = $client->messages
                   ->read([
                              "from" => $twilio_number,
                              "to" => $req->from_number,
                          ]
                   );
            foreach($messages2 as $key=>$message2)
            {
               $mes2[] = ['message'=>$message2->body,'from'=>$message2->from];
            }
            $mesArr = array_merge($mes,$mes2);
            $res['success'] = true;
            $res['statusCode'] = 200;
            $res['data'] = $mesArr;
            $res['message'] = "SMS retrieve successfully";
  
        } catch (Exception $e) {
            $res['success'] = false;
            $res['statusCode'] = 500;
            $res['data'] = null;
            $res['message'] = $e->getMessage();
        }

        return $res;
    }
}
