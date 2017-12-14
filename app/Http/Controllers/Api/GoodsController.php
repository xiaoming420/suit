<?php

namespace App\Http\Controllers\Api;

use App\Models\goods;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class GoodsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.token', ['only' => [
            ''
        ]]);
    }

    /**
     * 商品列表
     */
    public function goodslist(Request $request)
    {
         $page = $request->json('page', 0);
        if ($page >= 1) fun_respon(1, []);
        // $pageSize = $request->json('pageSize',10);
        $redis_list = Redis::get('goods_list');
        if ($redis_list) {
            $list = json_decode($redis_list, true);
            fun_respon(1, $list);
        }
        $listone = goods::listone();
        $listtwo = goods::listtwo();
        if (count($listone) > 0 && count($listtwo) > 0) {
            $list = array_merge($listone,$listtwo);
        } elseif (count($listone) > 0 && count($listtwo) == 0){
            $list = $listone;
        } elseif (count($listone) == 0 && count($listtwo) > 0) {
            $list = $listtwo;
        } else {
            $list = [];
        }

        if (!empty($list)) {
            foreach ($list as &$v) {
                $v['images'] = env('APP_URL').'/goods_images/'.$v['images'];
            }
        }
        if ($page >= 1) fun_respon(1, []);
        Redis::setex('goods_list', 60, json_encode($list));
        fun_respon(1, $list);
    }


}
