<?php
header ( "Content-Type:text/html; charset=utf-8" );
define ( 'LIB_PATH', './ShuaiPHP/' );
define ( 'APP_PATH', './App/' );
define ( 'APP_DEBUG', true );
// require LIB_PATH . 'Start.php';
// echo get_include_path () . PATH_SEPARATOR ;
function func1() {
	static $count = 0; // 初始化静态变量,所有函数共享一个静态变量$count
	$count ++; // 注意这个累加的过程,每调用一次本函数,$count累加1
	echo $count . "<br>";
}

// func1();func1();func1();func1();
function convert($size) {
	$unit = array (
			'b',
			'kb',
			'mb',
			'gb',
			'tb',
			'pb' 
	);
	return @round ( $size / pow ( 1024, ($i = floor ( log ( $size, 1024 ) )) ), 2 ) . ' ' . $unit [$i];
}
//echo convert ( memory_get_usage ( true ) );

echo convert ( 1024*1024*1024 );