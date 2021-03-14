<?php
namespace App\BusinessLayer\Setting;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Exception;

class bl_Notification{

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

        if(!$id){
            $query       = $data['query'];
            if(isset($data['query']['notify_to'])){
                // $response = $this->_model::with('userInfo')->where('is_viewed',0)->where('notify_to',$data['query']['notify_to'])->get();
                $response = $this->_model::with('userInfo')
                ->whereHas('userInfo')
                ->orderBy('id', 'DESC')
                ->Paginate($query['length']);

                $perPage = $response->perPage();
                $total   = $response->total();

                $response0 = array(
                    "draw" => intval($query['draw']),
                    "iTotalRecords" => (int)$response->perPage(),
                    "iTotalDisplayRecords" => (int)$response->total(),
                    'aaData'=>$response->items()
                );

                return $response0;
            }

        }else{
            $response = $this->_model::find($id);
            return $response;

        }
        if(blank($response)){
            $message = array('message'=>'No Data foudn');
            return Helper::MakeResponse('error',$message);
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
        $this->_model::where('notify_to', $id)->update($request['body']);
        $response = $this->_model::find($id);

        return Helper::MakeResponse('ok',$response);

    }

}
