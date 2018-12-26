<?php
namespace application\service;
//微信小程序登录相关处理

class Wx{

    use \phpec\core\DITrait;

    private $appId;
    private $secret;

    //小程序登录
    const API_AUTH     = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
    //小程序获取token(基础token，与用户无关)
    const API_TOKEN    = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
 
    function __construct(){
        $conf = $this -> Config -> get("apps.".$this -> Request -> app_id);
        $this -> appId = $this -> Config -> get('wx_appid');
        $this -> secret = $this -> Config -> get('wx_secret');
    }
    //小程序登录
    function getSessionKey($code){
        $url = sprintf(self::API_AUTH, $this -> appId, $this -> secret, $code);
        $r = $this -> CurlService -> getWx($url);
        if(empty($r['session_key'] || empty($r['openid']))) {
            $this -> Logger -> error('WxminiService: auth fail, wx响应缺少必要字段,res=%s', $r);
            return false;
        }
        return $r;
    }
}