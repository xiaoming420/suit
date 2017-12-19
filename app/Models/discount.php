<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class discount extends Model
{
    public $table = 'discount';
    public $timestamps = true;
    protected $fillable = ['id','money','created_at','updated_at'];
    public $primaryKey = "id";


}
