<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class goods extends Model
{
    public $table = 'goods';
    public $timestamps = false;
    protected $fillable = [];
    public $primaryKey = "id";
    
    protected function goodslist($page,$pageSize)
    {
        $pageSize = 10;
        $res = $this->where(['is_valid'=>1])
            //->where('stock_use', '>', 0)
            ->orderBy('id', 'desc')
            ->take($pageSize)->skip($page*$pageSize)
            ->get();
        return $res;
    }

    /**
     * 获取所有有库存商品
     * @return mixed
     */
    protected function listone()
    {
        $res = $this->where(['is_valid'=>1])
            ->where('stock_use', '>', 0)
            ->orderBy('sorts', 'desc')
            ->get()->toArray();
        return $res;
    }

    /**
     * 获取所有没有库存商品
     * @return mixed
     */
    protected function listtwo()
    {
        $res = $this->where(['is_valid'=>1])
            ->where('stock_use', 0)
            ->orderBy('sorts', 'desc')
            ->get()->toArray();
        return $res;
    }
}
