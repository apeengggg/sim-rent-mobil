<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MFasilitas;
use App\Models\MMerekMobils;
use App\Utils\ResponseUtil;
use App\Models\MMobils;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use App\Utils\StringUtil;
use Illuminate\Support\Facades\DB;

class MobilApiController extends Controller
{   

    public function comboAdd(){
        try{
            $fasilitas = MFasilitas::getAll();
            $merek_mobil = MMerekMobils::getAllCombo();

            $data = [
                'fasilitas' => $fasilitas,
                'merek_mobil' => $merek_mobil
            ];

            return ResponseUtil::Ok("Berhasil Get Data", $data);
        }catch(\Exception $e){
            return ResponseUtil::InternalServerError($e);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'merek_mobil_id' => 'max:100|string',
                'model' => 'max:100|string',
                'orderBy' => 'string|required|in:merek_mobil,merek_mobil_id',
                'dir' => 'min:3|max:4|string|in:asc,desc|required',
                'perPage' => 'numeric|required',
                'status' => 'numeric|in:1,0',
            ],[
                'merek_mobil_id.max' => 'Merek Mobil Maximal 100 Character',
                'merek_mobil_id.string' => 'Merek Mobil Must Be String',
                'model.max' => 'Model Maximal 100 Character',
                'model.string' => 'Model Must Be String',
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

            $results = MMobils::getAll($request);

            return ResponseUtil::Ok("Berhasil Get Data", $results);
        }catch(\Exception $e){
            return ResponseUtil::InternalServerError($e->getMessage());
        }
    }

