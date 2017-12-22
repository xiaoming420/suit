<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sms_log extends Model
{
    public $table = 'sms_log';
    public $timestamps = false;
    protected $fillable = ['id','content','ts'];
    public $primaryKey = "id";


}
