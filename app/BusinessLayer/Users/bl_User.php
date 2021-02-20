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
        $data['body']['user_password'] = md5($data['body']['user_password']);
        $response = $this->_model['User']::updateOrCreate($data['body']);
        return Helper::MakeResponse('ok',$response);
    }

    public function show($request,$id=false){
        if(!$id){
            $response = $this->_model['User']::get();
        }else{
            $response = $this->_model['User']::find($id);
        }
        if(blank($response)){
            throw new Exception("No Data found", 404);
        }

        return Helper::MakeResponse('ok',$response);
    }

    public function remove($request,$id){
        if(!is_numeric($id)){
            throw new Exception("Id Is not numeric", 404);
        }
        $response = $this->_model['User']::destroy($id);
        return Helper::MakeResponse('ok',$response);
    }

    public function update($request,$id){
        if(!is_numeric($id)){
            throw new Exception("Id Is not numeric", 404);
        }
        $request['body']['user_password'] = md5($request['body']['user_password']);
        $this->_model['User']::where($this->_model['User']->getKeyName(),$id)->update($request['body']);
        $response = $this->_model['User']::find($id);
        return Helper::MakeResponse('ok',$response);

    }


    public function login($data){
        $mainData = $data['reqBody'];

        if($this->validateLoginData($mainData)['status'] == false){
            throw new Exception("No username or password found", 403);
        }

        if(Auth::attempt($mainData)){
            $userId          = Auth::id();
            $userInfo        = $this->_model['User']::with('userInfo')->find($userId);

            if($mainData['user_role_id'] == 1){
                $accessToken     = $userInfo->createToken('authToken',['validate-admin'])->accessToken;
            }else{
                $accessToken     = $userInfo->createToken('authToken')->accessToken;
            }

            $data            = ['Users'=>$userInfo,'accessToken'=>$accessToken];
        }else{
            throw new Exception("Wrong Credintials", 403);
        }
        return Helper::MakeResponse('ok',$data);
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
