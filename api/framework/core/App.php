<?php
namespace phpec\core;
//主控程序
class App {

    use DITrait;

    function __construct(){
        $this -> Request -> parse();
    }

    public function run(){
        $this -> Logger -> debug('App: run');
        $this -> _heartbeat();
        $this -> Request -> verify();
        $this -> _loadPlugins();
        $this -> _dispatch();
    }

    private function _loadPlugins(){
        $this -> Logger -> debug('App: _loadPlugins');
        $plugins = $this -> Config -> get('plugins', []);
        foreach($plugins as $k=>$v) {
            $this -> Logger -> debug('App: plugin=%s, params=%s', $k, $v);
            $this -> $k -> run($v);
        }
    }

    // 路由转发
    private function _dispatch(){
        $this -> Logger -> debug('App: _dispatch');
        $this -> Router -> run();
    }
    private function _heartbeat(){
        if(isset($_GET['heartbeat'])){
            $data = [
                'time' => time(),
                'mode' => ENV_MODE
            ];
            $this -> Res -> ok($data);
        }
    }
}
?>