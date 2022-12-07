<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Twilio\Rest\Client;

class TwilioCallController extends Controller
{
    public function index()
    {
        $receiverNumber = "+923101027145";
  
        try {
  
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
  
            $client = new Client($account_sid, $auth_token);
            $call = $client->calls->create(
                $receiverNumber, // Call this number
                $twilio_number, // From a valid Twilio number
                [
                    'url' => 'https://twimlets.com/holdmusic?Bucket=com.twilio.music.ambient'
                ]
              );

            print($call->sid);
    
        } catch (Exception $e) {
            dd("Error: ". $e->getMessage());
        }
    }
}
