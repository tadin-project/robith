<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use HasFactory;
    protected $table = "tenant";
    protected $primaryKey = "tenant_id";
    protected $guarded = [];
    public $timestamps = true;

    public function asesmen(): HasOne
    {
        return $this->hasOne(Asesmen::class, "tenant_id", "tenant_id");
    }
}
