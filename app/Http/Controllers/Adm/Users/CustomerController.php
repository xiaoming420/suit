<?php
/**
 * Created by PhpStorm.
 * User: yshow
 * Date: 2017/12/17
 * Time: 13:48
 */

namespace App\Http\Controllers\Adm\Users;


use App\Http\Controllers\Controller;
use App\Models\reserve;
use App\Models\users;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * 预约列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function customerList()
    {
        $list = users::where([])->paginate(15);
        return view('admin/user/customerlist', ['list'=>$list]);
    }

    /**
     * 执行修改标记状态
     * @param Request $request
     * @return mixed
     */
    public function doEditSign(Request $request)
    {
        $id = (int)$request->id;
        if (empty($id)) {
            return fun_error_view(0, '缺少参数', '/adm/user/customerlist');
        }
        $info = users::where(['id'=>$id])->first();
        if(!$info){
            return fun_error_view(0, '数据错误', '/adm/user/customerlist');
        }
        $res = users::where(['id'=>$id])->update(['is_used'=>1]);
        if (!$res) {
            return fun_error_view(0, '修改数据失败', '/adm/user/customerlist');
        }

    }
}