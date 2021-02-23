<?php

namespace App\Helpers;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Helper{

    public static $errorResponse   = ['status'=>'failed','message'=>'Error while processing your request!'];
    public static $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public static $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public static function MakeResponse($response,$message=array()){
        switch($response){
            case 'ok':
                SELF::$successResponse['data'] = $message;
                return SELF::$successResponse;
            break;
            case 'error':
                SELF::$errorResponse['data'] = $message;
                return SELF::$errorResponse;
            break;
            default:
                SELF::$warningResponse['data'] = $message;
                return SELF::$warningResponse['data']=$message;
            break;
        }
    }

    public static function LoadBl($layer,$param=false,$param2=false){

        $layerName = "App\BusinessLayer\\$layer";

        if($param && !$param2){
            return new $layerName($param);
        }
        else if($param && $param2){
            return new $layerName($param,$param2);
        }
        else{
            return new $layerName();
        }

    }

    public static function LoadMdl($models){
        $model  = explode(',',$models);
        $sizeOfModel = sizeof($model);
        if($sizeOfModel == 1){
            $mdl = "App\Models"."\\".'mdl_'.$models;
            return new $mdl();
        }
        foreach ($model as $key => $value) {
            $key = $value;
            $mdl = "App\Models"."\\".'mdl_'.$value;
            $array[$key] = new $mdl();
        }
        return $array;

    }


    public static function manageRequestData($request,$setRequestHanlder=False){
            $data = [];

            $ClientInfo = (isset($request['ClientInfo']) ? $request['ClientInfo'] : 'No Client Info Recorded');
            unset($request['ClientInfo']);

            if($setRequestHanlder == True){
                $requestBody = SELF::requestHandler($request->all());
            }else{
                $requestBody = $request->all();
            }

            $queryParam = array();

            if($request->query()){
                $queryParam = $request->query();
                $queryParam = SELF::AllowedQueryParam($queryParam);
            }

            $data = [
                'reqBody'    => $requestBody,
                'queryString'=> $queryParam,
                'clientInfo' => $ClientInfo,
            ];
            return $data;
    }



    public static function requestHandler($GetRequestInArray=array()){

            $response          = array();
            $ArrayParamOnly    = $GetRequestInArray;

            foreach ($ArrayParamOnly as $key => $value) {

                $nameUppercaseExplode = preg_split('/(?=[A-Z])/', $key);

                //MA - Sort according to array value length ASC
                usort($nameUppercaseExplode, function($a, $b) { $difference =  strlen($a) - strlen($b); return $difference ?: strcmp($a, $b); });

                //MA - If any value length is 1 it means it not proper camel case
                if(strlen($nameUppercaseExplode[0]) == 1){
                    $name =  ucfirst($key);
                    $NewKey = preg_replace('%([a-z])([a-z])%', '\1_\2',$name);
                    $response[$NewKey] = $value;
                }else{

                    //MA - studly predefined function to convert strnig into capitalize
                    $Studly_Text                = Str::snake($key);
                    $NewKey                     = preg_replace('/\B([A-Z])/', '_$1', $Studly_Text);
                    $response[$NewKey] = $value;
                }
            }

            return $response;
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function destroyAccessToken($data,$isRevoked =false){
        if(Auth::check()){
            if($isRevoked == true){
                $request->user()->token()->revoke();
            }else{
                Auth::user()->AauthAcessToken()->delete();
            }
            $response   = ['status'=>'success','code'=>200,'message'=>'Token removed'];
        }
        else{
            $response   = ['status'=>'failed','code'=>403,'message'=>'Access denied due to invalid credentials'];
        }
        return $response;
    }

    public static function AllowedQueryParam($queryParam){
        $query = array();
        if(isset($queryParam['search'])){

            $query['order_no'] = (isset($queryParam['search']['orderNo']) ? $queryParam['search']['orderNo'] : false );
            $query['product_code'] = (isset($queryParam['search']['productCode']) ? $queryParam['search']['productCode'] : false );

        }
        return $query;
    }
}
