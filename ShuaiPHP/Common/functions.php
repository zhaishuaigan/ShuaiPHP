<?php
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
		$return = microtime (true);
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

