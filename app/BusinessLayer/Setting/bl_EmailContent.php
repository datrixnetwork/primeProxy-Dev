<?php
namespace App\BusinessLayer\Setting;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Exception;

class bl_EmailContent{

    private $config          = false;
    private $_model          = false;
    public  $successResponse = ['status'=>'success','Code'=>200,'message'=>'Your request has been processed successfully'];

    public function __construct($data){
        if(!$this->_model){
            $this->_model = Helper::LoadMdl($data['model']);
        }
     }


    public function create($data){

        $response = $this->_model::updateOrCreate($data['body']);
        return Helper::MakeResponse('ok',$response);
    }


    public function show($data,$id=false){

        if(!$id){
            if(isset($data['query']['otherParam']['category']) && $data['query']['otherParam']['category'] != ''){
                $response = $this->_model::where('email_name',$data['query']['otherParam']['category'])->get()->first();
            }
            else{
                $response = $this->_model::get();
            }

        }else{
            $response = $this->_model::find($id);
        }
        if(blank($response)){
            throw new Exception("No data found", 404);
        }
        return $response;
    }


    public function remove($request,$id){

        if(!is_numeric($id)){
            throw new Exception("Id Is not numeric", 404);
        }
        $response = $this->_model::destroy($id);
        return Helper::MakeResponse('ok',$response);
    }

    public function update($request,$id){
        $this->_model::where('email_name', $id)->update($request['body']);
        $response = $this->_model::where('email_name',$id);
        return Helper::MakeResponse('ok',$response);

    }

}
