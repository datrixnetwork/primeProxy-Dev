<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Order extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Orders';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('active', '=', 1);
    }

    public function orderAttachment()
    {
        return $this->hasMany(mdl_Order_Attachment::class,'order_id','id');
    }

    public function status()
    {
        return $this->hasOne(mdl_Order_Status::class,'id','status_code');
    }

    public function proxyUser()
    {
        return $this->hasOne(mdl_User_Info::class,'user_id','created_by');
    }

    public function product()
    {
        return $this->hasOne(mdl_Product::class,'id','product_id');
    }

}
