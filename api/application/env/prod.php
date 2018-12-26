<?php
//生产环境配置

define('APP_HOST',     'https://fullyoung.vip/lj/index.php');
define('PDO_HOST',     'localhost');
define('PDO_USER',     'ADMIN');
define('PDO_PASS',     '~~');
define('DB_NAME',       'syl');
define('RUNTIME_PATH', __DIR__.'/runtime/');

//关闭系统日志
ini_set('display_errors', 0);