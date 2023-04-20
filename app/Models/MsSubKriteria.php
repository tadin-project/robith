<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MsSubKriteria extends Model
{
    use HasFactory;
    protected $table = "ms_sub_kriteria";
    protected $primaryKey = "msk_id";
    protected $guarded = [];
    public $timestamps = false;

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(MsKriteria::class, "mk_id", "mk_id");
    }

    public function asesmenDetail(): HasMany
    {
        return $this->hasMany(AsesmenDetail::class, "msk_id", "msk_id");
    }
}
