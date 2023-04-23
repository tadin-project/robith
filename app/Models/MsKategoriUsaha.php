<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsKategoriUsaha extends Model
{
    use HasFactory;
    protected $table = "ms_kategori_usaha";
    protected $primaryKey = "mku_id";
    protected $guarded = [];
    public $timestamps = false;
}
