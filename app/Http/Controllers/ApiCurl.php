<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiCurl extends Controller
{
    private static $last_request_time;
    public static function request($url, $method = 'GET', $pause = 500, $data = []){
        if(empty($url)) return false;
        if(!in_array($method,['GET','POST','PUT','DELETE'])) return false;
        $startrequest = self::microtime_float();
        if(!isset(self::$last_request_time)) self::$last_request_time = $startrequest;
        if(!empty($pause) && is_numeric($pause) && $startrequest != self::$last_request_time  && ($startrequest - self::$last_request_time)*1000 < $pause ){
            usleep($pause - ($startrequest - self::$last_request_time)*1000);
        }
        $err = fopen(__DIR__.'/../../../storage/logs/curl-'.date("Ymd").'.log', "a+");

        $curl_desc = curl_init($url);
        curl_setopt($curl_desc, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_desc, CURLOPT_CUSTOMREQUEST, $method);
        
        curl_setopt($curl_desc, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($curl_desc, CURLOPT_VERBOSE, 1);
        curl_setopt($curl_desc, CURLOPT_STDERR, $err);
        
        curl_setopt($curl_desc, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "X-Shopify-Access-Token: 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"
            )
        );

        $result = curl_exec($curl_desc);
        curl_close($curl_desc);
        fclose($err);

        $result = json_decode($result, true);
        self::$last_request_time = $startrequest;
        return $result ?? [];
    }

    private static function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}
