<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;

class ctrl_Order extends Controller
{

    private $_bl;                                 //Buisness Layer Name
    private $_layer         = 'bl_Order';      //Buisness Layer Name
    private $_buisness      = 'Order';        //Buisness Layer folder name
    private $_model         = 'Order,Order_Attachment,Order_Status';       //Model Name

    public function __construct(){
        $this->_bl     = $this->_buisness."\\".$this->_layer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //MA - Set Client info and request body data
        $data = Helper::manageRequestData($request);

        //Save models into data array
        $data['models']['model'] = $this->_model;

        //Load BL with models
        $buisnessLayer           =  Helper::LoadBl($this->_bl,$data['models']);

        $requestedData           = array('reqBody'=>$data['reqBody'],'query'=>array_filter($data['queryString']));

        //Load BL Function
        $response                = $buisnessLayer->show($requestedData);
        return $response;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Helper::MakeResponse('info');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //MA - Set Client info and request body data
        $data = Helper::manageRequestData($request,true);

        //Save models into data array
        $data['models']['model'] = $this->_model;

        //Load BL with models
        $buisnessLayer           =  Helper::LoadBl($this->_bl,$data['models']);

        $orderData       =array('product_id'=>$data['reqBody']['product_id']
                                ,'seller_code'=>$data['reqBody']['seller_code']
                                ,'proxy_comm'=>$data['reqBody']['proxy_comm']
                                ,'sold_by'=>$data['reqBody']['sold_by']
                                ,'buyer_name'=>$data['reqBody']['buyer_name']
                                ,'order_description'=>$data['reqBody']['order_description']
                                ,'status_code'=>$data['reqBody']['order_status']
                                ,'store_order_no'=>$data['reqBody']['store_order_no']
                                ,'buyer_email'=>$data['reqBody']['buyer_email']
                                );
        $attachmentData  =array(
                    'attachment_note'=>$data['reqBody']['attachment_note'],
                    'status_id'=>$data['reqBody']['order_status']
                );


        $requestedData           = array('body'=>$data['reqBody'],'orderData'=>$orderData,'attachmentData'=>$attachmentData);

        //Load BL Function
        $response                = $buisnessLayer->create($requestedData);
        return Helper::MakeResponse('ok',$response);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        //MA - Set Client info and request body data
        $data = Helper::manageRequestData($request);

        //Save models into data array
        $data['models']['model'] = $this->_model;

        //Load BL with models
        $buisnessLayer           =  Helper::LoadBl($this->_bl,$data['models']);

        $requestedData           = array('reqBody'=>$data['reqBody'],'query'=>array_filter($data['queryString']));

        //Load BL Function
        $response                = $buisnessLayer->show($requestedData,$id);
        return Helper::MakeResponse('ok',$response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        //MA - Set Client info and request body data
        $data = Helper::manageRequestData($request,true);

        //Save models into data array
        $data['models']['model'] = $this->_model;

        //Load BL with models
        $buisnessLayer           =  Helper::LoadBl($this->_bl,$data['models']);

        $requestedData           = array('body'=>$data['reqBody']);

        //Load BL Function
        $response                = $buisnessLayer->update($requestedData,$id);
        return Helper::MakeResponse('ok',$response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {

        //MA - Set Client info and request body data
        $data = Helper::manageRequestData($request);

        //Save models into data array
        $data['models']['model'] = $this->_model;

        //Load BL with models
        $buisnessLayer           =  Helper::LoadBl($this->_bl,$data['models']);

        //Load BL Function
        $response                = $buisnessLayer->remove($data,$id);
        return $response;

    }

    public function showOrdersCount(Request $request)
    {
        //MA - Set Client info and request body data
        $data = Helper::manageRequestData($request);

        //Save models into data array
        $data['models']['model'] = $this->_model;

        //Load BL with models
        $buisnessLayer           =  Helper::LoadBl($this->_bl,$data['models']);


        //Load BL Function
        $response                = $buisnessLayer->showCount();
        return Helper::MakeResponse('ok',$response);
    }

    public function showOrdersCountUser(Request $request)
    {
        //MA - Set Client info and request body data
        $data = Helper::manageRequestData($request);

        //Save models into data array
        $data['models']['model'] = $this->_model;

        //Load BL with models
        $buisnessLayer           =  Helper::LoadBl($this->_bl,$data['models']);


        //Load BL Function
        $response                = $buisnessLayer->showCountUser();
        return Helper::MakeResponse('ok',$response);
    }

    public function showOrderCommission(Request $request)
    {
        //MA - Set Client info and request body data
        $data = Helper::manageRequestData($request);

        //Save models into data array
        $data['models']['model'] = $this->_model;

        //Load BL with models
        $buisnessLayer           =  Helper::LoadBl($this->_bl,$data['models']);


        //Load BL Function
        $response                = $buisnessLayer->showOrderCommissionByUser();
        return Helper::MakeResponse('ok',$response);
    }

    public function showCommissionForAdmin(Request $request)
    {
        //MA - Set Client info and request body data
        $data = Helper::manageRequestData($request);

        //Save models into data array
        $data['models']['model'] = $this->_model;

        //Load BL with models
        $buisnessLayer           =  Helper::LoadBl($this->_bl,$data['models']);


        //Load BL Function
        $response                = $buisnessLayer->showCommissionForAdmin();
        return Helper::MakeResponse('ok',$response);
    }
}
