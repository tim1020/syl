<?php
namespace phpec\plugins;
//设置响应的header
class CheckMethod{

    use \phpec\core\DITrait;

    public function run($p = []){
        $method = $this -> Request -> headers['Method'];
        if (in_array($method,$p)){
            if ($method == 'OPTIONS'){
                $this -> Res -> setHeader('Access-Control-Allow-Origin', '*');
                $this -> Res -> setHeader("Access-Control-Allow-Credentials", "true");
                $this -> Res -> setHeader("Access-Control-Allow-Methods", "*");
                $this -> Res -> setHeader("Access-Control-Expose-Headers", "*");
                $this -> Res -> setHeader("Access-Control-Allow-Headers", "Content-Type,XSRF-TOKEN,Accept,X-XSRF-TOKEN");

                $this -> Res -> ok("ok");
            }
        }else{
            $this -> Res -> error("请求的方法被禁止", 405);
        }
    }
}