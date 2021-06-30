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
use App\Models\mdl_Company;

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

            $query       = $request['query'];
            $query['start']   = (isset($query['start']) && $query['start'] ==1 ? 0 : $query['start']);

            if(isset($request['reqBody']['active'])){
                $activeFlag  = (isset($request['reqBody']['active']) ? $request['reqBody']['active'] : 1);

                $response = $this->_model['User']::with('userInfo')->with('userAccountInfo')
                ->whereHas('userInfo')
                ->whereHas('userAccountInfo')
                ->where('is_verified',$activeFlag)
                ->orderBy('id', 'DESC')
                ->skip($query['start'])
                ->take($query['length'])
                ->get();

                $totalRecordswithFilter = $this->_model['User']::with('userInfo')->with('userAccountInfo')
                ->whereHas('userInfo')
                ->whereHas('userAccountInfo')->select('count(*) as allcount')->where('is_verified',$activeFlag)->count();
            }
            else{

                $query['otherParam'] = array_filter($query['otherParam']);
                $disableLazyLoad = 0;
                if(isset($request['reqBody']['removeLazyLoading']) && $request['reqBody']['removeLazyLoading'] == 1){
                    $disableLazyLoad = 1;
                }
                if(isset($query['otherParam']) && $disableLazyLoad ==0){
                    // if($query['otherParam']['is_verified'] == 3){
                    //     $query['otherParam']['is_verified'] == 0;
                    // }
                    $response = $this->_model['User']::with('userInfo')->with('userAccountInfo')
                    ->whereHas('userInfo')
                    ->whereHas('userAccountInfo')
                    ->where($query['otherParam'])
                    ->orderBy('id', 'DESC')
                    ->skip($query['start'])
                    ->take($query['length'])
                    ->get();
                    //->Paginate($query['length']);
                    $totalRecordswithFilter = $this->_model['User']::with('userInfo')->with('userAccountInfo')
                    ->whereHas('userInfo')
                    ->whereHas('userAccountInfo')->select('count(*) as allcount')->where($query['otherParam'])->count();
                }
                else{
                    if($disableLazyLoad == 1){
                        $response = $this->_model['User']::with('userInfo')->with('userAccountInfo')
                        ->whereHas('userInfo')
                        ->whereHas('userAccountInfo')
                        ->orderBy('id', 'DESC')
                        ->get();
                        return $response;
                    }else{
                        $response = $this->_model['User']::with('userInfo')->with('userAccountInfo')
                        ->whereHas('userInfo')
                        ->whereHas('userAccountInfo')
                        ->orderBy('id', 'DESC')
                        ->skip($query['start'])
                        ->take($query['length'])
                        ->get();

                        $totalRecordswithFilter = $this->_model['User']::with('userInfo')->with('userAccountInfo')
                        ->whereHas('userInfo')
                        ->whereHas('userAccountInfo')->select('count(*) as allcount')->count();
                    }

                }
            }

            $totalRecords = $this->_model['User']::with('userInfo')->with('userAccountInfo')
            ->whereHas('userInfo')
            ->whereHas('userAccountInfo')->select('count(*) as allcount')->count();

            // $perPage = $response->perPage();
            // $total   = $response->total();

            $response0 = array(
                "draw" => intval($query['draw']),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordswithFilter,
                'aaData'=>$response->toArray()
            );
            return $response0;

        }
        else{

            $response = $this->_model['User']::with('userInfo')
            ->with('userAccountInfo',function($query){
                return $query->with('paymentGateway');
            })
            ->with('orders')
            ->whereHas('userInfo')
            ->find($id);
            return $response;
        }

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

        if(isset($request['body']['is_verified'])){

            $flag     = $request['body']['is_verified'];

            $user     = $this->_model['User']::where($this->_model['User']->getKeyName(),$id)->update($request['body']);
            $userInfo = $this->_model['User_Info']::where('user_id',$id)->update(['is_user_verified'=>$flag]);
            $response = $this->show($request,$id);
            $isEmailValid  = filter_var($response->userInfo['user_email'], FILTER_VALIDATE_EMAIL );
            if($isEmailValid && $flag == 1){
                Helper::SendCredintialEmail($response->userInfo['user_email'],$response);
            }
            else if($isEmailValid && $flag == 0){
                $response['isCredintialEmailSent'] = array('status'=>'info','message'=>'User was de-activated');
            }
            else{
                $response['isCredintialEmailSent'] = array('status'=>'failed','message'=>'invalid email format');
            }

            return $response;
        }
        else if(isset($request['body']['user_password'])){
            $request['body']['user_password'] = md5($request['body']['user_password']);
            $this->_model['User']::where($this->_model['User']->getKeyName(),$id)->update($request['body']);
            $response = $this->_model['User']::find($id);
            return $response;
        }
        else if(isset($request['body']['refer_update']) && $request['body']['refer_update'] == 1){
            unset($request['body']['refer_update']);
            $updateQuery = DB::update('UPDATE tbl_Users set referel_bonus=referel_bonus+1 WHERE id ='.$id);
            $response = $this->_model['User']::find($id);
            return $response;
        }
        else if(isset($request['body']['refer_update']) && $request['body']['refer_update'] == 2){
            unset($request['body']['refer_update']);
            $updateQuery = DB::update('UPDATE tbl_Users set is_refer=0 WHERE id ='.$id);
            $response = $this->_model['User']::find($id);
            return $response;
        }
        else if(isset($request['body']['refer_update']) && $request['body']['refer_update'] == 3){
            unset($request['body']['refer_update']);
            $updateQuery = DB::update('UPDATE tbl_Users set paid_bonus=paid_bonus+referel_bonus,referel_count=0,referel_bonus=0 WHERE id ='.$id);
            $response = $this->_model['User']::find($id);
            return $response;
        }
        else if(isset($request['body']['active_update']) && $request['body']['active_update'] == 1){
            unset($request['body']['active_update']);
            $updateQuery = DB::update('UPDATE tbl_Users set active='.$request['body']['active'].' WHERE id ='.$id);
            $response = $this->_model['User']::find($id);
            return $response;
        }
        else{

            $first_name           = $request['body']['first_name'];
            $last_name            = $request['body']['last_name'];
            $gender               = $request['body']['gender'];
            $user_email           = $request['body']['user_email'];
            $user_phone           = $request['body']['user_phone'];
            $gateway_id           = $request['body']['gateway_id'];
            $acc_title            = $request['body']['acc_title'];
            $acc_number           = $request['body']['acc_number'];
            $social_profile_link1 = $request['body']['social_profile_link1'];
            $userInfo             = array('first_name'=>$first_name,'last_name'=>$last_name,'gender'=>$gender,'user_email'=>$user_email,'user_phone'=>$user_phone,'social_profile_link1'=>$social_profile_link1);
            $paymentGateway       = array('gateway_id'=>$gateway_id,'gateway_id'=>$gateway_id,'acc_title'=>$acc_title,'acc_number'=>$acc_number);


            $this->_model['User_Info']::where('user_id',$id)->update($userInfo);
           $r = $this->_model['User_Payment_Info']::where('user_id',$id)->update($paymentGateway);

            $response = $this->show($request,$id);
            return $response;
        }

    }


    public function login($data){
        $mainData = $data['reqBody'];

        if($this->validateLoginData($mainData)['status'] == false){
            $message = array('message'=>'Required User name or Password missing!');
            return Helper::MakeResponse('error',$message);
        }
        $role = $mainData['user_role_id'];
        unset($mainData['user_role_id']);

        if(Auth::attempt($mainData)){
            $userId          = Auth::id();

            $userInfo        = $this->_model['User']::select('user_name','id','user_role_id')->whereHas('userInfo',function($query){ return $query->where('is_user_verified',1); })->with('userInfo')->where('is_verified',1)->where('user_role_id',$role)->find($userId);
            if($role == 1 && blank($userInfo)){
                $userInfo        = $this->_model['User']::select('user_name','id','user_role_id')->whereHas('userInfo',function($query){ return $query->where('is_user_verified',1); })->with('userInfo')->where('is_verified',1)->where('user_role_id',3)->find($userId);
            }

            if(blank($userInfo)){
                $message = array('message'=>'Invalid Login! User is not active');
                return Helper::MakeResponse('error',$message);
            }

            if($role== 1){
                $accessToken     = $userInfo->createToken('authToken',['validate-admin'])->accessToken;
            }else{
                $accessToken     = $userInfo->createToken('authToken')->accessToken;
            }
            $this->_model['User']::where('id',$userId)->update(array('is_login'=>1));
            $company        = new mdl_Company();
            $companyDetails = $company::get()->first();

            $data            = ['Users'=>$userInfo,'accessToken'=>$accessToken,'companyDetails'=>$companyDetails];
        }else{
            $message = array('message'=>'Wrong Crendtials');
            return Helper::MakeResponse('error',$message);
        }
        return Helper::MakeResponse('ok',$data);
    }

    public function logout($data){
        $userId          = Auth::id();
        $this->_model['User']::where('id',$userId)->update(array('is_login'=>0));
        $response = Helper::destroyAccessToken($data); // Helper function

    }

    public function forgotPassword($data){

        if(isset($data['reqBody']['username'])){
            $response = $this->_model['User']::where('user_name','=',$data['reqBody']['username'])->first();
            return Helper::MakeResponse('ok',$response);
        }
        return Helper::MakeResponse('error');
    }

    public function editPassword($data,$id){

        if(isset($data['reqBody'])){

            $data['reqBody']['user_password'] = md5($data['reqBody']['user_password']);
            $response =  $this->_model['User']::where($this->_model['User']->getKeyName(),$id)->update($data['reqBody']);

            $response = $this->_model['User']::find($id);

            return Helper::MakeResponse('ok',$response);
        }

    }

    private function validateLoginData($data){

        $validated = TRUE;

        $validator = Validator::make($data, [
            'user_name' => 'required',
            'user_password' => 'required',
            'user_role_id' => 'required'
        ]);
        if ($validator->fails()) {
            $validated = FALSE;
        }
        return array('status'=>$validated,'data'=>$validator);
    }

    public function showUserActCount(){

        $userActCount = DB::select("
        SELECT
                COALESCE(SUM(a.activeUsers),0) AS activeUsers,
                COALESCE(SUM(a.pendingSignup),0) AS pendingSignup,
                COALESCE(SUM(a.pendingCustomerReview),0) AS pendingCustomerReview,
                COALESCE(SUM(a.commEarned),0) AS commEarned ,
                COALESCE(SUM(a.completedOrders),0) AS completedOrders
            FROM
            (

            SELECT
            (SELECT COUNT(id) AS activeUsers FROM tbl_Users WHERE active=1 AND is_verified = 1) AS activeUsers,
            (SELECT COUNT(id) AS activeUsers FROM tbl_Users WHERE active=1 AND is_verified = 0) AS pendingSignup,
            (SELECT COUNT(id) FROM tbl_Orders WHERE status_code=2 AND is_order_verified=1) AS pendingCustomerReview,
            IFNULL((SELECT SUM(COALESCE(IF(o.is_comm_paid = 0,IF(o.status_code = 5,IF(o.is_order_verified=1,1,0),0),0),0)) FROM tbl_Orders o  ),0) AS commEarned,
            (SELECT COUNT(id) FROM tbl_Orders WHERE status_code=13 AND is_order_verified=1 AND DATE(user_comm_paid_on) = DATE(NOW())) AS completedOrders

            ) a ;");
        return $userActCount;
    }

}
