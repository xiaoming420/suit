<?php
/**
 * Created by PhpStorm.
 * User: yshow
 * Date: 2017/12/17
 * Time: 13:48
 */

namespace App\Http\Controllers\Adm\Users;


use App\Http\Controllers\Controller;
use App\Models\discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * 注册折扣设置
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function discountDetail()
    {
        $info = discount::orderBy('id','DESC')->get();
        $money = end($info);
        var_dump($money);
        var_dump($money[0]->money);exit;
        return view('admin/discount/discountdetail',['list'=>$info]);
    }

    /**
     * 执行添加注册时红包金额
     * @param Request $request
     * @return mixed
     */
    public function doEditDiscount(Request $request)
    {
        $money = (int)$request->money;
        if (empty($money)) {
            return fun_respon_head(0, '缺少参数', 0);
        }
        $res = discount::create(['money'=>$money]);
        if (!$res) {
            return fun_error_view(0, '添加数据失败', 0);
        }
        return fun_error_view(1, '设置成功', 'discountdetail');
    }
}