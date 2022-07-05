<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
//        $validatedData = $request->validate([
//            'name' => 'required|max:55',
//            'email' => 'email|required|unique:users',
//            'mobile' => 'required|unique:users',
//            'password' => 'required|confirmed',
//            'nid' => 'required',
////            'village' => 'required',
////            'city' => 'required',
////            'region' => 'required',
////            'country' => 'required',
//        ]);
        $user_id = auth()->user()->id;

        $user = User::find($user_id);

//        $validatedData['password'] = bcrypt($request->password);
//
//        $user = User::create($validatedData);
//        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->nid = $request->nid;
        $user->residential_address = $request->residential_address;
        $user->office_name = $request->office_name;
        $user->office_address = $request->office_address;
        $user->dob = $request->dob;
        $user->password = Hash::make( $request->password);
        $user->save();

        $accessToken = $user->createToken('authToken')->accessToken;

        return response([ 'user' => $user, 'access_token' => $accessToken]);
    }

    public function login(Request $request)
    {


        $loginData = $request->validate([
            'mobile' => 'required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);

    }

    public function profile()
    {
        $user_data = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "User data",
            "data" => $user_data
        ]);
    }

    public function logout(Request $request)
    {
        // get token value
        $token = $request->user()->token();

        // revoke this token value
        $token->revoke();

        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }

    public function send_otp(Request $request){
        $request->validate([
           'mobile'=>'required'
        ]);

        $user = Otp::where('mobile','=',$request->mobile)->first();
        if ($user){
            $user->delete();
        }

        $mobile = $request->mobile;
        $DOMAIN = env('DOMAIN');
        $SID = env('SID');
        $API_TOKEN  = env('API_TOKEN');
//        $bookData = $request->all();

//            return response($value['mobile']);

//        return response($bookData['contacts']);
//        $phone[] = $request->mobile;
        $code = rand(10000,99999);
            $message = "Your DSMS OTP is ".$code;
//        $total = count($phone);
//        return response()->json($total);
            $messageData = [

                [
                    "msisdn" => $mobile,
                    "text" => $message,
                    "csms_id" => uniqid(),
                ]
            ];

            $params = [
                "api_token" => $API_TOKEN,
                "sid" => $SID,
                "sms" => $messageData,
            ];

            $params = json_encode($params);
            $url = trim($DOMAIN, '/') . "/api/v3/send-sms/dynamic";

            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params),
                'accept:application/json'
            ));


            $response[] = curl_exec($ch);
            curl_close($ch);


            $otp = new Otp();
            $otp->mobile = $mobile;
            $otp->otp = $code;
            $otp->save();

            return response()->json([
               'message'=>'Otp Send',
                'data'=>$otp
            ]);

//        return response()->json($response) ;
    }

    public function otp_check(Request $request){
        $request->validate([
           'code'=>'required'
        ]);

        $otp = Otp::where('otp','=',$request->code)->first();
        if ($otp){
            return response()->json([
                'message'=>'Otp correct',
                'otp'=>$otp
            ]);
        }else{
            return response()->json([
               'message'=>"Wrong Otp"
            ]);
        }

    }
    public function pin_set(Request $request){
        $request->validate([
            'pin'=>'required',
            'otp'=>'required'
        ]);

        $otp = Otp::where('otp','=',$request->otp)->first();
        if ($otp){
            $mobile = $otp->mobile;

            $user = User::where('mobile','=',$mobile)->first();
            if ($user == null){
                $u = new User();
                $u->mobile = $mobile;
                $u->password = Hash::make($request->pin);
                $u->save();
                $otp->delete();

                return response()->json([
                    'message'=>'Sign Up Successful',
                    'data'=>$u
                ],200);

            }else{
                $user->password = Hash::make($request->pin);
                $user->save();

                $otp->delete();

                return  response()->json([
                    'message'=>'Pin Change Successful',
                ],200);
            }
        }else{
            return  response()->json([
               'otp'=>'Otp expired. please try again'
            ],404);
        }


    }

    public function kyc(Request $request){

        $request->validate([
           'nid'=>'required|image',
           'bin'=>'required|image',
           'trade'=>'required|image',
           'tin'=>'required|image',
//           'image2'=>'required|image',
           'doc1'=>'required',
           'doc2'=>'required'
        ]);

        $image1 = $request->file('nid');
        $image2 = $request->file('bin');
        $image3 = $request->file('trade');
        $image4 = $request->file('tin');
        $image5 = $request->file('image2');

        $file1 = $request->file('doc1');
        $file2 = $request->file('doc2');

        $fileUrl1 = file_upload($file1);
        $fileUrl2 = file_upload($file2);


        $image_url1 = image_upload($image1);
        $image_url2 = image_upload($image2);
        $image_url3 = image_upload($image3);
        $image_url4 = image_upload($image4);
        $image_url5 = image_upload($image5);


        $kyc = Kyc::where('user_id','=',auth()->user()->id)->first();
         if ($kyc == null){
             $k = new Kyc();
             $k->user_id = auth()->user()->id;
             $k->nid=$image_url1;
             $k->bin=$image_url2;
             $k->trade=$image_url3;
             $k->tin=$image_url4;
             $k->image=$image_url5;
             $k->doc1=$fileUrl1;
             $k->doc2=$fileUrl2;

             $k->save();
              return response()->json([
                  'message'=>'file uploaded successful',
                  'data'=>$k
              ]);
         }else{
             $kyc->nid=$image_url1;
             $kyc->bin=$image_url2;
             $kyc->trade=$image_url3;
             $kyc->tin=$image_url4;
             $kyc->doc1=$fileUrl1;
             $kyc->doc2=$fileUrl2;

             $kyc->save();

             return response()->json([
                 'message'=>'file uploaded successful',
                 'data'=>$kyc
             ]);
         }

    }

    public function kyc_list(){
        $user_id = auth()->user()->id;
        $kyc = Kyc::where('user_id','=',$user_id)->first();

        if ($kyc==null){
            return response()->json([
                'message'=>'No data found',

            ],404);
        }
        else{
            return response()->json([
                'message'=>'Success',
                'kyc'=>$kyc
            ],200);
        }

    }

}
