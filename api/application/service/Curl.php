<?php
namespace application\service;
//curl请求

class Curl{

    use \phpec\core\DITrait;

    private $ctxOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];

    //用get方式请求微信接口
    function getWx($url){
        $res = file_get_contents($url, false, stream_context_create($this -> ctxOptions));
        $this -> Logger -> debug('CurlService: getWx, url=%s, res=%s', $url, $res);
        if($res) {
            $data = json_decode($res,true);
            if(false != $data) return $data;
        }
        return false;
    }
    //发送普通post请求
    public function post($url, $data){ 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		if (is_array($data)) {
			$data = http_build_query($data);
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		// curl_setopt($ch, CURLOPT_HEADER, true);
        $re = curl_exec($ch);
        $errno = (int)curl_errno($ch);
        $this -> Logger -> debug('CurlService: post, url=%s, data=%s, errno=%s, res=%s', $url, $data, $errno, $re);
        curl_close($ch);
		if ($errno !== 0) {
			return false;
		} else {
            return $re;
        }
	}
}