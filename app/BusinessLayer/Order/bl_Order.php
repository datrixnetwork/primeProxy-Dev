<?php
namespace App\BusinessLayer\Order;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Exception;
use DB;
use App\Models\mdl_Company;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Auth;

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
        $orderData      = $data['orderData'];
        $attachmentData = $data['attachmentData'];
        $orderData['proxy_id'] = Auth::id();
        $orderData['created_by'] = Auth::id();
        $company       = new mdl_Company();
        $startOrderNo = $company::select('start_order_no')->first();
        $startOrderNo = $startOrderNo['start_order_no'];

        $lastOrder = $this->_model['Order']::orderBy('created_on', 'desc')->first();
        if(blank($lastOrder) || $lastOrder->order_no == null){
            $code       =$startOrderNo;
            $orderNo    =$code;
        }else{
            $orderNo     = $lastOrder->order_no;
            $orderNo++;
        }

        $orderData['order_no'] = $orderNo;
        $order    = $this->_model['Order']::create($orderData);
        $orderId  = $order->id;
        $attachmentData['order_id'] = $orderId;

        // $attachment    = $this->_model['Order_Attachment']::create($attachmentData);

        $response = array('Order'=>$order,'Attachment'=>$attachmentData);
        return $response;
    }


    public function show($data,$id=false){

        $query       = $data['query'];
        $sizeOfQuery = sizeof($query);
        $company     = new mdl_Company();
        $orderImgUrl = $company::select('order_img_url')->first();
        $orderImgUrl = $orderImgUrl['order_img_url'];

        if(!$id){

            if($sizeOfQuery > 0){
                $searchVal      = (isset($query['search']) ? $query['search'] : '');
                $filter         = (isset($query['filter']) ? $query['filter'] : '');

                $sql = $this->_model['Order']->newQuery();

                if($searchVal != ''){
                    $sql->orWhere('order_no','like',"%$searchVal%");
                    $sql->orWhere('buyer_email','like',"%$searchVal%");
                    $sql->orWhere('buyer_name','like',"%$searchVal%");
                    $sql->orWhere('store_order_no','like',"%$searchVal%");
                }
                $sql->with('product')->whereHas('product')->with('status')->with('orderAttachment')->with('proxyUser')->whereHas('proxyUser');
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
                return $response0;
            }
            else{
                $sql = $this->_model['Order']->newQuery();
                $sql->with('product')->whereHas('product')->with('status')->with('orderAttachment')->with('proxyUser')->whereHas('proxyUser');
                $response = $sql->orderBy('id', 'DESC')->get();
            }
        }
            else
            {
                $orderData = DB::select("SELECT o.id,o.order_no,is_order_rejected,o.store_order_no,o.seller_code,prd.product_code,o.is_order_verified,CONCAT(usin.first_name) AS userP,o.created_on,
                    o.is_comm_paid,o.status_code,o.store_order_no,o.sold_by,os.name,o.buyer_name,o.buyer_email,
                                    o.order_description,o.is_order_verified
                                    FROM tbl_Orders o , tbl_Order_Status os,tbl_Products prd,tbl_Users_Info usin WHERE prd.id = o.product_id AND usin.user_id = o.created_by AND o.status_code = os.id AND o.id =$id;");

            $attachmentData = DB::select("SELECT ot.id,ot.attachment_note,ot.attachment,ast.name,'$orderImgUrl' as imgPath,CONCAT(usin.first_name) AS userP,ot.created_on
                                            FROM tbl_Order_Attachments ot , tbl_Attachment_Status ast ,tbl_Users_Info usin
                                            WHERE ot.status_id = ast.id
                                            AND usin.user_id = ot.created_by AND ot.order_id=$id ORDER BY ot.id DESC;");

            $response = array('order'=>$orderData,'attachment'=>$attachmentData);

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

        $response = DB::select("SELECT concat(COALESCE(SUM(proxy_comm),0),'$') AS comEarn FROM `tbl_Orders` WHERE created_by =$userId");

        return $response;
    }
}
