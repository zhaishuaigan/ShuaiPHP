<?php

/**
 +------------------------------------------------------------------------------
 * ShuaiPHP 运行时文件 编译后不再加载
 +------------------------------------------------------------------------------
 */
if (! defined ( 'LIB_PATH' ))
	exit ();
if (version_compare ( PHP_VERSION, '5.2.0', '<' ))
	die ( 'PHP版本必须大于 5.2.0 !' );
define ( 'IS_CGI', substr ( PHP_SAPI, 0, 3 ) == 'cgi' ? 1 : 0 );
define ( 'IS_WIN', strstr ( PHP_OS, 'WIN' ) ? 1 : 0 );
define ( 'IS_CLI', PHP_SAPI == 'cli' ? 1 : 0 );

if (! IS_CLI) {
	// 当前文件名
	if (! defined ( '_PHP_FILE_' )) {
		if (IS_CGI) {
			// CGI/FASTCGI模式下
			$_temp = explode ( '.php', $_SERVER ['PHP_SELF'] );
			define ( '_PHP_FILE_', rtrim ( str_replace ( $_SERVER ['HTTP_HOST'], '', $_temp [0] . '.php' ), '/' ) );
		} else {
			define ( '_PHP_FILE_', rtrim ( $_SERVER ['SCRIPT_NAME'], '/' ) );
		}
	}
	if (! defined ( '__ROOT__' )) {
		// 网站URL根目录
		if (strtoupper ( APP_NAME ) == strtoupper ( basename ( dirname ( _PHP_FILE_ ) ) )) {
			$_root = dirname ( dirname ( _PHP_FILE_ ) );
		} else {
			$_root = dirname ( _PHP_FILE_ );
		}
		define ( '__ROOT__', (($_root == '/' || $_root == '\\') ? '' : $_root) );
	}
	
	// 支持的URL模式
	define ( 'URL_PATHINFO', 1 ); // PATHINFO模式
	define ( 'URL_REWRITE', 2 ); // REWRITE模式
}

// 常用变量
defined ( 'APP_CONF_PATH' ) or define ( 'APP_CONF_PATH', APP_PATH . 'Config/' );
defined ( 'APP_COMMON_PATH' ) or define ( 'APP_COMMON_PATH', APP_PATH . 'Common/' );
defined ( 'APP_EXTEND_PATH' ) or define ( 'APP_EXTEND_PATH', APP_PATH . 'Extend/' );
defined ( 'APP_ACTION_PATH' ) or define ( 'APP_ACTION_PATH', APP_PATH . 'Lib/Action/' );
defined ( 'APP_MODEL_PATH' ) or define ( 'APP_MODEL_PATH', APP_PATH . 'Lib/Model/' );
defined ( 'APP_VIEW_PATH' ) or define ( 'APP_VIEW_PATH', APP_PATH . 'Lib/View/' );
defined ( 'APP_LANG_PATH' ) or define ( 'APP_LANG_PATH', APP_PATH . 'Lang/' );

defined ( 'LIB_CONF_PATH' ) or define ( 'LIB_CONF_PATH', LIB_PATH . 'Config/' );
defined ( 'LIB_COMMON_PATH' ) or define ( 'LIB_COMMON_PATH', LIB_PATH . 'Common/' );
defined ( 'LIB_EXTEND_PATH' ) or define ( 'LIB_EXTEND_PATH', LIB_PATH . 'Extend/' );
defined ( 'LIB_ACTION_PATH' ) or define ( 'LIB_ACTION_PATH', LIB_PATH . 'Lib/Action/' );
defined ( 'LIB_MODEL_PATH' ) or define ( 'LIB_MODEL_PATH', LIB_PATH . 'Lib/Model/' );
defined ( 'LIB_VIEW_PATH' ) or define ( 'LIB_VIEW_PATH', LIB_PATH . 'Lib/View/' );
defined ( 'LIB_CORE_PATH' ) or define ( 'LIB_CORE_PATH', LIB_PATH . 'Lib/Core/' );

defined ( 'LIB_LANG_PATH' ) or define ( 'LIB_LANG_PATH', LIB_PATH . 'Lang/' );
// 为了方便导入第三方类库 设置Vendor目录到include_path
set_include_path ( get_include_path () . PATH_SEPARATOR . LIB_EXTEND_PATH );

// 加载运行时所需要的文件 并负责自动目录生成
function load_runtime_file() {
	// 加载系统基础函数库
	require LIB_COMMON_PATH . 'functions.php';
	// 读取核心编译文件列表
	$list = array (
			LIB_CORE_PATH . 'LIB.class.php', // 核心类
			LIB_CORE_PATH . 'LIBException.class.php', // 异常处理类
			LIB_CORE_PATH . 'Action.class.php', // 控制器类
			LIB_CORE_PATH . 'Model.class.php', // 模型类
			LIB_CORE_PATH . 'View.class.php',  // 视图类
			LIB_CORE_PATH . 'Log.class.php',  // 日志类
	);
	// 加载模式文件列表
	foreach ( $list as $key => $file ) {
		if (is_file ( $file ))
			include $file;
	}
	
	// 检查项目目录结构 如果不存在则自动创建
	if (! is_dir ( APP_PATH )) {
		// 创建项目目录结构
		build_app_dir ();
	}
	if (APP_DEBUG) {
		// 调试模式切换删除编译缓存
		if (is_file ( APP_RUNTIME_FILE )) {
			unlink ( APP_RUNTIME_FILE );
		}
	}
}

// 创建项目目录结构
function build_app_dir() {
	// 没有创建项目目录的话自动创建
	if (! is_dir ( APP_PATH ))
		mk_dir ( APP_PATH, 0777 );
	if (is_writeable ( APP_PATH )) {
		$dirs = array (
				APP_PATH,
				APP_CONF_PATH,
				APP_COMMON_PATH,
				APP_EXTEND_PATH,
				APP_ACTION_PATH,
				APP_MODEL_PATH,
				APP_VIEW_PATH,
				APP_LANG_PATH,
				APP_RUNTIME_PATH 
		);
		foreach ( $dirs as $dir ) {
			if (! is_dir ( $dir ))
				mk_dir ( $dir, 0777 );
		}
		// 写入配置文件
		if (! is_file ( APP_CONF_PATH . 'config.php' )) {
			build_defult_config ();
		}
		// 写入测试Action
		if (! is_file ( APP_ACTION_PATH . 'IndexAction.class.php' )) {
			build_first_action ();
		}
	} else {
		exit ( '项目目录不可写，目录无法自动生成！<BR>请手动生成项目目录~' );
	}
}

// 创建默认配置文件
function build_defult_config() {
	file_put_contents ( APP_CONF_PATH . 'config.php', "<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);\n?>" );
}

// 创建测试Action
function build_first_action() {
	$content = file_get_contents ( LIB_ACTION_PATH . 'IndexAction.class.php' );
	file_put_contents ( APP_ACTION_PATH . 'IndexAction.class.php', $content );
}

// 加载运行时所需文件
load_runtime_file ();
// 执行入口
LIB::Start ();