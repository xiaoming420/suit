<?php
/**
 * Created by PhpStorm.
 * User: yshow
 * Date: 2017/12/17
 * Time: 13:48
 */

namespace App\Http\Controllers\Adm\Users;


use App\Http\Controllers\Controller;

class reserve extends Controller
{
    /**
     * 预约列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reserveList()
    {
        $list = reserve::where(['is_valid'=>1])->get();
        return view('admin/user/reservelist', ['list'=>$list]);
    }
}