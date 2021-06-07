<?php
namespace App\BusinessLayer\Product;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Exception;
use DB;
use App\Models\mdl_Company;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Auth;

class bl_Product{

    private $config          = false;
    private $_model          = false;
    public  $successResponse = ['status'=>'success','Code'=>200,'message'=>'Your request has been processed successfully'];

    public function __construct($data){
        if(!$this->_model){
            $this->_model = Helper::LoadMdl($data['model']);
        }
     }


    public function create($data){

        $lastRecord    = $this->_model::orderBy('created_on', 'desc')->first();
        $company       = new mdl_Company();
        $productPrefix = $company::select('product_prefix')->first();
        $productPrefix = $productPrefix['product_prefix'];
        $userId        = Auth::id();
        if(blank($lastRecord)){
            $codePrefix = (isset($data['body']['product_code'])? $data['body']['product_code'] : $productPrefix.'-');
            $code       = "1000001";
            $productCode=$codePrefix.$code;
        }else{
            $prdCode     = $lastRecord->product_code;
            $code        = substr($prdCode,4,strlen($prdCode));
            $code++;
            $codePrefix = (isset($data['body']['product_code'])? $data['body']['product_code'] : $productPrefix.'-');
            $productCode=$codePrefix.$code;

        }
        $data['body']['product_code'] = $productCode;
        $data['body']['created_by'] = $userId;

        $response = $this->_model::create($data['body']);

        return Helper::MakeResponse('ok',$response);
    }


    public function show($data,$id=false){

        $query       = $data['query'];
        $sizeOfQuery = sizeof($query);
        $company       = new mdl_Company();
        $productImgUrl = $company::select('product_img_url')->first();
        $productImgUrl = $productImgUrl['product_img_url'];


        if(!$id){

            if($sizeOfQuery > 0){
                $searchVal      = (isset($query['search']) ? $query['search'] : '');
                $filter         = (isset($query['filter']) ? $query['filter'] : '');

                $sql = $this->_model::select('product_img','block_on','product_code','id','active','product_name','proxy_comm','product_keywords','product_price','created_on','market_place','total_product_limit','product_daily_limit','product_monthly_qty','product_daily_qty','product_price','seller_code','is_block','sold_by','asin','product_daily_qty',DB::raw("'$productImgUrl' AS imgPath"))->newQuery();

                if($searchVal != ''){
                    $sql->orWhere('product_code','like',"%$searchVal%");
                    $sql->orWhere('product_name','like',"%$searchVal%");
                    $sql->orWhere('seller_code','like',"%$searchVal%");
                    $sql->orWhere('market_place','like',"%$searchVal%");
                }
                if($filter != ''){
                    $sql->where($filter);
                }

                $response = $sql->orderBy('id', 'DESC')->Paginate($query['length']);

                $perPage = $response->perPage();
                $total   = $response->total();

                $response0 = array(
                    "draw" => intval($query['draw']),
                    "iTotalRecords" => (int)$response->perPage(),
                    "iTotalDisplayRecords" => (int)$response->total(),
                    'aaData'=>$response->items()
                );


            }
            else{

                $response0 = $this->_model::select('product_img','block_on','product_name','seller_code','product_code','is_block','id','active','proxy_comm','product_keywords','product_price','created_on','market_place','total_product_limit','product_daily_limit','product_monthly_qty','product_daily_qty','asin','product_price','sold_by','product_daily_qty',DB::raw("'$productImgUrl' AS imgPath"))
                ->where('product_daily_qty','>','0')
                ->where('is_block',0)->get();
                return Helper::MakeResponse('ok',$response0);
            }
        }
        else{
            $response0 = $this->_model::select('product_img','block_on','asin','product_name','seller_code','product_code','product_description','is_block','id','active','proxy_comm','product_keywords','product_price','created_on','market_place','total_product_limit','product_daily_limit','asin','product_monthly_qty','product_daily_qty','product_price','sold_by','product_daily_qty',DB::raw("'$productImgUrl' AS imgPath"))
                         ->find($id);
        }

        if(blank($response0)){
            // throw new Exception("No data found", 404);
            $response = array('data'=>'No data found');
            return Helper::MakeResponse('error',$response);
        }


        return $response0;
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

        if(isset($request['body']['attachment'])){
            $file         = $request['body']['attachment'];
            $fileName     = $file->getClientOriginalName();
            $newFileName  = $id.'-'.$fileName;

            if(is_dir(public_path().'/storage/images/uploads/products')){
                if(!$file->move(public_path('/storage/images/uploads/products'), $newFileName)){

                }
            }
            $request['body']['product_img'] = $newFileName;
            unset($request['body']['attachment']);
        }

        if(isset($request['body']['order_created']) && $request['body']['order_created'] == true){

            $productData      = DB::select("UPDATE tbl_Products SET product_monthly_qty = product_monthly_qty-1,product_daily_qty = product_daily_qty-1 where id =$id;");
        }else{

            $this->_model::where('id', $id)->update($request['body']);

        }

        $response = $this->_model::find($id);
        return Helper::MakeResponse('ok',$response);

    }

    public function showAllProduct($data){
        $response = $this->_model::get();

        return $response;
    }
}
