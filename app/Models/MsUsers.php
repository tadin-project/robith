<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MsUsers extends Model
{
    use HasFactory;
    protected $table = "ms_users";
    protected $primaryKey = "user_id";
    protected $guarded = [];
    public $timestamps = true;

    public function group(): BelongsTo
    {
        return $this->belongsTo(MsGroups::class, "group_id", "group_id");
    }

    public function asesmenDetail(): HasMany
    {
        return $this->hasMany(AsesmenDetail::class, "user_id", "user_id");
    }
}
