<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Hamcrest\Type\IsNumeric;


class authController extends Controller
{
    public function register(Request $request){
        $fields = $request->validate([
            "name"=>"required|string",
            "email"=>"required|email|string|unique:users,email",
            "phone"=>"required|numeric|unique:users,phone|",
            "role"=>"required|boolean",
            "carNum"=>"string",
            "location"=>"string",
            "license"=>"string",
            "password"=>"required|string|confirmed"
        ]);

        $user =User::create([
            'name'=>$fields['name'],
            'email'=>$fields['email'],
            'phone'=>$fields['phone'],
            'role'=>$fields['role'],
            'carNum'=>$fields['carNum'],
            'location'=>$fields['location'],
            'license'=>$fields['license'],
            'userImg'=>'any',
            'password'=>bcrypt($fields['password'])
        ]);
        $token = $user->createToken("myToken")->plainTextToken;

        $response = [
            'user'=>$user,
            'myToken'=>$token
        ];

        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }

    public function login(Request $request){
        if(Is_numeric($request->identify)){
            $fields = $request->validate([
                "identify"=>"required|numeric",
                "password"=>"required|string"
            ]);
    
            //check phone number exists or not  
            $user= User::where('phone',$fields['identify'])->first();
            if(!$user || ! Hash::check($fields['password'],$user->password)) {
            abort (403,'Invalid Credentials');
            }
            else {
                $token=$user->createToken("myToken")->plainTextToken;
            }
            $response=[
                'user' =>$user ,
                'myToken'=>$token
                ] ;
            return Response()->json(['status'=>'success','data'=>$response], 200 );
        }else{

       
            $fields = $request->validate([
                "identify"=>"required|email|string",
                "password"=>"required|string"
            ]);

            $user= User::where('emial',$fields['identify'])->first();
            if(!$user || ! Hash::check($fields['password'],$user->password)) {
            abort (403,'Invalid Credentials');
            }
            else {
                $token=$user->createToken("myToken")->plainTextToken;
            }
            $response=[
                'user' =>$user ,
                'myToken'=>$token
                ] ;
            return Response()->json(['status'=>'success','data'=>$response], 200 );
        }
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        
        return [
            'status'=>'success',
            "message"=>"logged out"
        ];
    }
}
