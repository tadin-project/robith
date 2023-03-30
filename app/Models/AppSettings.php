<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    use HasFactory;
    protected $table = "app_settings";
    protected $primaryKey = "as_id";
    protected $guarded = [];
    public $timestamps = false;
}
