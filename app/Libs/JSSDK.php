<?php
namespace App\Libs;

use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Redis;

class JSSDK
{


    private $appid = 'wx5dd4f5ae95592d61';
    private $secrect = 'abd091a9ff677acbd84821dbe26725ff';
    private $accessToken;

    public $data = null;
    public $user = null;

    public function __construct()
    {
        $this->accessToken = $this->getToken();
    }

    /*
     *
     * 获取用户详细信息
     * @return 用户详细信息
     */
    public function __GetUserInfo()
    {
        $this->GetCode();
        $open_id = $this->user['openid'];
        $access_token = $this->user['access_token'];

        return $this->user;

        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$open_id&lang=zh_CN";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true);
    }

    /**
     * @param $appid
     * @param $appsecret
     * @return mixed
     * 获取token
     */
    public function getToken()
    {

        $data = json_decode(file_get_contents("access_token.json"));
        if ($data->expire_time < time()) {
            // 如果是企业号用以下URL获取access_token
            //$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secrect";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen("access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    /**
     * 推送消息
     * @param $touser  openid
     * @param $template_id  模板id
     * @param string $page 点击模板跳转地址
     * @param $data         模板数据
     * @param $formId
     * @param string $color 字体颜色
     * @return mixed
     */
    public function sendTemplate($touser, $template_id, $page = 'pages/index/index', $data, $formId, $color = '#173177')
    {
        $template = array(
            'touser' => $touser,
            'template_id' => $template_id,
            'page' => env('page'),
            'form_id' => $formId,
            'data' => $data,
        );
        $json_template = json_encode($template);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=" . $this->accessToken;
        $dataRes = Curl::to($url)
            ->withData(urldecode($json_template))
            ->post();
        $dataRes = json_decode($dataRes, true);
        return $dataRes;
    }


    /**
     * [doSend 公众号发送消息] 发送模板消息
     * @param $touser
     * @param $template_id
     * @param $url
     * @param $data
     * @param string $topcolor
     * @return
     */
    public function doSend($touser, $template_id, $url, $data, $topcolor = '#173177')
    {
        $template = array(
            'touser' => $touser,
            'template_id' => $template_id,
            'url' => $url,
            'topcolor' => $topcolor,
            'data' => $data
        );
        $json_template = json_encode($template);
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token= " . $this->LMaccessToken;
        $dataRes = $this->httpRequest($url, urldecode($json_template));
        $dataRes = json_decode($dataRes, true);
        return $dataRes;
    }

    /**
     * 推送客服消息
     * @return array
     */
    public function servicemsg($content = array())
    {
        if (!$content) {
            return false;
        }
        $accessToken = $this->getToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$accessToken";
        $resp = Curl::to($url)
            ->withContentType('application/json')
            ->withData(urlencode($content))
            ->asJsonRequest()
            ->post();
        $resp = json_decode($resp, true);
        return $resp;
    }


    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->appid,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }


    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket()
    {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("jsapi_ticket.json"));
        if ($data->expire_time < time()) {
            $accessToken = $this->getToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen("jsapi_ticket.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }

        return $ticket;
    }

    /**
     * 发送get请求
     * @param string $url
     * @return bool|mixed
     */
    private function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    /**
     * 发送post请求
     * @param string $url
     * @param string $param
     * @return bool|mixed
     */
    public function httpRequest($url, $post, $header = array(), $connectTimeout = 15, $readTimeout = 300)
    {
        if (function_exists('curl_init')) {
            $timeout = $connectTimeout + $readTimeout;
            $ch = curl_init();
            if (strpos($url, 'https://') !== false) {    // HTTPS
                //curl_setopt($ch, CURLOPT_SSLVERSION, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if ($post == 'get') {
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
            } else {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
    }

    /**
     *
     * @return 用户的openid
     */
    private function GetCode()
    {
        //通过code获得openid
        if (!isset($_GET['code'])) {
            //触发微信返回code码
            $baseUrl = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $url = $this->__CreateOauthUrlForUserCode($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $user = $this->GetOpenidFromCode($code);
            return $user;
        }
    }

    /**
     *
     * 构造获取能获取用户信息的code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForUserCode($redirectUrl)
    {
        $urlObj["appid"] = env('WX_APPID', 'wx5dd4f5ae95592d61');//WxPayConfig::APPID;
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        //$str = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxf0e81c3bee622d60&redirect_uri=http%3A%2F%2Fnba.bluewebgame.com%2Foauth_response.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    /**
     *
     * 通过code从公众平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return openid
     */
    public function GetOpenidFromCode($code)
    {
        $url = $this->__CreateOauthUrlForOpenid($code);
        //初始化curl
        $ch = curl_init();
        //设置超时
        //curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        //curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res, true);
        $this->user = $data;
        return $data;
    }

    /**
     *
     * 构造获取open和access_toke的url地址
     * @param string $code ，微信跳转带回的code
     *
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = env('WX_APPID', 'wx5dd4f5ae95592d61');//WxPayConfig::APPID;
        $urlObj["secret"] = env('WX_APPSECRET', 'abd091a9ff677acbd84821dbe26725ff');//WxPayConfig::APPSECRET;
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }

    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
}

