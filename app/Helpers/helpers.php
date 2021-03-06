<?php



    function singleSms($msisdn, $messageBody, $csmsId)
    {
        $params = [
            "api_token" => env('API_TOKEN'),
            "sid" => env('SID'),
            "msisdn" => $msisdn,
            "sms" => $messageBody,
            "csms_id" => $csmsId
        ];
        $url = trim(env('DOMAIN'), '/')."/api/v3/send-sms";
        $params = json_encode($params);

        return callApi($url, $params);
    }


    function callApi($url, $params)
    {
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

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
}

function image_upload ($image){
//    $image = $request->file('image');
    $imageName = uniqid().$image->getClientOriginalName();
    $directory = 'assets/backend/image/kyc/';
    $imageUrl = $directory.$imageName;
    $image -> move($directory,$imageName);
    return $imageUrl;
}
function file_upload ($file){
//    $image = $request->file('image');
    $fileName = uniqid().$file->getClientOriginalName();
    $directory = 'assets/backend/file/kyc/';
    $fileUrl = $directory.$fileName;
    $file -> move($directory,$fileName);
    return $fileUrl;
}
