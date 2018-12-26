<?php
namespace phpec\core;

// 实现依赖自动注入
Trait DITrait{

    public function __get($k){
        if(isset($this -> $k)) return $this -> $k;
        $objs = Container::getInstance();
        if (isset( $objs -> $k)) {
            return  $objs -> $k;
        }
        if (empty($objs -> Config)) {  //Config is inject default
            $objs -> Config = new Config;
        }
        if (preg_match('/^[A-Z]/', $k)) {
            $obj = null;
            while(true){
                if(substr($k, -5) == 'Model' && strlen($k) > 5) {
                    $obj = $this -> Model -> load(substr($k, 0, -5));
                    break;
                }
                if(substr($k, -7) == 'Service' && strlen($k) > 7) {
                    $class = sprintf("\\application\\service\\%s", substr($k, 0, -7));
                    if(class_exists($class)){
                        $obj = new $class;
                        break;
                    }
                }
                foreach(['core','dal', 'plugins','libs'] as $dir) {
                    $class = sprintf("\\phpec\\%s\\%s", $dir, $k);
                    if(class_exists($class)){
                        $obj = new $class;
                        break 2;
                    }
                }
                $class = sprintf("\\application\\controller\\%s", $k);
                if(class_exists($class)){
                    $obj = new $class;
                }
            }
            if($obj){
                $objs -> $k = $obj;
                return $obj;
            }
        }
        return null;
    }
}