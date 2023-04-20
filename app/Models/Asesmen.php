<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asesmen extends Model
{
    use HasFactory;
    protected $table = "asesmen";
    protected $primaryKey = "as_id";
    protected $guarded = [];
    public $timestamps = true;

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, "tenant_id", "tenant_id");
    }

    public function asesmenDetail(): HasMany
    {
        return $this->hasMany(AsesmenDetail::class, "as_id", "as_id");
    }
}
