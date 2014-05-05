<?php
class Action {
	public $vals = array();
	public function assign($name, $val) {
		$vals[$name] = $val;
	}
}