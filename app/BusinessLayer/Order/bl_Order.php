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

       $checkOrderExist  = $this->_model['Order']::where('store_order_no',$orderData['store_order_no'])->orderBy('created_on', 'desc')->first();

       if(!blank($checkOrderExist)){
            $response = array('message'=>'Order Already Exist');
            return Helper::MakeResponse('error',$response);
        }

        $lastOrder = $this->_model['Order']::orderBy('created_on', 'desc')->first();
        if(blank($lastOrder) || $lastOrder->order_no == null){
            $code       =$startOrderNo;
            $orderNo    =$code;
        }else{
            $orderNo     = $lastOrder->order_no;
            $orderNo++;
        }
        // $this->checkReferlBonus(Auth::id());
        $orderData['order_no'] = $orderNo;
        $order    = $this->_model['Order']::create($orderData);
        $orderId  = $order->id;
        $attachmentData['order_id'] = $orderId;

        // $attachment    = $this->_model['Order_Attachment']::create($attachmentData);

        $response = array('Order'=>$order,'Attachment'=>$attachmentData);
        return $response;
    }


    public function show($data,$id=false){

        $query            = $data['query'];
        $removeQueryParam = 0;
        $company          = new mdl_Company();
        $orderImgUrl      = $company::select('order_img_url')->first();
        $orderImgUrl      = $orderImgUrl['order_img_url'];
        if(isset($data['reqBody']['removeLazyLoading']) && $data['reqBody']['removeLazyLoading'] == 1){
            $removeQueryParam = 1;
        }

        if(!$id){

            if($removeQueryParam == 0){
                $searchVal      = (isset($query['search']) ? $query['search'] : '');
                $filter         = (isset($query['filter']) ? $query['filter'] : '');
                $query['start']   = (isset($query['start']) && $query['start'] ==1 ? 0 : $query['start']);
                // DB::connection()->enableQueryLog();
                $sql = $this->_model['Order']->newQuery();

                $sql->with('product')->whereHas('product')->with('status')->with('orderAttachment')->with('proxyUser')->whereHas('proxyUser');
                if($filter != ''){
                    $sql->where($filter);
                }

                if($searchVal != ''){
                    $sql->where('store_order_no','like',"%$searchVal%");
                    $sql->orWhere('buyer_email','like',"%$searchVal%");
                    $sql->orWhere('buyer_name','like',"%$searchVal%");
                    $sql->orWhere('sold_by','like',"%$searchVal%");
                }

                // $response = $sql->where('is_arcieved',0)->orderBy('id', 'DESC')->Paginate($query['length']);
                $totalRecordswithFilter = $sql->count();

                $response = $sql->orderBy('id', 'DESC')
                ->skip($query['start'])
                ->take($query['length'])
                ->get();
                // dd(DB::getQueryLog());


                $totalRecords = $this->_model['Order']->with('product')->whereHas('product')->with('status')->with('orderAttachment')->with('proxyUser')->whereHas('proxyUser')->select('count(*) as allcount')->count();


                // $perPage = $response->perPage();
                // $total   = $response->total();
                if($totalRecordswithFilter == 0){
                    $totalRecordswithFilter = $totalRecords;
                }

                $response0 = array(
                    "draw" => intval($query['draw']),
                    "iTotalRecords" => $totalRecords,
                    "iTotalDisplayRecords" => $totalRecordswithFilter,
                    'aaData'=>$response->toArray()
                );

                // $response0 = array(
                //     "draw" => intval($query['draw']),
                //     "iTotalRecords" => (int)$response->perPage(),
                //     "iTotalDisplayRecords" => (int)$response->total(),
                //     'aaData'=>$response->items()
                // );

                return $response0;
            }
            else{

                $filter         = (isset($query['filter']) ? $query['filter'] : '');
                $dateRange      = (isset($query['date']) ? $query['date'] : '');
                // DB::connection()->enableQueryLog();

                $sql = $this->_model['Order']->newQuery();

                if($filter != ''){
                    $sql->where($filter);
                }

                if($dateRange != ''){
                    $dateRanges = $dateRange[array_keys($dateRange)[0]];
                    $dateInArray= explode(',',$dateRanges);

                    $sql->whereBetween(array_keys($dateRange)[0],$dateInArray);
                }

                $sql->with('product')->whereHas('product')->with('status')->with('orderAttachment')->with('proxyUser')->whereHas('proxyUser');
                $response = $sql->orderBy('id', 'DESC')->get();
                // $queries = DB::getQueryLog();
                // dd($queries);
                return $response;
            }
        }
            else
            {

                $orderData = DB::select("SELECT o.id,o.order_no,is_admin_comm_paid,is_order_rejected,o.store_order_no,
                o.seller_code,prd.product_name,prd.product_code,o.is_order_verified,us.is_refer AS isUserRefer,us.refer_by as referalBy,us.referel_count as referalCount,CONCAT(usin.first_name) AS userP,o.created_on,o.created_by,
                    o.is_comm_paid,o.status_code,o.store_order_no,o.sold_by,os.name,o.buyer_name,o.buyer_email,
                                    o.order_description,o.is_order_verified
                                    FROM tbl_Orders o , tbl_Order_Status os,tbl_Products prd,tbl_Users_Info usin,tbl_Users us WHERE prd.id = o.product_id AND usin.user_id = us.id AND usin.user_id = o.created_by AND o.status_code = os.id AND o.id =$id;");

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

        $orderStatusCount = DB::select("SELECT COUNT(status_code) AS cnt,st.name , st.id FROM tbl_Orders o INNER JOIN tbl_Users us ON o.created_by= us.id RIGHT JOIN tbl_Order_Status st ON o.status_code = st.id and o.is_arcieved = 0 GROUP BY st.id");
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

        $response = DB::select("SELECT
        COALESCE(SUM(a.dailyProductLimit),0) AS dailyProductLimit,
        COALESCE(SUM(a.monthlyProductLimit),0) AS monthlyProductLimit,
        CONCAT(COALESCE(SUM(a.commEarned),0),'$') AS commEarned ,
        CONCAT(COALESCE(SUM(a.totalCommPaid),0),'$') AS totalCommPaid,
        CONCAT(COALESCE(SUM(a.refrelBonus),0),'$') AS refrelBonus,
        CONCAT(COALESCE(SUM(a.paid_bonus),0),'$') AS refrelBonusPaid
        FROM
        (
            SELECT
            (SELECT SUM(COALESCE(IF(p.active = 1,p.product_daily_qty,0),0)) FROM tbl_Products p ) AS dailyProductLimit,
            (SELECT SUM(COALESCE(IF(p.active = 1,p.product_monthly_qty,0),0)) FROM tbl_Products p ) AS monthlyProductLimit,
            IFNULL((SELECT SUM(COALESCE(IF(o.is_comm_paid = 0,IF(o.status_code = 5,IF(o.is_order_verified=1,o.proxy_comm,0),0),0),0)) FROM tbl_Orders o,tbl_Users us WHERE  us.id=o.created_by AND o.created_by =$userId),0) AS commEarned,
            IFNULL((SELECT SUM(COALESCE(IF(o.is_comm_paid = 1,IF(o.status_code = 13,IF(o.is_order_verified=1,o.proxy_comm,0),0),0),0)) FROM tbl_Orders o,tbl_Users us WHERE us.id=o.created_by AND o.created_by =$userId),0) AS totalCommPaid,
            IFNULL((SELECT SUM(us.referel_bonus) FROM tbl_Orders o,tbl_Users us WHERE us.id=o.created_by AND o.created_by =$userId),0) AS refrelBonus,
            IFNULL((SELECT SUM(us.paid_bonus) FROM tbl_Orders o,tbl_Users us WHERE us.id=o.created_by AND o.created_by =$userId),0) AS paid_bonus
        ) a ;");
        return $response;
    }

    public function showCommissionForAdmin(){
        $response = DB::select("SELECT
        (SELECT COALESCE(SUM(o.proxy_comm+us.refer_bonus),0) AS totalEarnedCommission FROM tbl_Orders o,tbl_Users us WHERE  us.id=o.created_by and o.status_code=5 AND o.is_comm_paid=0) AS earnedCommission,
        (SELECT COALESCE(SUM(o.proxy_comm+us.refer_bonus),0) AS totalEarnedCommissionCurrentMonth FROM tbl_Orders o,tbl_Users us WHERE  us.id=o.created_by and status_code=5 AND is_comm_paid=0 AND DATE(created_on) = DATE(NOW())) AS earnedCommissionCurrentMonth,
        (SELECT COALESCE(SUM(proxy_comm),0) AS totalPaidCommission FROM tbl_Orders WHERE is_comm_paid=1) AS paidCommission,
        (SELECT COALESCE(SUM(proxy_comm),0) AS totalPaidCommissionCurrentMonth FROM tbl_Orders WHERE is_comm_paid=1 AND DATE(user_comm_paid_on) = DATE(NOW())) AS paidCommissionCurrentMonth");

        return $response;
    }

    // private function checkReferlBonus($userId){
    //     $userInfo       = $this->_model['User']::find($userId);
    //     $isUserRefered  = $userInfo->is_refer;
    //     $referBy        = $userInfo->refer_by;

    // }
}
