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
        "mobil_id", "merek_mobil_id", "model", "no_plat", "warna", "bahan_bakar", "description", "seat", "tarif", "is_rent", "status", "foto",
        "created_at", "created_by", "updated_at", "updated_by"
    ];

    public static function getMobilFromNoPlat($no_plat){
        return DB::table("m_mobils")->where("no_plat", $no_plat)->first();
    }
}
