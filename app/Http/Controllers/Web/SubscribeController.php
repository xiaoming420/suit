<?php

namespace App\Http\Controllers\Web;

use App\Libs\JSSDK;
use App\Models\city;
use App\Models\push_msg;
use App\Models\reserve;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SubscribeController extends Controller
{
    public function infopage(Request $request)
    {
        //$provines = city::getprovines();
        //$signPage = new JSSDK();
        //$getSignPackage = $signPage->getSignPackage();
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
        if (in_array($data['province'], ['请选择市','请选择区'])) {
            $data['province'] = '';
        }
        if (in_array($data['city'], ['请选择市','请选择区'])) {
            $data['city'] = '';
        }
        if (in_array($data['area'], ['请选择市','请选择区'])) {
            $data['area'] = '';
        }
        $res = reserve::insert($data);
        if ($res) {

            /*// 预约成功，推送消息给客服人员
            $txt = "亲，有人预约了哦，赶快联系他吧! \n".
                "预约人手机号：".$data['phone']." \n".
                '预约人姓名：'.$data['name']."\n".
                '预约认地址：'.$data['province'].' '.$data['city'].' '.$data['area']." \n".
                '详细地址：'.$data['address']." \n".
                '备注：'.$data['remark'];
            $content = array(
                'touser'=>'oenEY1Wq8u0_VIGo7F2Ddb4ravnQ',
                'msgtype'=>'text',
                'text'=>array(
                    'content'=> $txt
                )
            );*/

            $tools = new JSSDK();
            $newdata=array(
                'first'=>array('value'=>'预约成功通知','color'=>"#7167ce"),
                'keyword1'=>array('value'=>date('Y-m-d H:i:s'),'color'=>'#7167ce'),
                'keyword2'=>array('value'=>$data['name'],'color'=>'#7167ce'),
                'keyword3'=>array('value'=>$data['phone'],'color'=>'#7167ce'),
                'remark'=>array('value'=>'地址:'.$data['address'].'   备注:'.$data['remark'],'color'=>'#7167ce'),
            );
            $template_id = 'kfpeS1KfOeXuS1-VqTKNRzjEMX3vb2jrCvGrtzgaaog';

            $user_list = push_msg::where('is_valid', 1)->get()->toArray();
            if ($user_list) {
                foreach ($user_list as $v) {
                    if (empty($v['openid'])) {
                        continue;
                    }
                    $result = $tools->doSend($v['openid'],$template_id,'',$newdata);
                    Storage::disk('local')->append('sendmsg.log', json_encode($result));
                }
            }

            fun_respon(1, '成功');
        } else {
            fun_respon(0, '预约失败');
        }
    }

    private function is_utf8($str)
    {
        return preg_match('//u', $str);
    }

    public function toMessage()
    {
        $content = array(
            'touser'=>'oenEY1Wq8u0_VIGo7F2Ddb4ravnQ',
            'msgtype'=>'text',
            'text'=>array(
                'content'=>'Hello'
            )
        );
        $jssdk = new JSSDK();
        $res = $jssdk->servicemsg($content);
        var_dump($res);
        exit;
    }
}
