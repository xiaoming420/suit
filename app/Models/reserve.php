<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reserve extends Model
{
    public $table = 'reserve';
    public $timestamps = true;
    protected $fillable = ['id','name','phone','city','address','remark','sign','created_at','updated_at'];
    public $primaryKey = "id";


}
