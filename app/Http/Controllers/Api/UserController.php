<?php

namespace App\Http\Controllers\Api;

use App\Libs\JSSDK;
use App\Libs\WeChat\WxSmallClient;
use App\Models\groups;
use App\Models\order_helps;
use App\Models\orders;
use App\Models\users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class UserController extends Controller
{

    public function __construct(){
        $this->middleware('auth.token', ['except' => [
            'login','getgid','add'
        ]]);
    }

    /**
     * 小程序登陆
     */
    public function login(Request $request)
    {
        $code = $request->json('code');
        $iv = $request->json('iv');
        $cryptData = $request->json('cryptData');
        $groupid = $request->json('groupid', '');
        if (empty($code) || empty($iv) || empty($cryptData)) fun_respon(0, '缺少参数');

        $rs = WxSmallClient::getSessionKey($code);
        @Storage::disk('logs')->append('222.log', 'time:'.date('Y-m-d H:i:s').$rs);
        $array_user = json_decode($rs);

        if (!is_object($array_user)) fun_respon(0, '网络异常请重试');
        if ( !property_exists($array_user, 'session_key')) fun_respon(0, '解码失败');

        $userDatas = WxSmallClient::decryptData($array_user->session_key, $iv, $cryptData);
        $userData = json_decode($userDatas, true);
        if (is_array($userData) && !empty($userData)) {
            // 去添加用户
            $user_info = users::where(['openid' => $userData['openId'], 'is_valid'=>1])->first();
            if (!$user_info) {
                $nickname_bak = '';
                $regex = '/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u';
                if (preg_match($regex, $userData['nickName'])) {
                    //$nickname_bak = @iconv("GBK", "UTF-8",($userData['nickName']));
                    //$nickname_bak = mb_convert_encoding($userData['nickName'],"UTF-8","GBK");
                } else {
                    $nickname_bak = $userData['nickName'];
                }
                $add_data = [
                    'nickname' => base64_encode($userData['nickName']),
                    //'nickname_bak' => $nickname_bak,
                    'phone' => '',
                    'openid' => $userData['openId'],
                    'unionid' => $userData['unionId'],
                    'gender' => $userData['gender'],
                    'province' => $userData['province'],
                    'city' => $userData['city'],
                    'avatar_url' => $userData['avatarUrl'],
                    'is_check' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $res = users::insertGetId($add_data);
                $add_data['nickname'] = base64_decode($add_data['nickname']);
                if ($res) {
                    //判断是否有权限添加福利群
                    $add_data['add_permissions'] = 0;
                    if(env('enable_addgroup')==1){
                        // o9rflt5Ec6kg8CBAWgUdfcU_OEbg o9rflt07CFsEc-nCvIDfjWzj92uM o9rfltwoN4-mbNrlCohUNPQ5zv4s
                        if (in_array($userData['unionId'], ['o9rfltxLniGy5XvsiDsllDRuljhw'])) {
                            $add_data['add_permissions'] = 1;
                        }
                    }
                    // 创建token
                    $token = $this->createToken($userData['openId'], $userData['unionId']);

                    if (!$groupid) {
                        fun_respon(1, ['token'=>$token, 'userinfo'=>$add_data,'tt'=>11]);
                    }

                    //查询群表里面是否有该群
                    $valid_group = groups::where(['group'=>$groupid,'is_valid'=>1])->first();
                    if (!$valid_group) {
                        fun_respon(1, ['token'=>$token, 'userinfo'=>$add_data,'tt'=>22]);
                    }

                    // 存在群ID  判断用户是否有助力记录
                    $is_helps = order_helps::where(['unionid_help'=>$userData['unionId']])->orderBy('id', 'desc')->first();
                    if ($is_helps && $is_helps->is_valid == 0) {
                        $edit_helps = order_helps::where(['unionid_help'=>$userData['unionId']])->update(['is_valid'=>1,'group_id'=>$groupid,'ut'=>date('Y-m-d H:i:s')]);
                        if (!$edit_helps) {
                            fun_respon(0, '修改助力记录失败');
                            throw new \Exception('修改助力记录失败');
                        }
                        $count_helps = order_helps::leftjoin('users','users.unionid','=','order_helps.unionid_help')->where(['order_id'=>$is_helps->order_id, 'order_helps.is_valid'=>1])->get();
                        //这个地方判断最多显示助力人数为5人
                        $helps = '';
                        $num = count($count_helps);
                        foreach ($count_helps as $k=>$item){
                            if($k >= 5){
                                $helps.= $num>5?base64_decode($item['nickname'])."...":base64_decode($item['nickname']);
                                break;
                            }else{
                                if($k == $num-1){
                                    $helps.= base64_decode($item['nickname']);
                                }else{
                                    $helps.= base64_decode($item['nickname']).'、';
                                }
                            }
                        }
                        $count_helps = $count_helps ? count($count_helps) : 1;
                        $help_userinfo = orders::getUserInfo($is_helps->order_id);
                        if (!$help_userinfo) {
                            fun_respon(1, ['token'=>$token, 'userinfo'=>$add_data]);
                        }
                        if ($help_userinfo['helps'] <= $count_helps) {
                            orders::where(['id'=>$is_helps->order_id])->update(['order_status'=>3]);
                            //发送通知
                            $jssdk = new JSSDK();
                            $touser = $help_userinfo['openid'];
                            $formId = $help_userinfo['form_id'];
                            $template_id = env('s_template_id');
                            $pages = 'pages/index/index';
                            $prompt = '恭喜您已经完成助力！点击领取》';
                            //示例数据根据消息模板填充
                            $data = array(
                                'keyword1'=>array('value'=>$help_userinfo['name'],'color'=>'#7167ce'),
                                'keyword2'=>array('value'=>(string)$help_userinfo['price'].'元','color'=>'#7167ce'),
                                'keyword3'=>array('value'=>'0元','color'=>'#ff0000'),
                                'keyword4'=>array('value'=>$helps,'color'=>'#7167ce'),
                                'keyword5'=>array('value'=>$prompt,'color'=>'#ff0000'),
                            );
                            $res = $jssdk->sendTemplate($touser,$template_id,$pages,$data,$formId);
                        }
                        // 查看是第几位助力人，如果是最后一个助力人
                        $str = "您成为第%d位助力人，%s在你的帮助下即将免费领取到“【%s】%s”。";
                        $str = sprintf($str, $count_helps, base64_decode($help_userinfo['nickname']), $help_userinfo['name'], $help_userinfo['description']);

                        fun_respon(1, ['token'=>$token, 'helps'=>$str, 'userinfo'=>$add_data]);
                    }
                    fun_respon(1, ['token'=>$token, 'userinfo'=>$add_data,'tt'=>33]);
                }
                fun_respon(0, '注册失败');
            } else { // 存在用户的时候返回token


                $user_info = $user_info->toArray();
                $user_info['nickname'] = base64_decode($user_info['nickname']);
                $token = $this->createToken($user_info['openid'], $user_info['unionid']);
                //判断是否有权限添加福利群
                $user_info['add_permissions'] = 0;
                if(env('enable_addgroup')==1){
                    // 'o9rflt8FG8LWYSEU7NWgoucpaU6M','o9rfltwoN4-mbNrlCohUNPQ5zv4s','o9rflt07CFsEc-nCvIDfjWzj92uM','o9rflt5Ec6kg8CBAWgUdfcU_OEbg'
                    if (in_array($userData['unionId'], ['o9rfltxLniGy5XvsiDsllDRuljhw'])) {
                        $user_info['add_permissions'] = 1;
                    }
                }

                // 注册成功的时候判断是否传入groupid
                if (!$groupid) {
                    fun_respon(1, ['token'=>$token, 'userinfo'=>$user_info,'tt'=>11]);
                }

                //查询群表里面是否有该群
                $valid_group = groups::where(['group'=>$groupid,'is_valid'=>1])->first();
                if (!$valid_group) {
                    fun_respon(1, ['token'=>$token, 'userinfo'=>$user_info,'tt'=>22]);
                }

                // 存在群ID  判断用户是否有助力记录
                $is_helps = order_helps::where(['unionid_help'=>$user_info['unionid']])->orderBy('id', 'desc')->first();
                if ($is_helps && $is_helps->is_valid == 0) {
                    $edit_helps = order_helps::where(['unionid_help' => $user_info['unionid']])->update(['is_valid' => 1,'group_id'=>$groupid, 'ut' => date('Y-m-d H:i:s')]);
                    if (!$edit_helps) {
                        fun_respon(0, '修改助力记录失败');
                    }
                    $count_helps = order_helps::leftjoin('users','users.unionid','=','order_helps.unionid_help')->where(['order_id'=>$is_helps->order_id, 'order_helps.is_valid'=>1])->get();
                    //这个地方判断最多显示助力人数为5人
                    $helps = '';
                    $num = count($count_helps);
                    foreach ($count_helps as $k=>$item){
                        if($k >= 5){
                            $helps.= $num>5?base64_decode($item['nickname'])."...":base64_decode($item['nickname']);
                            break;
                        }else{
                            if($k == $num-1){
                                $helps.= base64_decode($item['nickname']);
                            }else{
                                $helps.= base64_decode($item['nickname']).'、';
                            }
                        }
                    }
                    $count_helps = $count_helps ? count($count_helps) : 1;
                    $help_userinfo = orders::getUserInfo($is_helps->order_id);
                    if (!$help_userinfo) {
                        fun_respon(1, ['token' => $token, 'userinfo' => $user_info]);
                    }
                    if ($help_userinfo['helps'] <= $count_helps) {
                        orders::where(['id' => $is_helps->order_id])->update(['order_status' => 3]);
                        //发送通知
                        $jssdk = new JSSDK();
                        $touser = $help_userinfo['openid'];
                        $formId = $help_userinfo['form_id'];
                        $template_id = env('s_template_id');
                        $pages = 'pages/index/index';
                        $prompt = '恭喜您已经完成助力！点击领取》';
                        //示例数据根据消息模板填充
                        $data = array(
                            'keyword1'=>array('value'=>$help_userinfo['name'],'color'=>'#7167ce'),
                            'keyword2'=>array('value'=>(string)$help_userinfo['price'].'元','color'=>'#7167ce'),
                            'keyword3'=>array('value'=>'0元','color'=>'#ff0000'),
                            'keyword4'=>array('value'=>$helps,'color'=>'#7167ce'),
                            'keyword5'=>array('value'=>$prompt,'color'=>'#ff0000'),
                        );
                        $res = $jssdk->sendTemplate($touser,$template_id,$pages,$data,$formId);
                    }
                    // 查看是第几位助力人，如果是最后一个助力人
                    $str = "您成为第%d位助力人，%s在你的帮助下即将免费领取到“【%s】%s”。";
                    $str = sprintf($str, $count_helps, base64_decode($help_userinfo['nickname']), $help_userinfo['name'], $help_userinfo['description']);
                    fun_respon(1, ['token' => $token, 'helps' => $str, 'userinfo' => $user_info]);
                }
                fun_respon(1, ['token'=>$token, 'userinfo'=>$user_info]);
            }
        } else {
            fun_respon(0, '解码失败！');
        }
    }


    /**
     *  创建新的Token
     * @param string $openid
     * @param string $additive
     * @return string
     */
    public function createToken($openid, $unionid)
    {
        if (empty($unionid)) return false;
        $key = 'token_' . $unionid;
        $value = Redis::get($key);
        if ($value) {
            return $value;
        }
        $credentials = [
            'sub' => $unionid,
            'exp' => time()+60*60*24*31,
            'openid' => $openid
        ];

        $payload = JWTFactory::make($credentials);
        $token = JWTAuth::encode($payload)->__toString();
        Redis::setex($key, 60*60*12, $token);
        return $token;
    }

    /**
     * 获取拼团成功的状态
     * @param Request $request
     */
    public function receivetype(Request $request)
    {
        $uid = $request->input('validate_uid');
        $user_info = users::where(['id' => $uid, 'is_valid'=>1])->first();
        $user_info = $user_info->toArray();
        //查询用户是否有助力成功的订单的订单
        $current = orders::currentList($user_info['unionid']);
        if (!$current) {
            fun_respon(1, []);
        }
        $finish = [];
        foreach ($current as &$v) {
            $helps = order_helps::where(['order_id'=>$v['id']])->count();
            if ($helps >= $v['helps']) {
                $finish[] = $v;
            }
        }
        fun_respon(1, $finish);
    }

    /**
     * 添加群ID
     */
    public function addgroup(Request $request)
    {
        $unionid = $request->input('unionid');
        // 'o9rflt8FG8LWYSEU7NWgoucpaU6M','o9rfltwoN4-mbNrlCohUNPQ5zv4s','o9rflt07CFsEc-nCvIDfjWzj92uM','o9rflt5Ec6kg8CBAWgUdfcU_OEbg'
        if (!in_array($unionid, ['o9rfltxLniGy5XvsiDsllDRuljhw'])) {
            fun_respon(0, '无权限');
        }
        $groupid = $request->json('groupid');
        if (empty($groupid)) fun_respon(0, '缺少参数');

        $valid_groupid = groups::where(['group'=>$groupid])->first();
        if ($valid_groupid) fun_respon(1, '添加成功');
        $res = groups::insert(['group'=>$groupid,'is_valid'=>1,'ct'=>date('Y-m-d H:i:s')]);
        if ($res) fun_respon(1, '添加成功');
        fun_respon(0, '添加失败');
    }

    /**
     * 获取群ID
     * @param Request $request
     */
    public function getgid(Request $request)
    {
        $code = $request->json('code');
        $iv = $request->json('iv');
        $cryptData = $request->json('cryptData');

        $rs = WxSmallClient::getSessionKey($code);
        $array_user = json_decode($rs);

        if (!is_object($array_user)) fun_respon(0, '网络异常请重试');
        if ( !property_exists($array_user, 'session_key')) fun_respon(0, '解码失败');

        $Datas = WxSmallClient::decryptData($array_user->session_key, $iv, $cryptData);
        $Datas = json_decode($Datas, true);
        if (is_array($Datas) && !empty($Datas)) {
            fun_respon(1, $Datas);
        }
        @Storage::disk('logs')->append('group.log', 'time:'.date('Y-m-d H:i:s').$Datas);
        fun_respon(0, '获取失败');
    }

    /**
     * [sendCode 发送短信验证码]
     * @return
     */
    public function sendCode(){
        $data = json_decode(file_get_contents('php://input'),true);
        $phone = $data['phone'];
        $rule  = "/^1[34578]{1}\d{9}$/";
        $result = preg_match($rule,$phone);
        if(!$result){
            $this->respon(0,'手机号格式不正确！');
        }
        $type = isset($data['type'])?$data['type']:0;
        if($type==1){
            $channel = users::where(['phone'=>$data['phone']])->first();
            if(isset($channel->id) && $channel->id){
                $this->respon(0,'手机号已经注册！');
            }
        }
        $num = rand(100000,999999);
        $tpl_id = "55654";
        send_message($phone,$tpl_id,$num);
        
    }
}
