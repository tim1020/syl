<?php
namespace phpec\plugins;
//设置响应的header
class SetHeader{

    use \phpec\core\DITrait;

    public function run($p = []){
        if(empty($p['Content-Type'])) {
            $p['Content-Type'] = 'application/json;charset=utf-8';
        }
        foreach($p as $k => $v) {
            $this -> Res -> setHeader($k, $v);
        }
    }
}