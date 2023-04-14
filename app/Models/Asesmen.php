<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asesmen extends Model
{
    use HasFactory;
    protected $table = "asesmen";
    protected $primaryKey = "as_id";
    protected $guarded = [];
    public $timestamps = true;

    public function asemenDetail(): HasMany
    {
        return $this->hasMany(AsesmenDetail::class, "as_id", "as_id");
    }
}
