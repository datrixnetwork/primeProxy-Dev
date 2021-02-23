<?php
namespace App\BusinessLayer\Order;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Exception;

class bl_Order{

    private $config          = false;
    private $_model          = false;
    public  $successResponse = ['status'=>'success','Code'=>200,'message'=>'Your request has been processed successfully'];

    public function __construct($data){
        if(!$this->_model){
            $this->_model = Helper::LoadMdl($data['model']);
        }
     }


    public function create($data){

        $response = $this->_model::create($data['body']);
        return Helper::MakeResponse('ok',$response);
    }


    public function show($data,$id=false){
        $query       = $data['query'];
        $sizeOfQuery = sizeof($query);

        if(!$id){

            if($sizeOfQuery > 0){
                $response = $this->_model::where($query)->get();
            }
            else{
                $response = $this->_model::get();
            }
        }
        else{
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
