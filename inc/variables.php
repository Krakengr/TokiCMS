<?php defined('TOKICMS') or die('Hacking attempt...');

/*
	Environment variables
	These settings are based on your site's settings
	Do not edit these settings
	If you are going to do some changes, do them from the admin panel
*/

// Your Time Zone
// Example values (note how :15, :30, :45 will be entered as .25, .5 and .75 respectively):
// +12.75 New Zealand (UTC+12:45)
// +8.75  Australia (UTC+08:45)
// +5.5   India (UTC+05:30)
// +1     Germany (UTC+01:00)
// 0      United Kingdom (UTC±0)
// -2     Brazil (UTC-02:00)
// -4.5   Venezuela (UTC-04:30)
// -6     United States (Central Time) (UTC-06:00)
// -8     United States (Pacific Time) (UTC-08:00)
date_default_timezone_set( Settings::Get()['timezone_set'] );

// Set the following to 'TRUE' if you wish to enable debug mode
define ( 'DEBUG_MODE', Settings::IsTrue( 'enable_debug_mode' ) );

// GMT offset
define( 'GMT_OFFSET', Settings::Get()['gmt_offset'] );

// Set the following to 1 to put your site in maintenance mode.
// In this mode only admins will be able to access the site while the visitors will be
// shown the 'Site undergoing site_offline' message.
define( 'SITE_OFFLINE', Settings::IsTrue( 'set_offline', 'site' ) );

// Cache settings.
// Enable or disable cache
define( 'ENABLE_CACHE', Settings::IsTrue( 'enable_cache' ) );

// Cache type
//// If set, TOKICMS will cache generated pages and serve them if possible.
define( 'CACHE_TYPE', Settings::Get()['cache_type'] );

// Cache all visitors
define( 'CACHE_ALL_VISITORS', Settings::IsTrue( 'cache_all_visitors' ) );

// Cache expire time
//If the cache is enabled, you need to set a value here (in seconds)
define( 'EXPIRE_CACHE', (int) Settings::Get()['cache_time'] ); //86400 = 24 hrs = 1 day

// Set the following to 'TRUE' if you wish to enable multilingual mode
// Translation for posts, pages, categories and more in many languages
define( 'MULTILANG', Settings::IsTrue( 'enable_multilang', 'site' ) );

// Enable or hide comments globally
define( 'ENABLE_COMMENTS', Settings::IsTrue( 'enable_comments' ) );

// Set the status of this site
define( 'IS_DEFAULT', Settings::Get()['isDefaultSite'] );

// Set the Preview Hash
define( 'PREVIEW_HASH', Settings::Get()['previewHash'] );

define( 'HIDE_COMMENTS', ( ( !empty( Settings::Comments() ) && Settings::Comments()['hide_comments'] ) ? true : false ) );

//Set the following to 'TRUE' if you wish to enable multiblog mode
//Set it to 'FALSE' to have your CMS as a regular blog
//eg, to have more than one blog in the same domain
//www.mysite.com is the default blog
//www.mysite.com/tutorials/ is the "Tutorials" Blog and it's different from the "Default" Blog
//www.mysite.com/forum/ is the "Forum" Blog that will be used as a discussion board
//www.mysite.com/videos/ is the "Videos" Blog and it's different from the others
//etc
define( 'MULTIBLOG', Settings::IsTrue( 'enable_multiblog', 'site' ) );

//Set the following to 'TRUE' if you wish to enable multisite mode
define( 'MULTISITE', Settings::IsTrue( 'enable_multisite', 'site' ) );

// Set how many items you want to have in every page
define( 'HOMEPAGE_ITEMS', (int) Settings::Get()['article_limit'] );

// Set the theme you want to enable
define( 'THEME_MAIN', Settings::Get()['theme'] );

// Set the AMP theme you want to enable
define( 'THEME_AMP', ( !empty( Settings::Amp()['theme'] ) ? Settings::Amp()['theme'] : null ) );

// Set the following to 'TRUE' if you wish to enable amp
// Allows you to assemble fully AMP compatible sites
define( 'AMP_MODE', Settings::IsTrue( 'enable_amp' ) );

//If the admin is enabled, you need to set the theme here
define( 'THEME_ADMIN', 'adminlte' );

//Check if we want to get the images locally
define( 'LOAD_IMAGES_LOCALLY', ( isset( Settings::Json()['shareData']['sync_uploads'] ) ? Settings::Json()['shareData']['sync_uploads'] : true ) );

//Set the images url, if you want to serve them from somewhere else
define('IMAGES_HTML', ( !empty( Settings::Get()['images_html'] ) ? Settings::Get()['images_html'] : SITE_URL . 'uploads' . PS ) );

//Set the images absolute directory
define('IMAGES_ROOT', ( !empty( Settings::Get()['images_root'] ) ? Settings::Get()['images_root'] : ROOT . 'uploads' . DS ) );

define('IMAGES_STORES_ROOT', ( !empty( Settings::Get()['images_stores_html'] ) ? Settings::Get()['images_stores_html'] : IMAGES_HTML . 'uploads' . PS . 'stores' . PS ) );

define('IMAGES_STORES_HTML', ( !empty( Settings::Get()['images_stores_root'] ) ? Settings::Get()['images_stores_root'] : ROOT . 'uploads' . DS . 'stores' . DS ) );

if ( AMP_MODE )
{	
	define('THEME_AMP_DIR', ( !empty( THEME_AMP ) ? ROOT . 'themes' . DS . THEME_AMP . DS : null ) );
	define('THEME_AMP_HTML', SITE_URL . 'themes' . PS . THEME_AMP . PS );
}

else
{
	define('THEME_AMP_DIR', null );
	define('THEME_AMP_HTML', null );
}
	
//Set theme variables here
define('THEME_AMP_DIR_PHP', THEME_AMP_DIR . 'php' . DS);
define('THEME_MAIN_DIR', ROOT . 'themes' . DS );
define('ADMIN_THEME_ROOT', ADMIN_MAIN_ROOT . 'themes' . DS);
define('ADMIN_THEME', ADMIN_THEME_ROOT . THEME_ADMIN . DS);
define('ADMIN_THEME_PHP_ROOT', ADMIN_THEME . 'php' . DS);
define('ADMIN_THEME_PAGES_ROOT', ADMIN_THEME . 'pages' . DS);
define('ADMIN_URI', SITE_URL . ADMIN_SLUG . PS);
define('HTML_ADMIN_PATH_THEME', SITE_URL . 'inc' . PS . 'admin' . PS . 'themes' . PS . THEME_ADMIN . PS);
define('AJAX_ADMIN_PATH', ADMIN_URI . 'ajax' . PS );