<?php
namespace application\controller;
//控制器基类

class BaseController{
    use \phpec\core\DITrait;

    //验证access_token
    protected function _checkToken(){
        if($this -> Request -> headers['Method'] == 'POST'){
            $token = empty($this -> Request -> payload['access_token']) ? '' : $this -> Request -> payload['access_token'];
        }else{
            $token = $this -> Request -> access_token;
        }
        if(ENV_MODE == 'dev' && !$token) {//dev模式时不传token可不验证
            return;
        }
        if(!$token) {
            $this -> Res -> error('access_token无效', $this -> Config -> get('err_code.auth_fail', ERR_AUTHFAIL));
        }

        $r = $this -> UserModel -> getRow(['token=?',$token]);
        if(!$r) {
            $this -> Res -> error('access_token无效', $this -> Config -> get('err_code.auth_fail', ERR_AUTHFAIL));
        }else{
            $this -> Request -> user_id = intval($r['id']);
        }
        $this -> Logger -> debug('BaseController: _checkToken ok, token=%s, user_id=%s', $token, $r);
    }
    //以service结果进行响应
    protected function _render($result){
        if(is_a($result, 'phpec\core\Error')) {
            $this -> Res -> error(...$result());
        }elseif(false == $result) {
            $this -> Res -> error('服务错误', ERR_DEFAULT);
        }
        $this -> Res -> ok($result);
    }

    //check AdminToken
    protected function _checkAdminToken(){

    }
    //add Origin
    protected function _addAdminOrigin(){
        $this -> Res -> setHeader('Access-Control-Allow-Origin', '*');
        $this -> Res -> setHeader("Access-Control-Allow-Credentials", "true");
        $this -> Res -> setHeader("Access-Control-Allow-Methods", "*");
        $this -> Res -> setHeader("Access-Control-Expose-Headers", "*");
        $this -> Res -> setHeader("Access-Control-Allow-Headers", "Content-Type,Access-Token");

    }
}