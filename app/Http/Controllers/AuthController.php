<?php

namespace App\Http\Controllers;


use App\Models\Post;
use App\Models\User;
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
            return $client->post(config('service.passport.login_endpoint'),[
                "form_params" => [
                    "client_secret" => config('service.passport.client_secret'),
                    "grant_type" => "password",
                    "client_id"  => config('service.passport.client_id'),
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

    public function register(Request $request){
        $email = $request->email;
        $password = $request->password;

        if (empty($email) or empty($password)){
            return response()->json([
                'status' => 'error',
                'message' => 'You must fill all the fields'
            ]);
        }
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
            return response()->json([
                'status' => 'error',
                'message' => 'You must enter a valid email'
            ]);
        }
        if (strlen($password)<6){
            return response()->json([
                'status' => 'error',
                'message' => 'Password should have more than 5 characters'
            ]);
        }
        if (User::where('email','=',$email)->exists()){
            return response()->json([
                'status' => 'error',
                'message' => 'This email is already registered'
            ]);
        }
        try {
            $user = new User();
            $user->email = $email;
            $user->password = app('hash')->make($password);
            if ($user->save()){
                return response()->json([
                    'status' => 'success',
                    'message' => 'User registered successfully',
                    'login'  => json_decode($this->login($request))
                ]);
            }
        }
        catch (\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function logout(Request $request){
        try {
            auth()->user()->tokens()->each(function ($token){
                $token->delete();
            });
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully'
            ]);
        }
        catch (\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
