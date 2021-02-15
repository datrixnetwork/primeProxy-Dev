<?php
namespace App\BusinessLayer\Users;

use DB;
use Illuminate\Http\Request;
use Exception;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Validator;

class bl_User{

    private $config          = false;
    private $_model          = false;
    public  $successResponse = ['status'=>'success','Code'=>200,'message'=>'Your request has been processed successfully'];

    public function __construct($data){
        if(!$this->_model){
            $this->_model = Helper::LoadMdl($data['model']);
        }
     }


    public function create($data){
        return Helper::MakeResponse('info');
    }


    public function show($data){
        $mainData = $data['reqBody'];

        if($this->validateLoginData($mainData)['status'] == false){
            throw new Exception("No username or password found", 403);
        }

        if(Auth::attempt($mainData)){
            $userId          = Auth::id();
            $userInfo        = $this->_model['User']::with('userInfo')->find($userId);
            $accessToken     = $userInfo->createToken('authToken')->accessToken;
            $data            = ['user'=>$userInfo,'accessToken'=>$accessToken];
        }else{
            throw new Exception("Wrong Credintials", 403);
        }
        return Helper::MakeResponse('ok',$data);
    }


    public function remove($request,$id){

    }

    public function update($request,$id){

    }

    private function validateLoginData($data){

        $validated = TRUE;

        $validator = Validator::make($data, [
            'user_name' => 'required',
            'user_password' => 'required'
        ]);
        if ($validator->fails()) {
            $validated = FALSE;
        }
        return array('status'=>$validated,'data'=>$validator);
    }


}
