<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MsDimensi extends Model
{
    use HasFactory;
    protected $table = "ms_dimensi";
    protected $primaryKey = "md_id";
    protected $guarded = [];
    public $timestamps = false;

    public function kriteria(): HasMany
    {
        return $this->hasMany(MsKriteria::class, "md_id", "md_id");
    }
}
