<?php

class App {

    static function run() {
        $pathInfo = isset($_SERVER ['PATH_INFO']) ? $_SERVER ['PATH_INFO'] : '';
        $pathInfo = explode('/', $pathInfo);
        $module = isset($pathInfo [1]) ? $pathInfo [1] : C('DEFAULT_MODULE');
        $action = isset($pathInfo [2]) ? $pathInfo [2] : C('DEFAULT_ACTION');
        $moduleClass = A($module);
        // 记录加载文件时间
        if ($moduleClass && method_exists($moduleClass, $action)) {
            define('__MODULE__', $module);
            define('__ACTION__', $action);
            call_user_func(array(
                &$moduleClass,
                $action
            ));
        } else {
            echo file_get_contents(LIB_VIEW_PATH . '404.html');
        }
    }

}
