<?php

class LIB {

    static function Start() {
        // 设定错误
        set_error_handler(array(
            'LIB',
            'appError'
        ));
        // 异常处理
        set_exception_handler(array(
            'LIB',
            'appException'
        ));
        // 注册AUTOLOAD方法
        spl_autoload_register(array(
            'LIB',
            'autoload'
        ));
        // [RUNTIME]
        self::loadConfig();
        self::buildLib();
        // [/RUNTIME]
        App::run();
    }

    static function appError($errno, $errstr, $errfile, $errline) {
        $errorStr = "[$errno] $errstr " . basename($errfile) . " 第 $errline 行.";
        switch ($errno) {
            case E_ERROR :
            case E_USER_ERROR :
                Log::Write($errorStr, Log::ERR);
                break;
            case E_STRICT :
            case E_USER_WARNING :
            case E_USER_NOTICE :
            default :
                Log::Write($errorStr, Log::WARN);
                break;
        }
    }

    static function appException($e) {
        
    }

    static function autoload($class) {
        $dirs = array(
            LIB_ACTION_PATH,
            LIB_CORE_PATH,
            LIB_MODEL_PATH,
            LIB_EXTEND_PATH,
            APP_ACTION_PATH,
            APP_MODEL_PATH,
            APP_EXTEND_PATH
        );
        foreach ($dirs as $dir) {
            if (is_file($dir . $class . '.class.php')) {
                require $dir . $class . '.class.php';
                return true;
            }
        }
        return false;
    }

    // [RUNTIME]
    // 加载配置
    static function loadConfig() {
        $configFiles = array(
            LIB_CONF_PATH . 'common.php',
            APP_CONF_PATH . 'common.php',
            APP_CONF_PATH . 'db.php',
            APP_CONF_PATH . 'debug.php'
        );
        foreach ($configFiles as $file) {
            if (is_file($file)) {
                C(include $file);
            }
        }
    }

    // 编译lib到runtime文件
    static function buildLib() {
        if (APP_DEBUG) {
            return;
        }
        // 生成编译文件
        $defs = get_defined_constants(TRUE);
        $content = "\n" . '$GLOBALS[\'_beginTime\'] = microtime(TRUE);';
        $content .= array_define($defs ['user']);
        $content .= 'set_include_path(get_include_path() . PATH_SEPARATOR . LIB_EXTEND_PATH);' . "\n";
        // 读取核心编译文件列表
        $list = array(
            LIB_COMMON_PATH . 'functions.php', // 公共函数库
            LIB_CORE_PATH . 'LIB.class.php', // 核心类
            LIB_CORE_PATH . 'LIBException.class.php', // 异常处理类
            LIB_CORE_PATH . 'Action.class.php', // 控制器类
            LIB_CORE_PATH . 'Model.class.php', // 模型类
            LIB_CORE_PATH . 'View.class.php', // 视图类
            LIB_CORE_PATH . 'Log.class.php'  // 日志类
        );
        foreach ($list as $file) {
            $content .= compile($file);
        }
        $content .= 'C(' . var_export(C(), true) . ');' . "\n";
        // 编译框架默认语言包和配置参数
        $content .= 'LIB::Start();' . "\n";
        file_put_contents(APP_RUNTIME_FILE, strip_whitespace('<?php ' . $content));
        // file_put_contents ( APP_RUNTIME_FILE, '<?php ' . $content );
    }

    // [/RUNTIME]
}
