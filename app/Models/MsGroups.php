<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsGroups extends Model
{
    use HasFactory;
    protected $table = "ms_groups";
    protected $primaryKey = "group_id";
    protected $guard = [];
    public $timestamps = false;
}
