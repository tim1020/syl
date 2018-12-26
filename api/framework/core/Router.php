<?php
namespace phpec\core;
//服务路由处理

class Router{
    use DITrait;
    public function __construct(){
        $this -> Logger -> debug('Router: __construct');
    }
    /**
     * 采用规则路由，根据url的api参数分发，api="controller-action",如果没有指定action，则使用index作为缺省值
     */
    public function run(){
        @list($c, $a) = explode('-', $this -> Request -> c, 2);
        $c = ucfirst($c);
        if(!$a) $a = 'index';
        $this -> Logger -> debug('Router: controller=%s, method=%s', $c, $a);
        $class = sprintf("\\application\\controller\\%s", $c);
        if(!class_exists($class)) {
            $this -> Res -> error('API Not Found(controller not exists)', ERR_DEFAULT);
        }
        if(!method_exists($class, $a)) {
            $this -> Res -> error('API Not Found(action not exists)', ERR_DEFAULT);
        }
        $r = call_user_func([new $class, $a], $this -> Request -> payload);
        $this -> Res -> ok($r);
    }
    
}
