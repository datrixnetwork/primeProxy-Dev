<?php


if(!function_exists('getModel')){

    function getModel($model){
        $model = "App\Models"."\\".'mdl_'.$model;
        return new $model();
    }

}


if(!function_exists('requestHandler')){

    function requestHandler($GetRequestInArray=array()){

        $response          = array();
        $ArrayParamOnly    = $GetRequestInArray;

        foreach ($ArrayParamOnly as $key => $value) {

            $nameUppercaseExplode = preg_split('/(?=[A-Z])/', $key);

            //MA - Sort according to array value length ASC
            usort($nameUppercaseExplode, function($a, $b) { $difference =  strlen($a) - strlen($b); return $difference ?: strcmp($a, $b); });

            //MA - If any value length is 1 it means it not proper camel case
            if(strlen($nameUppercaseExplode[0]) == 1){
                $name =  ucfirst($key);
                $NewKey = preg_replace('%([a-z])([A-Z])%', '\1_\2',$name);
                $response[$NewKey] = $value;
            }else{

                //MA - studly predefined function to convert strnig into capitalize
                $Studly_Text                = Str::studly($key);
                $NewKey                     = preg_replace('/\B([A-Z])/', '_$1', $Studly_Text);
                $response[$NewKey] = $value;
            }
        }

        return $response;
    }
}

if(!function_exists('responseHandler')){

    function responseHandler($GetResponseInArray){

        $response      = array();
        $param         = array();

        if(is_object($GetResponseInArray)){
            $GetResponseInArray = json_decode(json_encode($GetResponseInArray), true);
        }

        foreach ($GetResponseInArray as $key => $value) {
            $Text_In_Camel = Str::camel($key);
            $response[$Text_In_Camel] = $value;
        }
        $response = returnResponse($response);
        return $response;
    }

}

if(!function_exists('returnResponse')){

    function returnResponse($GetValuesForResponse=array()){
        $accessToken   = false;
        $expiryTime    = false;
        $status        = 'success';
        $code          = '200';
        $statusMessage = 'Your request has been processed successfully';

        if(isset($GetValuesForResponse['message']) && strlen($GetValuesForResponse['message'])>0){
            $statusMessage = $GetValuesForResponse['message'];
            unset($GetValuesForResponse['message']);
        }
        if(isset($GetValuesForResponse['status']) && strlen($GetValuesForResponse['status'])>0){
            $status = $GetValuesForResponse['status'];
            unset($GetValuesForResponse['status']);
        }
        if(isset($GetValuesForResponse['code']) && strlen($GetValuesForResponse['code'])>0){
            $code = $GetValuesForResponse['code'];
            unset($GetValuesForResponse['code']);
        }

        if(isset($GetValuesForResponse['accessToken']) && strlen($GetValuesForResponse['accessToken'])>0){
            $accessToken = $GetValuesForResponse['accessToken'];
            unset($GetValuesForResponse['accessToken']);
        }

        if(isset($GetValuesForResponse['expiryTime']) && strlen($GetValuesForResponse['expiryTime'])>0){
            $expiryTime = $GetValuesForResponse['expiryTime'];
            unset($GetValuesForResponse['expiryTime']);
        }


        // $object = new stdClass();
        // $object->status        = $status;
        // $object->statusCode    = $code;
        // $object->statusMessage = $statusMessage;
        // if(sizeof($GetValuesForResponse) > 0){
        //     $object->data          = $GetValuesForResponse;
        // }
        // $object->accessToken   = $accessToken;
        // $object->expiryTime    = $expiryTime;

        $response = array();
        $response =[
            'status'=> $status,
            'code'=> $code,
            'message'=> $statusMessage,
            'data'=> $GetValuesForResponse,
            'accessToken'=>$accessToken,
            'expiryTime'=>$expiryTime
        ];

        return $response;
    }
}


if(!function_exists('woocom')){

    function woocom($string){

        $wooObject = "Codexshaper\WooCommerce\Facades$string";

        return new $wooObject();
    }
}

