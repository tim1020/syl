<?php
namespace application\service;


class User {
    use \phpec\core\DITrait;

    //更新资料(昵称，头像)
    function updateProfile(Array $arr) {
        $this -> Logger -> debug('UserService: updateProfile, arr=%s,uid=%s', $arr, $this -> Request -> user_id);
        $uid = intval($this -> Request -> user_id);
        if(!$uid) return false;
        $data = ['mtime' => time()];
        if(!empty($arr['nickName'])) {
            $data['wx_nickname'] = $arr['nickName'];
        }
        if(!empty($arr['avatarUrl'])) {
            $data['wx_avatar'] = $arr['avatarUrl'];
        }
        return $this -> UserModel -> update($data, $uid);
    }
    //微信登录验证
    function signin($code){
        $r = $this -> WxService -> getSessionKey($code);
        if(false == $r) {
            return new \phpec\core\Error('登录微信失败',AUTH_FAIL);
        }
        $openId  = $r['openid'];
        $sessKey = $r['session_key'];
        $token = $this -> _generateToken();
        $data = [
            'token' => $token,
            'mtime' => time()
        ];
     
        $r = $this -> UserModel -> getRow(['wx_id=?',$openId]);
        if($r) { //已有
            $r = $this -> UserModel -> update($data,$r['id']);
        } else {
            $data['wx_id']          = $openId;
            $data['wx_session_key'] = $sessKey;
            $data['ctime']          = time();
            $r = $this -> UserModel -> add($data);
       }
       if(false !== $r) return $token;
       return false;
    }
    //生成token
    private function _generateToken(){
        return md5(uniqid('',true));
    } 
}