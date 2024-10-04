<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MMerekMobils extends Model
{
    use HasFactory;
    protected $primaryKey = "merek_mobil_id";
    protected $fillable = [
        "merek_mobil_id", "merek_mobil", "status",
        "created_at", "created_by", "updated_at", "updated_by"
    ];

    public static function getMerekMobilsName($merek_mobil, $merek_mobil_id = null){
        $query =DB::table('m_merek_mobils as mm')
        ->select('mm.merek_mobil')
        ->where('mm.status', 1);

        if($merek_mobil_id != null){
            $query = $query->where('mm.merek_mobil_id', '<>' ,$merek_mobil_id);
        }

        return $query->whereRaw('LOWER(mm.merek_mobil) LIKE ?', ['%' . strtolower($merek_mobil) . '%'])
        ->first();
    }

    public static function getMerekById($id){
        return DB::table('m_merek_mobils as mm')
        ->select('mm.merek_mobil', 'mm.status', 'mm.merek_mobil_id')
        ->where('mm.merek_mobil_id', $id)
        ->first();
    }

    public static function getAll($param){
        $query = DB::table('m_merek_mobils as mm')
        ->select('mm.merek_mobil_id', 'mm.merek_mobil', 'mm.status', DB::raw('COUNT(mb.mobil_id) as jumlah'))
        ->leftJoin('m_mobils as mb', 'mb.merek_mobil_id', '=', 'mm.merek_mobil_id')
        ->where('mm.status', $param->status);

        if($param->merek_mobil){
            $query = $query->whereRaw('LOWER(mm.merek_mobil) LIKE ?', ['%' . strtolower($param->merek_mobil) . '%']);
        }

        if($param->orderBy){
            $dir = 'asc';
            if($param->dir && ($param->dir == 'asc' || $param->dir == 'desc')){
                $dir = $param->dir;
            }

            $query = $query->orderBy($param->orderBy, $dir);
        }

        $query = $query->groupBy('mm.merek_mobil_id', 'mm.merek_mobil', 'mm.status');

        // dd($query->toSql());

        return $query->paginate($param->perPage);

        
    }
}
