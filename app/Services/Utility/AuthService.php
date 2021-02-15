<?php

namespace App\Services\Utility;

use Exception;

class AuthService{

    private $key             = "*&^%$@EWSDGHUI*&$%#RWFDFVGYHTU&*^%$#8555599-QWSCDFGHKIIUJYHR*";
    private $platFormKey     = ["1"=>"kso8GaEgUsdadh7LE796WeRt9P4Mn61Q0PoKEEWq","2"=>"iMYsA1STsMxJKitLoaKdszvQgt3rOvKJZhyEFGJy"];
    public  $validPlatform   = false;
    public  $clientInfo;
    public function __construct($request){
        $this->authentication($request);
    }


    private function authentication($request){
            $id  = ($request->header('x-clx-id') !=null ? $request->header('x-clx-id') : false);
            $key = ($request->header('x-clx-key')!=null ? $request->header('x-clx-key') : false);

            if(!$key || !$id ){
                return false;
            }
            if(!isset($this->platFormKey[$id]) || $this->platFormKey[$id] != $key){
                return false;
            }

            $this->clientInfo['Browser'] = $request->header('User-Agent'); //Get Browser
            $this->clientInfo['Ip']      =  $request->ip(); //Get Ip

            $this->validPlatform = true;

    }


}
