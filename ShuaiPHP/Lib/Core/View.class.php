<?php
class View {
	public $tVar = array ();
	public function assign($name, $val) {
		$this->tVar [$name] = $val;
	}
	public function fetch($tpl = '') {
		if ($tpl == '') {
			$tpl = APP_VIEW_PATH . __MODULE__ . '/' . __ACTION__ . '.php';
		} else {
			$tpl = APP_VIEW_PATH . __MODULE__ . '/' . $tpl . '.php';
		}
		// 模板文件不存在直接返回
		if (! is_file ( $tpl )) {
			return null;
		}
		// 页面缓存
		ob_start ();
		ob_implicit_flush ( 0 );
		// 模板阵列变量分解成为独立变量
		extract ( $this->tVar, EXTR_OVERWRITE );
		// 直接载入PHP模板
		include $tpl;
		// 获取并清空缓存
		$content = ob_get_clean ();
		// 输出模板文件
		return $content;
	}
	// 调试页面所有的模板变量
	public function traceVar() {
		foreach ( $this->tVar as $name => $val ) {
			dump ( $val, 1, '[' . $name . ']<br/>' );
		}
	}
	public function get($name) {
		if (isset ( $this->tVar [$name] ))
			return $this->tVar [$name];
		else
			return false;
	}
	
	/* 取得所有模板变量 */
	public function getAllVar() {
		return $this->tVar;
	}
}