<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{

    public $successStatus = 200;

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */

    public function login(Request $request)
    {


        if (!$request->isMethod('post')) {
            return response(["statusCode" => "0", "message" => "404 Not found.", "errors" => []], 404)->header('Content-Type', "json");
        }

        $messages = [
            'mobile.min' => 'Mobile number should be atleast 12 digits with country code.',
            'mobile.max' => 'Mobile number should not be more than 12 digits with country code.',
        ];

        if (isset($request['mobile'])) {

            $validator = Validator::make($request->all(), [
                'mobile' => 'required|min:12|max:12',
                'device_token' => 'required',
                'device_type' => 'required',
                'device_details' => 'required',
            ], $messages);

        } else {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
                'device_token' => 'required',
                'device_type' => 'required',
                'device_details' => 'required',
            ]);

        }

        if ($validator->fails()) {
            return response(['statusCode' => 0, 'errors' => $validator->errors()->all(), 'message' => ['Failed']]);
        }

        if (isset($request->mobile)) {

            //$checkNumber = User::whereJsonContains('data->contact_no', $request->mobile)->get();
            $checkNumber = User::where('mobile', $request->mobile)->first();

            if (empty($checkNumber)) {

                $userCreate = new User;
                $userCreate->mobile = $request->mobile;
                $userCreate->device_token = $request->device_token;
                $userCreate->device_type = $request->device_type;
                $userCreate->device_details = $request->device_details;
                $userCreate->save();

                //return response(['statusCode' => 0, 'errors' => "Number is not registered", 'message' => ['Failed']]);
            }

            $otp = rand(100000, 999999);
            $otp_length = '6';
            $message = 'Your OTP is ' . $otp;
            $sender = 'RAKTSEVADAL';
            $otp_expiry = '3';
            $template = 'mkmkkk';
            $mobile = $request->mobile;
            $email = "";

            $url = "http://control.msg91.com/api/sendotp.php?template=" . $template . "&otp_length=" . $otp_length . "&authkey=" . env("TRANSAUTHKEY", "235391AG8E7NoOgG5b8d4843") . "&message=" . $message . "&sender=" . $sender . "&mobile=" . $mobile . "&otp=" . $otp . "&otp_expiry=" . $otp_expiry . "&email=" . $email;
            $sendOtp = $this->sendVerifyOtp($url);

            $arr = json_decode($sendOtp);
            if (isset($arr->type) && $arr->type == "success") {
                //$checkNumber = User::where('mobile',$request->mobile)->update(['otp'=>$otp,'updated_at'=> date('Y-m-d H:i:s')]);
                return response(['statusCode' => 1, 'success' => "Otp Send Successfully", 'message' => ['Success']]);

            } else {

                return response(['statusCode' => 0, 'success' => "Fail to send Otp", 'message' => ['Failed']]);
            }

        } else {

            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                $user = Auth::user();
                $success['token'] = $user->createToken('RaktsevaDal')->accessToken;
                $success['data'] = $user;
                return response()->json(['success' => $success], $this->successStatus);
            } else {
                return response()->json(['error' => 'Unauthorised'], 401);
            }

        }
    }

    /**
     * Api For Verify Otp
     * @param $url With parameter
     * @return Message Success or Fail
     */

    public function otpVerify(Request $request)
    {

        if (!$request->isMethod('post')) {
            return response(["statusCode" => "0", "message" => "404 Not found.", "errors" => []], 404)->header('Content-Type', "json");
        }

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|min:10|max:12',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['statusCode' => 0, 'errors' => $validator->errors()->all(), 'message' => ['Failed']]);
        }

        $url = "https://control.msg91.com/api/verifyRequestOTP.php?authkey=" . env("TRANSAUTHKEY", "235391AG8E7NoOgG5b8d4843") . "&mobile=" . $request->mobile . "&otp=" . $request->otp;

        $result = $this->sendVerifyOtp($url);
        $varify = json_decode($result);

        $auth = User::where('mobile', $request->mobile)->first();
        if (empty($auth)) {
            return response(['statusCode' => 0, 'error' => ['Mobile number not Found'], 'message' => ['Mobile number not Found']]);
        }
        if ($varify->type == "success") {


            $user = Auth::loginUsingId($auth['id'], true);

            $success['token'] = $user->createToken('RaktsevaDal')->accessToken;
            $success['data'] = $user;
            return response(['statusCode' => 1, 'data' => $success, 'success' => ['Verify SuccessFully'], 'message' => [$varify->message]]);
        } else {
            return response(['statusCode' => 0, 'error' => [$varify->message], 'message' => [$varify->message]]);
        }

    }

    public function otpResend(Request $request)
    {

        if (!$request->isMethod('post')) {
            return response(["statusCode" => "0", "message" => "404 Not found.", "errors" => []], 404)->header('Content-Type', "json");
        }

        $messages = [
            'mobile.min' => 'Mobile number should be atleast 12 digits with country code.',
            'mobile.max' => 'Mobile number should not be more than 12 digits with country code.',
        ];

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|min:12|max:12',
            'retrytype' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return response(['statusCode' => 0, 'errors' => $validator->errors()->all(), 'message' => ['Failed']]);
        }

        $url = "http://control.msg91.com/api/retryotp.php?authkey=" . env("TRANSAUTHKEY", "235391AG8E7NoOgG5b8d4843") . "&mobile=" . $request->mobile . "&retrytype=" . $request->retrytype;
        $result = $this->sendVerifyOtp($url);
        $varify = json_decode($result);

        if ($varify->type == "success") {
            return response(['statusCode' => 1, 'success' => ['Otp Resend successfully'], 'message' => [$varify->message]]);
        } else {
            return response(['statusCode' => 0, 'error' => [$varify->message], 'message' => [$varify->message]]);
        }
    }

    /**
     * Update Profile
     * @params Login id ,Image, name , age , Dob , Gender ,Blood Group ,Location( lat,Long )
     * @return \Illuminate\Http\Response
     */

    public function updateProfile(Request $request)
    {
        /*
        |-------------------------------------------------------------------------------
        | Updates a User's Profile
        |-------------------------------------------------------------------------------
        | URL:            /api/update_profile
        | Method:         POST
        | Description:    Updates the authenticated user's profile
        */
        $user = $request->user();

        $name = $request->get('name');
        $age = $request->get('age');
        $dob = $request->get('dob');
        $gender = $request->get('gender');
        $blood_group = $request->get('blood_group');
        $lat = $request->get('lat');
        $long = $request->get('long');
        $notification_token = $request->get('notification_token');
        $images = $request->get('image');
        if($images){
            $image = $images;  // your base64 encoded
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = str_random(10).'.'.'png';
            \File::put(public_path(). '/images/' . $imageName, base64_decode($image));

        }

        /*
          Ensure the user has entered a name
        */
        if ($name != '') {
            $user->name = $name;
        }
        /*
          Ensure the user has entered age
        */
        if ($age != '') {
            $user->age = $age;
        }
        /*
          Ensure the user has submitted a profile visibility update
        */
        if ($dob != '') {
            $user->dob = $dob;
        }
        /*
          Ensure the user has entered something for gender.
        */
        if ($gender != '') {
            $user->gender = $gender;
        }

        /*
          Ensure the user has entered something for blood_group
        */
        if ($blood_group != '') {
            $user->blood_group = $blood_group;
        }

        /*
         Ensure the user has entered something for lat
       */
        if ($lat != '') {
            $user->lat = $lat;
        }

        /*
         Ensure the user has entered something for long
       */
        if ($long != '') {
            $user->long = $long;
        }

        /*
         Ensure the user has entered something for notification_token
       */
        if ($notification_token != '') {
            $user->notification_token = $notification_token;
        }

        /*
        Ensure the user has entered something for image
      */
        if ($imageName != '') {
            $user->profile_pic = $imageName;
        }
        $user->save();

        $users = $request->user();
        $imagePath = url("images/".$users->profile_pic);
        /*
          Return a response that the user was updated successfully.
        */
        return response()->json(['success' => 'Profile Updated Successfully', 'data' => $users, 'profile_image' => $imagePath], 201);
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;

        return response()->json(['success' => $success], $this->successStatus);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }

    public function sendVerifyOtp($url)
    {
        //dd(env("TRANSAUTHKEY", "235391AG8E7NoOgG5b8d4843"));
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }

}