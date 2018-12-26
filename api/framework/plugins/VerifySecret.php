<?php
namespace phpec\plugins;
/**
 * 验证密钥：$p = ['skip'=>['']]  (skip为不需要验证的接口) 
 */

class VerifySecret{

    use \phpec\core\DITrait;
    public function run($p = []){
        if(!empty($p['skip']) && false !== array_search($this -> Request -> c, $p['skip'])) {
            return;
        }
        if(empty($this -> Request -> secret)) {
            $this -> Res -> error('缺少secret参数', ERR_AUTHFAIL);
        }
        if($this -> Request -> secret != $this -> _expect()) {
            $this -> Res -> error('secret不匹配', ERR_AUTHFAIL);
        }
        return;
    }

    private function _expect() {
        $key = $this -> Config -> get(sprintf('apps.%s', $this -> Request -> app_id), '');
        if(!$key) trigger_error('未配置secret', E_USER_ERROR);
        return $key;
    }
}