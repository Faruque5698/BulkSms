<?php

namespace App\Http\Controllers;

use App\Models\SMSCredential;
use Illuminate\Http\Request;
use SslWireless\SslWirelessSms;


class SMSController extends Controller
{
    public function send(Request $request){

        $request->validate([
//            'mobile'=>'required',
            'message'=>'required',
        ]);

        $user_id = auth()->user()->id;
        $credential = SMSCredential::where('user_id','=',$user_id)->first();

//        return response($credential);
        $DOMAIN = $credential->domain;
        $SID = $credential->sid;
        $API_TOKEN  = $credential->api_token;
        $bookData = $request->all();
        foreach ($bookData['contacts'] as $value) {
//            return response($value['mobile']);

//        return response($bookData['contacts']);
//        $phone[] = $request->mobile;
            $message = $request->message;
//        $total = count($phone);
//        return response()->json($total);
            $messageData = [

                [
                    "msisdn" => $value['mobile'],
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
        }
        return response()->json($response) ;


//        // username, password, sid provided by sslwireless
//        $SslWirelessSms = new SslWirelessSms('DesktopIT','c&I2+1%1kanta2%#G', 'DESKTOPIT');
//        // You can change the api url if needed. i.e.
//        // $SslWirelessSms->setUrl('new_url');
//        $output = $SslWirelessSms->send('01648583443','This is a test message.');
//
//        dd($output);

    }


}
