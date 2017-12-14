<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    public $table = 'address';
    public $timestamps = false;
    protected $fillable = [];
    public $primaryKey = "id";

    /**
     * 地址列表
     */
    protected function addresslist($uid, $page)
    {
        $pageSize = 10;
        $res = $this->where(['uid'=>$uid])
            ->where(['is_valid'=>1])
            ->orderBy('id', 'desc')
            ->take($pageSize)
            ->skip($page*$pageSize)
            ->get();
        return $res;
    }
}
