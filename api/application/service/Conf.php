<?php
namespace application\service;


class Conf {
    use \phpec\core\DITrait;

    function get($type){
        $row = $this -> ConfModel -> getBy(['type=?',$type]);
        $result = [];
        if($row){
            foreach($row as $r) {
                $result[] = $r['val'];
            }
        }
        return $result;
    }
}