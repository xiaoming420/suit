<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class groups extends Model
{
    public $table = 'groups';
    public $timestamps = false;
    protected $fillable = [];
    public $primaryKey = "id";
}
