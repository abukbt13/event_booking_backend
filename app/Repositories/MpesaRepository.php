<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MpesaRepository
{
    public function C2BMpesaApi($booking_id,$phone,$amount){

        Log::info('Request Initiated');

        $timestamp = Carbon::now()->format('YmdHis');
//        dd($neTimesptamp,$timestamp);
        $password = base64_encode(env('C2B_SHORTCODE').env('PASSKEY').$timestamp);
        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' =>env('C2B_SHORTCODE'),
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => round($amount,0),
            'PartyA' => $this->formatPhone($phone),
            'PartyB' => env('C2B_SHORTCODE'),
            'PhoneNumber' => $this->formatPhone($phone),
            'CallBackURL' => url('https://event.kwetunyumbani.store/api/payment/'.$booking_id ),
            'AccountReference' =>$phone.'T',
            'TransactionDesc' => 'Payment for connection'
        );
        $data_string = json_encode($curl_post_data);
        $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $headers = array('Content-Type:application/json','Authorization:Bearer '.$this->getAccessToken()->access_token);
        $res = $this->doCurl($url,$data_string,'POST',$headers);
        return $res;
    }

    protected function getAccessToken(){

        $consumer_key = env('C2B_CONSUMER_KEY');
        $consumer_secret = env('C2B_CONSUMER_SECRET');
        $url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $data = '';
        $header = [
            'Authorization: Basic '.base64_encode($consumer_key.':'.$consumer_secret)
        ];
        $response = $this->doCurl($url,$data,'GET',$header);
        return $response;
    }

    protected function doCurl($url,$data,$method='POST',$header = null){
        if (!$header) {
            $header = array(
                'Accept: application/json',
                'Accept-Language: en_US',
            );
        }
        $curl = \curl_init($url);
        \curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_POST, true);
        \curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($curl, CURLOPT_HEADER, 0);
        \curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        \curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $content = \curl_exec($curl);
        $status = \curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $json_response = null;
        if ($status == 200 || $status == 201) {
            $json_response = json_decode($content);
            return $json_response;
        } else {
            Log::info('C2B request failed with error ',[
                'data' => $data,
                'url' => $url,
                'content' => $content,
                'status' => $status
            ]);
            echo json_encode($data);
            echo $url;
            echo $content;
            echo $status;
            throw new \Exception($content);
        }
    }

    public function formatPhone($phone){

        $phone = 'hfhsgdgs'.$phone;
        $phone = str_replace('hfhsgdgs0','',$phone);
        $phone = str_replace('hfhsgdgs','',$phone);
        $phone = str_replace('+','',$phone);
        if(strlen($phone) == 9){
            $phone = '254'.$phone;
        }
        Log::info('Formatted Phone: '.$phone);

        return $phone;

    }
}
