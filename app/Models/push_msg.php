<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class push_msg extends Model
{
    public $table = 'push_msg';
    public $timestamps = true;
    protected $fillable = ['id','phone','openid','created_at','updated_at'];
    public $primaryKey = "id";


}
