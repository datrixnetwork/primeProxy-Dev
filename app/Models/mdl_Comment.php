<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Comment extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Comments';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}
