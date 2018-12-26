<?php
namespace application\service;

class Poi {
    use \phpec\core\DITrait;

    //查询指定线路详情
    function getById($poiId) {
        if(!$poiId) {
            $r = $this -> PoiModel -> getLast(['1=1']);
            $poiId = rand(1, $r['id']);

        }
        $r = $this -> PoiModel -> get($poiId);
        if(!$r) return false;
        $rExt = $this -> ExtModel -> getBy(['poi_id=?',$poiId]);
        foreach($rExt as $row) {
            switch($row['type']) {
                case 1: $k = 'tags';      break;
                case 2: $k = 'related';   break;
                case 3: $k = 'photos';    break;
                default: continue;
            }
            $r[$k][] = $row['content'];
        }
        return $r;
    }
    //返回指定数量的最新线路（title, cover_thumb,desc,like,）
    function getNewest($nums){
        $options = [
            'fields'    => 'id,title,desc,like, signin,recommend, thumb',
            'sort'     => 'id desc',
            'size'      => $nums,
            'page'      => 1
        ];
        $list = $this -> PoiModel -> getBy(['1=1'], $options);
        return $this -> _stripDesc($list);
    }
    //随机
    function getRand($nums) {
        $sql = sprintf("select %s from `%s` AS t1 JOIN (SELECT ROUND(RAND() * (SELECT MAX(id) FROM `%s`)) AS id) AS t2 WHERE t1.id >= t2.id ORDER BY t1.id ASC LIMIT 1",
            "t1.id,title, `desc`, `like`, signin,recommend, cover",
            "poi","poi"
        );
        $r = [];
        $ids = [];
        //TODO: 获取封面图像
        for($i = 0;$i < $nums; $i++) {
            $row = $this -> PoiModel -> exec($sql);
            $re = $row[0];
            if(isset($ids[$re['id']])) continue;
            $ids[$re['id']] = 1;
            $r[] = $row[0];
        }
        return $this -> _stripDesc($r);
    }
    //最热(想去、推荐、去过)
    function getHot($nums){
        $options = [
            'fields'    => 'id,title, desc, like, signin,recommend, thumb',
            'sort'     => 'like desc, recommend desc, signin desc',
            'size'      => $nums,
            'page'      => 1
        ];
        $list = $this -> PoiModel -> getBy(['1=1'], $options);
        $list = $this -> _stripDesc($list);
        return $list;
    }
    //用户相关，$type = 'like|signin'
    function getByUser($uid, $type, $page, $size) {
        $offset = ($page - 1) * $size;
        if($type == 'signin')   $typeIdx = 1;
        elseif($type == 'like') $typeIdx = 2;
        $total = $this -> StatsModel -> getCount(['user_id=? and type=?', [$uid, $typeIdx]]);
        $list = [];
        if($total > 0) {
            $sql = sprintf("select t2.* from `stats` as t1 left join `poi` t2 on t1.poi_id=t2.id where t1.user_id=? and t1.type=? order by t1.id desc limit %d,%d", $offset, $size);
            $list =  $this -> PoiModel -> exec($sql, [$uid, $typeIdx]);
            $list = $this -> _stripDesc($list);
        }
        return ['total' => $total, 'list' => $list];
    }
    //搜索,返回 ['total'=>12, list=>[]],$sort = ['key'=> 0|1|2]
    function search($kw, $page, $size, $sort) {
        $list = [];
        $total = 0;
        $this -> Logger -> event('kw=%s',$kw);
        $options = [
            'size' => $size,
            'page' => $page
        ];
        $order = [];
        if(!empty($sort['like'])) {
            $order[] = $sort['like'] == 1 ? 'like desc': 'like asc';
        }
        if(!empty($sort['recommend'])) {
            $order[] = $sort['recommend'] == 1 ? 'recommend desc': 'recommend asc';
        }
        if($order) {
            $options['sort'] = implode(",", $order);
        }

        if(empty($kw)) { //未指定，返回全部
            $wh = ['1=1'];
        } else{
            $pos = strpos($kw, 'tag:');
            if (false !== $pos) { //kw=tag:xxx 按标签搜索
                $type = 'tag';
                $tag = substr($kw, 4);
                $total = $this -> ExtModel -> getCount(['type=? and content=?',[1, $tag]]);
                if($total > 0) {
                    $ids = [];
                    $row = $this -> ExtModel -> getBy(['type=? and content=?',[1, $tag]]);
                    foreach($row as $r) {
                        $ids[] = $r['poi_id'];
                    }
                    $wh = ['id in (?)', [$ids]];
                }
            } else { //普通关键字搜索，从标题搜
                $wh = ['title like ?', "%$kw%"];
            }
        }
        if(empty($type) || $type != 'tag') { //tag方式已有total
            $total = $this -> PoiModel -> getCount($wh);
        } 
        if($total > 0) {
            $list  = $this -> PoiModel -> getBy($wh, $options);
            $list  = $this -> _stripDesc($list);
        }
        return [
            'total' => $total,
            'list' => $list
        ];
    }
    //去掉desc的html标签
    private function _stripDesc($rows) {
        foreach($rows as $k => $r) {
            if(isset($r['desc'])) $r['desc'] = strip_tags($r['desc']);
            $rows[$k] = $r;
        }
        return $rows;
    }
}