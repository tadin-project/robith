<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMenus extends Model
{
    use HasFactory;
    protected $table = "group_menus";
    protected $primaryKey = "gm_id";
    protected $guarded = [];
    public $timestamps = false;
}
