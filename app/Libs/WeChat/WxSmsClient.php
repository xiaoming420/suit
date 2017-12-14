<?php
/**
 * 微信SMS接口类
 *  app id    wx51fa2f9eabf66605
 *  secert:   1d3e10ce3ea7da269e10f7805564d2c9
 */

namespace App\Libs\WeChat;

use App\Libs\WeChat\QcloudSms\SmsSingleSender;

class WxSmsClient {
    // private static $SMS_URL    = 'https://api.weixin.qq.com';
    private static $SMS_APP_ID = '1400029080';
    private static $SMS_SECRET = '334d99086eb0295cddc201d60c65e7a4';
    private static $SMS_OBJECT = '';
    private static $SMS_COUNTRY = '86';

    private static function __init() {
        if (self::$SMS_OBJECT == '') {
            self::$SMS_OBJECT = new SmsSingleSender(self::$SMS_APP_ID, self::$SMS_SECRET);
        }
    }

    public static function send($phone, $content)
    {
        self::__init();
        $resp = self::$SMS_OBJECT->send(0, $phone, $content, '', '');
        return json_decode($resp);
    }

    /**
     * 指定模板单发
     * 假设模板
     *    测试： 16835  发送时间：{1}
     *    验证码：16834  欢迎注册麦当劳会员，验证码为{1}（5分钟内有效), 请完成注册。
     */
    public static function tempSend($phone, $params, $tip = '16834', $country = '86')
    {
        self::__init();
        if (empty($country)) {
            $country = self::$SMS_COUNTRY;
        }
        if (empty($tip)) {
            $tip = '16834';
        }
        $resp = self::$SMS_OBJECT->sendWithParam($country, $phone, $tip, $params, "", "", "");
        return json_decode($resp);
    }
}