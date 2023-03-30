<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsUsers extends Model
{
    use HasFactory;
    protected $table = "ms_users";
    protected $primaryKey = "user_id";
    protected $guarded = [];
    public $timestamps = true;
}
