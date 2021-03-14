<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Order_Status extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Order_Status';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('active', '=', 1);
    }

    public function orders(){
        return $this->belongsTo('\App\Models\mdl_Order', 'id', 'status_code');
    }
}
