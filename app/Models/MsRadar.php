<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsRadar extends Model
{
    use HasFactory;
    protected $table = "ms_radar";
    protected $primaryKey = "mr_id";
    protected $guarded = [];
    public $timestamps = false;
}
