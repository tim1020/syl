<?php
namespace phpec\dal;
//创建数据模型处理
class Model {
    use \phpec\core\DITrait;

    private $prefix = '';
    private $pk = 'id';

    function __construct(){
        $this -> Logger -> debug('Model: __construct');
        $this -> prefix = $this -> Config -> get('pdo.prefix', '');
    }
    // 根据表名生成model对象
    function load($table){
        $this -> Logger -> debug('Model: load, prefix=%s, table=%s', $this -> prefix, $table);
        $table = sprintf('%s%s', $this -> prefix, strtolower($table));
        return new PDOProvider($table, $this -> pk);
    }
}