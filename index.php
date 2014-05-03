<?php
header ( "Content-Type:text/html; charset=utf-8" );
define ( 'APP_PATH', './App/' );
define ( 'APP_DEBUG', true );
//require '/ShuaiPHP/Start.php';
echo 'hello world';
// 页面缓存
ob_start();
ob_implicit_flush(0);
include 't.php';
// 获取并清空缓存
$content = ob_get_clean();

//echo $content;
