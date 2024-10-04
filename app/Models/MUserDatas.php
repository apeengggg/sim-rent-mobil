<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MUserDatas extends Model
{
    use HasFactory;
    protected $primaryKey = "user_data_id";
    protected $fillable = [
        "no_sim", "foto_sim", "user_id", "user_data_id",
        "created_at", "created_by", "updated_at", "updated_by"
    ];
}
