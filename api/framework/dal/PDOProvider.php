<?php
namespace phpec\dal;
//pdo方式实现数据访问

class PDOProvider{
    use \phpec\core\DITrait;
    private $table;
    private $pk = 'id';

    public function __construct($table, $pk){
        $this -> Logger -> debug('PDOProviderImpl: table=%s',$table);
        $this -> pk = $pk;
        $this -> table = $table;
    }
    //新增记录，返回新增的id
    public function add(array $data){
        @list($exp, $params) = $this -> _buildData($data);
        $sql = sprintf("insert into `%s` set %s", $this -> table, $exp);
        return $this -> exec($sql, $params);
    }
    //批量新增,$keys = [field1,fields],$data= [[v1,v2],[v1,v2]],返回插入的记录数
    public function batchAdd(array $keys, array $data){
        $fields = implode('`,`', $keys);
        $nums = count($keys);
        $holder = array_fill(0, $nums, '?');
        $p = [];
        foreach($data as $d) {
            if(count($d) != $nums) {
                trigger_error('keys and data not match',E_USER_ERROR);
            }
            $p = array_merge($p, $d);
        }
        $holders = array_fill(0, count($data), '('.implode(",", $holder).')');
        $sql = sprintf("insert into `%s` (`%s`) values %s",$this -> table, $fields, implode(",", $holders));
        return $this -> exec($sql, $p);
    }
    //根据id删除
    public function delete($id){
        $sql = sprintf('delete from `%s` where `%s`=?', $this -> table, $this -> pk);
        return $this -> exec($sql, $id);
    }
    //根据条件删除
    public function deleteBy(array $where){
        @list($exp, $params) = $where;
        $sql = sprintf('delete from `%s` where %s', $this -> table, $exp);
        return $this -> exec($sql, $params);
    }
    //根据id更新
    public function update(array $data, $id){
        @list($exp, $params) = $this -> _buildData($data);     
        $sql = sprintf('update `%s` set %s where `%s`=?', $this -> table, $exp, $this -> pk);
        $params[] = $id;
        return $this -> exec($sql, $params);
    }
    //根据条件更新
    public function updateBy(array $data, array $where) {
        @list($exp,$params) = $this -> _buildData($data);
        @list($wexp,$wparams) = $where;
        $sql = sprintf('update `%s` set %s where %s', $this -> table, $exp, $wexp);
        if($wparams){
            $p = is_array($wparams) ? $wparams : [$wparams];
            $params = array_merge($params, $p);
        } 
        return $this -> exec($sql, $params);
    }
    //根据id查询
    public function get($id, $options =[]){
        $fields = $this -> _parseFields($options);
        $jStr = $this -> _parseJoin($options);
        if($jStr) {
            $sql = sprintf("select %s from `%s` a%s where a.`%s`=?", $fields, $this -> table, $jStr, $this -> pk);
        } else{
            $sql = sprintf("select %s from `%s` where `%s`=?", $fields, $this -> table, $this -> pk);
        } 
        $r = $this -> exec($sql, [$id]);
        if($r) return $r[0];
        else return null;
    }
    //查询满足条件的最新一条记录,$options sort和size无效
    public function getLast(array $where, array $options = []) {
        $options['sort'] = sprintf('%s desc', $this -> pk);
        $options['size'] = 1;
        return $this -> getRow($where, $options);
    }
    //获取满足一条记录
    public function getRow(array $where, array $options =[]){
        $r = $this -> getBy($where, $options);
        if($r) return $r[0];
        else return null;
    }
    //查询符合条件的记录数
    public function getCount(array $where){
        @list($exp, $params) = $where;
        $sql = sprintf("select count(`%s`) as nums from `%s` where %s", $this -> pk, $this -> table, $exp);
        $r = $this -> exec($sql, $params);
        return intval($r[0]['nums']);
    }
    /**
     * 根据$where和$options查询多条记录
     * @param array $where 查询条件
     *  ['a=? and b=?', [1,2]]
     * @param array $options 查询选项
     *  [
     *    'page'   => 1, 'size' => 10, 'offset' => 0
     *    'sort'   => "a desc,b",
     *    'fields' => "a,b as b1,c"
     *  ]
     * @return [{},{}]
     */
    public function getBy(array $where, array $options = []){
        $jStr = $this -> _parseJoin(isset($options['join']) ? $options['join'] : '');
        $fields = $this -> _parseFields($options);
        $sort   = $this -> _parseSort($options);
        $limit  = $this -> _parseLimit($options);
        @list($exp, $params) = $where;
        $sql  = sprintf("select %s from `%s` a %s where %s%s%s", $fields, $this -> table, $jStr, $exp, $sort, $limit);
        return $this -> exec($sql, $params);
    }
    //执行分组查询
    public function group($fields, $by, array $where, array $options){
        @list($exp, $params) = $where;
        $sql = sprintf("select %s from `%s` where %s group by %s",$fields, $op, $this -> table, $exp, $by);
        $sort   = $this -> _parseSort($options);
        $limit  = $this -> _parseLimit($options);
        $sql.= sprintf(" %s%s", $sort, $limit);
        return $this -> exec($sql, $params);
    }
    //字段自增操作
    public function inc($field, $where, $step = 1){
        if(is_array($where)) {
            @list($exp, $params) = $where;
            $sql = sprintf('update `%s` set `%s` = `%s` + %s where %s',  $this -> table, $field, $field, $step, $exp);
        }else{
            $sql = sprintf('update `%s` set `%s` = `%s` + %s where %s=?',  $this -> table, $field, $field, $step,  $this -> pk);
            $params = $where;
        }
        return $this -> exec($sql, $params);
    }
    //字段自减操作
    public function dec($field, $where, $step = 1){
        if(is_array($where)) {
            @list($exp, $params) = $where;
            $sql = sprintf('update `%s` set `%s` = `%s` - %s where %s',  $this -> table, $field, $field, $step, $exp);
        }else{
            $sql = sprintf('update `%s` set `%s` = `%s` - %s where %s=?',  $this -> table, $field, $field, $step,  $this -> pk);
            $params = $where;
        }
        return $this -> exec($sql, $params);
    }

