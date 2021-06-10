<?php

namespace App\Helpers;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\mdl_Notification as notifcation;
use Mail;

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

   public static function postNotification($request){
       $notification = new notifcation;
       return $notification::create($request);

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
                $deletedTokenId = $data->user()->token()->id;
                $data->user()->token()->delete();
                Auth::user()->AauthAcessToken()->where('id','=',$deletedTokenId)->delete();
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

        $query['draw']    = (isset($queryParam['draw']) ? $queryParam['draw'] : 1 );
        $query['start']   = (isset($queryParam['start']) && $queryParam['start']!=0 ? $queryParam['start'] : 1 );
        $query['length']  = (isset($queryParam['length']) ? $queryParam['length'] : 10 );

        if(isset($queryParam['search'])){
            $query['search'] = $queryParam['search']['value'];
        }
        if(isset($queryParam['status_code'])){
            $query['status_code'] = $queryParam['status_code'];
        }
        if(isset($queryParam['active'])){
            $query['active'] = $queryParam['active'];
        }
        if(isset($queryParam['created_by'])){
            $query['created_by'] = $queryParam['created_by'];
        }
        if(isset($queryParam['notify_to'])){
            $query['notify_to'] = $queryParam['notify_to'];
        }

        if(isset($queryParam['filter'])){
            $query['filter'] = $queryParam['filter'];
        }

        if(isset($queryParam['date'])){
            $query['date'] = $queryParam['date'];
        }

        if(isset($queryParam['referal'])){
            $query['referal'] = $queryParam['referal'];
        }
        if(isset($queryParam['user'])){
            $query['user'] = $queryParam['user'];
        }

        // if(isset($queryParam['is_login']) || isset($queryParam['is_verified'])){
            $isLogin    = (isset($queryParam['is_login']) ? $queryParam['is_login'] : '');
            $isVerified = (isset($queryParam['is_verified']) ? $queryParam['is_verified'] : '');
            $accountCategory= (isset($queryParam['category']) ? $queryParam['category'] : '');
            $isBlock= (isset($queryParam['is_block']) ? $queryParam['is_block'] : '');

            $query['otherParam'] = array('is_login'=>$isLogin,'is_verified'=>$isVerified,'category'=>$accountCategory,'isBlock'=>$isBlock);
        // }

        // if(isset($queryParam['search'])){

            //     $query['order_no'] = (isset($queryParam['search']['orderNo']) ? $queryParam['search']['orderNo'] : false );
        //     $query['product_code'] = (isset($queryParam['search']['productCode']) ? $queryParam['search']['productCode'] : false );

        // }
        return $query;
    }

    public static function SendCredintialEmail($sentTo,$bodyRequest){

        $companyModel = SELF::LoadMdl('Company');
        $emailModel   = SELF::LoadMdl('Email_Content');
        $company      = $companyModel::get()->first();
        $emailContent = $emailModel::where('email_name','credintials')->get()->first();

        $firstName    = $bodyRequest->userInfo['first_name'];
        $sentTo       = $bodyRequest->userInfo['user_email'];
        $userPhone    = $bodyRequest->userInfo['user_phone'];
        $passDecrypt  = $bodyRequest['pass_decrypt'];
        $user_name    = $bodyRequest['user_name'];
        $companyName  = $company['company_name'];
        $companyPhone = $company['company_phone'];
        $companyEmail = $company['company_email'];
        $emailSubject = $emailContent['email_subject'];
        $emailContent = $emailContent['email_content'];

        $emailContent = str_replace("[[name]]",$firstName,$emailContent);
        $emailContent = str_replace("[[password]]",$passDecrypt,$emailContent);
        $emailContent = str_replace("[[userName]]",$user_name,$emailContent);
        $emailContent = str_replace("[[userPhone]]",$userPhone,$emailContent);
        $emailContent = str_replace("[[companyName]]",$companyName,$emailContent);
        $emailContent = str_replace("[[companyPhone]]",$companyPhone,$emailContent);
        $emailContent = str_replace("[[companyEmail]]",$companyEmail,$emailContent);

        $data = array('name'=>$firstName,'pass'=>$passDecrypt,'userName'=>$user_name,'companyName'=>$companyName,'companyPhone'=>$companyPhone,'companyEmail'=>$companyEmail,'emailContent'=>$emailContent);

        Mail::send(['html'=>'credintial-mail'], $data, function($message) use($sentTo,$firstName,$companyEmail,$companyName,$emailSubject) {
           $message->to($sentTo, $firstName)->subject($emailSubject);
           $message->from($companyEmail,$companyName);
        });

    }

    public static function sendWelcomEmail($bodyRequest){
        $companyModel = SELF::LoadMdl('Company');
        $emailModel   = SELF::LoadMdl('Email_Content');
        $company      = $companyModel::get()->first();
        $emailContent = $emailModel::where('email_name','welcome')->get()->first();


        $firstName   = $bodyRequest['userInfo']['first_name'];
        $sentTo      = $bodyRequest['userInfo']['user_email'];
        $userPhone   = $bodyRequest['userInfo']['user_phone'];
        $userPhone   = $bodyRequest['userInfo']['user_phone'];
        $passDecrypt = $bodyRequest['user']['pass_decrypt'];
        $user_name   = $bodyRequest['user']['user_name'];
        $companyName = $company['company_name'];
        $companyPhone= $company['company_phone'];
        $companyEmail= $company['company_email'];
        $emailSubject = $emailContent['email_subject'];
        $emailContent = $emailContent['email_content'];

        $emailContent = str_replace("[[name]]",$firstName,$emailContent);
        $emailContent = str_replace("[[password]]",$passDecrypt,$emailContent);
        $emailContent = str_replace("[[userName]]",$user_name,$emailContent);
        $emailContent = str_replace("[[userPhone]]",$userPhone,$emailContent);
        $emailContent = str_replace("[[companyName]]",$companyName,$emailContent);
        $emailContent = str_replace("[[companyPhone]]",$companyPhone,$emailContent);
        $emailContent = str_replace("[[companyEmail]]",$companyEmail,$emailContent);

        $data = array('name'=>$firstName,'pass'=>$passDecrypt,'userName'=>$user_name,'userPhone'=>$userPhone,'companyName'=>$companyName,'companyPhone'=>$companyPhone,'companyEmail'=>$companyEmail,'emailContent'=>$emailContent);

        Mail::send(['html'=>'welcom-mail'], $data, function($message) use($sentTo,$firstName,$companyEmail,$companyName,$emailSubject){
           $message->to($sentTo, $firstName)->subject($emailSubject);
           $message->from($companyEmail,$companyName);
        });
    }
}
