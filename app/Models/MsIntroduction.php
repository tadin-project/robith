<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MsIntroduction extends Model
{
    use HasFactory;
    protected $table = "ms_introduction";
    protected $primaryKey = "mi_id";
    protected $guarded = [];
    public $timestamps = false;
}
