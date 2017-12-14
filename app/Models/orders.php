<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    public $table = 'orders';
    public $timestamps = false;
    protected $fillable = [];
    public $primaryKey = "id";

    /**
     * 查询用户的助力单
     * @param $page
     * @param $unionid
     * @return mixed
     */
    protected function orderlist($page, $unionid)
    {
        $pageSize = 10;
        $res = $this->select('orders.*','goods.name','goods.images','goods.description','goods.price')
            ->leftjoin('goods', 'goods.id', '=', 'orders.goods_id')
            ->where(['orders.unionid'=>$unionid, 'orders.is_valid'=>1])
            ->take($pageSize)
            ->skip($page*$pageSize)
            ->orderBy('orders.id', 'desc')
            ->get()
            ->toArray();
        return $res;
    }

    /**
     * 查询进行中的订单是否到期
     */
    protected function resetorder()
    {
        $time = date('Y-m-d H:i:s', time()-env('END_TIME',259200));
        $res = $this->where(['order_status'=>1])->where('ct', '<', $time)->get()->toArray();
        return $res;
    }

    /**
     * 通过订单ID查询用户
     */
    protected function getUserInfo($order_id)
    {
        $res = $this->select('users.nickname','users.unionid', 'goods.name', 'goods.description','orders.helps','orders.form_id','goods.price','users.openid')
            ->leftjoin('users', 'users.unionid', '=', 'orders.unionid')
            ->leftjoin('goods', 'goods.id', '=', 'orders.goods_id')
            ->where(['orders.id'=>$order_id, 'orders.order_status'=>1])
            ->first();
        return $res ? $res->toArray() : '';
    }

    /**
     * 查询用户助力单中进行中的订单
     */
    protected function currentList($unionid)
    {
        $res = $this->select('orders.*', 'goods.sku_id', 'goods.name')
            ->leftjoin('goods', 'goods.id', '=', 'orders.goods_id')
            ->where(['orders.unionid'=>$unionid, 'orders.is_receive'=>0, 'orders.order_status'=>3, 'orders.is_valid'=>1])
            ->get()
            ->toArray();
        return $res;
    }

    /**
     * 统计开团总数
     */
    protected function getgroups()
    {
        $res = $this->groupBy('unionid')->count('id');
        return $res ? $res : 0;
    }

    /**
     * 统计单个商品对应的助力总数
     */
    protected function goodshelps($goods_id)
    {
        $res = $this->leftjoin('order_helps', 'order_helps.order_id', '=', 'orders.id')
            ->where('orders.goods_id', $goods_id)
            ->where('order_helps.is_valid', 1)
            ->count('order_helps.id');
        return $res;
    }


}
