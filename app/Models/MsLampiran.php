<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsLampiran extends Model
{
    use HasFactory;
    protected $table = "ms_lampiran";
    protected $primaryKey = "lampiran_id";
    protected $guarded = [];
    public $timestamps = false;
}
