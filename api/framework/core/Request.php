<?php
namespace phpec\core;

if (!function_exists('getallheaders')) {
    function getallheaders(){ 
        $headers = [];
        foreach ($_SERVER as $name => $value)  {
            if (substr($name, 0, 5) == 'HTTP_') {
                $k = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$k] = $value; 
            } 
        } 
        return $headers; 
    } 
} 
// 请求参数处理
class Request {

    use DITrait;

    private $headers = [];
    private $payload = [];
    private $requiredP = ['app_id' ,'c']; //必填的URL参数
    
    //解释请求参数
    public function parse(){
        $this -> _parseParams();
        $this -> headers = getallheaders();
        $this -> headers['Method'] = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        $this -> _parsePayload();
        //请求标识
        $this -> req_id = sprintf('%s-%s-%s', substr(md5(TS), -4), $this -> app_id,$this -> c);
        $this -> Logger -> debug('App: start>>>');
        $this -> Logger -> debug('Request: headers=%s', $this -> headers);
        $this -> Logger -> debug('Request: params=%s',  $_GET);
        $this -> Logger -> debug('Request: payload=%s', $this -> payload);
    }
    //验证参数apibox->run时执行
    public function verify(){
        $this -> _checkRequiredParam();
        $this -> _verifyParam();
    }

    private function _parseParams(){
        foreach($_GET as $k => $v) {
            $this -> $k = strip_tags($v);
        }
    }
    //获取payload数据
    private function _parsePayload(){
        if($this -> headers['Method'] == 'POST' && isset($this -> headers['Content-Type'])){
            list($ct,) = explode(";",$this -> headers['Content-Type']);
            switch(trim($ct)) {
                case 'multipart/form-data':
                case 'application/x-www-form-urlencoded':
                    $this -> payload = $_POST;
                    break;
                case 'application/json':
                    $input = file_get_contents("php://input");
                    if($input) {
                        $payload = json_decode($input, true);
                        if(false !== $payload) {
                            $this -> payload = $payload;
                        }
                    }
            }
        }
    }
    //判断必填的url参数
    private function _checkRequiredParam(){
        foreach($this -> requiredP as $k) {
            if (!$this -> {$k}) {
                $this -> Logger -> warn('Request: _requiredParams, 缺少必填参数：%s', $k); //写warning，监控需关注
                $this -> Res -> error('缺少必填参数:'.$k, $this -> Config -> get('err_code.bad_request', ERR_BAD_REQUEST));
            }
        }
        //api=controll-action

    }
    private function _verifyParam() {
        $apps = $this -> Config -> get('apps');
        if(empty($apps[$this -> app_id])) {
            $this -> Logger -> error('Request:  appid invalid, appid=%s',$this -> app_id);
            trigger_error('appid无效', E_USER_ERROR);
        }
    }
}