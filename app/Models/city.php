<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class city extends Model
{
    public $table = 'city';
    public $timestamps = false;
    protected $fillable = [];

    // 获取 京津冀
    protected function getprovines()
    {
        $res = $this->whereIn('id', [11,12,13])->get()->toArray();
        return $res;
    }
}
