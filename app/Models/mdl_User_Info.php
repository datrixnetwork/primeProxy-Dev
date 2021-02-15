<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_User_Info extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Users_Info';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('active', '=', 1);
    }

    public function user()
    {
        return $this->hasOne(mdl_User::class,'id','user_id');
    }
    public function paymentGatewayUser()
    {
        return $this->hasMany(mdl_Payment_Gateway::class,'user_id','user_id');
    }

}
