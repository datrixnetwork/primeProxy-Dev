<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Payment_Gateway extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Payment_Gateway';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('active', '=', 1);
    }
}
