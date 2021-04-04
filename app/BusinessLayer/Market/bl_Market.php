<?php
namespace App\BusinessLayer\Market;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\mdl_Company;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Exception;
use DB;

class bl_Market{

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

        $query            = $data['query'];
        $removeQueryParam = 0;
        if(isset($data['reqBody']['removeLazyLoading']) && $data['reqBody']['removeLazyLoading'] == 1){
            $removeQueryParam = 1;
        }

        if(!$id){
            if($removeQueryParam == 0){
                $searchVal      = (isset($query['search']) ? $query['search'] : '');
                $filter         = (isset($query['filter']) ? $query['filter'] : '');

                $sql = $this->_model->newQuery();

                if($searchVal != ''){
                    $sql->orWhere('market_place','like',"%$searchVal%");
                }
                $response = $sql->orderBy('id', 'DESC')->Paginate($query['length']);
                $perPage = $response->perPage();
                $total   = $response->total();

                foreach ($response->items() as $key => $value) {
                    $marketPlace = $value->market_place;
                    $productCount= DB::select("Select count(id) as cnt from tbl_Products where market_place ='$marketPlace'");
                    $response->items()[$key]->productCount = $productCount;
                }

                $response0 = array(
                    "draw" => intval($query['draw']),
                    "iTotalRecords" => (int)$response->perPage(),
                    "iTotalDisplayRecords" => (int)$response->total(),
                    'aaData'=>$response->items()
                );
                return $response0;
            }
            else{
                $response = $this->_model::get();
            }
        }
        else{
            $response = $this->_model::find($id);
        }



        if(blank($response)){
            $response = array('data'=>'No data found');
            return Helper::MakeResponse('error',$response);
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
        if(!is_numeric($id)){
            throw new Exception("Id Is not numeric", 404);
        }
        $this->_model::where('id', $id)->update($request['body'],$id);
        $response = $this->_model::find($id);
        return Helper::MakeResponse('ok',$response);

    }

}
