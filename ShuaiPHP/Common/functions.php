<?php

// 浏览器友好的变量输出
function dump($var, $echo=true, $label=null, $strict=true) {
	$label = ($label === null) ? '' : rtrim($label) . ' ';
	if (!$strict) {
		if (ini_get('html_errors')) {
			$output = print_r($var, true);
			$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
		} else {
			$output = $label . print_r($var, true);
		}
	} else {
		ob_start();
		var_dump($var);
		$output = ob_get_clean();
		if (!extension_loaded('xdebug')) {
			$output = preg_replace("/\]\=\>\n(\s+)/m", '] => ', $output);
			$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
		}
	}
	if ($echo) {
		echo($output);
		return null;
	}else
		return $output;
}

// URL重定向
function redirect($url, $time=0, $msg='') {
	//多行URL地址支持
	$url = str_replace(array("\n", "\r"), '', $url);
	if (empty($msg))
		$msg = "系统将在{$time}秒之后自动跳转到{$url}！";
	if (!headers_sent()) {
		// redirect
		if (0 === $time) {
			header('Location: ' . $url);
		} else {
			header("refresh:{$time};url={$url}");
			echo($msg);
		}
		exit();
	} else {
		$str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0)
			$str .= $msg;
		exit($str);
	}
}

// 快速文件数据读取和保存 针对简单类型数据 字符串、数组
function F($name, $value='', $path=DATA_PATH) {
	static $_cache = array();
	$filename = $path . $name . '.php';
	if ('' !== $value) {
		if (is_null($value)) {
			// 删除缓存
			return unlink($filename);
		} else {
			// 缓存数据
			$dir = dirname($filename);
			// 目录不存在则创建
			if (!is_dir($dir))
				mkdir($dir);
			$_cache[$name] =   $value;
			return file_put_contents($filename, strip_whitespace("<?php\nreturn " . var_export($value, true) . ";\n?>"));
		}
	}
	if (isset($_cache[$name]))
		return $_cache[$name];
	// 获取缓存数据
	if (is_file($filename)) {
		$value = include $filename;
		$_cache[$name] = $value;
	} else {
		$value = false;
	}
	return $value;
}
