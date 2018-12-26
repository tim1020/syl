<?php
//一般地，部署时此配置只需要修改ENV_MODE,环境相关配置在application/env中
define('ENV_MODE',  'dev'); // dev | prod | test ..

$envDir = __DIR__. '/env/';
$envMode = $envDir. ENV_MODE.'.php';
file_exists($envMode) && require($envMode);
$envDefault = $envDir.'default.php';
file_exists($envDefault) && require($envDefault);

//配置内容
$config = [
    //插件设定
    'plugins' => [
        'CheckMethod' => ['POST', 'GET', 'OPTIONS'],
        'SetHeader'   => ['Content-Type' => 'application/json;charset=utf-8'],
        'VerifySecret'=> [
            'skip' =>[]
        ],
    ],
    //数据库连接
    'pdo' => [
        'dsn'        => sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4',PDO_HOST, DB_NAME),
        'user'       => PDO_USER,
        'password'   => PDO_PASS,
        'prefix'     => '',
        'persistent' => false
    ],
    //接入的应用
    'apps' => [
        '0x11' => ''
    ],
    //微信授权相关
    'wx_appid'      => '',
    'wx_secret'     => '',
    //错误码
    'err_code' => [
        'bad_request' => 1, 
        'auth_fail'   => 2
    ],
    //管理后台相关
    'admin' => [
        'user' => '',
        'pass' => '',
        'secret' => ''
    ],
    'poi_rand_nums'   => 5,
    'poi_newest_nums' => 5,
    'poi_hot_nums'    => 10
];

//设置为常量，方便在Config对象统一读取
define('APP_CONFIG', $config);
