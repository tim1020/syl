<?php
//开发环境配置

define('APP_HOST',     'http://localhost:8181/index.php');
define('PDO_HOST',     'localhost:3306');
define('PDO_USER',     'root');
define('PDO_PASS',     '');
define('DB_NAME',       'syl');
define('LOG_LEVEL',    (1 << 6) - 1);  //输出所有日志

//开启系统日志
ini_set('display_errors',1);
error_reporting(E_ALL);
