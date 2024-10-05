<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MUsers;
use App\Models\MRoles;
use App\Models\MUserDatas;
use App\Utils\ResponseUtil;
use Illuminate\Support\Facades\Hash;
use App\Utils\JwtUtil;
use App\Utils\StringUtil;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{

    protected $jwtUtil;

    public function __construct(JwtUtil $jwtUtil)
    {
        $this->jwtUtil = $jwtUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string|min:5'
            ],[
                'username.required' => 'Username is Required',
                'password.required' => 'Password is Required',
                'password.string' => 'Password Must Be String',
                'password.min' => 'Password Must Be At Least 5 Character'
            ]);
    
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }

            $results = MUsers::getUserFromUsername($request->username);
            if($results == null){
                return ResponseUtil::Unauthorized("Login failed, either your User Id isn't registered in our system or your password is incorrect");
            }
            
            $compare = Hash::check($request->password, $results->password);
            if(!$compare){
                return ResponseUtil::Unauthorized("Login failed, either your User Id isn't registered in our system or your password is incorrect");
            }
            
            // $permission = MPermissions::getPermissionById($results->role_id);
            // // echo $permission;
            // if(!$permission){
            //     return ResponseUtil::Unauthorized("Login failed, either your User Id isn't registered in our system or your password is incorrect");
            // }
            
            // $object_permission = PermissionUtil::createObjectPermission($permission);
            
            $payload_data = [
                'user_id' => $results->user_id,
                'role_id' => $results->role_id
            ];
            
            $results = [
                'user_id' => $results->user_id,
                'name' => $results->nama,
                'role_id' => $results->role_id,
                'role_name' => $results->role_name,
                'no_sim' => $results->no_sim,
                'alamat' => $results->alamat,
                'telepon' => $results->telepon,
                'token' => $this->jwtUtil->generateToken($payload_data, $results->role_id),
            ];


            return ResponseUtil::Ok("Successfully Login", $results);
        }catch(\Exception $e){
            dd($e);
            return ResponseUtil::InternalServerError($e);
        }
    }

    public function register(Request $request)
    {
        try{
            $user_id =  Uuid::uuid4()->toString();

            $validator = Validator::make($request->all(), [
                'nama' => 'max:100|string|required',
                'username' => 'max:10|string|required',
                'alamat' => 'string|required',
                'no_telepon' => 'max:13|string|required',
                'foto_sim_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:1024',
                'no_sim' => 'required|string|min:16|max:16',
                'password' => 'required|string|min:8',
            ],[
                'nama.max' => 'Nama Maximal 100 Character',
                'nama.string' => 'Nama Must Be String',
                'nama.required' => 'Nama Is Required',
                'alamat.string' => 'Alamat Must Be String',
                'alamat.required' => 'Alamat Is Required',
                'username.max' => 'Username Maximal 10 Character',
                'username.string' => 'Username Must Be String',
                'username.required' => 'Username Is Required',
                'no_telepon.max' => 'No Telepon Maximal 13 Character',
                'no_telepon.string' => 'No Telepon Must Be A String',
                'no_telepon.required' => 'No Telepon Is Required',
                'foto_sim_file.required' => 'Foto SIM Is Required',
                'foto_sim_file.file' => 'Foto SIM Is Must Be A File',
                'foto_sim_file.mimes' => 'Foto SIM Not Allowed File Format',
                'foto_sim_file.max' => 'Foto SIM Max Size 1 MB',
                'password.required' => 'Password Is Required',
                'password.min' => 'Password Minimal 8 Character',
                'password.string' => 'Password Must Be A String',
                'no_sim.required' => 'No SIM Is Required',
                'no_sim.string' => 'No SIM Must Be A String',
                'no_sim.max' => 'No SIM Must Be 16 Character',
                'no_sim.min' => 'No SIM Must Be 16 Character',
            ]);

            if ($validator->fails()) {
                $errorMessages = StringUtil::ErrorMessage($validator);
                return ResponseUtil::BadRequest($errorMessages);
            }

            $base64_file = null;

            if($request->hasFile('foto_sim_file')){
                try{
                    $allowed_mime = ['image/jpg', 'image/jpeg', 'image/png'];
                    $file = $request->file('foto_sim_file');
                    if($file->getSize() > 1048576 || !in_array($file->getMimeType(), $allowed_mime)){
                        return ResponseUtil::BadRequest('Failed Upload Foto SIM');
                    }
                    $fileName = $user_id . "_" . $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $base64_file = base64_encode(file_get_contents($file->getRealPath()));
                    $dataUrl = "data:{$mimeType};base64,{$base64_file}";
                    $base64_file = $dataUrl;
                }catch(\Exception $e){
                    return ResponseUtil::BadRequest('Failed Upload Foto SIM');
                }
            }

            $path = 'foto_sim/'.$fileName;

            $validatePhoneNumberFormat = StringUtil::validateIndonesianPhoneNumber($request->no_telepon);
            if(!$validatePhoneNumberFormat){
                return ResponseUtil::BadRequest('Phone Number is Not Valid');
            }

            $validateUsername = MUsers::getUserFromUsername($request->username);
            if($validateUsername){
                return ResponseUtil::BadRequest('Username is exists');
            }

            $validatePhoneNumber = MUsers::getUserFromPhoneNumber($request->no_telepon);
            if($validatePhoneNumber){
                return ResponseUtil::BadRequest('Phone Number is exists');
            }

            $role = MRoles::getUserRoleId();
            if(!$role){
                return ResponseUtil::BadRequest('Role User Not Found');
            }

            $role_id = $role->role_id;

            DB::beginTransaction();

            $data = [
                'user_id' => $user_id,
                'role_id' => $role_id,
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'username' => $request->username,
                'telepon' => $request->no_telepon,
                'password' => bcrypt($request->password),
                'status' => 1,
                'created_by' => $user_id
            ];

            $data_user = [
                'user_data_id' => Uuid::uuid4()->toString(),
                'user_id' => $user_id,
                'no_sim' => $request->no_sim,
                'foto_sim' => $base64_file,
                'created_by' => $user_id
            ];

            MUsers::create($data);
            MUserDatas::create($data_user);

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
