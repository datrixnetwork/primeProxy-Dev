<?php
namespace App\BusinessLayer\Order;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Exception;
use App\Models\mdl_Order;
use Auth;

class bl_OrderAttachment{

    private $config          = false;
    private $_model          = false;
    public  $successResponse = ['status'=>'success','Code'=>200,'message'=>'Your request has been processed successfully'];

    public function __construct($data){
        if(!$this->_model){
            $this->_model = Helper::LoadMdl($data['model']);
        }
     }


    public function create($data,$id){

        if(!$data['reqBody']['attachment']){
            throw new Exception("No files attached", 404);
        }
        $userId    = Auth::id();
        $order     = new mdl_Order();
        $orderData = $order::find($id);
        $file         = $data['reqBody']['attachment'];
        $fileName     = $file->getClientOriginalName();
        $fileExtension= $file->getClientOriginalExtension();
        $newFileName  = $id.'-'.$orderData->order_no.'-'.$orderData->status_code.'.'.$fileExtension;

        if(is_dir(public_path().'/storage/images/uploads/order')){
            if(!$file->move(public_path('/storage/images/uploads/order'), $newFileName)){

            }
        }
        $data['reqBody']['attachment'] = $newFileName;
        $data['reqBody']['created_by'] = $userId;

        $response = $this->_model::create($data['reqBody']);
        return Helper::MakeResponse('ok',$response);
    }


    public function show($data,$id=false){

        if(!$id){
            $response = $this->_model::get();
        }else{
            $response = $this->_model::find($id);
        }
        if(blank($response)){
            throw new Exception("No data found", 404);
        }
        return Helper::MakeResponse('ok',$response);
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
        $this->_model::where('id', $id)->update($request['body']);
        $response = $this->_model::find($id);
        return Helper::MakeResponse('ok',$response);

    }

}
