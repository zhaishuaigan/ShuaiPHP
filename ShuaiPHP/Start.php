<?php
// 框架入口文件
// 记录开始运行时间
$GLOBALS ['_beginTime'] = microtime ( TRUE );
defined ( 'APP_PATH' ) or define ( 'APP_PATH', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/App/' );
defined ( 'APP_RUNTIME_PATH' ) or define ( 'APP_RUNTIME_PATH', APP_PATH . 'Runtime/' );
defined ( 'APP_DEBUG' ) or define ( 'APP_DEBUG', false ); // 是否调试模式
defined ( 'APP_RUNTIME_FILE' ) or define ( 'APP_RUNTIME_FILE', APP_RUNTIME_PATH . 'runtime.php' );
if (! APP_DEBUG && is_file ( APP_RUNTIME_FILE )) {
	// 部署模式直接载入运行缓存
	require APP_RUNTIME_FILE;
} else {
	// 系统目录定义
	defined ( 'LIB_PATH' ) or define ( 'LIB_PATH', dirname ( __FILE__ ) . '/' );
	// 加载运行时文件
	require LIB_PATH . 'Common/runtime.php';
}