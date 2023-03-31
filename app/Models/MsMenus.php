<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MsMenus extends Model
{
    use HasFactory;
    protected $table = "ms_menus";
    protected $primaryKey = "menu_id";
    protected $guarded = [];
    public $timestamps = false;

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(MsGroups::class, "group_menus", "menu_id", "group_id");
    }
}
