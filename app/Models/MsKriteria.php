<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MsKriteria extends Model
{
    use HasFactory;
    protected $table = "ms_kriteria";
    protected $primaryKey = "mk_id";
    protected $guarded = [];
    public $timestamps = false;

    public function dimensi(): BelongsTo
    {
        return $this->belongsTo(MsDimensi::class, "md_id", "md_id");
    }

    public function subKriteria(): HasMany
    {
        return $this->hasMany(MsSubKriteria::class, "mk_id", "mk_id");
    }
}
