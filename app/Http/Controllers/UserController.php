<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Hash;
use Lcobucci\JWT\Builder;

class UserController extends Controller
{
    public function register(Request $request){
        if(User::whereUsername($request->username)->count()>0){
            return response()->json(['message'=>'Username or already exists','success'=>0]);
        }

        if(User::where('user_email',$request->email)->count()>0){
            return response()->json(['message'=>'Email or already exists','success'=>0]);
        }

        $user=new User;
        $user->username=$request->username;
        $user->user_email=$request->user_email;
        $user->created_at_t=time();
        $user->user_password=bcrypt($request->password);
        $user->type=1;

        $user->save();

        return self::authenticate($request->username,$request->password);
    }

    public function login(Request $request){
        return self::authenticate($request->username,$request->password);
    }

    public static function authenticate($username,$password){
        $password_from_db=User::whereUsername($username)
            ->orWhere('user_email',$username)
            ->value('user_password');

        if(empty($password)){
            return response()->json(['message'=>'Error on authentication','success'=>0]);
        }    

        $hash=Hash::check($password,$password_from_db);
        if(!$hash){
            return response()->json(['message'=>'Error on authentication2','success'=>0]);
        }

        return self::getToken($username);
    }

    private static function getToken($username){
        $token = (new Builder())
            ->setIssuer('http://api.gazeta.com') // Configures the issuer (iss claim)
            ->setAudience('http://gazeta.com') // Configures the audience (aud claim)
            ->setId('pneumonoultramicroscopicsilicovolcanoconiosis', true) // Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time()) // Configures the time that the token was issued (iat claim)
            ->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
            ->setExpiration(time() + 36000000) // Configures the expiration time of the token (exp claim)
            ->set('user', User::whereUsername($username)->first()) // Configures a new claim, called "uid"
            ->getToken(); // Retrieves the generated token

        return response()->json(['token'=>(string)$token,'success'=>1]);
    }
}
