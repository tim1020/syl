<?php
namespace phpec\dal;
//pdo链接处理

class PDOConn{

    use \phpec\core\DITrait;

    private $nums = 0;   // 执行语句数
    private $times = 0; //执行时间
    private $conns = []; //连接的句柄，以app_id+ '_m|_s'为key
    
    /**
     * 获取主库连接
     */
    public function getMaster(){
        $idx = $this -> Request -> app_id . '_m';
        if(isset($this -> conns[$idx])) return $this -> conns[$idx];
        $conf = $this -> Config -> get('pdo.master');
        $this -> Logger -> debug('PDOConn: get master');
        if(!$conf){
            $conf = $this -> Config -> get('pdo');
            if(!$conf) trigger_error("没有找到数据库配置",E_USER_ERROR);
        }
        $conn = $this -> _conn($conf);
        if(is_object($conn)) {
            $this -> conns[$idx] = $conn;
            return $conn;
        }
        return null;
    }
    /**
     * 获取从库连接
     */
    public function getSlave(){
        $idx = $this -> Request -> app_id . '_s';
        if(isset($this -> conns[$idx])) return $this -> conns[$idx];
        $conf = $this -> Config -> get('pdo.slave');
        $this -> Logger -> debug('PDOConn: get slave');
        if(!$conf) { //没有设置从，使用主
            $conn = $this -> getMaster();
        } else {
            if (!empty($conf[0]) && is_array($conf[0])) { //有多个，随机选一个
                $k = array_rand($conf);
                $conf = $conf[$k];
            }
            $conn = $this -> _conn($conf);
        }
        if(is_object($conn)) {
            $this -> conns[$idx] = $conn;
            return $conn;
        }
        return null;
    }

    //建立pdo连接
    private function _conn($conf){
        $tmp = $conf;
        $tmp['password'] = '****';
        $this -> Logger -> debug('PDOConn: conf=%s', $tmp);
        if (empty($conf['dsn']) || empty($conf['user']) || !isset($conf['password'])) {
            $this -> Logger -> error('PDOConn: miss connect param, app_id=%s',$this -> Request -> app_id);
            trigger_error("PDO Error: miss connect param", E_USER_ERROR);
        }

        $options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION];
        if (!empty($conf['persistent'])) { //持久链接
            $options[\PDO::ATTR_PERSISTENT] = true;
        }
        return new \PDO($conf['dsn'], $conf['user'], $conf['password'], $options);
    }
    
    //执行的语句计数
    public function incNums(){
        $this -> nums ++;
    }
    public function incTime($t){
        $this -> times += $t;
    }
    public function getNums(){
        return $this -> nums;
    }
    public function getTime(){
        return $this -> times;
    }
}