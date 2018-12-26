<?php
// 环境变量配置(缺省值)，请勿直接修改default设置,有需要修改时去相应环境的配置中修改或新增

defined('APP_PATH')        || define('APP_PATH',        __DIR__.'/../');                //application所在路径
defined('FRAMEWORK_PATH')  || define('FRAMEWORK_PATH',  APP_PATH.'/../framework/');     //framework所在路径
defined('RUNTIME_PATH')    || define('RUNTIME_PATH',    APP_PATH.'/runtime/');          //运行时目录
defined('LOG_LEVEL')       || define('LOG_LEVEL',       2+32);                          //日志级别 access+error
defined('LOG_PATH')        || define('LOG_PATH',        RUNTIME_PATH.'log/');
defined('IMG_PATH')        || define('IMG_PATH',        RUNTIME_PATH.'imgs/');        //头像存放目录