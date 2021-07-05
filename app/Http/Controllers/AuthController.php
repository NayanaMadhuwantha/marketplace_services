<?php

namespace App\Http\Controllers;


use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Client;
use http\Env\Response;
use Illuminate\Http\Request;
use Mockery\Exception;
use phpseclib3\Crypt\RSA;

class AuthController extends Controller
{
    public function login(Request $request){
        $email = $request->email;
        $password = $request->password;

        if(empty($email) or empty($password)){
            return response()->json([
                'status' => 'error',
                'message' => 'fill all fields'
            ]);
        }

        $client = new Client();
        try {
            return $client->post('http://localhost/marketplace_services/public/v1/oauth/token',[
                "form_params" => [
                    "client_secret" => "k82KlhucgNqSOqrRlPU3DcNvS70Te2bFJrymrOxj",
                    "grant_type" => "password",
                    "client_id"  => 2,
                    "username"  => $request->email,
                    "password" => $request->password,
                    "scope"=> ""
                ]
            ])->getBody();
        }
        catch (BadResponseException $e){
            return response()->json([
                'ststus' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
