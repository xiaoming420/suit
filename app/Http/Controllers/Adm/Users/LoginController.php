<?php

namespace App\Http\Controllers\Adm\Users;

use App\Models\adm_user;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\View;

class LoginController extends Controller
{
    /**
     * 登陆界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function login()
    {
        return View::make('admin.login');
    }

    /**
     * 后台登陆
     * @param Request $request
     */
    public function dologin(Request $request)
    {
        $username = trim($request->input('user_name'));
        $pass = trim($request->input('pass_word'));
        $pass = md5(md5($pass.'adm_key'));
        $userinfo = adm_user::getOne(['phone'=>$username, 'is_valid'=>1]);
        if (!$userinfo) {
            ajax_respon(0, '用户名错误');
        }
        if ($userinfo['pass'] != $pass) {
            ajax_respon(0, '密码错误');
        }
        $cook = [
            'uid'   =>  $userinfo['id'],
            'phone' =>  $userinfo['phone'],
        ];
        $adm_token = Crypt::encrypt(json_encode($cook));
        setcookie('adm_token', $adm_token,time()+60*60*4,'/');
        Redis::setex('adm_token_'.$adm_token, 60*60*4, 1);
        ajax_respon(1, '登陆成功');
    }

    public function signout()
    {
        //清除用户的数据之后跳转到登录页面
        if (isset($_COOKIE['adm_token']) && $_COOKIE['adm_token']) {
            setcookie('adm_token','',time()-86400,'/');
            Redis::del('adm_token_'.$_COOKIE['adm_token']);
            return redirect('adm/login');
        } else {
            return redirect('adm/login');
        }
    }

    /**
     * 解密pc_token统一方法
     */
    public static function decryptToken()
    {
        $cook = isset($_COOKIE['adm_token']) ? $_COOKIE['adm_token'] : '';
        if (!$cook) {
            return redirect('admin/login');
        }
        return json_decode(Crypt::decrypt($cook), true);
    }

}
