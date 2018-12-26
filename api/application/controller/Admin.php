<?php
namespace application\controller;

class Admin extends AdminAclController{
    const T_HW = 1; //热词
    const T_TAG = 2; //标签
    //登录
    function signin($p) {
        if(empty($p['user']) || empty($p['pass'])) {
            $this -> Res -> error('请填写帐号密码登录',ERR_BAD_REQUEST);
        }
        if($p['user'] != $this -> Config -> get('admin.user') || $p['pass'] != $this -> Config -> get('admin.pass')) {
            $this -> Res -> error('帐号密码错误，请重试',ERR_BAD_REQUEST);
        }
        $token = time(). substr(md5(uniqid('',true)), 0, 16);
        $sign = substr(md5($token.$this -> Config -> get('admin.secret')), - 6);
        $token.=$sign;
        return ['token' => $token];
    }
    //线路列表
    function lstPoi($p) {
        $size = empty($p['size']) ? 2 : intval($p['size']);
        $page = intval($p['page']);
        if(!$page) $page = 1;
        $option = ['size' => $size, 'page' => $page, 'sort' => 'id desc'];
        $total = $this -> PoiModel -> getCount(['1=1']);
        $result = [
            'total' => $total,
            'pages' => ceil($total / $size)
        ];
        if($total > 0) {
            $result['list'] = $this -> PoiModel -> getBy(['1=1'], $option);
        }
        return $result;
    } 
    //添加线路
    function addPoi($p){
        extract($p);
        if(empty($title) || empty($locate) || empty($desc) || empty($guide)) {
            return $this -> Res -> error('标题，所在地，描述，攻略 为必填字段',ERR_BAD_REQUEST);
        }
        if(empty($_FILES['cover_img']) || empty($_FILES['thumb_img']) || empty($_FILES['imgs'])) {
            return $this -> Res -> error('缺少必须的图片',ERR_BAD_REQUEST);
        }
        $cImgUrl = $this -> _saveImg('c',$_FILES['cover_img']);
        if(!$cImgUrl) {
            return $this -> Res -> error('保存封面图片失败',ERR_BAD_REQUEST);
        }
        $tImgUrl = $this -> _saveImg('t',$_FILES['thumb_img']);
        if(!$tImgUrl) {
            return $this -> Res -> error('保存封面小图失败',ERR_BAD_REQUEST);
        }
        
        $data = $p;
        unset($data['access_token'], $data['tags']);
        $data['ctime']      = time();
        $data['cover']      = $cImgUrl;
        $data['thumb']      = $tImgUrl;
        $data['like']       = rand(10,20);
        $data['signin']     = rand(10,20);
        $data['recommend']  = rand(10,20);
        $r = $this -> PoiModel -> add($data);

        if(false != $r) { //OK,插入相关
            if(!empty($tags)) $this -> _addPoiTags($r['insert_id'], $tags);
            $this -> _addPoiImgs($r['insert_id'], $_FILES['imgs']);
            return $r;
        }
        $this -> Res -> error('系统错误', ERR_DEFAULT);
    }
    //获取标签
    function tag($p){
        return $this -> ConfModel -> getBy(['type=?', self::T_TAG]);
    }
    //添加标签
    function addTag($p){
        if($this -> ConfModel -> getCount(['type=? and val=?',[self::T_TAG, $p['tag']]]) > 0) {
            return $this -> Res -> error('此标签已存在',ERR_BAD_REQUEST);
        }
        return $this -> ConfModel -> add(['type' => self::T_TAG, 'val' => $p['tag']]);
    }
    //删除标签
    function delTag($p){
        $id = intval($p['id']);
        return $this -> ConfModel -> deleteBy(['id=? and type=?', [$id, self::T_TAG]]);
    }
    //获取热词
    function hotword($p){
        return $this -> ConfModel -> getBy(['type=?',  self::T_HW]);
    }
    //添加热词
    function addHotword($p){
        if($this -> ConfModel -> getCount(['type=? and val=?',[self::T_HW, $p['word']]]) > 0) {
            return $this -> Res -> error('此热词已存在',ERR_BAD_REQUEST);
        }
        return $this -> ConfModel -> add(['type' => self::T_HW, 'val' => $p['word']]);
    }
    //删除热词
    function delHotword($p){
        $id = intval($p['id']);
        return $this -> ConfModel -> deleteBy(['id=? and type=?', [$id, self::T_HW]]);
    }

    //辅助方法
    //保存文件，返回路径
    private function _saveImg($t, $f){
        if($f['error'] != 0) return false;
        //TODO:SIZE,TYPE
        $fname = sprintf("%s/%s_%s.%s",date('Ym'),$t, time(),'jpg');
        $dest = IMG_PATH. $fname;
        $dir = dirname($dest);
        if(!file_exists($dir)) mkdir($dir, 0755,1); 
        move_uploaded_file($f['tmp_name'], $dest);
        return $fname;
    }
    //保存路线的标签
    private function _addPoiTags($id, $tags) {
        $tags = explode(" ", $tags);
        foreach($tags as $tag) {
            $tag = trim($tag);
            if(!$tag) continue;
            $data = ['poi_id' => $id, 'type' => 1, 'content' => $tag];
            $r= $this -> ExtModel -> add($data);
        }
    }
    //保存路线的图片
    private function _addPoiImgs($id, $imgs) {
        $f = [];
        foreach($imgs['name'] as $k=> $img) {
            $f[] = [
                'type' => $imgs['type'][$k],
                'tmp_name' => $imgs['tmp_name'][$k],
                'error' => $imgs['error'][$k],
                'size'  => $imgs['size'][$k]
            ];
        }
        foreach($f as $k => $ff) {
            $fname = sprintf("%s/%s_%s.%s", date('Ym'), $id, $k, 'jpg');
            $dest = IMG_PATH. $fname;
            $dir = dirname($dest);
            if(!file_exists($dir)) mkdir($dir, 0755,1);
            move_uploaded_file($ff['tmp_name'], $dest);
            $data = ['poi_id' => $id, 'type' => 3, 'content' => $fname];
            $this -> ExtModel -> add($data);
        }
    }
}