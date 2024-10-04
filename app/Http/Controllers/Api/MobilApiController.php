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

            return ResponseUtil::Ok("Successfully Get Data", $data);
        }catch(\Exception $e){
            return ResponseUtil::InternalServerError($e);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
                'bahan_bakar' => 'max:10|string|required',
                'description' => 'string|required',
                'seat' => 'numeric|required',
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
                'bahan_bakar.max' => 'Bahan Bakar Maximal 10 Character',
                'bahan_bakar.string' => 'Bahan Bakar Must Be String',
                'bahan_bakar.required' => 'Bahan Bakar Is Required',
                'description.string' => 'Fasilitas Must Be String',
                'description.required' => 'Fasilitas Is Required',
                'seat.string' => 'Seat Must Be Numeric',
                'seat.required' => 'Seat Is Required',
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
                        return ResponseUtil::BadRequest('Failed Upload Foto Mobil');
                    }
                    $fileName = $mobil_id . "_" . $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $base64_file = base64_encode(file_get_contents($file->getRealPath()));
                    $dataUrl = "data:{$mimeType};base64,{$base64_file}";
                    $base64_file = $dataUrl;
                }catch(\Exception $e){
                    return ResponseUtil::BadRequest('Failed Upload Foto Mobil');
                }
            }

            $validatePlatNo = MMobils::getMobilFromNoPlat($request->no_plat);
            if($validatePlatNo){
                return ResponseUtil::BadRequest('No Plat is exists');
            }

            DB::beginTransaction();

            $data = [
                'mobil_id' => $mobil_id,
                'merek_mobil_id' => $request->merek_mobil_id,
                'model' => $request->model,
                'no_plat' => $request->no_plat,
                'warna' => $request->warna,
                'bahan_bakar' => $request->bahan_bakar,
                'description' => $request->description,
                'seat' => $request->seat,
                'tarif' => $request->tarif,
                'foto' => $base64_file,
                'is_rent' => 0,
                'status' => 1,
                'created_by' => $request->attributes->get('user_id')
            ];


            MMobils::create($data);

            DB::commit();
            return ResponseUtil::Ok('Successfully created', null);
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
