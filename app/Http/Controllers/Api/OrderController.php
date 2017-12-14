<?php

namespace App\Http\Controllers\Api;

use App\Libs\CryptAES;
use App\Models\address;
use App\Models\goods;
use App\Models\group_qrcode;
use App\Models\order_helps;
use App\Models\orders;
use App\Models\users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Ixudra\Curl\Facades\Curl;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.token', ['except' => [
            'qrcode',
            'checkauthor',
            'groupqrcode',
        ]]);
    }

    /**
     * 创建订单
     */
    public function addorder(Request $request)
    {
        $goods_id = (int) $request->json('goods_id');
        $unionid = $request->input('unionid');
        $uid = $request->input('validate_uid');
        $address_id = (int) $request->json('address_id');
        $form_id = $request->json('formId');
        if (empty($goods_id) || empty($address_id)) fun_respon(0, '缺少参数');

        $userinfo = users::where(['id'=>$uid])->first();
        if (empty($userinfo)) fun_respon(0 ,'用户不存在');
        if($userinfo->is_check == 0) {
            $keyStr = env('encode_key', 'DBA78&*^IK78#!36');
            $sign = substr(strtoupper(md5($unionid.'_'.$keyStr)), 0, 16);
            $url = env('missfresh_url', 'http://as-vip-staging.missfresh.cn:8008').'/web20/user/checkUser?u='.$unionid.'&s='.$sign;
            $res = Curl::to($url)->get();
            $res = json_decode($res, true);
            if ($res['code'] != 0){
                fun_respon(0, '请先去注册每日优鲜小程序');
            } else { // 验证通过修改用户验证状态
                // 修改用户注册状态
                users::where(['unionid'=>$unionid])->update(['is_check'=>1, 'updated_at'=>date('Y-m-d H:i:s')]);
            }
        }

        $goods_info = goods::where(['id'=>$goods_id, 'is_valid'=>1])->first();
        if (!$goods_info) fun_respon(0, '该商品已下架');
        if ($goods_info->stock_use < 1) fun_respon(0, '抱歉您来晚了，该商品已经被抢完啦！');

        $valid_addres = address::where(['id'=>$address_id, 'uid'=>$uid, 'is_valid'=>1])->first();
        if (empty($valid_addres)) fun_respon(0, '请选择有效的地址');
        // 创建之前查看是否有 该商品正在进行的订单
        $order_valid = orders::where(['goods_id'=>$goods_id, 'unionid'=>$unionid])->orderBy('id', 'desc')->first();
        if ($order_valid) {
            $order_valid = $order_valid->toArray();
            if ($order_valid['order_status'] == 1) {
                fun_respon(0, '已有进行中订单');
            }
        }
        $share_url = env('APP_URL').'/api/order/qrcode?uid='.$uid.'&gid='.$goods_id;
        $data = [
            'unionid' => $unionid,
            'goods_id' => $goods_id,
            'order_status' => 1,
            'helps' => $goods_info->helps,
            'address_id' => $address_id,
            'share_url' => $share_url,
            'form_id' => $form_id,
            'ct' => date('Y-m-d H:i:s'),
            'ut' => date('Y-m-d H:i:s')
        ];
        $res = orders::insert($data);
        $data['description'] = $goods_info['description'];
        // 修改商品库存
        goods::where(['id'=>$goods_id])->decrement('stock_use');
        fun_respon(1, $data);
    }

    /**
     * 二维码界面h5
     * @param Request $request
     */
    public function qrcode(Request $request)
    {
        $unionid_help = $request->input('unionid', '');
        $goods_id = (int) $request->input('gid');
        $uid = $request->input('uid');
        if (empty($goods_id) || empty($uid)) {
            return view('h5error', ['error'=>'该链接已失效']);
        }
        if (!$unionid_help) { // 不存在就是授权之前
            $current_url = urlencode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&');
            header('Location:https://as-vip.missfresh.cn/v1/interface/get_user_base_info?callback='.$current_url);
        } else {
            // 经过静默授权 判断是否输出到群二维码界面
            if (empty($goods_id) || empty($uid) || empty($unionid_help)) {
                return view('h5error', ['error'=>'该链接已失效！']);
            }
            $userinfo = users::where(['id'=>$uid])->first();
            if (empty($userinfo)) {
                return view('h5error', ['error'=>'该用户不存在！']);
            }
            if ($userinfo->unionid == $unionid_help) {
                return view('h5error', ['error'=>'自己不能给自己助力！']);
            }
            $order_info = orders::where(['unionid'=>$userinfo->unionid, 'goods_id'=>$goods_id, 'order_status'=>1,'is_valid'=>1])->orderBy('id', 'desc')->first();
            if (!$order_info) {
                return view('h5error', ['error'=>'该活动已结束！']);
            }
            /*
            if ($order_info->order_status == 2) {
                return view('h5error', ['error'=>'该活动已取消！']);
            }
            if ($order_info->order_status == 3) {
                return view('h5error', ['error'=>'您的好友已经完成助力！']);
            }*/
            // 判断是否完成助力
            $count = order_helps::where(['order_id'=>$order_info->id, 'is_valid'=>1])->count();
            if ($count >= $order_info->helps) {
                return view('h5error', ['error'=>'您的好友已经完成助力！']);
            }

            $user_valid = order_helps::where(['unionid_help'=>$unionid_help, 'is_valid'=>1])->first();
            if ($user_valid) {
                return view('h5error', ['error'=>'您已经给某位好友助力过，一个人只能助力一次哦！']);
            }
            $user = order_helps::where(['unionid_help'=>$unionid_help])->first();
            if (!$user) {
                $data = [
                    'order_id' => $order_info->id,
                    'unionid_help' => $unionid_help,
                    'group_id' => '',
                    'is_valid' => 0,
                    'ct' => date('Y-m-d H:i:s'),
                    'ut' => date('Y-m-d H:i:s')
                ];
                $res = order_helps::insert($data);
                if (!$res) {
                    return view('h5error', ['error'=>'系统繁忙，请稍后在试']);
                }

                header('Location:'.env('APP_URL','https://pintuan.missfresh.cn').'/api/order/groupqrcode');
                //header('Location:http://uc.jianqunbao16.cn/weixin/Share/1af807506fd6edcb33134acd933ef414');
                //fun_respon(1, 'Here is the group RQ code interface');
            } else {
                // 存在记录，判断是不是之前的订单，不是修改
                if ($user->order_id == $order_info->id) {
                    header('Location:'.env('APP_URL','https://pintuan.missfresh.cn').'/api/order/groupqrcode');
                    //header('Location:http://uc.jianqunbao16.cn/weixin/Share/1af807506fd6edcb33134acd933ef414');
                    //return '这个是群界面，你添加过助力记录';
                }
                $res = order_helps::where(['id'=>$user->id])->update(['order_id'=>$order_info->id, 'ut'=>date('Y-m-d H:i:s')]);
                if (!$res) {
                    return view('h5error', ['error'=>'系统繁忙，请稍后在试！']);
                }
                header('Location:'.env('APP_URL','https://pintuan.missfresh.cn').'/api/order/groupqrcode');
                //header('Location:http://uc.jianqunbao16.cn/weixin/Share/1af807506fd6edcb33134acd933ef414');
                //return '这个是群界面，你添加过助力记录2,修改';
            }
        }
    }

    /**
     * 我的助力单
     */
    public function orderlist(Request $request)
    {
        try {
            $unionid = $request->input('unionid');
            $page = (int) $request->json('page', 0);
            $list = orders::orderlist($page, $unionid);
            if (!$list) fun_respon(1, []);
            foreach ($list as &$v) {
                $v['end_time'] = date('m月d日H:i', strtotime($v['ct'])+env('END_TIME',259200));
                $v['images'] = env('APP_URL').'/goods_images/'.$v['images'];
                $v['help_list'] = order_helps::helplist($v['id']);
            }
            fun_respon(1, $list);

        } catch (\Exception $ex) {
            fun_respon(0, $ex->getMessage());
        }
    }

    /**
     * 领取券
     */
    public function sendcard(Request $request)
    {
        $order_id = (int) $request->json('order_id');
        $unionid = $request->input('unionid');
        if (empty($order_id)) fun_respon(0, '缺少参数');

        $order_info = orders::where(['id'=>$order_id, 'unionid'=>$unionid, 'order_status'=>3])->first();
        if (!$order_info) fun_respon(0, '订单已过期');
        if ($order_info->is_receive == 1) fun_respon(0, '已领取');
        $goods_info = goods::where(['id'=>$order_info->goods_id])->first();
        if (empty($goods_info)) fun_respon(0, '该商品不存在');

        // 调用每日优鲜接口，领取免费券
        $keyStr = env('encode_key', 'DBA78&*^IK78#!36');
        $str = $unionid.'_'.$goods_info->sku_id.'_'.$keyStr;
        $sign = substr(strtoupper(md5($str)), 0, 16);
        $plainText = 'p='.$unionid."_".$goods_info->sku_id.'&s='.$sign;
        $url = env('missfresh_url', 'http://as-vip-staging.missfresh.cn:8008').'/web20/user/sendVoucherApi?'.$plainText;
        $res = Curl::to($url)->get();

        $res = json_decode($res, true);
        if ($res['code'] != 0) {
            @Storage::disk('logs')->append('lingqu.log', 'time:'.date('Y-m-d H:i:s').json_encode($res, JSON_UNESCAPED_UNICODE).'==param:'.$plainText);
            fun_respon(0, $res['msg']);
        }

        //每日优鲜领取成功，修改订单领取状态
        $res = orders::where(['id'=>$order_id])->update(['is_receive'=>1, 'ut'=>date('Y-m-d H:i:s')]);
        if (!$res) fun_respon(0, '修改订单领取状态失败');
        fun_respon(1, '领取成功，赶快去每日优鲜小程序使用吧！');
    }

    /**
     * 每日优鲜反检测用户有效性
     * @param Request $request
     * @return string
     */
    public function checkauthor(Request $request)
    {
        $u = $request->input('u', '');
        $s = $request->input('s', '');
        if (empty($u) || empty($s)) {
            @Storage::disk('logs')->append('111.log', 'time:'.date('Y-m-d H:i:s').'缺少参数');
            return json_encode(['code'=>1, 'success'=>false, 'msg'=>'缺少参数'],JSON_UNESCAPED_UNICODE);
        }
        $sign = substr(strtoupper(md5($u.'_'.env('md5_key'))), 0, 16);
        if ($sign != $s) {
            @Storage::disk('logs')->append('111.log', 'time:'.date('Y-m-d H:i:s').'签名错误');
            return json_encode(['code'=>1, 'success'=>false, 'msg'=>'签名错误'],JSON_UNESCAPED_UNICODE);
        }
        $res = orders::where(['unionid'=>$u, 'order_status'=>3, 'is_receive'=>0])->first();
        if (!$res) {
            @Storage::disk('logs')->append('111.log', 'time:'.date('Y-m-d H:i:s').'您还未达到领取优惠券资格');
            return json_encode(['code'=>1, 'success'=>false, 'msg'=>'您还未达到领取优惠券资格'],JSON_UNESCAPED_UNICODE);
        }
        //@Storage::disk('logs')->append('111.log', 'time:'.date('Y-m-d H:i:s').'ok');
        return json_encode(['code'=>0, 'success'=>true]);
    }

    /**
     * 识别二维码跳转界面
     */
    public function groupqrcode()
    {
        $info = group_qrcode::orderBy('id', 'desc')->first();
        $info = $info ? $info->toArray() : '';
        return view('groupqrcode', ['info'=>$info]);
    }
}
