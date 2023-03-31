<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MsGroups extends Model
{
    use HasFactory;
    protected $table = "ms_groups";
    protected $primaryKey = "group_id";
    protected $guarded = [];
    public $timestamps = false;

    public function users(): HasMany
    {
        return $this->hasMany(MsUsers::class, "group_id", "group_id");
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(MsMenus::class, "group_menus", "group_id", "menu_id");
    }
}
