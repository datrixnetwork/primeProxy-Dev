<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class mdl_User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable   = ['user_name','user_role_id','user_password'];
    protected $table      = 'tbl_Users';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('active', '=', 1);
    }

    protected $attributes = array(
        'created_by' => 'sys-admin'
      );

    public function getAuthPassword() {
        return $this->user_password;
    }

    public function userInfo(){
        return $this->hasOne('\App\Models\mdl_User_Info','user_id','id');
    }
    public function userAccountInfo(){
        return $this->hasOne('\App\Models\mdl_User_Payment_Info','user_id','id');
    }
    public function orders(){
        return $this->hasMany('\App\Models\mdl_Order','created_by','id');
    }
    public function AauthAcessToken(){
        return $this->hasMany('\App\Models\mdl_OauthAccessToken','user_id','id');
    }
}
