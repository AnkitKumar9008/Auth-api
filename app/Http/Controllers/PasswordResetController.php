<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
class PasswordResetController extends Controller
{
    public function send_reset_password_email(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
        ]);
        $email = $request->email;
        $user = User::where('email',$request->email)->first();
        if(!$user){
            return response([
                'message'=>"Email doesn't Exists",
                'status'=>'Success'
            ],404);
        }
        
        $token = Str::random(60);

        PasswordReset::create([
            'email'=>$request->email,
            'token'=>$token,
            'created_at'=>Carbon::now()
        ]);

        // dump("http://127.0.0.1:3000/api/user/reset/". $token);

        Mail::send('reset', ['token' => $token], function ($message) use ($email) {
            $message->subject('Reset Your password');
            $message->to($email);
        });

        return response([
            'message'=>"Password Reset Email Sent... Check Your Email",
            'status'=>'Success'
        ],404);
    }

    public function reset(Request $request, $token){
        $formatted = Carbon::now()->subMinutes(1)->toDateTimeString();
        PasswordReset::where('created_at', '<=',$formatted)->delete();

        $request->validate([
            'password'=> 'required|confirmed',
        ]);

        $passwordreset = PasswordReset::where('token',$token)->first();
        if(!$passwordreset){
            return response([
                'message'=>"Token is Invalid or Expired",
                'status'=>'Failed'
            ],404);
        }
        $user = User::where('email',$passwordreset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        PasswordReset::where('email',$user->email)->delete();

        return response([
            'message'=>"Token is Invalid or Expired",
            'status'=>'Failed'
        ],404);
    }
}
