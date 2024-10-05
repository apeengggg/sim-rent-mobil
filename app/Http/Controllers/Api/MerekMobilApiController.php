<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utils\ResponseUtil;
use App\Models\MMerekMobils;
use App\Utils\StringUtil;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class MerekMobilApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'merek_mobil' => 'max:100|string',
                'orderBy' => 'string|required|in:merek_mobil,merek_mobil_id',
                'dir' => 'min:3|max:3|string|in:asc,desc|required',
                'perPage' => 'numeric|required',
                'status' => 'numeric|in:1,0',
            ],[
                'merek_mobil.max' => 'Merek Mobil Maximal 100 Character',
                'merek_mobil.string' => 'Merek Mobil Must Be String',
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
                'status.in' => 'Status Is Not Valid Value'
            ]);
            
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }
    
            $results = MMerekMobils::getAll($request);

            return ResponseUtil::Ok("Berhasil Tambah Data", $results);
        }catch(\Exception $e){
            return ResponseUtil::InternalServerError($e->getMessage());
        }
    }

    public function getById(Request $request, $id)
    {
        try{
            $validator = Validator::make($request->all(), [
                'merek_mobil_id' => 'max:100|string',
            ],[
                'merek_mobil_id.max' => 'Merek Mobil ID Maximal 100 Character',
                'merek_mobil_id.string' => 'Merek Mobil ID Must Be String',
            ]);
            
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }
    
            $results = MMerekMobils::getMerekById($id);

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
            $merek_mobil_id =  Uuid::uuid4()->toString();

            $validator = Validator::make($request->all(), [
                'merek_mobil' => 'max:100|string|required',
            ],[
                'merek_mobil.max' => 'Merek Mobil Maximal 100 Character',
                'merek_mobil.string' => 'Merek Mobil Id Must Be String',
                'merek_mobil.required' => 'Merek Mobil Id Is Required',
            ]);

            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }
            
            $validate_duplicate = MMerekMobils::getMerekMobilsName($request->merek_mobil);
            if($validate_duplicate){
                return ResponseUtil::BadRequest("Merek Mobil Duplikat!");
            }
            

            $data = [
                'merek_mobil_id' => $merek_mobil_id,
                'merek_mobil' => $request->merek_mobil,
                'created_by' => $request->attributes->get('user_id')
            ];

            MMerekMobils::create($data);
            
            return ResponseUtil::Ok('Berhasil Tambah Data', null);
        }catch(\Exception $e){
            return ResponseUtil::InternalServerError($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
         //
         try{
            $merek_mobil_id = $request->merek_mobil_id;

            $validator = Validator::make($request->all(), [
                'merek_mobil' => 'max:100|string|required',
                'merek_mobil_id' => 'max:100|string|required',
                'status' => 'required|numeric|in:0,1',
            ],[
                'merek_mobil.max' => 'Merek Mobil Maximal 100 Character',
                'merek_mobil.string' => 'Merek Mobil Id Must Be String',
                'merek_mobil.required' => 'Merek Mobil Id Is Required',
                'merek_mobil_id.max' => 'Merek Mobil ID Maximal 100 Character',
                'merek_mobil_id.string' => 'Merek Mobil ID Id Must Be String',
                'merek_mobil_id.required' => 'Merek Mobil ID Id Is Required',
                'status.numeric' => 'Status Merek Must Be A Number',
                'status.in' => 'Status Merek Mobil Id Must Be 1 or 0',
                'status.required' => 'Status Merek Mobil Id Is Required',
            ]);

            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }

            $merek_mobil = MMerekMobils::where('merek_mobil_id', $request->merek_mobil_id)->first();
            if(!$merek_mobil){
                return ResponseUtil::BadRequest("Merek Mobil Tidak Ditemukan!");
            }
            
            $validate_duplicate = MMerekMobils::getMerekMobilsName($request->merek_mobil, $request->merek_mobil_id);
            if($validate_duplicate){
                return ResponseUtil::BadRequest("Merek Mobil Duplikat!");
            }            

            $data = [
                'merek_mobil_id' => $merek_mobil_id,
                'merek_mobil' => $request->merek_mobil,
                'status' => $request->status,
                'updated_by' => $request->attributes->get('user_id'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $merek_mobil->update($data);
            
            return ResponseUtil::Ok('Berhasil Ubah Data', null);
        }catch(\Exception $e){
            return ResponseUtil::InternalServerError($e);
        }
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
