<?php defined('TOKICMS') or die('Hacking attempt...');

// Check Version
if (version_compare(phpversion(), '7.0', '<') == true)
{
    die('TOKICMS ' . TOKI_VERSION . ' requires PHP 7.0+ to run properly. Your PHP version: ' . phpversion() );
}

if (function_exists('apache_get_modules') && !in_array('mod_rewrite', apache_get_modules())) 
{
    die('TOKICMS ' . TOKI_VERSION . ' requires the mod_rewrite module to run properly.');
}

//Set the following to 'TRUE' if you wish to disable the addons
define ( 'DISABLE_HOOKS', FALSE );

define('PS', '/');

@ini_set('memory_limit', '256M');

set_time_limit (300);
#########################################################################################################
// Set internal character encoding
mb_internal_encoding( CHARSET );

// Set HTTP output character encoding
mb_http_output( CHARSET );

//Main variables
define('CACHE_ROOT', ROOT . 'cache' . DS);
define('CACHE_STATIC_ROOT', CACHE_ROOT . 'static' . DS);
define('LANG_ROOT', ROOT . 'languages' . DS);
define('FLAGS_HTML', SITE_URL . 'languages' . PS . 'flags' . PS );
define('FLAGS_ROOT', LANG_ROOT . 'flags' . DS);
define('TOOLS_ROOT', INC_ROOT . 'tools' . DS);
define('TOOLS_THEME_PHP_ROOT', TOOLS_ROOT . 'theme_files' . DS . 'php' . DS);
define('DATA_ROOT', INC_ROOT . 'data' . DS);
define('ARRAYS_ROOT', DATA_ROOT . 'arrays' . DS);
define('FORMS_ROOT', DATA_ROOT . 'forms' . DS);
define('DB_DATA_ROOT', ROOT . 'data' . DS);
define('TOOLS_HTML', SITE_URL . 'inc' . PS . 'tools' . PS );
define('SITE_AJAX_URL', SITE_URL . 'ajax.php' );
define('THEMES_ROOT', ROOT . 'themes' . DS);
define('BACKUPS_ROOT', ROOT . 'data' . DS . 'backups' . DS);
define('PLUGINS_ROOT', ROOT . 'plugins' . DS);
define('FUNCTIONS_ROOT', INC_ROOT . 'functions' . DS);
define('CLASSES_ROOT', INC_ROOT . 'classes' . DS);
define('CACHE_DB_FILE', CACHE_ROOT . 'db.php');
define('SYS_LOG_FILE', CACHE_ROOT . 'log.php');
define('SITEMAP_ARRAY_FILE', DB_DATA_ROOT . 'sitemap.php');
define('POSTS_VIEWS_FILE', DB_DATA_ROOT . 'views.php');
define('STATS_FILE', DB_DATA_ROOT . 'stats.php');
define('GUESTS_PERMISSIONS_FILE', DB_DATA_ROOT . 'permissions.php');
define('EXTERNAL_POSTS_FILE', DB_DATA_ROOT . 'posts.php');
define('EXTERNAL_PAGES_FILE', DB_DATA_ROOT . 'pages.php');
define('CACHE_POST_ROOT', CACHE_ROOT . 'post' . DS);
define('FORMAT_DATE', 'F j, Y');
define('CACHE_SITEMAP_ROOT', CACHE_ROOT . 'sitemap' . DS);
define('ADMIN_MAIN_ROOT', ROOT . 'inc' . DS . 'admin' . DS);
define('ADMIN_PHP_ROOT', ADMIN_MAIN_ROOT . 'php' . DS);
define('UPLOADS_ROOT', ROOT . 'uploads' . DS);
define('CONTROLLER_ROOT', INC_ROOT . 'controller' . DS);
define('CRON_URL', SITE_URL . 'cron.php?token=' . MAIN_HASH );
####################################################################################
global $Settings, $L, $Paginator;

//Require the main files here
require_once ( CLASSES_ROOT 	. 'App.php' );
require_once ( CLASSES_ROOT 	. 'Controller.php' );
require_once ( CLASSES_ROOT 	. 'Settings.php' );
require_once ( CLASSES_ROOT 	. 'Csrf.php' );
require_once ( CLASSES_ROOT 	. 'Router.php' );
require_once ( CLASSES_ROOT 	. 'Database.php' );
require_once ( CLASSES_ROOT 	. 'Theme.php' );
require_once ( CLASSES_ROOT 	. 'Log.php' );
require_once ( CLASSES_ROOT 	. 'Plugin.php' );
require_once ( CLASSES_ROOT 	. 'Paginator.php' );
require_once ( CLASSES_ROOT 	. 'Database.php' );
require_once ( CLASSES_ROOT 	. 'Post.php' );
require_once ( CLASSES_ROOT 	. 'GetPost.php' );
require_once ( CLASSES_ROOT 	. 'Image.php' );
require_once ( TOOLS_ROOT 		. 'delight-im' . DS . 'cookie' . DS . 'src' . DS . 'Cookie.php' );
require_once ( FUNCTIONS_ROOT 	. 'db-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'posts-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'blogs-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'user-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'categories-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'comments-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'tags-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'api-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'slug-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'menu-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'theme-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'sitemap-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'filters-functions.php' );
require_once ( FUNCTIONS_ROOT 	. 'plugins-functions.php' );

//Do a quick and simple security check
Sec();

$Settings 	= new Settings;
$Csrf 		= new Csrf;
$Log 		= new Log;
$L 			= LoadLang();

require_once ( 'variables.php' );

require_once ( FUNCTIONS_ROOT 	. 'file-functions.php' );

if ( DEBUG_MODE )
{
	ini_set("display_errors", 1);
	ini_set('display_startup_errors', 1);
	ini_set("html_errors", 1);
	ini_set('log_errors', 1);
	error_reporting(E_ALL | E_STRICT | E_NOTICE);
}
else
{
	error_reporting(0);
}