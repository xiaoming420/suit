<?php

namespace App\Http\Controllers\Adm\Users;

use App\Models\adm_user;
use App\Models\group_qrcode;
use App\Models\groups;
use App\Models\order_helps;
use App\Models\orders;
use App\Models\users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $res = LoginController::decryptToken();
        $userinfo = adm_user::getOne(['id'=>$res['uid']]);
        $total_users = users::count('id');      // 用户注册量
        $help_users = order_helps::where('is_valid', 1)->count('id');
        $data = [
            'users' => $total_users,
            'help_users' => $help_users,
        ];
        return view('admin/user/index', ['info'=>$userinfo, 'datas'=>$data]);
    }

    /**
     * 用户列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userlist()
    {
        $list = adm_user::getWhere(['is_valid'=>1]);
        return view('admin/user/userlist', ['list'=>$list]);
    }

    /**
     * 添加用户界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adduser()
    {
        return view('admin/user/adduser');
    }

    /**
     * 执行添加用户
     * @param Request $request
     */
    public function doadduser(Request $request)
    {
        $phone = trim($request->phone);
        $nickname = trim($request->nickname);
        $pass = trim($request->pass);

        if (empty($phone) || empty($nickname) || empty($pass)) {
            return fun_error_view(0, '缺少参数', 'adduser');
        }
        // 正则匹配下账户号和密码
        $pattern = "/^[0-9A-Za-z\-_\.]{4,16}$/i";
        if ( !preg_match( $pattern, $phone ) ) {
            return fun_error_view(0, '账号格式错误', 'adduser');
        }
        $pattern = "/^[0-9A-Za-z\-_\.\!\@\#\%\^\&\*]{6,16}$/i";
        if ( !preg_match( $pattern, $pass ) ) {
            return fun_error_view(0, '密码格式错误', 'adduser');
        }
        $data = [
            'phone' => $phone,
            'pass' => md5(md5($pass.'adm_key')),
            'nickname' => $nickname,
            'user_type' => 1,
        ];
        $res = adm_user::add($data);
        if (!$res) {
            return fun_error_view(0, '添加用户失败', 'adduser');
        }
        return fun_error_view(1, '添加用户成功', 'userlist');

    }

    /**
     * 修改用户信息试图
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function editUserView(Request $request)
    {
        $id = (int) $request->id;
        if (empty($id)) {
            return fun_error_view(0, '缺少参数', 'userlist');
        }
        $info = adm_user::getOne(['id'=>$id]);
        if (empty($info)) {
            return fun_error_view(0, '数据不存在', 'userlist');
        }
        return view('admin/user/edituser', ['info'=>$info]);
    }

    /**
     * 执行修改用户信息
     * @param Request $request
     * @return mixed
     */
    public function doedituser(Request $request)
    {
        $id = (int)$request->id;
        $nickname = trim($request->nickname);
        if (empty($id) || empty($nickname)) {
            return fun_error_view(0, '缺少参数', 'edituser?id='.$id);
        }
        $res = adm_user::edit(['id'=>$id], ['nickname'=>$nickname]);
        if (!$res) {
            return fun_error_view(0, '修改用户信息失败', 'edituser?id='.$id);
        }
        return fun_error_view(1, '修改用户信息成功', 'userlist');
    }

    /**
     * 删除用户
     * @param Request $request
     */
    public function deluser(Request $request)
    {
        $id = (int) $request->id;
        if (empty($id)) {
            ajax_respon(0, '缺少参数');
        }
        $info = adm_user::edit(['id'=>$id], ['is_valid'=>0]);
        if (empty($info)) {
            ajax_respon(0, '删除失败');
        }
        ajax_respon(1, '删除成功');
    }

    /**
     * 修改用户密码
     * @param Request $request
     */
    public function edituserpass(Request $request)
    {
        $uinfo = LoginController::decryptToken();
        $old_pass = $request->old_pass;
        $new_pass = $request->new_pass;
        $new_pass_ok = $request->new_pass_ok;
        if (empty($old_pass) || empty($new_pass) || empty($new_pass_ok)) {
            return fun_error_view(0, '缺少参数', 'index');
        }
        if ($new_pass != $new_pass_ok) {
            return fun_error_view(0, '两次密码不一致', 'index');
        }
        $userinfo = adm_user::getOne(['id'=>$uinfo['uid']]);
        if (!$userinfo) {
            return fun_error_view(0, '请重新登陆之后操作', 'index');
        }
        if ($userinfo['pass'] != md5(md5($old_pass.'adm_key'))) {
            return fun_error_view(0, '原密码错误', 'index');
        }
        $pattern = "/^[0-9A-Za-z\-_\.\!\@\#]{6,16}$/i";
        if ( !preg_match( $pattern, $new_pass ) ) {
            return fun_error_view(0, '密码格式错误', 'index');
        }
        $res = adm_user::edit(['id'=>$uinfo['uid']], ['pass'=>md5(md5($new_pass.'adm_key'))]);
        if (empty($res)) {
            return fun_error_view(0, '修改密码错误', 'index');
        }
        return fun_error_view(1, '修改密码成功', 'login');
    }

    /**
     * 群二维码
     */
    public function group(Request $request)
    {
        if ($request->isMethod('get')) {
            $list = group_qrcode::orderBy('id', 'desc')->first();
            if ($list) {
                $list = $list->toArray();
            }
            return view('admin.user.group', ['list'=>$list]);
        }
        $img_url = $request->file('images');
        $redirect_url = 'group';
        if (empty($img_url)) {
            return fun_error_view(0, '缺少图片', $redirect_url);
        }
        $ext = strtolower($img_url->getClientOriginalExtension());     // 扩展名
        if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
            return fun_error_view(0, '上传图片格式错误', $redirect_url);
        }
        if (isset($_FILES['images']['size']) && $_FILES['images']['size'] >= 8*1024*1024) {
            return fun_error_view(0, '上传图片大小不得超过8M', $redirect_url);
        }
        $tem_img = 'qrcode_'.str_random(16) . '.'.$ext;
        $put_result = Storage::disk('goods_images')->put(
            $tem_img,
            file_get_contents($img_url->getRealPath()),
            'public'
        );
        if (!$put_result) {
            return fun_error_view(0, '上传图片失败', $redirect_url);
        }
        $data['qrcode_url'] = $tem_img;
        $data['ct'] = date('Y-m-d H:i:s');

        $list = group_qrcode::get();
        if ($list) {
            foreach ($list as $v) {
                group_qrcode::where('id', $v['id'])->delete();
            }
        }

        $res = group_qrcode::insert($data);
        if ($res) {
            return fun_error_view(1, '上传二维码成功', $redirect_url);
        }
        return fun_error_view(0, '上传二维码失败', $redirect_url);
    }
}
