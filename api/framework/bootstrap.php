<?php
date_default_timezone_set('asia/shanghai');
defined('APP_PATH') || trigger_error('未定义"APP_PATH"', E_USER_ERROR);

define('TS', microtime(1));

//内置响应编码(可被配置中err_code覆盖)
define('CODE_OK',           0);  // 正常处理
define('ERR_EXCEPTION',    -1);  // 程序内部异常
define('ERR_DEFAULT',       99); // 未定义错误
define('ERR_BAD_REQUEST',   1);  // 请求格式错误(一般缺少必填参数或不符要求)
define('ERR_AUTHFAIL',      2);

//自动加载
spl_autoload_register(function($class){
    $class = str_replace('.','', $class); //安全过滤
    $path = explode('\\', $class);
    $ns = array_shift($path);
    if ($ns == 'phpec') {
        $prefix = FRAMEWORK_PATH;
    } elseif($ns == 'application') {
        $prefix = APP_PATH;
    } else {
        return;
    }
    $classFile = $prefix. implode("/",$path).'.php';
    file_exists($classFile) && require $classFile;
});

//错误处理
set_error_handler(function($errno, $errstr, $errfile, $errline){
    if ($errno == E_USER_ERROR) {
        throw new \Exception($errstr, 1);
    }
    return false;
});