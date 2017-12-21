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
use Illuminate\Http\Request;

class ReserveController extends Controller
{
    /**
     * 预约列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reserveList()
    {
        $list = reserve::where(['is_valid'=>1])->paginate(15);
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
        $remarks = $request->remarks;
        if (empty($id) || empty($remarks)) {
            return fun_error_view(0, '缺少参数','/adm/reserve/reserveList');
        }
        $info = reserve::where(['id'=>$id])->first();
        if(!$info){
            return fun_error_view(0, '数据错误','/adm/reserve/reserveList');
        }
        $res = reserve::where(['id'=>$id])->update(['feedback'=>$remarks,'sign'=>1]);
        if (!$res) {
            return fun_error_view(0, '修改数据失败', '/adm/reserve/reserveList');
        }
        ajax_respon(1, '编辑成功');
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
        $info = reserve::where(['id'=>$id])->update(['is_valid'=>0]);
        if (empty($info)) {
            ajax_respon(0, '删除失败');
        }
        ajax_respon(1, '删除成功');
    }
}