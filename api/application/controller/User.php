<?php
namespace application\controller;

class User extends BaseController{
    //登录,提交wx.login所获得的code,返回token
    function signin($p){
        if(empty($p['code'])) {
            $this -> Res -> error('非法访问', AUTH_FAIL);
        }
        $token = $this -> UserService -> signin($p['code']);
        return ['token' => $token];
    }
    //更新当前用户资料, nickname,avatar
    function update($p) {
        $this -> _checkToken();
        return $this -> UserService -> updateProfile($p);
    }
    //对线路标记,$p = ['poi_id'=>'', 'type' => 'signin|like|recommend']
    function addStats($p){
        $this -> _checkToken();
        if(empty($p['poi_id'])  || empty($p['type']) || false === array_search($p['type'], ['signin','like','recommend'])) {
            return $this -> Res -> error('请求参数错误', ERR_BAD_REQUEST);
        }
        return $this -> StatsService -> add($this -> Request -> user_id, $p['poi_id'], $p['type']);
    }
    //查询
    function queryStats($p){
        if(empty($p['poi_id'])) {
            return $this -> Res -> error('请求参数错误', ERR_BAD_REQUEST);
        }
        $this -> _checkToken();
        return $this -> StatsService -> query($this -> Request -> user_id, $p['poi_id']);
    }
    //我的线路($p = ['type'=>'like|signin', 'page'=>1, 'size'])
    function getPoi($p) {
        $this -> _checkToken();
        $page = empty($p['page']) ? 1 : intval($p['page']);
        $size = empty($p['size']) ? 10 : intval($p['size']);
        if(empty($p['type']) || false === array_search($p['type'], ['like','signin'])) {
            return $this -> Res -> error('请求参数错误', ERR_BAD_REQUEST);
        }
        return $this -> PoiService -> getByUser($this -> Request -> user_id, $p['type'], $page, $size);
    }
}