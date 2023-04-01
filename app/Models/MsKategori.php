<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MsKategori extends Model
{
    use HasFactory;
    protected $table = "ms_kategori";
    protected $primaryKey = "mk_id";
    protected $guarded = [];
    public $timestamps = false;

    public function subKategories(): HasMany
    {
        return $this->hasMany(MsSubKategori::class, "mk_id", "mk_id");
    }
}
