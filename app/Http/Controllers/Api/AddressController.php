<?php

namespace App\Http\Controllers\Api;

use App\Models\address;
use App\Models\users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ixudra\Curl\Facades\Curl;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.token', ['except' => ['test','checkarea']]); // 除了某个方法都调用
    }

    /**
     * 收货地址列表
     */
    public function addresslist(Request $request)
    {
        $unionid = $request->input('unionid');
        $page = $request->json('page', 0);
        $userinfo = users::userinfo($unionid);
        $list = address::addresslist($userinfo['id'], $page);
        fun_respon(1, $list);
    }

    /**
     * 获取所在城市
     */
    public function getareas()
    {
        $url = 'https://as-vip.missfresh.cn/v2/address/list';
        $res = Curl::to($url)->get();
        $res = json_decode($res, true);
        $arr = [];
        foreach ($res as $v) {
            if ($v['ordering'] == 100) {
                $arr = $v['areas'];
            }
        }
        fun_respon(1, $arr);
    }

    public function checkarea(Request $request)
    {
        $lat = $request->json('lat');
        $lng = $request->json('lng');
        $address_code = $request->json('address_code');
        if (empty($address_code)) {
            fun_respon(0, '请您输入您的收货城市');
        }
        $data = ['lat'=>$lat, 'lng'=>$lng, 'address_code'=>$address_code];
        $code = ['address_code'=>$address_code];
        $code = json_encode($code);
        $url = "https://as-vip.missfresh.cn/v1/product/chrome/view?version=6.2.3";
        //$url = "https://pintuan.missfresh.cn/api/address/test";
        $res = Curl::to($url)
            ->withContentType('application/json')
            ->withHeader('x-region: {"address_code": '.$address_code.'}')
            ->withData(json_encode($data))
            ->post();
        var_dump($res);die;
        $b = ["address_code"=>"110105"];
        $b = json_encode($b);
        $a = ['Content-Type:application/json',"x-region:".$b];
        $res = fun_curl_header($url, $data, $a);
        var_dump($res);
    }

    public function test(Request $request)
    {
        $all = $request->all();
        $header = getallheaders();
        return [$all,  $header];
    }

    /**
     * 添加地址/修改地址
     */
    public function address(Request $request)
    {
        $uid = $request->input('validate_uid');
        $status = (int) $request->json('status', 1); //操作类型 1添加，2修改
        $name = $request->json('name', '');
        $phone = $request->json('phone');
        $province = $request->json('province');
        $city = $request->json('city','');
        $area = $request->json('area','');
        $detail = $request->json('detail');
        $store_num = $request->json('store_num');
        $area_code = $request->json('area_code');
        $type = $request->json('type'); // 地址类型
        $address_id = (int) $request->json('address_id');

        if (!in_array($status, [1,2])) fun_respon(0, '请求类型错误');
        if (empty($name)) fun_respon(0, '收货人不能为空');
        if (empty($phone)) fun_respon(0, '手机号不能为空');
        if (!preg_match("/^1[34578]{1}\d{9}$/",$phone)) fun_respon(0, '手机号格式错误');
        if (empty($province)) fun_respon(0, '所在城市不能为空');
        if (empty($area_code)) fun_respon(0, '城市code不能为空');
        if (empty($detail)) fun_respon(0, '收货地址不能为空');
        if (empty($store_num)) fun_respon(0, '门牌号不能为空');

        $data = [
            'phone' => $phone,
            'name' => $name,
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'detail' => $detail,
            'store_num' => $store_num,
            'area_code' => $area_code,
            'type' => $type
        ];
        if ($status == 1) {
            $add_data = $data;
            $add_data['ct'] = date('Y-m-d H:i:s');
            $add_data['ut'] = date('Y-m-d H:i:s');
            $add_data['uid'] = $uid;
            $res = address::insertGetId($add_data);
            if ($res) {
                $add_data['id'] = $res;
                fun_respon(1, [$add_data]);
            }
            fun_respon(0, '添加失败');
        } else {
            if (empty($address_id)) fun_respon(0, '缺少参数');
            $data['ut'] = date('Y-m-d H:i:s');
            $res = address::where(['uid'=>$uid, 'id'=>$address_id])->update($data);
            if ($res) {
                $data['id'] = $address_id;
                fun_respon(1, [$data]);
            }
            fun_respon(0, '修改失败');
        }
    }


}