    public function getById(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'mobil_id' => 'max:100|string',
            ],[
                'mobil_id.max' => 'Merek Mobil Maximal 100 Character',
                'mobil_id.string' => 'Merek Mobil Must Be String',
            ]);
            
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }

            $results = MMobils::getMobilByMobilId($id);

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
        $mobil_id =  Uuid::uuid4()->toString();

        try{
            $validator = Validator::make($request->all(), [
                'merek_mobil_id' => 'max:100|string|required',
                'model' => 'max:100|string|required',
                'no_plat' => 'string|required|max:12',
                'warna' => 'max:12|string|required',
                'description' => 'string|required',
                'tarif' => 'numeric|required',
                'foto' => 'required|file|mimes:jpg,jpeg,png,pdf|max:1024',
            ],[
                'merk_mobil_id.max' => 'Merk Mobil Maximal 100 Character',
                'merk_mobil_id.string' => 'Merk Mobil Must Be String',
                'merk_mobil_id.required' => 'Merk Mobil Is Required',
                'model.max' => 'Model Maximal 100 Character',
                'model.string' => 'model Must Be String',
                'model.required' => 'model Is Required',
                'no_plat.max' => 'No Plat Maximal 12 Character',
                'no_plat.string' => 'No Plat Must Be String',
                'no_plat.required' => 'No Plat Is Required',
                'warna.max' => 'Warna Maximal 12 Character',
                'warna.string' => 'Warna Must Be String',
                'warna.required' => 'Warna Is Required',
                'description.string' => 'Fasilitas Must Be String',
                'description.required' => 'Fasilitas Is Required',
                'tarif.string' => 'Tarif Must Be Numeric',
                'tarif.required' => 'Tarif Is Required',
                'foto.required' => 'Foto Mobil Is Required',
                'foto.file' => 'Foto Mobil Is Must Be A File',
                'foto.mimes' => 'Foto Mobil Not Allowed File Format',
                'foto.max' => 'Foto Mobil Max Size 1 MB',
            ]);

            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }

            $base64_file = null;

            if($request->hasFile('foto')){
                try{
                    $allowed_mime = ['image/jpg', 'image/jpeg', 'image/png'];
                    $file = $request->file('foto');
                    if($file->getSize() > 1048576 || !in_array($file->getMimeType(), $allowed_mime)){
                        return ResponseUtil::BadRequest('Gagal Upload Foto Mobil');
                    }
                    $fileName = $mobil_id . "_" . $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $base64_file = base64_encode(file_get_contents($file->getRealPath()));
                    $dataUrl = "data:{$mimeType};base64,{$base64_file}";
                    $base64_file = $dataUrl;
                }catch(\Exception $e){
                    return ResponseUtil::BadRequest('Gagal Upload Foto Mobil');
                }
            }

            $validatePlatNo = MMobils::getMobilFromNoPlat($request->no_plat);
            if($validatePlatNo){
                return ResponseUtil::BadRequest('No Plat Sudah Terpakai!');
            }

            DB::beginTransaction();

            $data = [
                'mobil_id' => $mobil_id,
                'merek_mobil_id' => $request->merek_mobil_id,
                'model' => $request->model,
                'no_plat' => $request->no_plat,
                'warna' => $request->warna,
                'description' => $request->description,
                'tarif' => $request->tarif,
                'foto' => $base64_file,
                'is_rent' => 2,
                'status' => 1,
                'created_by' => $request->attributes->get('user_id')
            ];


            MMobils::create($data);

            DB::commit();
            return ResponseUtil::Ok('Berhasil Tambah Data', null);
        }catch(\Exception $e){
            DB::rollback();
            return ResponseUtil::InternalServerError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        // dd($request->all());
        // return ResponseUtil::Ok('Ok', $request->all());
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
            $validator = Validator::make($request->all(), [
                'mobil_id' => 'max:100|string|required',
                'merek_mobil_id' => 'max:100|string|required',
                'model' => 'max:100|string|required',
                'no_plat' => 'string|required|max:12',
                'warna' => 'max:12|string|required',
                'description' => 'string|required',
                'tarif' => 'numeric|required',
                'foto' => 'file|mimes:jpg,jpeg,png,pdf|max:1024',
            ],[
                'mobil_id.max' => 'Mobil ID Maximal 100 Character',
                'mobil_id.string' => 'Mobil ID Must Be String',
                'mobil_id.required' => 'Mobil ID Is Required',
                'merk_mobil_id.max' => 'Merk Mobil Maximal 100 Character',
                'merk_mobil_id.string' => 'Merk Mobil Must Be String',
                'merk_mobil_id.required' => 'Merk Mobil Is Required',
                'model.max' => 'Model Maximal 100 Character',
                'model.string' => 'model Must Be String',
                'model.required' => 'model Is Required',
                'no_plat.max' => 'No Plat Maximal 12 Character',
                'no_plat.string' => 'No Plat Must Be String',
                'no_plat.required' => 'No Plat Is Required',
                'warna.max' => 'Warna Maximal 12 Character',
                'warna.string' => 'Warna Must Be String',
                'warna.required' => 'Warna Is Required',
                'description.string' => 'Fasilitas Must Be String',
                'description.required' => 'Fasilitas Is Required',
                'tarif.string' => 'Tarif Must Be Numeric',
                'tarif.required' => 'Tarif Is Required',
                'foto.file' => 'Foto Mobil Is Must Be A File',
                'foto.mimes' => 'Foto Mobil Not Allowed File Format',
                'foto.max' => 'Foto Mobil Max Size 1 MB',
            ]);
            
            // dd($request->file('foto'));

            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }
            
            $mobil_id = $request->mobil_id;
            
            $mobil = MMobils::where('mobil_id', $mobil_id)->first();
            if(!$mobil){
                return ResponseUtil::BadRequest("Mobil Tidak Ditemukan!");
            }

            $base64_file = null;

            if($request->hasFile('foto')){
                try{
                    $allowed_mime = ['image/jpg', 'image/jpeg', 'image/png'];
                    $file = $request->file('foto');
                    if($file->getSize() > 1048576 || !in_array($file->getMimeType(), $allowed_mime)){
                        return ResponseUtil::BadRequest('Gagal Upload Foto Mobil');
                    }
                    $fileName = $mobil_id . "_" . $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $base64_file = base64_encode(file_get_contents($file->getRealPath()));
                    $dataUrl = "data:{$mimeType};base64,{$base64_file}";
                    $base64_file = $dataUrl;
                }catch(\Exception $e){
                    return ResponseUtil::BadRequest('Gagal Upload Foto Mobil');
                }
            }

            $validatePlatNo = MMobils::getMobilFromNoPlat($request->no_plat, true, $mobil_id);
            if($validatePlatNo){
                return ResponseUtil::BadRequest('No Plat sudah terpakai!');
            }

            DB::beginTransaction();

            $data = [
                'mobil_id' => $mobil_id,
                'merek_mobil_id' => $request->merek_mobil_id,
                'model' => $request->model,
                'no_plat' => $request->no_plat,
                'warna' => $request->warna,
                'description' => $request->description,
                'tarif' => $request->tarif,
                'is_rent' => 2,
                'status' => 1,
                'created_by' => $request->attributes->get('user_id')
            ];

            if($base64_file != null){
                $data['foto'] = $base64_file;
            }


            $mobil->update($data);

            DB::commit();
            return ResponseUtil::Ok('Berhasil Edit Data', $data);
        }catch(\Exception $e){
            DB::rollback();
            return ResponseUtil::InternalServerError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'mobil_id' => 'max:100|string|required',
            ],[
                'mobil_id.max' => 'Mobil ID Maximal 100 Character',
                'mobil_id.string' => 'Mobil ID Must Be String',
                'mobil_id.required' => 'Mobil ID Is Required',
            ]);
    
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }
            
            $mobil = MMobils::getMobilByMobilId($request->mobil_id);
            if(!$mobil){
                return ResponseUtil::BadRequest('Mobil Tidak Ditemukan!');
            }

            if($mobil->is_rent == 1){
                return ResponseUtil::BadRequest('Mobil Sedang Masa Penyewaan!');
            }
            
            MMobils::deleteMobil($request->mobil_id, $request->attributes->get('user_id'));
            return ResponseUtil::Ok('Berhasil Hapus Data', null);
        }catch(\Exception $e){
            return ResponseUtil::InternalServerError($e->getMessage());
        }
    }
}
