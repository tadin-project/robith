<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsesmenFile extends Model
{
    use HasFactory;
    protected $table = "asesmen_file";
    protected $primaryKey = "asf_id";
    protected $guarded = [];
    public $timestamps = false;

    public function asesmen(): BelongsTo
    {
        return $this->belongsTo(Asesmen::class, "as_id", "as_id");
    }

    public function subKriteria(): BelongsTo
    {
        return $this->belongsTo(MsSubKriteria::class, "msk_id", "msk_id");
    }
}
