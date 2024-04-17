<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use DB;
class ApiController extends Controller
{
    //register api//
    public function register(Request $request)
    {
        $request->validate([
            "name"=> "required",
            "email" => "required|email|unique:users",
            "password" => "required"
        ]);

        //create User
        $user = User::create([
            'name' => $request->name,
            'email' =>$request->email,
            'password'=>Hash::make($request->password),
        ]);

        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json([
            "token"=>$token,
            "status" =>"success",
            "message"=>"User Registered Successfully"
        ]);
    }

    //login api//
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);
        $user = User::where('email',$request->email)->first();
        if($user && Hash::check($request->password,$user->password))
        {
            $token = $user->createToken($request->email)->plainTextToken;
            return response()->json([
                "token"=>$token,
                "status" =>"success",
                "message"=>"User Login Successfully"
            ]);
        }
        else{
            return response()->json([
                "status" =>"success",
                "message"=>"User credentials Wrong !!",
            ]);
        }
    }

    //logout api//
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            "status" => "success",
            "message" => "User Logout Successfully!!",
        ]);
    }

    public function logged_user(){
        $loggeduser = auth()->user();
        return response([
            'user'=>$loggeduser,
            'message'=> 'Logged User Data',
            'status' => 'success'
        ],200);
    }

    public function profile(){
        $loggedUser = auth()->user(); 
        $loggedEmail = $loggedUser->email;
        $userData = DB::table('users')->where('email', $loggedEmail)->first();
        return response([
            'user'=>$userData,
            'message'=> 'User Profile',
            'status' => 'success'
        ],200);
    }
    

    //change password

    public function changePassword(Request $request)
    {
        $request->validate([
            'password'=> 'required|confirmed'
        ]);

        $loggeduser = auth()->user();
        $loggeduser->password = Hash::make($request->password);
        $loggeduser->save();

        return response([
            'message'=>'Password changed Successfully',
            'status'=> 'success'
        ],200);
    }
    
}
