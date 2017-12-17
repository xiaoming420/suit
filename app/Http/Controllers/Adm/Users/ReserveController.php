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

class reserveController extends Controller
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
}