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

class ReserveController extends Controller
{
    /**
     * 预约列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reserveList()
    {
        $list = reserve::where([])->paginate(15);
        return view('admin/reserve/reservelist', ['list'=>$list]);
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
            return fun_error_view(0, '缺少参数', 'edituser?id='.$id);
        }
        $info = reserve::where(['id'=>$id])->first();
        if(!$info){
            return fun_error_view(0, '数据错误', 'edituser?id='.$id);
        }
        $res = reserve::edit(['id'=>$id], ['sign'=>1]);
        if (!$res) {
            return fun_error_view(0, '修改数据失败', 'edituser?id='.$id);
        }
    }


    /**
     * 删除用户
     * @param Request $request
     */
    public function del(Request $request)
    {
        $id = (int) $request->id;
        if (empty($id)) {
            ajax_respon(0, '缺少参数');
        }
        $info = reserve::edit(['id'=>$id], ['is_valid'=>0]);
        if (empty($info)) {
            ajax_respon(0, '删除失败');
        }
        ajax_respon(1, '删除成功');
    }
}