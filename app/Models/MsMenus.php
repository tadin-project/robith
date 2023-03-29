<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsMenus extends Model
{
    use HasFactory;
    protected $table = "ms_menus";
    protected $primaryKey = "menu_id";
    protected $guard = [];
    public $timestamps = false;
}
