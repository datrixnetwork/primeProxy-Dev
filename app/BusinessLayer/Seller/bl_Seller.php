<?php
namespace App\BusinessLayer\Seller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\mdl_Company;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Exception;
use DB;

class bl_Seller{

    private $config          = false;
    private $_model          = false;

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

        $query       = $data['query'];
        $sizeOfQuery = sizeof($query);
        $company         = new mdl_Company();
        $sellerLoadSheet = $company::select('seller_load_sheet_url')->first();
        $sellerLoadSheet = $sellerLoadSheet['seller_load_sheet_url'];

        if(!$id){

            if($sizeOfQuery > 0){
                $qVal1       = (isset($query['search']) ? $query['search'] : '');
                if($qVal1 == ''){
                    $response = $this->_model::select('seller_code','seller_email','seller_phone','seller_url','seller_load_sheet','id','active',DB::raw("'$sellerLoadSheet' AS loadSheetUrl"))
                    ->Paginate($query['length']);
                }else{
                    $response = $this->_model::select('seller_code','seller_email','seller_phone','seller_url','seller_load_sheet','id','active',DB::raw("'$sellerLoadSheet' AS loadSheetUrl"))
                    ->where('seller_code','like',"%$qVal1%")
                    ->Paginate($query['length']);
                }

            }
            else{

                $response      = $this->_model::select('product_img','product_code','id','active','proxy_comm','product_qty',DB::raw("'$productImgUrl' AS imgPath"))->skip($query['start'])->take($query['length'])->get();
            }
            $perPage = $response->perPage();
            $total   = $response->total();
            $response0 = array(
                "draw" => intval($query['draw']),
                "iTotalRecords" => (int)$response->perPage(),
                "iTotalDisplayRecords" => (int)$response->total(),
                'aaData'=>$response->items()
            );
            $data0 = array($response,$response0);

            return $response0;

        }
        else{
            return $response = $this->_model::find($id);
        }

        if(blank($response)){
            // throw new Exception("No data found", 404);
            $response = array('data'=>'No data found');
            return Helper::MakeResponse('error',$response);
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
        $this->_model::where('id', $id)->update($request['body'],$id);
        $response = $this->_model::find($id);
        return Helper::MakeResponse('ok',$response);

    }

}