if(!function_exists('manageRequestData')){

    function manageRequestData($request,$setRequestHanlder=False){
        $data = [];

        $ClientInfo = (isset($request['ClientInfo']) ? $request['ClientInfo'] : 'No Client Info Recorded');
        unset($request['ClientInfo']);

        $StoreInfo = (isset($request['StoreInfo']) ? $request['StoreInfo'] : 'No Store info');
        unset($request['StoreInfo']);

        if($setRequestHanlder == True){
            $requestBody = requestHandler($request->all());
        }else{
            $requestBody = $request->all();
        }
        $queryParam = $request->query();

        $data = [
            'reqBody'    => $requestBody,
            'queryString'=> $queryParam,
            'clientInfo' => $ClientInfo,
            'StoreInfo'  => $StoreInfo
        ];
        return $data;
    }
}

if(!function_exists('destroyAccessToken')){

    function destroyAccessToken($data,$isRevoked =false){
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

}


if(!function_exists('createUserAccessToken')){
    function createUserAccessToken($data){

        if(Auth::attempt($data)){
            $accessToken = Auth::User()->createToken('authToken')->accessToken;
            $userId      = Auth::id();
            $response    = ['status'=>'success','code'=>'200','userId'=>$userId,'accessToken'=>$accessToken];
        }else{
            $response    = ['status'=>'failed','code'=>'401','message'=>'Wrong usercredintials'];
        }
        return $response;
    }

}


if(!function_exists('getBusinessLayer')){
    function getBusinessLayer($Layer,$param=false,$param2=false){
        $layerName = "App\BusinessLayer\\$Layer";

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
}

if(!function_exists('ktDatatableQueryHandle')){

    function ktDatatableQueryHandle($queryString){
        $data=array();
        if(isset($queryString['pagination'])){

            $data             = (isset($queryString['pagination']) ?$queryString['pagination'] : array() );
            $data['per_page'] = (isset($queryString['pagination']['perpage']) ? $queryString['pagination']['perpage'] : 10 );
            unset($data['perpage']);
        }

        if(isset($queryString['query'])){

            if(isset($queryString['query']['Category'])){
                $data['category']=$queryString['query']['Category'];
            }

            if(isset($queryString['query']['stock_status'])){
                $data['stock_status']=$queryString['query']['stock_status'];
            }

            if(isset($queryString['query']['generalSearch'])){
                $data['search']=$queryString['query']['generalSearch'];
            }
            if(isset($queryString['query']['search'])){
                $data['search']=$queryString['query']['search'];
            }
            if(isset($queryString['query']['customer'])){
                $data['customer']=$queryString['query']['customer'];
            }
        }
        else if(isset($queryString['search'])){
            $data['search']=$queryString['search'];
        }
        $data['orderby'] ='id';
        $data['order']   ='desc';
        return $data;
    }

}


// if(!function_exists('checkStoreAccess')){

//     function checkStoreAccess($userId,$storeId){
//         $isStoreValid = DB::table('tbl_Account_Users')
//                             ->leftJoin('tbl_Stores', 'tbl_Account_Users.Account_Id', '=', 'tbl_Stores.Account_Id')
//                             ->leftJoin('tbl_Store_Settings', 'tbl_Store_Settings.Store_Id', '=', 'tbl_Stores.Store_Id')
//                             ->where('tbl_Stores.Store_Id','=',$storeId)
//                             ->where('tbl_Account_Users.User_Id','=',$userId)->get()->toArray();

//         $storeSize = sizeof($isStoreValid);
//         if($storeSize <=0){
//             $response=array('status'=>'failed','code'=>404,'message'=>'Not Allowed to use this store');
//         }else{
//             $response=$isStoreValid;
//         }
//         return $response;
//     }

// }




if(!function_exists('validateWooConfig')){

    function validateWooConfig($data){

        $Domain         =(isset($data['Domain']) ? $data['Domain'] : false);
        $consumerKey    =(isset($data['consumerKey']) ? $data['consumerKey'] : false);
        $consumerSecret =(isset($data['consumerSecret']) ? $data['consumerSecret'] : false);

        if(!$Domain || !$consumerKey || !$consumerSecret){
            $response = array('status'=>'failed','code'=>401,'message'=>'Some configuration setting are missing');
        }
        else{
            $response = array('status'=>'success','code'=>200,'message'=>'Configuration validation success');
        }

        return $response;
    }
}

if(!function_exists('activateWooCommerce')){

    function activateWooCommerce($domain,$key,$secret){

       return $woocommerce = new Client(
            $domain,
            $key,
            $secret,
            [
                'wp_api' => true,
                'version' => 'wc/v3'
            ]
        );
    }
}
