<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Notification extends Model
{
    use HasFactory;

    protected $guarded    = [];
    protected $table      = 'tbl_Notification';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    public function userInfo(){
        return $this->hasOne('\App\Models\mdl_User_Info','user_id','notify_to');
    }

}
