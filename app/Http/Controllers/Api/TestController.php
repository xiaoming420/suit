<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\UserController;
use App\Libs\CryptAES;
use App\Libs\JSSDK;
use App\Models\orders;
use App\Models\users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ixudra\Curl\Facades\Curl;

function msectime()
{
    list($msec, $sec) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
}

class TestController extends Controller
{

    //测试获取token
    public function gettoken(Request $request)
    {
        $user = new UserController();
        $unionid = $request->input('unionid');
        if (empty($unionid)) fun_respon(0, '缺少参数');

        $info = users::where(['unionid'=>$unionid])->first();
        $res = $user->createToken($info->openid, $info->unionid);
        var_dump($res);

    }


    public function tests()
    {
        header('Location:'.env('APP_URL','https://pintuan.missfresh.cn').'/api/order/groupqrcode');
        $arr = orders::groupBy('unionid')->get()->toArray();
        $a = count($arr);
        var_dump($a);
        /*$keyStr = env('encode_key', 'DBA78&*^IK78#!36');
        $unionid = 'o9rflt4pZYhOEtuApd0pJhXsb18Q';
        $plainText = 'u='.$unionid;*/
        /*$aes = new CryptAES();
        $aes->set_key($keyStr);
        $aes->require_pkcs5();
        $encText = $aes->encrypt($plainText);
        $decString = $aes->decrypt($encText);
        var_dump($encText);*/
        /*$sign = substr(strtoupper(md5($unionid.'_'.$keyStr)), 0, 16);
        $url = env('missfresh_url', 'http://as-vip-staging.missfresh.cn:8008').'/web20/user/checkUser?u='.$unionid.'&s='.$sign;
        $res = Curl::to($url)->get();
        var_dump($res);die;

        echo '#=========#';

        $sku_id = 110;
        $str = $unionid.'_'.$sku_id.'_'.$keyStr;
        $sign = substr(strtoupper(md5($str)), 0, 16);
        $plainText = 'p='.$unionid."_".$sku_id.'&s='.$sign;
        $url = env('missfresh_url', 'http://as-vip-staging.missfresh.cn:8008').'/web20/user/sendVoucherApi?'.$plainText;
        $res = Curl::to($url)->get();
        var_dump($res);die;*/
    }

    /*小程序发送模板消息*/
    public function test()
    {
        /*$appid = 'wxe7985a3d339996c5';
        $secret = '20f3ace9cdb7d5ee9cc0b9fd9f6e1f57';*/
        $jssdk = new JSSDK();
        //$touser = 'o7AAi0XaDG27qFufxmD-viEtgJwQ';
        $touser = 'o7AAi0VTjTvFZc_O_xI6DiPwrO4I';
        $template_id = 'FUHysV2hng3xRrN3DjCDj0EBGlOMdmHJX2V7Tqp5n2g';
        $page = 'pages/index/index';
        $formId = '1511240931581';
        //示例数据根据消息模板填充
        $data = array(
            'keyword1'=>array('value'=>'商品名称','color'=>'#7167ce'),
            'keyword2'=>array('value'=>'下单时间','color'=>'#7167ce'),
            'keyword3'=>array('value'=>'下单时间','color'=>'#7167ce'),
            'keyword4'=>array('value'=>'下单时间','color'=>'#7167ce'),
            'keyword5'=>array('value'=>'下单时间','color'=>'#7167ce'),
        );
        $res = $jssdk->sendTemplate($touser,$template_id,$page,$data,$formId);
    }
}
