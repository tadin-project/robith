<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConvertionValue extends Model
{
    use HasFactory;
    protected $table = "convertion_value";
    protected $primaryKey = "cval_id";
    protected $guarded = [];
    public $timestamps = false;
}
