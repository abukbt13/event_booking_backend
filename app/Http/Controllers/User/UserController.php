<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function createUser(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response([
                'status' => 'failed',
                'errors' => $validator->errors()
            ]);
        }

        // Create user
        $user = new User();
        $user->email = $request->email;
        $user->last_name = $request->last_name;
        $user->first_name = $request->first_name;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->save();

        // Create and return token
        $token = $user->createToken('token')->plainTextToken;

        return response([
            'status' => 'success',
            'token' => $token,
            'user' => $user
        ]);
    }

//    public function UpdateProfile(Request $request)
//    {
//        $user_id=Auth::user()->id;
//        $profile = $request->file('profile');
//        $profileName = time() . '_' .  $profile->getClientOriginalName();
////        $profile->move(public_path('media'), $profileName);
//        $profile->storeAs('Profiles', $profileName, 'public');
//        $user =User::findOrfail($user_id);
//        $user ->profile = $profileName;
//        $user->update();
//        return response([
//            'status' =>'success',
//            'message'=>'Profile successfully Updated',
//            'data' => $user
//        ]);
//
//    }
    public function login(Request $request)
    {

        $userAgent = $request->header('User-Agent');

        // Use a library like jenssegers/agent to parse the User-Agent string
        $agent = new \Jenssegers\Agent\Agent();

        // Detect the platform using the parsed User-Agent string
        $platform = $agent->platform();

        $data = request()->all();
        $rules = [
            'email' => 'required',
            'password' => 'required'
        ];
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())) {
            return response([
                'status' => 'failed',
                'errors' => $valid->errors()
            ],422);
        }
        $email = request('email');
        $password = request('password');
        $user = User::where('email', $email)->get()->first();

        if (Auth::attempt(['email' => $email, 'password' => $password])) {

            storelog($user->id,'Loggin in',$platform);
            $token = $user->createToken('token')->plainTextToken;

            return response([
                'status' => 'success',
                'token' => $token,
                'user' => request()->user()
            ]);
        }
        else{

            return response([
                'status' => 'failed',
                'message' => 'Enter correct details',
            ]);
        }
    }
    public function auth(){
        if (Auth::check()) {
            return response()->json(['authenticated' => true]);
        } else {
            return response()->json(['authenticated' => false]);
        }
    }
    public function show_all_users(){
        $users =User::all();
        return response([
            'status'=>'success',
            'users'=>$users
        ]);
    }
    public function forget_pass(){
        $rules = [
            'email' => 'required',
        ];
        $data = request()->all();
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())){
            return response([
                'status' => 'failed',
                'error' => $valid->errors()
            ]);
        }
        $email=$data['email'];
        $user = User::where('email',$email)->first();
        if ($user) {
            $otp = rand(999, 10000);
            $user->otp = $otp;
            if ($user->update()) {
                $otp = rand(999, 10000);
                $user->otp = $otp;


                $body = "We have received a password reset request. Use the Otp " . $otp . " to reset your password.";
//                $details = [
//                    'subject' => 'Password Reset Request',
//                    'body' => $body,
//                    'date' => Carbon::now()->format('d-m-Y')
//                ];
                    sendNotification("+254" . $user->phone, $body);
                    $user->update();
                    return response([
                        'status' => 'success',
                        'message' => 'Check your email for password reset request'
                    ]);



            }
        }
        return response([
            'status'=>'User not found',
            'message' => 'User with the credentials not found'
        ]);
    }
    public function confirmOtp(){
        $rules = [
            'email' => 'required',
            'otp' => 'required',
        ];
        $data = request()->all();
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())){
            return response([
                'status' => 'failed',
                'error' => $valid->errors(),
                'message' => $valid->errors()
            ]);
        }
        $email=$data['email'];
        $otp=$data['otp'];

        $user = User::where('email',$email)->where('otp',$otp)->first();

        if ($user){
            return response([
                'status'=>'success',
                'message' =>'Success you can change your password'
            ]);
        }
        else{
            return response([
                'status'=>'failed',
                'message' =>'Enter correct details '
            ]);
        }
    }
    public function finish_reset(Request $request,$email_value,$otp_value){
        $data = request()->all();
        $email=$email_value;
        $otp=$otp_value;
        $password=$data['password'];
        $user = User::where('email',$email)->where('otp',$otp)->first();
        if ($user){
            $user->password = Hash::make($password);
            $user->update();
            return response([
                'status'=>'success',
                'message' =>'Password changed successfully'
            ]);
        }
        else{
            return response([
                'status'=>'failed',
                'message' =>'Ensure correct details are entered'
            ]);
        }



    }
}
