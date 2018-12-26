<?php
namespace application\controller;
//配置相关

class Conf extends BaseController{
    const T_HOT_WORDS = 1;
    const T_TYPES = 2;
    //搜索热词
    function hotWords($p){
        //TODO: 建立搜索记录
        return $this -> ConfService -> get(self::T_HOT_WORDS) ;
    }
    //标签列表
    function tags($p){
        return $this -> ConfService -> get(self::T_TYPES);
    }
}