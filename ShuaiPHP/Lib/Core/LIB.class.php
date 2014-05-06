<?php
class LIB {
	static function Start() {
		// 设定错误
		set_error_handler ( array (
				'LIB',
				'appError' 
		) );
		// 异常处理
		set_exception_handler ( array (
				'LIB',
				'appException' 
		) );
		// 注册AUTOLOAD方法
		spl_autoload_register ( array (
				'LIB',
				'autoload' 
		) );
		
		self::loadConfig();
		
		App::run();
	}
	static function loadConfig(){
		$configFiles = array(
			LIB_CONF_PATH . 'common.php',
			APP_CONF_PATH . 'common.php',
			APP_CONF_PATH . 'db.php',
			APP_CONF_PATH . 'debug.php',
		);
		foreach($configFiles as $file){
			if(is_file($file)){
				C(include $file);
			}
		}
	}
	static function appError($errno, $errstr, $errfile, $errline) {
		$errorStr = "[$errno] $errstr " . basename ( $errfile ) . " 第 $errline 行.";
		switch ($errno) {
			case E_ERROR :
			case E_USER_ERROR :
				Log::Write ( $errorStr, Log::ERR );
				break;
			case E_STRICT :
			case E_USER_WARNING :
			case E_USER_NOTICE :
			default :
				Log::Write ( $errorStr, Log::WARN );
				break;
		}
	}
	static function appException($e) {
	}
	static function autoload($class) {
		$dirs = array (
				LIB_ACTION_PATH,
				LIB_CORE_PATH,
				LIB_MODEL_PATH,
				LIB_EXTEND_PATH,
				
				APP_ACTION_PATH,
				APP_MODEL_PATH,
				APP_EXTEND_PATH 
		);
		foreach ( $dirs as $dir ) {
			if (is_file ( $dir . $class . '.class.php' )) {
				require $dir . $class . '.class.php';
				return true;
			}
		}
		return false;
	}
}