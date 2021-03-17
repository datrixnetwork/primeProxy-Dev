<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Email_Content extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Email_Content';
    protected $primaryKey = 'id';
    public $timestamps    = false;

}
