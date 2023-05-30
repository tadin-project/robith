<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingSubKriteriaRadar extends Model
{
    use HasFactory;
    protected $table = "setting_sub_kriteria_radar";
    protected $primaryKey = "sskr_id";
    protected $guarded = [];
    public $timestamps = false;
}
