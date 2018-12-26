<?php
namespace application\service;

class Stats {
    use \phpec\core\DITrait;

    //标记状态
    function add($uid, $poiId, $type) {
        $data = [
            'user_id' => $uid,
            'poi_id'  => $poiId,
        ];
        switch($type) {
            case 'signin':      $data['type'] = 1; break;
            case 'like':        $data['type'] = 2; break;
            case 'recommend':   $data['type'] = 3; break;
            default:return false;
        }
        if($this -> StatsModel -> getBy(['user_id=? and poi_id=? and type=?',array_values($data)])){
            return 0;
        } else {
            $this -> PoiModel -> inc($type, ['id=?',$poiId]);
            $this -> StatsModel -> add($data);
            return [
                'user_stats' =>  $this -> query($uid,$poiId),
                'pick_stats' => $this -> PoiModel -> get($poiId, ['fields'=> 'signin,like,recommend'])];
        }
    }
    //查询
    function query($uid, $poiId) {
        $row = $this -> StatsModel -> getBy(['user_id=? and poi_id=?', [$uid, $poiId]]);
        $result = [];
        foreach($row as $r) {
            switch($r['type']) {
                case 1: $result['signin']       = true; break;
                case 2: $result['like']         = true; break;
                case 3: $result['recommend']    = true; break;
            }
        }
        return $result;
    }
}