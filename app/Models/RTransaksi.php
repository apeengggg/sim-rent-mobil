<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RTransaksi extends Model
{
    use HasFactory;
    protected $table = 'r_transaksi';
    protected $primaryKey = "transaksi_id";
    protected $fillable = [
        "transaksi_id", "user_id", "mobil_id", "tanggal_mulai", "tanggal_selesai", "tanggal_kembali", "is_return", "total_tarif",
        "created_at", "created_by", "updated_at", "updated_by"
    ];

    public static function validateSim($transaksi_id){
        $query = DB::table('r_transaksi as r')
                    ->leftJoin('m_users as u', 'r.user_id', '=', 'u.user_id')
                    ->leftJoin('m_user_datas as ud', 'ud.user_id', '=', 'u.user_id')
                    ->select('ud.no_sim')
                    ->where('r.transaksi_id', $transaksi_id)
                    ->first();
        // dd([$query, $transaksi_id]);
        return $query;
    }

    public static function getAvailabelCar($param, $paginate = true){
        $start_date = $param->tanggal_mulai;
        $end_date = $param->tanggal_selesai;

        $query = DB::table('m_mobils as m')
                    ->leftJoin('r_transaksi as r', function($join) use ($start_date, $end_date) {
                        $join->on('m.mobil_id', '=', 'r.mobil_id')
                            ->where('r.tanggal_mulai', '<=', $end_date)
                            ->where('r.tanggal_selesai', '>=', $start_date)
                            ->where('r.is_return', '=', 0);
                    })
                    ->join('m_merek_mobils as mm', 'm.merek_mobil_id', '=', 'mm.merek_mobil_id')
                    ->select(
                        'm.*', 'mm.merek_mobil',
                        DB::raw('CASE WHEN r.transaksi_id IS NULL THEN 1 ELSE 0 END AS is_avail')
                    )->where('m.status', 1);
        
        if($param->merek_mobil_id){
            $query = $query->where('m.merek_mobil_id', $param->merek_mobil_id);
        }

        if($param->model){
            $query->whereRaw('LOWER(m.model) LIKE ?', ['%' . strtolower($param->model) . '%']);
        }

        if($param->mobil_id){
            $query = $query->where('m.mobil_id', "eaea1cf9-75fb-415b-b13d-95c396912166");
            $query = $query->whereNull('r.transaksi_id');
        }

        if($param->orderBy){
            $dir = 'asc';
            if($param->dir && ($param->dir == 'asc' || $param->dir == 'desc')){
                $dir = $param->dir;
            }

            $query = $query->orderBy($param->orderBy, $dir);
        }

        if($paginate){
            return $query->paginate($param->perPage);
        }
        // dd($query->toSql());

        return $query->first();
    }

    public static function getAllTransaction($param){
        $query = DB::table('r_transaksi as r')
                    ->join('m_mobils as m', 'r.mobil_id', '=', 'm.mobil_id')
                    ->join('m_merek_mobils as mm', 'mm.merek_mobil_id', '=', 'm.merek_mobil_id')
                    ->join('m_users as u', 'r.user_id', '=', 'u.user_id')
                    ->select('r.transaksi_id', 'u.nama', 'm.tarif', 'm.model', 'mm.merek_mobil', 'm.no_plat', 'r.tanggal_mulai', 'r.tanggal_selesai', 'r.tanggal_kembali', 'r.total_tarif', 'r.is_return');
        
        if($param->no_plat){
            $query = $query->where('m.no_plat', $param->no_plat);
        }

        if($param->transaksi_id){
            $query = $query->where('r.transaksi_id', $param->transaksi_id);
        }

        return $query->orderBy("tanggal_mulai", $param->dir)->orderBy("is_return", $param->dir)
                ->paginate($param->perPage);
        
    }
}
