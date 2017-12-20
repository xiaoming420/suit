<?php

namespace App\Http\Controllers\Web;

use App\Models\city;
use App\Models\reserve;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscribeController extends Controller
{
    public function infopage(Request $request)
    {
        //$provines = city::getprovines();
        return view('web.reserve');
    }

    // 获取市区
    public function getcity(Request $request)
    {
        $type = $request->input('type', 1);
        $num = $request->input('num');
        if (empty($num)) {
            fun_respon(0, '缺少参数');
        }
        $str = "<option value='0'>请选择对应</option>";
        if ($type == 1) { // 获取对应的市
            $str = "<option value='0'>请选择市</option>";
            $list = city::where('parent_id', $num)->get()->toArray();
        } else {
            // 获取对应的省
            $str = "<option value='0'>请选择区</option>";
            $list = city::where('parent_id', $num)->get()->toArray();
        }
        if ($list) {
            foreach ($list as $v) {
                $str .= "<option value='".$v['id']."'>".$v['name']."</option>";
            }
        }
        fun_respon(1, $str);
    }

    // 申请预约
    public function suppy(Request $request)
    {
        $data = $request->only(['name','sex','phone','remark','province','city','area','address']);
        $data['ct'] = date('Y-m-d H:i:s');
        $data['ut'] = date('Y-m-d H:i:s');
        $res = reserve::insert($data);
        if ($res) {
            fun_respon(1, '成功');
        } else {
            fun_respon(0, '预约失败');
        }
    }
}
