<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MFasilitas extends Model
{
    
    use HasFactory;
    protected $primaryKey = "fasilitas_id";
    protected $fillable = [
        "fasilitas_id", "fasilitas",
        "created_at", "created_by", "updated_at", "updated_by"
    ];

    public static function getAll(){
        return DB::table('m_fasilitas as mf')
        ->select('mf.fasilitas_id', 'mf.fasilitas')
        ->get();
    }
}
