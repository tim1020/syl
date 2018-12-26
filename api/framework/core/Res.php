<?php
namespace phpec\core;
//响应封装
class Res{
    use DITrait;
    private $headers = [];
    function setHeader($k, $v){
        $this -> headers[$k] = $v;
    }

    function redirect($url) {
        $this -> setHeader('Location', $url);
        $this -> flush('正在跳转...');
    }
    //通用方法
    public function ok($data){
        $body = [
            'code'  => CODE_OK,
            'error' => null,
            'data'  => $data,
        ];
        $this -> Res -> setHeader('Content-Type', 'application/json;charset=utf-8');
        $this -> Res -> flush($body);
    }
    public function error($error, $code = ERR_DEFAULT, $data = null){
        if($code == null) $code = ERR_DEFAULT;
        $body = [
            'code'  => $code,
            'error' => $error,
            'data'  => $data,
        ];
        $this -> Res -> flush($body);
    }
    //输出响应
    public function flush($data){
        if(is_array($data)) {
            $this -> Res -> setHeader('Content-Type', 'application/json;charset=utf-8');
            $content = json_encode($data);
        } else {
            $content = $data;
        }
        if (!headers_sent($f,$l)) {
            foreach ($this -> headers as $k => $v) {
                header("$k:$v");
            }
        }
        echo $content;;
        function_exists('fastcgi_finish_request') && fastcgi_finish_request();
        $len = strlen($content);
        $time = round(microtime(1) - TS, 3);
        $this -> Logger -> debug('SQL: total=%s，time=%s', $this -> PDOConn -> getNums(), $this -> PDOConn -> getTime());
        $this -> Logger -> debug('Res: content_length=%s, total_time=%s', $len, $time);
        //访问日志： method`url`code`time`length`error`data`header`payload`
        $this -> Logger -> access('%s`%s`%d`%s`%d`%s`%s',
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            isset($content['code']) ? $content['code'] : 0,
            $time,
            $len,
            $this -> Request -> headers,
            $this -> Request -> payload
        );
        exit;
    }

    //简单模板渲染
    public function view($html, Array $data){
        $f = VIEW_PATH. $html;
        if(!file_exists($f)) {
            exit('模板文件不存在');
        }
        header("Content-Type:text/html;chartset=utf8");
        extract($data);
        include $f;
        function_exists('fastcgi_finish_request') && fastcgi_finish_request();
        exit;
    }
}