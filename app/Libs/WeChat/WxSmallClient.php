<?php
/**
 * 微信小程序接口类
 *  app id    wx51fa2f9eabf66605
 *  secert:   1d3e10ce3ea7da269e10f7805564d2c9
 */

namespace App\Libs\WeChat;

use Ixudra\Curl\Facades\Curl;

class WxSmallClient {
    private static $WX_URL    = 'https://api.weixin.qq.com';
    // 每日优鲜
    private static $WX_APP_ID = 'wx04af487490fa1713';
    private static $WX_SECRET = '76164f6824c72e1269f784cb98a309f1';

    // 测试
    //private static $WX_APP_ID = 'wxd3e354f1b0d7feb3';
    //private static $WX_SECRET = '0caf9493b5190e06f4d96eb3a3158870';

    public static function getSessionKey($code)
    {
        if (empty($code)) {
            return false;
        }
        return Curl::to( self::$WX_URL . '/sns/jscode2session')
            ->withData([
                'appid'      => self::$WX_APP_ID,
                'secret'     => self::$WX_SECRET,
                'js_code'    => $code,
                'grant_type' => 'authorization_code'
            ])
            ->get();
    }

    /**
     * 根据session_key解密用户数据
     */
    public static function decryptData($session_key, $iv, $datas)
    {
        include_once "BizCrypt/wxBizDataCrypt.php";
        $pc = new \WXBizDataCrypt(self::$WX_APP_ID, $session_key);

        $rs = $pc->decryptData( $datas, $iv, $data );
        if ($rs == 0) {
            return $data;
        } else {
            return $rs;
        }
    }
}