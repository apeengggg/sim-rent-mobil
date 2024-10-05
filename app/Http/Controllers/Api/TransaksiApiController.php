<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\RTransaksi;
use App\Utils\ResponseUtil;
use App\Utils\StringUtil;

class TransaksiApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPeminjaman(Request $request)
    {
        //
        try{
            $validator = Validator::make($request->all(), [
                'merek_mobil_id' => 'max:100|string',
                'model' => 'max:100|string',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date',
                'orderBy' => 'string|required|in:transaksi_id,is_return',
                'dir' => 'min:3|max:3|string|in:asc,desc|required',
                'perPage' => 'numeric|required',
            ],[
                'merek_mobil_id.max' => 'Merek Mobil Maximal 100 Character',
                'merek_mobil_id.string' => 'Merek Mobil Must Be String',
                'model.max' => 'Model Maximal 100 Character',
                'model.string' => 'Model Must Be String',
                'tanggal_mulai.required' => 'Tanggal Mulai Is Required',
                'tanggal_mulai.date' => 'Tanggal Mulai Must Be Date',
                'tanggal_selesai.required' => 'Tanggal Selesai Is Required',
                'tanggal_selesai.date' => 'Tanggal Selesai Must Be Date',
                'orderBy.string' => 'Order By Must Be String',
                'orderBy.in' => 'Order Is Not Valid Column',
                'orderBy.required' => 'Order is Required',
                'dir.min' => 'Dir Minimal 3 Character',
                'dir.max' => 'Dir Maximal 3 Character',
                'dir.string' => 'Dir Must Be String',
                'dir.in' => 'Dir Value Is Not Valid Value',
                'dir.required' => 'Dir is Required',
                'perPage.number' => 'PerPage Must Be Number',
                'perPage.required' => 'PerPage is Required',
            ]);
            
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }

            // dd($request->all());

            $results = RTransaksi::getAvailabelCar($request);

            return ResponseUtil::Ok("Berhasil Get Data", $results);
        }catch(\Exception $e){
            return ResponseUtil::InternalServerError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        try{
            $validator = Validator::make($request->all(), [
                'transaksi_id' => 'max:100|string|required',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date',
                'mobil_id' => 'required|string|max:100',
                'user_id' => 'required|string|max:100',
                'total_tarif' => 'required|numeric',
            ],[
                'transaksi_id.max' => 'Transaksi ID Maximal 100 Character',
                'transaksi_id.string' => 'Transaksi ID Must Be String',
                'tanggal_mulai.required' => 'Tanggal Mulai Is Required',
                'tanggal_mulai.date' => 'Tanggal Mulai Must Be Date',
                'tanggal_selesai.required' => 'Tanggal Selesai Is Required',
                'tanggal_selesai.date' => 'Tanggal Selesai Must Be Date',
                'mobil_id.string' => 'Mobil ID Must Be String',
                'mobil_id.max' => 'Mobil ID Max 100 Characters',
                'mobil_id.required' => 'Mobil ID Is Required',
                'user_id.string' => 'User ID Must Be String',
                'user_id.max' => 'User ID Max 100 Characters',
                'user_id.required' => 'User ID Is Required',
                'total_tarif.numeric' => 'Total Tarif Must Be Numeric',
                'total_tarif.required' => 'Total Tarif Is Required',
            ]);
            
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }

            $results = RTransaksi::getAvailabelCar($request, false);
            if(!$results){
                return ResponseUtil::BadRequest("Mobil Sedang Masa Penyewaan Pada Tanggal Yang Anda Pilih", $results);
            }

            $data = [
                'transaksi_id' => $request->transaksi_id,
                'user_id' => $request->user_id,
                'mobil_id' => $request->mobil_id,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_return' => 0,
                'total_tarif' =>  $request->total_tarif,
                'created_by' => $request->attributes->get('user_id')
            ];

            // dd($data);
            RTransaksi::create($data);


            return ResponseUtil::Ok("Berhasil Tambah Transaksi", null);
        }catch(\Exception $e){
            return ResponseUtil::InternalServerError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
