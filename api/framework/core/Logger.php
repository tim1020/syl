<?php
namespace phpec\core;
// 日志处理
class Logger {
    use DITrait;

    const LEVELS = [
        'debug'     => 1,
        'access'    => 2,
        'info'      => 4,
        'event'     => 8,
        'warn'      => 16,
        'error'     => 32
    ];
    const LEVEL_ALL = (1 << 6) - 1;
    const BUFF_SIZE = 100;

    private $hadAcc = false; //是否已打印过访问日志
    private $buff = [];//暂存日志内容
    private $bNums = 0;
     
    private function log($level, $msg, ...$args){
        if (!isset(self::LEVELS[$level])) {
            $level = 'warn';
        }
        if (self::LEVELS[$level] & (defined('LOG_LEVEL') ? LOG_LEVEL: self:: LEVEL_ALL)) {
            foreach($args as $k => $v) {
                if(is_array($v)) $args[$k] = json_encode($v, JSON_UNESCAPED_UNICODE);
            }
            $msg = sprintf($msg, ...$args);
            $msg  = sprintf("%s %s %s\n", date('H:i:s'), $this -> Request -> req_id, $msg);
            $this -> buff($level, $msg);
        }
    }
    //日志放到buff中
    private function buff($level, $msg){
        $this -> buff[$level][] = $msg;
        $this -> bNums ++;
        if($this -> bNums > self::BUFF_SIZE) {
            $this -> output();
            $this -> bNums = 0;
        }
    }
    // 输出日志
    private function output(){
        $log_path  = defined('LOG_PATH') ? LOG_PATH : APP_PATH.'/runtime/log/';
        foreach($this -> buff as $k => $data) {
            $target = sprintf("%s/%s-%s", $log_path, $k ,date('Ymd'));
            $dir = dirname($target);
            if(!file_exists($dir)) mkdir($dir,0755, true);
            file_put_contents($target, implode('', $data), FILE_APPEND);
        }
        $this -> buff = [];
    }
    public function __destruct(){
        if(!empty($this -> buff)) {
            $this -> output();
        }
    }

    // 调试日志
    public function debug($msg, ...$args){
        $this -> log('debug', $msg, ...$args);
    }
    // 访问日志
    public function access($msg, ...$args){
        if(!$this -> hadAcc) {
            $this -> log('access', $msg, ...$args);
            $this -> hadAcc = true;
        } else {
            $this -> log('warn', '不能重复输出访问日志');
        }
    }
    // 一般信息
    public function info($msg, ...$args){
        $this -> log('info', $msg, ...$args);
    }
    // 事件日志
    public function event($msg, ...$args){
        $this -> log('event', $msg, ...$args);
    }
    // 警告信息
    public function warn($msg, ...$args){
        $this -> log('warn', $msg, ...$args);
    }
    // 错误日志
    public function error($msg, ...$args){
        $this -> log('error', $msg, ...$args);
    }
}