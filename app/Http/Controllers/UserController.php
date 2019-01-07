<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Hash;
use JWTAuth;
use JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public $current_user;
    public $user_id;

    public function __construct(){
        // $this->current_user=JWTAuth::parseToken()->toUser();
        // $this->user_id=$this->current_user->user_id;
    }

    public function register(Request $request){
        if(User::whereUsername($request->username)->count()>0){
            return response()->json(['message'=>'Username already exists','success'=>0]);
        }

        if(User::where('user_email',$request->email)->count()>0){
            return response()->json(['message'=>'Email already exists','success'=>0]);
        }

        $user=new User;
        $user->username=$request->username;
        $user->user_email=$request->user_email;
        $user->created_at_t=time();
        $user->password=bcrypt($request->password);
        $user->type=1;

        $user->save();

        return $this->authenticate($request->user_email,$request->password);
    }

    public function login(Request $request){
        return $this->authenticate($request->user_email,$request->password);
    }

    
    public function authenticate($email,$password)
    {

        $credentials=['user_email'=>$email,'password'=>$password];

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user=User::where('user_email',$email)->first();
        $payload=[
            'user_id'=>$user->user_id,
            'user_email'=>$user->user_email,
            'user_name'=>$user->user_name
        ];

        $token = JWTAuth::fromUser($user,$payload);
        // all good so return the token
        return response()->json(compact('token'));
    }
    

    
}
