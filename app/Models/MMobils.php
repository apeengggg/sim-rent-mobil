<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MMobils extends Model
{
    use HasFactory;
    protected $primaryKey = "mobil_id";
    protected $fillable = [
        "mobil_id", "merek_mobil_id", "model", "no_plat", "warna", "description", "tarif", "is_rent", "status", "foto",
        "created_at", "created_by", "updated_at", "updated_by"
    ];


    public static function deleteMobil($mobil_id, $who){
        return DB::table('m_mobils as m')->where('mobil_id', $mobil_id)
                ->update([
                    'status' => 0, 
                    'updated_by' => $who,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
    }

    public static function getMobilFromNoPlat($no_plat){
        return DB::table("m_mobils")->where("no_plat", $no_plat)->first();
    }

    public static function getMobilByMobilId($mobil_id){
        return DB::table("m_mobils")->where("mobil_id", $mobil_id)->first();
    }

    public static function getAll($param){
        $query = DB::table('m_mobils as m')
            ->select('m.*', 'mm.merek_mobil')
            ->leftJoin('m_merek_mobils as mm', 'mm.merek_mobil_id', '=', 'm.merek_mobil_id')
            ->where('m.status', $param->status);

        if($param->merek_mobil_id){
            $query = $query->where('mm.merek_mobil_id', $param->merek_mobil_id);
        }

        if($param->model){
            $query = $query->whereRaw('LOWER(m.model) LIKE ?', ['%' . strtolower($param->model) . '%']);
        }

        if($param->is_rent){
            $query = $query->where('m.is_rent', (int)$param->is_rent);
        }

        if($param->orderBy){
            $dir = 'asc';
            if($param->dir && ($param->dir == 'asc' || $param->dir == 'desc')){
                $dir = $param->dir;
            }

            $query = $query->orderBy($param->orderBy, $dir);
        }

        return $query->paginate($param->perPage);
    }
}
