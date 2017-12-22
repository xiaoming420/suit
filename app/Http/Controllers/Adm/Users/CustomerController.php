<?php
/**
 * Created by PhpStorm.
 * User: yshow
 * Date: 2017/12/17
 * Time: 13:48
 */

namespace App\Http\Controllers\Adm\Users;


use App\Http\Controllers\Controller;
use App\Models\push_msg;
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
        ajax_respon(1, '编辑成功');
    }


    /**
     * 推送消息列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pushMesList()
    {
        $list = push_msg::where(['is_valid'=>1])->paginate(15);
        return view('admin/user/pushmsgdetail', ['list'=>$list]);
    }


    /**
     * 添加推送人员
     * @param Request $request
     * @return mixed
     */
    public function addPush(Request $request)
    {
        $phone = (int)$request->phone;
        if (empty($phone)) {
            return fun_error_view(0, '缺少参数', '/adm/push/pushmeslist');
        }
        $info = users::where(['phone'=>$phone])->first();
        if(!$info){
            return fun_error_view(0, '数据错误', '/adm/push/pushmeslist');
        }
        $push_info = push_msg::where(['phone'=>$phone])->first();
        if($push_info){
            return fun_error_view(0, '该成员已经添加过啦！', '/adm/push/pushmeslist');
        }
        $res = push_msg::create(['phone'=>$info['phone'],'openid'=>$info['openid']]);
        if (!$res) {
            return fun_error_view(0, '添加数据失败', '/adm/push/pushmeslist');
        }
        return fun_error_view(1, '添加数据成功', '/adm/push/pushmeslist');
    }

    /**
     * 删除推送用户
     * @param Request $request
     */
    public function del(Request $request)
    {
        $id = (int) $request->id;
        if (empty($id)) {
            ajax_respon(0, '缺少参数');
        }
        $info = push_msg::where(['id'=>$id])->update(['is_valid'=>0]);
        if (empty($info)) {
            ajax_respon(0, '删除失败');
        }
        ajax_respon(1, '删除成功');
    }
}