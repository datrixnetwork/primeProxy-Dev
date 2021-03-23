<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use Route;
use DB;
use \stdClass;
use App\Models\mdl_Company;

class ctrl_SellerSheet extends Controller
{

    public function index(Request $request,$id)
    {


    }

    public function show($id)
    {
        $company       = new mdl_Company();
        $orderImgUrl = $company::select('order_img_url')->first();
        $orderImgUrl = $orderImgUrl['order_img_url'];

        $data = DB::select("SELECT p.market_place,p.product_name,o.order_no,p.product_price,o.is_comm_paid,
        (SELECT CONCAT('$orderImgUrl','',oa.attachment) FROM tbl_Order_Attachments oa WHERE o.id = oa.order_id AND oa.status_id = 2) AS reviewAttachment,
        (SELECT CONCAT('$orderImgUrl','',oa.attachment) FROM tbl_Order_Attachments oa WHERE o.id = oa.order_id AND oa.status_id = 3) AS feedbackAttachment,
        (SELECT CONCAT('$orderImgUrl','',oa.attachment) FROM tbl_Order_Attachments oa WHERE o.id = oa.order_id AND oa.status_id = 4) AS refundAttachment
        FROM tbl_Orders o,tbl_Products p ,`tbl_Sellers` seller
        WHERE o.product_id = p.id
        AND p.seller_code = seller.seller_code AND seller.id=$id;");

        $perPage = 1000;
        $total   = 1000;

        $response0 = array(
            "draw" => intval(0),
            "iTotalRecords" => $perPage,
            "iTotalDisplayRecords" => $total,
            'aaData'=>$data
        );
        return $response0;

    }
}
