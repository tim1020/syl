<?php
namespace application\controller;
//后台管理权限拦载

class AdminAclController{
    use \phpec\core\DITrait;

    public function __construct(){
        $this -> Res -> setHeader('Access-Control-Allow-Origin', '*');
        $this -> Res -> setHeader("Access-Control-Allow-Credentials", "true");
        $this -> Res -> setHeader("Access-Control-Allow-Methods", "*");
        $this -> Res -> setHeader("Access-Control-Expose-Headers", "*");
        $this -> Res -> setHeader("Access-Control-Allow-Headers", "Content-Type,Accept");
        $this -> _checkToken();
    }

    //验证access_token
    protected function _checkToken(){
        if($this -> Request -> c == 'admin-signin') return;
        if($this -> Request -> headers['Method'] == 'POST'){
            $token = empty($this -> Request -> payload['access_token']) ? '' : $this -> Request -> payload['access_token'];
        }else{
            $token = $this -> Request -> access_token;
        }
        if(!$token) {
            $this -> Res -> error('缺少access_token',  ERR_AUTHFAIL);
        }
        $t = substr($token, 0, 10);
        $s = substr($token, -6);
        if(time() - intval($t) > 7 * 86400) {
            $this -> Res -> error('access_token过期', ERR_AUTHFAIL);
        }
        $c = substr($token, 0, -6);
        $key = $this -> Config -> get('admin.secret');
        if(substr(md5($c. $key), -6) != $s) {
            $this -> Res -> error('access_token无效', ERR_AUTHFAIL);
        }
    }
}