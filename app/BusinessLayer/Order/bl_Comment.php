<?php
namespace App\BusinessLayer\Order;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Exception;
use DB;
use App\Models\mdl_Company;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Auth;

class bl_Comment{

    private $config          = false;
    private $_model          = false;
    public  $successResponse = ['status'=>'success','Code'=>200,'message'=>'Your request has been processed successfully'];

    public function __construct($data){
        if(!$this->_model){
            $this->_model = Helper::LoadMdl($data['model']);
        }
     }


    public function create($data){
        $postedData   = $data['body'];
        $roleId       = $postedData['role_id'];
        unset($postedData['role_id']);
        if($roleId == 1){
            $postedBy = 'admin';
        }
        else{
            $postedBy = 'proxy';
        }
        $userId       = Auth::id();

        $postedData['created_by'] = $userId;
        $postedData['comment_from'] = $userId;
        $postedData['post_by'] = $postedBy;

        $response    = $this->_model::create($postedData);

        return $response;
    }


    public function show($data,$id=false){

        if(!$id){
            $sql      = $this->_model->newQuery();
            $response = $sql->get();
            return $response;
        }
        else
        {
            // $response = array('order'=>$orderData,'attachment'=>$attachmentData);

            return $response;
        }

    }


    public function remove($request,$id){

        if(!is_numeric($id)){
            throw new Exception("Id Is not numeric", 404);
        }
        $response = $this->_model::destroy($id);
        return Helper::MakeResponse('ok',$response);
    }

    public function update($request,$id){
        if(!is_numeric($id)){
            throw new Exception("Id Is not numeric", 404);
        }

       $this->_model['Order']::where('id', $id)->update($request['body']);

       $response = $this->_model['Order']::find($id);
    return $response;

    }

    public function showCount(){

        $orderStatusCount = DB::select("SELECT COUNT(status_code) AS cnt,st.name , st.id FROM tbl_Orders o RIGHT JOIN tbl_Order_Status st ON o.status_code = st.id GROUP BY st.id");
        return $orderStatusCount;
    }

    public function showCountUser(){
       $userId       = Auth::id();

       $response = $this->_model['Order_Status']::withCount([
        'orders',
        'orders as cnt' => function ($query) use($userId){
            $query->where('created_by', '=',$userId);
        }])
        ->get();

       return $response;
    }

    public function showOrderCommissionByUser(){
        $userId       = Auth::id();

        $response = DB::select("SELECT COALESCE(SUM(proxy_comm),0) AS comEarn FROM `tbl_Orders` WHERE created_by =$userId");

        return $response;
    }
}
