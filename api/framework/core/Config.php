<?php
namespace phpec\core;

class Config {
    use DITrait;

    // 获取指定配置，如果没有，使用$default
    function get($k, $default = null){
        $re = $this -> _getData($k);
        if($re === null) {
            $re = $default;
        }
        $this -> Logger -> debug('Config: key=%s, ...%s',$k, $re === null ? 'miss' : 'ok');
        return $re;
    }

    private function _getData($k){
        $data = APP_CONFIG;
        $ks = explode(".", $k);
        foreach ($ks as $k) {
            if (empty($k)) {
                $this -> Logger -> error('Config: _getData, key error');
            }
            if (isset($data[$k])) {
                $data = $data[$k];
            } else {
                return null;
            }
        }
        return $data;
    }
}
