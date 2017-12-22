<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class push_msg extends Model
{
    public $table = 'push_msg';
    public $timestamps = false;
    protected $fillable = ['id','phone','openid','ts'];
    public $primaryKey = "id";


}
