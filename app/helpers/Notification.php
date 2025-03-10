<?php

function sendNotification($to, $message)
{

    $url = 'https://app.sharasms.co.ke/api/messages/new';
    $accessToken = env('ACCESS_TOKEN');
    $shortCodeId = env('SHORT_CODE_ID');
//    dd($accessToken, $shortCodeId);
    $data = [
        'message' => $message,
        'phone' => $to,
        'short_code_id' => $shortCodeId,
    ];

    $headers = [
        'Authorization: Bearer ' . '483|9LuXqe08dBkDyYTbLT9A2DA2M1r4VRChYr6r1MzT94c297d3',
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);
//    dd($response);
//    echo $response;
}
