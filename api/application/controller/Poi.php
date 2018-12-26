<?php
namespace application\controller;

class Poi extends BaseController{

    //根据id获取
    function getDetail($p){
        $id = empty($p['poi_id']) ? '' : intval($p['poi_id']);
        return $this -> PoiService -> getById($id);
    }
    //随机
    function getRand($p) {
        $nums = !empty($p['nums']) ? $p['nums'] : $this -> Config -> get('poi_rand_nums', 3);
        return $this -> PoiService -> getRand($nums);
    }
    //最新
    function getNewest($p) {
        $nums = !empty($p['nums']) ? $p['nums'] : $this -> Config -> get('poi_newest_nums', 5);
        return $this -> PoiService -> getNewest($nums);
    }
    //最热
    function getHot($p){
        $nums = !empty($p['nums']) ? $p['nums'] : $this -> Config -> get('poi_hot_nums', 10);
       // $kw = !empty($p['kw']) ? $p['kw'] : '';
        return $this -> PoiService -> getHot( $nums);
    }
    //搜索
    function search($p){
        $kw = empty($p['kw']) ? '' : $p['kw']; //搜索关键字，可空
        $page = empty($p['page']) ? 1 : intval($p['page']);
        $size = empty($p['size']) ? 10 : intval($p['size']);
        $sort = empty($p['sort']) ? [] : $p['sort'];
        $r = $this -> PoiService -> search($kw, $page, $size, $sort);
        return $r;
    }
}
