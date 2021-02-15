<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_OauthAccessToken extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'oauth_access_tokens';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}
