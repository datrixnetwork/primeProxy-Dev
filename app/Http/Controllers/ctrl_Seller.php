<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;

class ctrl_Seller extends Controller
{

    private $_bl;                                 //Buisness Layer full Name we want to store
    private $_layer         = 'bl_Seller';      //Buisness Layer Name we want to load
    private $_buisness      = 'Seller';        //Buisness Layer folder name
    private $_model         = 'Seller';       //Model Name

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
        $data = Helper::manageRequestData($request,true);

        //Save models into data array
        $data['models']['model'] = $this->_model;

        //Load BL with models
        $buisnessLayer           =  Helper::LoadBl($this->_bl,$data['models']);

        $requestedData           = array('reqBody'=>$data['reqBody'],'query'=>$data['queryString']);

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

        $requestedData           = array('body'=>$data['reqBody']);

        //Load BL Function
        $response                = $buisnessLayer->create($requestedData);
        return $response;

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

        $requestedData           = array('reqBody'=>$data['reqBody'],'query'=>$data['queryString']);

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
        return $response;


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

    public function downloadfile(Request $request)
    {

        $file = "exports/".urldecode($request->file_name);

        if (((!is_null($request->file_name)) || (!empty($request->file_name))) && (File::exists($file))) {
            $headers = array(
                'Content-Type: application/vnd.ms-excel',
            );

            if (File::isFile($file)) {
                $file = File::get($file);

                return response()->download( $file, "product-list.csv", $headers);
            }
        }
}

}
