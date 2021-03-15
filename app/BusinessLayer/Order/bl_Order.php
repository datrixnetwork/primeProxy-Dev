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
                $qVal1          = (isset($query['search']) ? $query['search'] : '');
                $statusFilter   = (isset($query['status_code']) ? $query['status_code'] : '');
                $productFilter  = (isset($query['product_id']) ? $query['product_id'] : '');
                $createdByFilter= (isset($query['created_by']) ? $query['created_by'] : '');

                if($qVal1 == '' && $statusFilter =='' && $productFilter==''&& $createdByFilter==''){

                    $response = $this->_model['Order']::select('order_no','is_order_verified','is_comm_paid','store_order_no','id','active','status_code','seller_code','sold_by',DB::raw("concat('$orderImgUrl','/',order_no) AS imgPath"))
                    ->with('status')
                    ->with('orderAttachment')
                    ->with('proxyUser')
                    ->orderBy('id', 'DESC')
                    ->Paginate($query['length']);
                }
                else if($statusFilter !='' && $qVal1 == '' && $productFilter=='' && $createdByFilter==''){
                    $response = $this->_model['Order']::select('order_no','is_order_verified','is_comm_paid','store_order_no','id','active','status_code','seller_code','sold_by',DB::raw("concat('$orderImgUrl','/',order_no) AS imgPath"))
                    ->with('status')
                    ->with('orderAttachment')
                    ->with('proxyUser')
                    ->where('status_code',$statusFilter)
                    ->orderBy('id', 'DESC')
                    ->Paginate($query['length']);
                }
                else if($statusFilter =='' && $qVal1 == '' && $productFilter!='' && $createdByFilter==''){
                    $response = $this->_model['Order']::select('order_no','is_order_verified','is_comm_paid','store_order_no','id','active','status_code','seller_code','sold_by',DB::raw("concat('$orderImgUrl','/',order_no) AS imgPath"))
                    ->with('status')
                    ->with('orderAttachment')
                    ->with('proxyUser')
                    ->where('product_id',$productFilter)
                    ->orderBy('id', 'DESC')
                    ->Paginate($query['length']);
                }
                else if ($statusFilter =='' && $qVal1 == '' && $productFilter==''  && $createdByFilter!=''){
                    $response = $this->_model['Order']::select('order_no','is_order_verified','is_comm_paid','store_order_no','id','active','status_code','seller_code','sold_by',DB::raw("concat('$orderImgUrl','/',order_no) AS imgPath"))
                    ->with('status')
                    ->with('orderAttachment')
                    ->with('proxyUser')
                    ->where('created_by',$createdByFilter)
                    ->orderBy('id', 'DESC')
                    ->Paginate($query['length']);
                }
                else{
                    $response = $this->_model['Order']::select('order_no','is_order_verified','is_comm_paid','store_order_no','id','active','status_code','seller_code','sold_by',DB::raw("concat('$orderImgUrl','/',order_no) AS imgPath"))
                    ->with('status')
                    ->with('orderAttachment')
                    ->with('proxyUser')
                    ->where('order_no','like',"%$qVal1%")
                    ->orderBy('id', 'DESC')
                    ->Paginate($query['length']);
                }

            }
            else{
                // $response0     = $this->_model::select('product_img','product_code','id','proxy_comm','product_qty',DB::raw("'$productImgUrl' AS imgPath"))->get();
                $response      = $this->_model['Order']::select('order_no','is_order_verified','is_comm_paid','store_order_no','id','active','status_code','seller_code','sold_by',DB::raw("concat('$orderImgUrl','/',order_no) AS imgPath"))
                                ->with('status',function($query){
                                    return $query;
                                })
                                ->skip(0)->take(10)->get();
                }
        }
        else{

            $orderData = DB::select("SELECT o.order_no,o.store_order_no,o.seller_code,prd.product_code,o.is_order_verified,CONCAT(usin.first_name,'',usin.first_name) AS userP,
                                    o.is_comm_paid,o.store_order_no,o.sold_by,os.name,o.buyer_name,o.buyer_email,
                                    o.order_description,o.is_comm_paid,o.is_order_verified
                                    FROM tbl_Orders o , tbl_Order_Status os,tbl_Products prd,tbl_Users_Info usin WHERE prd.id = o.product_id AND usin.user_id = o.created_by AND o.status_code = os.id AND o.id =$id;");

            $attachmentData = DB::select("SELECT ot.attachment_note,ot.attachment,ast.name,'$orderImgUrl' as imgPath,CONCAT(usin.first_name,' ',usin.first_name) AS userP,ot.created_on
                                            FROM tbl_Order_Attachments ot , tbl_Attachment_Status ast ,tbl_Users_Info usin
                                            WHERE ot.status_id = ast.id
                                            AND usin.user_id = ot.created_by AND ot.order_id=$id ORDER BY ot.id DESC;");

            $response = array('order'=>$orderData,'attachment'=>$attachmentData);

            return $response;
        }


        $perPage = $response->perPage();
        $total   = $response->total();

        // dd($response->perPage());
        // echo "<pre>";
        // print_r($response);
        // echo "</pre>";
        $response0 = array(
            "draw" => intval($query['draw']),
            "iTotalRecords" => (int)$response->perPage(),
            "iTotalDisplayRecords" => (int)$response->total(),
            'aaData'=>$response->items()
        );
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
