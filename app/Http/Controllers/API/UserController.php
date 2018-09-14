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
	 
    public function login(Request $request){


        if (!$request->isMethod('post')) {
            return response(["statusCode" => "0", "message" => "404 Not found.", "errors" => []], 404)->header('Content-Type', "json");
        }

        if(isset($request['mobile'])){

            $validator = Validator::make($request->all(), [
                'mobile' => 'required',
                //'otp' => 'required',
                'device_token' => 'required',
                'device_type' => 'required',
                'device_details' => 'required',
            ]);

        }else{

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

        if(isset($request->mobile)) {

            //$checkNumber = User::whereJsonContains('data->contact_no', $request->mobile)->get();
            $checkNumber = User::where('mobile', $request->mobile)->first();

            if(empty($checkNumber)){
                return response(['statusCode' => 0, 'errors' => "Number is not registered", 'message' => ['Failed']]);
            }

            $otp = rand(100000,999999);
            $otp_length= '6';
            $message = 'Your OTP is '. $otp;
            $sender = 'RAKTSEVADAL';
            $otp_expiry='3';
            $template ='mkmkkk';
            $mobile = '91'.$request->mobile;
            $email ="";

            $url = "http://control.msg91.com/api/sendotp.php?template=".$template."&otp_length=".$otp_length."&authkey=".env("TRANSAUTHKEY", "235391AG8E7NoOgG5b8d4843")."&message=".$message."&sender=".$sender."&mobile=".$mobile."&otp=".$otp."&otp_expiry=".$otp_expiry."&email=".$email;
            $sendOtp = $this->sendVerifyOtp($url);
            $arr = json_decode($sendOtp);
            if(isset($arr->type) && $arr->type =="success"){
                //$checkNumber = User::where('mobile',$request->mobile)->update(['otp'=>$otp,'updated_at'=> date('Y-m-d H:i:s')]);
                return response(['statusCode' => 1, 'success' => "Otp Send Successfully", 'message' => ['Success']]);

            }else{

                return response(['statusCode' => 0, 'success' => "Fail to send Otp", 'message' => ['Failed']]);
            }

        }else {

            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                $user = Auth::user();
                $success['token'] = $user->createToken('RaktsevaDal')->accessToken;
                $success['data'] =  $user;
                return response()->json(['success' => $success], $this->successStatus);
            } else {
                return response()->json(['error' => 'Unauthorised'], 401);
            }

        }
    }

    public function sendVerifyOtp($url){
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

    /**
     * Api For Verify Otp
     * @param $url With parameter
     * @return Message Success or Fail
     */

    public function otpVerify(Request $request){

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

        $url = "https://control.msg91.com/api/verifyRequestOTP.php?authkey=".env("TRANSAUTHKEY", "235391AG8E7NoOgG5b8d4843")."&mobile=".$request->mobile."&otp=".$request->otp;

        $result =  $this->sendVerifyOtp($url);
        $varify = json_decode($result);

        if($varify->type == "success"){
            $user = User::where('mobile', $request->mobile)->first();
            $success['token'] = $user->createToken('RaktsevaDal')->accessToken;
            $success['data'] =  $user;
            return response(['statusCode' => 1, 'data'=>$success, 'success' => ['Verify SuccessFully'], 'message' => [$varify->message]]);
        }else{
            return response(['statusCode' => 0, 'error' => [$varify->message], 'message' => [$varify->message]]);
        }

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
            return response()->json(['error'=>$validator->errors()], 401);            
        }
		
		$input = $request->all(); 
				$input['password'] = bcrypt($input['password']); 
				$user = User::create($input); 
				$success['token'] =  $user->createToken('MyApp')->accessToken; 
				$success['name'] =  $user->name;
				
		return response()->json(['success'=>$success], $this->successStatus); 
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
}