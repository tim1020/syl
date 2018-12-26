<?php
require __DIR__.'/../application/config.php';
require __DIR__.'/../framework/bootstrap.php';

$app = new \phpec\core\App();
try {
    $app -> run();
} catch(Exception $e) {
    $app -> Res -> error($e -> getMessage(), ERR_EXCEPTION);
}