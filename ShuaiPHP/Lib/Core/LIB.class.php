<?php
class LIB {
	static function Start() {
		echo '<p>is run</p>';
		echo '<p>__ROOT__:' . __ROOT__ . '</p>';
		echo '<p>_PHP_FILE_:' . _PHP_FILE_ . '</p>';
		echo '<p>URL_PATHINFO:' . URL_PATHINFO . '</p>';
		echo '<p>URL_REWRITE:' . URL_REWRITE . '</p>';
		$defs = get_defined_constants(TRUE);
		dump($defs['user']);
	}
}