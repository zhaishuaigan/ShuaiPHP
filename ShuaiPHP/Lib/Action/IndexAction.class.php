<?php

class IndexAction extends Action {

    public function index() {
        echo 'Hello World!<br />';
        echo '页面载入时间: ' . loadTime() . ' 秒';
    }

}
