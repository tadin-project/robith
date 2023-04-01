<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MsSubKategori extends Model
{
    use HasFactory;
    protected $table = "ms_sub_kategori";
    protected $primaryKey = "msk_id";
    protected $guarded = [];
    public $timestamps = false;

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(MsKategori::class, "mk_id", "mk_id");
    }
}
