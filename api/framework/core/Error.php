<?php
namespace phpec\core;
//错误处理
class Error {
    private $msg;
    private $code;
    private $data;
    function __construct($msg, $code = ERR_DEFAULT, $data = null){
        $this -> msg  = $msg;
        $this -> code = $code;
        $this -> data = $data;
    }

    function __invoke(){
        return [$this -> msg, $this -> code, $this -> data];
    }
}