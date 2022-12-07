<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Validator;
Use App\Models\User;
use Hash;

class TwilioAuthController extends Controller
{
    public function index(Request $req)
    {  
        try {
            $validated = Validator::make($req->all(),[
                'name' => 'required|unique:users|max:255',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:users',
                'password' => 'required|confirmed',
                'phone_number' => 'required|unique:users',
            ]);

            if ($validated->fails()) {
                $res['success'] = false;
                $res['statusCode'] = 300;
                $res['data'] = null;
                $res['message'] = $validated->messages();
                return $res;
            }
            $req['password'] = Hash::make($req['password']);
            $user = User::create($req->all());
            if(!$user)
            {
                $res['success'] = false;
                $res['statusCode'] = 300;
                $res['data'] = null;
                $res['message'] = "Unable to create user. Please try again later";
                return $res;
            }

            $res = [];
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $client = new Client($account_sid, $auth_token);
            $account = $client->api->v2010->accounts->create(["friendlyName" => $req->name]);
            if($account)
            {
                $res['success'] = true;
                $res['statusCode'] = 201;
                $res['data']['twilio_user'] = $account;
                $res['message'] = 'Successfully fetched';
            }
            else
            {
                $res['success'] = true;
                $res['statusCode'] = 201;
                $res['data'] = null;
                $res['message'] = 'Failed';
            }
    
        } catch (Exception $e) {
            $res['success'] = false;
            $res['statusCode'] = 500;
            $res['data'] = null;
            $res['message'] = $e->getMessage();
        }

        return $res;
    }

    public function getUser(Request $req)
    {
        try 
        {
            $user = User::where('email',$req->email)->first();
            if(!$user)
            {
                $res['success'] = false;
                $res['statusCode'] = 300;
                $res['data'] = null;
                $res['message'] = 'No user found.';
                return $res;
            }
            if (Hash::check($req->password, $user->password)) {
        
                $res = [];
                $account_sid = getenv("TWILIO_SID");
                $auth_token = getenv("TWILIO_TOKEN");
                
                $client = new Client($account_sid, $auth_token);
                if(!$user->sid)
                {
                    $res['success'] = false;
                    $res['statusCode'] = 300;
                    $res['data'] = null;
                    $res['message'] = 'No SID found.';
                    return $res;
                }
                $account = $client->api->v2010->accounts($user->sid)
                ->fetch();  
                if($account)
                {
                    $res['success'] = true;
                    $res['statusCode'] = 201;
                    $res['data'] = (array)$account;
                    $res['message'] = 'Successfully fetched';
                }
                else
                {
                    $res['success'] = true;
                    $res['statusCode'] = 201;
                    $res['data'] = null;
                    $res['message'] = 'Failed';
                }
            }
            else
            {
                $res['success'] = false;
                $res['statusCode'] = 300;
                $res['data'] = null;
                $res['message'] = 'Incorrect Password.';
            }
        } 
        catch (Exception $e) {
            $res['success'] = false;
            $res['statusCode'] = 500;
            $res['data'] = null;
            $res['message'] = $e->getMessage();
        }

        return $res;
    }
}