    /**
     * 执行SQL语句
     * @param string $sql 带占位的sql的语句(只支持insert,delete,update,select)
     * @param array $params 用来占位的参数
     * @return mixed 根据语句的返回，select返回结果集，insert返回insert_id，update|delete返回操作的记录数
     */
    public function exec(string $sql, $param=null){
        $this -> Logger -> debug('PDOProviderImpl: sql=%s, params=%s', $sql, $param);
        $ts = microtime(1);
        $ph = substr_count($sql, "?");
        $params = [];
        if ($ph > 0 && $param !== null) {
            if (!is_array($param)) $param = [$param];
            if ($ph != count($param)) {
                $this -> Logger -> error('PDOProviderImpl: placeholder not match');
                trigger_error('PDOProviderImpl: placeholder not match', E_USER_ERROR);
            }
            $p = '/\([\h]*\?[\h]*\)/';
            foreach ($param as $v) {
                if (!is_array($v)) $params[] = $v;
                else {
                    $params = array_merge($params, $v);
                    $placeHolder = '('.implode(",", array_fill(0, count($v), '?')).')';
                    $sql = preg_replace($p, $placeHolder, $sql, 1);
                } 
            }
        }
    
        $op = strtolower(substr($sql, 0, 6));
        if ($op == 'select') {
            $stmt = $this -> PDOConn -> getSlave() -> prepare($sql);
        } else {
            $stmt = $this -> PDOConn -> getMaster() -> prepare($sql);
        }
        foreach ($params as $k => $v) {
            $stmt -> bindParam($k+1, $params[$k], self::_getType($v));
        }
        $result = false;
        if ($stmt -> execute()) {
            if ($op == 'select') {
                $result =  $stmt -> fetchAll(\PDO::FETCH_ASSOC);
            } elseif ($op == 'insert') {
                $result = ["insert_id" => intval($this -> PDOConn -> getMaster() -> lastInsertId())];
            }
            else {
                $result = ["affect_rows" => $stmt -> rowCount()];
            }
        }
        $time = round(microtime(1) - $ts,4);
        $this -> PDOConn -> incNums();
        $this -> PDOConn -> incTime($time);
        $this -> Logger -> debug('PDOProviderImpl: exec elapsed_time=%s', $time);
        return $result;
    }
    /**
     * 执行事务操作
     * @param callable $func 要操作的指令
     * @return boolean
     */
    public function trans(callable $query){
        try {
            $this -> PDOConn -> getMaster() -> beginTransaction();
            $re = $query($err);
            if ($re === false) throw new \Exception("Transaction fail: ".$err); 
            return $this -> PDOConn -> getMaster() -> commit();
        } catch(\Exception $ex) {
            $this -> PDOConn -> getMaster() -> rollback();
            $this -> Logger -> error("PDOProviderImpl: trans fail，",$ex -> getMessage());
            return false;
        }
    }
    //从$options中生成安全的fields字串
    private function _parseFields($options){
        $fields = isset($options['fields']) ? $options['fields'] : '*';
        if($fields != '*') {
            $f = [];
            if(strpos(trim($fields),'distinct') === 0) { //指定distinct开头
                $dist = true;
                $fields = str_replace('distinct','', $fields);
            }
            foreach(explode(",", $fields) as $field){
                @list($n, $alias) = explode(' ', trim($field), 2);
                $n = strtolower(trim($n));
                if($n == '*' || substr($n, -2) == '.*') {
                    $f[] = $n;
                } else{
                    @list($t,$s) = explode(".",$n);
                    $alias = trim($alias);
                    if(!empty($s)) { //有写表名
                        $f[] = "$t.`$s` $alias";
                    } else{ //没写
                        $f[] = "`$n` $alias";
                    }
                }
            }
            $fields = implode(',', $f);
            if(!empty($dist)) $fields = 'distinct '.$fields;
        }
        return $fields;
    }
    //从$options中生成sort
    private function _parseSort($options){
        $sort = '';
        if(!empty($options['sort'])) {
            //判断sort的字段是否符合("a,b asc,c desc")
            $sorts = [];
            foreach(explode(',', $options['sort']) as $v) {
                @list($f, $d) = explode(' ', trim($v), 2);
                $f = strtolower(trim($f));
                if(!$d) $d = 'asc';
                else{
                    $d = strtolower(trim($d));
                    if($d !== 'desc' && $d != 'asc') {
                        trigger_error('PDOProviderImpl操作失败, sort定义无效',E_USER_ERROR);
                    }
                }
                $sorts[] = "`$f` $d"; 
            }
            //没有报错，正常返回
            $sort = ' order by '.implode(",", $sorts);
        }
        return $sort;
    }
    //解释join
    private function _parseJoin($options) {
        $join = isset($options['join']) ? $options['join'] : false;
        $this -> Logger -> debug('PDOProviderImpl: join=%s', $join);
        $jStr = '';
        if($join && isset($join['name']) && isset($join['on'])) {
            $name = sprintf('%s%s',$this -> Config -> get('pdo.prefix'),$join['name']);
            $type = isset($join['type']) ? $join['type'] : 'left';
            $jStr = sprintf(" %s join %s `b` on %s", $type, $name, $join['on']);
        }
        return $jStr;
    }
    //从$options中生成limit
    private function _parseLimit($options){
        if(!isset($options['size'])) return ''; //未指定size，返回全部
        $size = intval($options['size']);
        if(isset($options['offset'])) { //指定了offset，优先使用
            $offset = intval($options['offset']);
        } else {
            $page = isset($options['page']) ? intval($options['page']) :  1;
            $offset = ($page - 1) * $size;
        }
        return sprintf(' limit %d,%d', $offset, $size);
    }
    //处理add或update的data,['a'=>'b','a1'=>'b2'] => ['a=?,a1=?',['b','b2']]
    private function _buildData($data){
        if (!is_array($data) || empty($data)) {
            $this -> Logger -> error('PDOProviderImpl: _buildData error, data=%s', $data);
            trigger_error("PDOProviderImpl Error: \$data must be a array", E_USER_ERROR);
        }
        $fields = $params = [];
        foreach ($data as $k => $v) {
            $fields[] = "`$k`=?";
            $params[] = $v;
        }
        return [implode(",", $fields), $params];
    }
    static function _getType($val){
        $type = \PDO::PARAM_STR;
        if (is_bool($val))      $type = \PDO::PARAM_BOOL;
        elseif (is_int($val))   $type = \PDO::PARAM_INT;
        elseif (is_null($val))  $type = \PDO::PARAM_NULL;
        return $type;
    }
}