<?php
// 循环创建目录
function mk_dir($dir, $mode = 0777) {
	if (is_dir ( $dir ) || @mkdir ( $dir, $mode ))
		return true;
	if (! mk_dir ( dirname ( $dir ), $mode ))
		return false;
	return @mkdir ( $dir, $mode );
}

// 浏览器友好的变量输出
function dump($var, $echo = true, $label = null, $strict = true) {
	$label = ($label === null) ? '' : rtrim ( $label ) . ' ';
	if (! $strict) {
		if (ini_get ( 'html_errors' )) {
			$output = print_r ( $var, true );
			$output = '<pre>' . $label . htmlspecialchars ( $output, ENT_QUOTES ) . '</pre>';
		} else {
			$output = $label . print_r ( $var, true );
		}
	} else {
		ob_start ();
		var_dump ( $var );
		$output = ob_get_clean ();
		if (! extension_loaded ( 'xdebug' )) {
			$output = preg_replace ( "/\]\=\>\n(\s+)/m", '] => ', $output );
			$output = '<pre>' . $label . htmlspecialchars ( $output, ENT_QUOTES ) . '</pre>';
		}
	}
	if ($echo) {
		echo ($output);
		return null;
	} else
		return $output;
}

// URL重定向
function redirect($url, $time = 0, $msg = '') {
	// 多行URL地址支持
	$url = str_replace ( array (
			"\n",
			"\r" 
	), '', $url );
	if (empty ( $msg ))
		$msg = "系统将在{$time}秒之后自动跳转到{$url}！";
	if (! headers_sent ()) {
		// redirect
		if (0 === $time) {
			header ( 'Location: ' . $url );
		} else {
			header ( "refresh:{$time};url={$url}" );
			echo ($msg);
		}
		exit ();
	} else {
		$str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0)
			$str .= $msg;
		exit ( $str );
	}
}

// 格式化文件大小formatFileSize ( memory_get_usage ( true ) );
function formatFileSize($size) {
	$unit = explode ( ',', 'b,kb,mb,gb,tb,pb' );
	$i = floor ( log ( $size, 1024 ) );
	return @round ( $size / pow ( 1024, $i ), 2 ) . ' ' . $unit [$i];
}

// 记录时间
function T($start = '', $end = false) {
	static $_times = array ();
	$return = 0;
	if ($start == '' && $end == false) {
		$return = microtime ( true );
	} elseif ($end == false) {
		if (! isset ( $_times [$start] )) {
			$_times [$start] = T ();
		}
		$return = $_times [$start];
	} else {
		$return = T () - $_times [$start];
	}
	return $return;
}

// 获取文件执行后的代码
function fatch($filename) {
	ob_start ();
	ob_implicit_flush ( 0 );
	include $filename;
	return ob_get_clean ();
}
function C($name = null, $value = null) {
	static $_config = array ();
	// 无参数时获取所有
	if (empty ( $name )) {
		return $_config;
	}
	// 优先执行设置获取或赋值
	if (is_string ( $name )) {
		$name = strtolower ( $name );
		if (is_null ( $value )) {
			return isset ( $_config [$name] ) ? $_config [$name] : null;
		} else {
			$_config [$name] = $value;
			return;
		}
	}
	// 批量设置
	if (is_array ( $name )) {
		return $_config = array_merge ( $_config, array_change_key_case ( $name ) );
	}
	return null; // 避免非法参数
}
// 创建控制器
function A($name) {
	static $_action = array ();
	if (isset ( $_action [$name] )) {
		return $_action [$name];
	}
	$class = $name . 'Action';
	$file = APP_ACTION_PATH . $class . '.class.php';
	if (is_file ( $file )) {
		include $file;
	}
	if (class_exists ( $class, false )) {
		$action = new $class ();
		$_action [$name] = $action;
		return $action;
	} else {
		return false;
	}
}
// 获取程序运行时间
function loadTime() {
	return T ( 'end' ) - $GLOBALS ['_beginTime'];
}

// 编译文件
function compile($filename) {
	$content = file_get_contents($filename);
	// 替换预编译指令
	$content = preg_replace('/\/\/\s*\[RUNTIME\](.*?)\/\/\s*\[\/RUNTIME\]/s', '', $content);
	$content = substr(trim($content), 5);
	if ('?>' == substr($content, -2))
		$content = substr($content, 0, -2);
	return $content;
}

// 去除代码中的空白和注释
function strip_whitespace($content) {
	$stripStr = '';
	//分析php源码
	$tokens = token_get_all($content);
	$last_space = false;
	for ($i = 0, $j = count($tokens); $i < $j; $i++) {
		if (is_string($tokens[$i])) {
			$last_space = false;
			$stripStr .= $tokens[$i];
		} else {
			switch ($tokens[$i][0]) {
				//过滤各种PHP注释
				case T_COMMENT:
				case T_DOC_COMMENT:
					break;
					//过滤空格
				case T_WHITESPACE:
					if (!$last_space) {
						$stripStr .= ' ';
						$last_space = true;
					}
					break;
				case T_START_HEREDOC:
					$stripStr .= "<<<THINK\n";
					break;
				case T_END_HEREDOC:
					$stripStr .= "THINK;\n";
					for($k = $i+1; $k < $j; $k++) {
						if(is_string($tokens[$k]) && $tokens[$k] == ';') {
							$i = $k;
							break;
						} else if($tokens[$k][0] == T_CLOSE_TAG) {
							break;
						}
					}
					break;
				default:
					$last_space = false;
					$stripStr .= $tokens[$i][1];
			}
		}
	}
	return $stripStr;
}
// 根据数组生成常量定义
function array_define($array,$check=true) {
	$content = "\n";
	foreach ($array as $key => $val) {
		$key = strtoupper($key);
		if($check)   $content .= 'defined(\'' . $key . '\') or ';
		if (is_int($val) || is_float($val)) {
			$content .= "define('" . $key . "'," . $val . ');';
		} elseif (is_bool($val)) {
			$val = ($val) ? 'true' : 'false';
			$content .= "define('" . $key . "'," . $val . ');';
		} elseif (is_string($val)) {
			$content .= "define('" . $key . "','" . addslashes($val) . "');";
		}
		$content    .= "\n";
	}
	return $content;
}
