<?php
/******************************************************************************
 *
 * TOKICMS - open source content management system
 * Copyright (C) 2021 BadTooth <https://badtooth.studio>
 *
 * This file is part of TOKICMS.
 *
 * TOKICMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TOKICMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TOKICMS. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://badtooth.studio/
 *
 ******************************************************************************/
// Define TOKICMS
define ( 'TOKICMS', TRUE );

// Set as TRUE if you want to install TokiCMS and ovewrite the previous installation
// Note it will delete everything, use it for testing purposes
define ( 'FORCEINSTALL', FALSE );

// Report all Errors
error_reporting(E_ALL ^ E_NOTICE); 

define('DS', DIRECTORY_SEPARATOR);

define('ROOT', getcwd() . DS );

//define('INC_ROOT', ROOT . 'inc' . DS);

//Check if the cache folder exists, otherwise try to create it
if ( !is_dir( ROOT. 'cache' . DS ) )
	mkdir( ROOT . 'cache' . DS, 0755, true) or die ('Could not create folder ' . ROOT . 'cache' . DS );

// Check PHP version
if (version_compare( phpversion(), '7.0', '<' ) == true )
{
    exit('TOKICMS requires PHP 7.0+ to run properly. Your PHP version: ' . phpversion() );
}

//require ( INC_ROOT . 'classes' . DS . 'Database.php' );

// Check PHP modules
$modulesRequired = array( 'mbstring', 'json', 'gd', 'dom', 'curl' );

$modulesRequiredExit = false;

$modulesRequiredMissing = '';

foreach ($modulesRequired as $module)
{
	if ( !extension_loaded( $module ) )
	{
		$errorText = 'PHP module <b>' . $module . '</b> is not installed.';
		error_log('[ERROR] ' . $errorText, 0);

		$modulesRequiredExit = true;
		$modulesRequiredMissing .= $errorText . PHP_EOL;
	}
}

if ( $modulesRequiredExit )
{
	echo 'PHP modules missing: ' . $modulesRequiredMissing . '<br />';
	exit;
}

// Default format date
define( 'DATE_FORMAT', 'Y-m-d H:i:s' );

// Default Charset UTF-8.
define( 'CHARSET', 'UTF-8' );

// Default language file
define( 'DEFAULT_LANGUAGE', 'en' );

define( 'LANGS_DIR', ROOT . 'languages' . DS );

// Set internal character encoding
mb_internal_encoding( CHARSET );

// Set HTTP output character encoding
mb_http_output( CHARSET );

// Increase PHP Execution Time
set_time_limit(0);

// Check if timezone is defined in php.ini
$timezn = ini_get('date.timezone');

if (empty($timezn)) 
{
	// Timezone not defined in php.ini, set UTC as default
	date_default_timezone_set('UTC');
	$timezone = 'UTC';
} else
	$timezone = $timezn;

$error = $message = null;

$htaccess = '';

//Check if TOKI is already installed...
if ( !FORCEINSTALL && file_exists( ROOT . 'settings.php' ) && ( empty( $message ) ) )
{
	die( 'TOKI CMS is already installed...');
}

include ( ROOT . 'inc' . DS . 'functions' . DS . 'functions.php' );

$siteurl = ( (!empty($_SERVER['HTTPS'])) ? 'https' : 'http' ) . '://' . $_SERVER["SERVER_NAME"] . GetBase();

$installurl = ( (!empty($_SERVER['HTTPS'])) ? 'https' : 'http' ) . '://' . $_SERVER["SERVER_NAME"] . $_SERVER['SCRIPT_NAME'];

$step = ( ( isset( $_GET['step'] ) && is_numeric( $_GET['step'] ) ) ? $_GET['step'] : 0 );

if ( ( $step == '1' ) || ( $step == '2' ) )
{
	// Database
	define('DATABASE', $_POST['dbname'] );

	// Database username
	define('DBUSERNAME', $_POST['dbusername'] );

	// Database password
	define('DBPASSWORD', $_POST['dbpass'] );

	// MySQL hostname (it will usually be 'localhost')
	define('SERVER', $_POST['dbhost'] );
	
	$db = dbLoad();
}

//Start the installation
if ( $step == '2') 
{
	$email = Sanitize ( $_POST['email'], false );
	
	$adminSlug = Sanitize ( $_POST['admin_slug'], false );
	
	$pingSlug = Sanitize ( $_POST['ping_slug'], false );
	
	$postsDB = Sanitize ( $_POST['db_posts'], false );
	
	$usersDB = Sanitize ( $_POST['db_users'], false );
	
	if ( !Validate( $email ) )
	{
		$message = '<p>Please enter a valid email address.</p>' . PHP_EOL;
		
		$message .= '<p><a href="javascript:history.back()">&laquo; Back</a></p>';
	}
	else
	{
		$prefix = Sanitize ( $_POST['db_prefix'], false );
		
		$cache = ( isset( $_POST['enableCache'] ) ? true : false );
		
		$settings = "<?php defined('TOKICMS') or die('Hacking attempt...'); // cannot be loaded directly" . PHP_EOL;
		
		$settings .= PHP_EOL;
		
		$settings .= "// SITE_ID will default to 1 in a single site configuration." . PHP_EOL;
		$settings .= "define('SITE_ID', 1 );" . PHP_EOL;
		
		$settings .= PHP_EOL;

		$settings .= "// If necessary, define the full URL of your site including the subdomain, if any. Don't forget the trailing slash!" . PHP_EOL;
		$settings .= "define('SITE_URL', '" . $siteurl . "');" . PHP_EOL;
		
		$settings .= PHP_EOL;

		$settings .= "// Define the charset used by your site. If in any doubt, leave the default utf-8." . PHP_EOL;
		$settings .= "define( 'CHARSET', '" . CHARSET . "' );" . PHP_EOL;
		
		$settings .= PHP_EOL;

		$settings .= "// MySQL settings. You need to get this info from your web host." . PHP_EOL;
		$settings .= "// Name of the database" . PHP_EOL;
		$settings .= "define('DATABASE', '" . DATABASE . "');" . PHP_EOL;
		
		$settings .= PHP_EOL;

		$settings .= "// Database username" . PHP_EOL;
		$settings .= "define('DBUSERNAME', '" . DBUSERNAME . "');" . PHP_EOL;
		
		$settings .= PHP_EOL;

		$settings .= "// Database password" . PHP_EOL;
		$settings .= "define('DBPASSWORD', '" . DBPASSWORD . "');" . PHP_EOL;
		
		$settings .= PHP_EOL;

		$settings .= "// MySQL hostname (it will usually be 'localhost')" . PHP_EOL;
		$settings .= "define('SERVER', '" . SERVER . "');" . PHP_EOL;
		
		$settings .= PHP_EOL;

		$settings .= "// Needed only if multiple instances of this CMS are to be installed in the same database" . PHP_EOL;
		$settings .= "//(please use only alphanumeric characters or underscore (NO hyphen))" . PHP_EOL;
		$settings .= "define( 'DB_PREFIX', '" . $prefix . "' );" . PHP_EOL;
		
		$settings .= PHP_EOL;
		
		$settings .= "// Admin settings." . PHP_EOL;
		$settings .= "// If set, you will have access to the admin panel." . PHP_EOL;
		$settings .= "// If not, this blog will not have an admin panel, meaning that you have it as a child site" . PHP_EOL;
		$settings .= "define('ENABLE_ADMIN', TRUE );" . PHP_EOL;
		
		$settings .= PHP_EOL;
		
		$settings .= "// If the admin is enabled, you can set the slug here" . PHP_EOL;
		$settings .= "// It helps to make login to Administration panel more smooth and easy, and to reduce the chances of being hacked." . PHP_EOL;
		
		$settings .= "//For instanse, if you want your admin panel to be http://mysite.com/admin222/ set 'admin222' below" . PHP_EOL;
		$settings .= "//Only numbers, dashes and letters allowed" . PHP_EOL;
		$settings .= "define('ADMIN_SLUG', '" . $adminSlug . "' );" . PHP_EOL;
		
		$settings .= PHP_EOL;
		
		$settings .= "//If you want to change the slug for ping, change it below. It helps to protect your site and reduce the chances of overloading the system" . PHP_EOL;
		$settings .= "//For instanse, if you want your ping url to be http://mysite.com/my-ping/ set \'my-ping\' below" . PHP_EOL;
		$settings .= "//Only numbers, dashes and letters allowed" . PHP_EOL;
		$settings .= "define('PING_SLUG', '" . $pingSlug . "' );" . PHP_EOL;
		
		$settings .= PHP_EOL;
		
		$settings .= "//Set the posts table name here" . PHP_EOL;
		$settings .= "define('POSTS', '" . $postsDB  . "');" . PHP_EOL;
		
		$settings .= PHP_EOL;
		
		$settings .= "//Set the users table name here" . PHP_EOL;
		$settings .= "define('USERS', '" . $usersDB  . "');" . PHP_EOL;
		
		$settings .= PHP_EOL;
		
		$settings .= "// Set \"TRUE\" if this is the parent site or \"FALSE\" if it's a child" . PHP_EOL;
		$settings .= "define('PARENT_SITE', 'TRUE');" . PHP_EOL;
		
		$settings .= PHP_EOL;

		$settings .= "// Main hash" . PHP_EOL;
		$settings .= "// This unique key is needed for ping, and other routines" . PHP_EOL;
		$settings .= "define('MAIN_HASH', '" . GenerateStrongRandomKey( 15 ) . "');" . PHP_EOL;
		
		$settings .= "// Cache hash" . PHP_EOL;
		$settings .= "//If the cache is enabled, you need to set a unique key here" . PHP_EOL;
		$settings .= "define('CACHE_HASH', '" . GenerateStrongRandomKey( 15 ) . "');" . PHP_EOL;
		
		$settings .= PHP_EOL;
		
		$settings .= "// Admin hash" . PHP_EOL;
		$settings .= "// If admin is enabled, you need to set a unique key here" . PHP_EOL;
		$settings .= "define('ADMIN_HASH', '" . GenerateStrongRandomKey( 6 ) . "');" . PHP_EOL;
		
		$settings .= PHP_EOL;
		
		$settings .= "// Update hash" . PHP_EOL;
		$settings .= "// This unique key is needed only when you want to auto update your site" . PHP_EOL;
		$settings .= "define('UPDATE_HASH', '" . GenerateStrongRandomKey( 15 ) . "');" . PHP_EOL;
		
		//Write the htaccess file...
		file_put_contents( ROOT . 'settings.php', $settings, LOCK_EX );
	
		//No needed anymore
		unset( $settings );
		
		$dbh = dbBuild();
		
		foreach ( $dbh as $d )
		{
			try {
				$get = $db->prepare( $d );
				$get->execute();
			} catch (PDOException $e) {
				//
			}
		}
		
		$url = $installurl . '?step=3';
		
		@header("Location: " . $url );
		@exit;
	}
}

//End the installation
if ( $step == '3') 
{
	$message = '<p>Installation has been successfully completed. Click <a href="' . $siteurl . '">here</a> to visit your blog.</p>';
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="<?php echo CHARSET ?>">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Toki CMS Installer</title>
    
	<!-- Favicon-->
    <link rel="icon" href="admin/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	
	<style>html{background:#f7f7f7;}body{background:#fff;color:#333;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;margin:2em auto 0 auto;width:700px;padding:1em 2em;-moz-border-radius:11px;-khtml-border-radius:11px;-webkit-border-radius:11px;border-radius:11px;border:1px solid #dfdfdf;}a{color:#2583ad;text-decoration:none;}a:hover{color:#d54e21;}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px Georgia,"Times New Roman",Times,serif;margin:5px 0 0 -4px;padding:0;padding-bottom:7px;}h2{font-size:16px;}p,li{padding-bottom:2px;font-size:12px;line-height:18px;}code{font-size:13px;}ul,ol{padding:5px 5px 5px 22px;}#logo{margin:6px 0 14px 0;border-bottom:none;}.step{margin:20px 0 15px;}.step,th{text-align:left;padding:0;}.submit input,.button,.button-secondary{font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;text-decoration:none;font-size:14px!important;line-height:16px;padding:6px 12px;cursor:pointer;border:1px solid #bbb;color:#464646;-moz-border-radius:15px;-khtml-border-radius:15px;-webkit-border-radius:15px;border-radius:15px;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;-khtml-box-sizing:content-box;box-sizing:content-box;}.button:hover,.button-secondary:hover,.submit input:hover{color:#000;border-color:#666;}.button,.submit input,.button:active,.submit input:active,.button-secondary:active{background:#eee url(../images/white-grad-active.png) repeat-x scroll left top;}.form-table{border-collapse:collapse;margin-top:1em;width:100%;}.form-table td{margin-bottom:9px;padding:10px;border-bottom:8px solid #fff;font-size:12px;}.form-table th{font-size:13px;text-align:left;padding:16px 10px 10px 10px;border-bottom:8px solid #fff;width:110px;vertical-align:top;}.form-table tr{background:#f3f3f3;}.form-table code{line-height:18px;font-size:18px;}.form-table p{margin:4px 0 0 0;font-size:11px;}.form-table input{line-height:20px;font-size:15px;padding:2px;}#error-page{margin-top:50px;}#error-page p{font-size:12px;line-height:18px;margin:25px 0 20px;}#error-page code{font-family:Consolas,Monaco,Courier,monospace;}</style>
</head>

<body id="page">
    <div class="signup-box">
        <div class="logo">
            <h2>Toki CMS Installer</h2>
		<?php if ( empty( $message ) && !is_null( $db ) ) : ?>
            <p>Complete the form below to continue</p>
		<?php endif ?>
        </div>
		<div class="card">
            <div class="body">
		<?php 
		if (!empty($message )) 
		{
			echo $message;
		} 
		else 
		{
			if ( $step == '1' )
			{				
				if ( !$db )
				{
					echo '<p><strong>Failed to connect to database</strong>.<br />Check that the username you entered exists and make sure that the password is correct.</p>';
					echo '<p><a href="javascript:history.back()">&laquo; Back</a></p>';
					exit;
				}
			
				// Delete the htaccess file, if any, to build the new one
				@unlink ($dir . '.htaccess');
				
				if ( CHARSET == 'UTF-8' )
					$htaccess .= 'AddDefaultCharset UTF-8' . PHP_EOL;
					
				$htaccess .= '# Begin - Prevent Browsing and Set Default Resources' . PHP_EOL;
				$htaccess .= 'Options -Indexes' . PHP_EOL;
				$htaccess .= 'DirectoryIndex index.php index.html index.htm' . PHP_EOL;
				$htaccess .= '# End - Prevent Browsing and Set Default Resources' . PHP_EOL;
				$htaccess .= PHP_EOL;
				$htaccess .= '<FilesMatch "\.(htaccess|ini|log)$">' . PHP_EOL;
				$htaccess .= '	Order allow,deny' . PHP_EOL;
				$htaccess .= '	deny from all' . PHP_EOL;
				$htaccess .= '</FilesMatch>' . PHP_EOL;
				$htaccess .= 	PHP_EOL;
				$htaccess .= '# BEGIN TokiCMS' . PHP_EOL;
				$htaccess .= '<IfModule mod_rewrite.c>' . PHP_EOL;
				$htaccess .= '	RewriteEngine On' . PHP_EOL;
				$htaccess .= '	RewriteBase ' . GetBase() . PHP_EOL;
				$htaccess .=  PHP_EOL;
				$htaccess .= '	## Begin - Exploits' . PHP_EOL;
				$htaccess .= '	# If you experience problems on your site block out the operations listed below' . PHP_EOL;
				$htaccess .= '	# This attempts to block the most common type of exploit `attempts`' . PHP_EOL;
				$htaccess .= '	#' . PHP_EOL;
				$htaccess .= '	# Block out any script trying to base64_encode data within the URL.' . PHP_EOL;
				$htaccess .= '	RewriteCond %{QUERY_STRING} base64_encode[^(]*\([^)]*\) [OR]' . PHP_EOL;
				$htaccess .= '	# Block out any script that includes a <script> tag in URL.' . PHP_EOL;
				$htaccess .= '	RewriteCond %{QUERY_STRING} (<|%3C)([^s]*s)+cript.*(>|%3E) [NC,OR]' . PHP_EOL;
				$htaccess .= '	# Block out any script trying to set a PHP GLOBALS variable via URL.' . PHP_EOL;
				$htaccess .= '	RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]' . PHP_EOL;
				$htaccess .= '	# Block out any script trying to modify a _REQUEST variable via URL.' . PHP_EOL;
				$htaccess .= '	RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2})' . PHP_EOL;
				$htaccess .= '	# Return 403 Forbidden header and show the content of the root homepage' . PHP_EOL;
				$htaccess .= '	RewriteRule .* index.php [F]' . PHP_EOL;
				$htaccess .= '	#' . PHP_EOL;
				$htaccess .= '	## End - Exploits' . PHP_EOL;
				$htaccess .=  PHP_EOL;
				$htaccess .= '	# Pass all requests not referring directly to files in the filesystem to index.php.' . PHP_EOL;
				$htaccess .= '	RewriteCond %{REQUEST_FILENAME} !-f' . PHP_EOL;
				$htaccess .= '	RewriteCond %{REQUEST_FILENAME} !-d' . PHP_EOL;
				$htaccess .= '	RewriteRule ^ index.php [L]' . PHP_EOL;
				$htaccess .= '</IfModule>' . PHP_EOL;
				$htaccess .= '# END TokiCMS' . PHP_EOL;
				
				//Write the htaccess file...
				file_put_contents( ROOT . '.htaccess', $htaccess, LOCK_EX );
				
				//No needed anymore
				unset( $htaccess );
		?>
                <form id="sign_up" action="<?=$installurl;?>?step=2" method="post">
                    <h2>Register a new Admin</h2>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="username" placeholder="Username" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" minlength="6" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="confirm" minlength="6" placeholder="Confirm Password" required>
                        </div>
                    </div>
					
					<h2>Basic Settings</h2>
					<div class="form-group row">
						<label for="site_name" class="col-sm-2 col-form-label">Site Name</label>
						<div class="col-sm-10">
						  <input type="text" class="form-control" name="site_name" value="" required autofocus>
						</div>
					</div>
					
					<div class="form-group row">
						<label for="admin_slug" class="col-sm-2 col-form-label">Admin Slug</label>
						<div class="col-sm-10">
						  <input type="text" class="form-control" name="admin_slug" value="admin" required autofocus>
						  <p>Here you can rename the admin slug (mysite.com/<strong>admin</strong>/ to something different e.g. <strong>admina3c6</strong>.</p>
						</div>
					</div>
					
					<div class="form-group row">
						<label for="ping_slug" class="col-sm-2 col-form-label">Ping Slug</label>
						<div class="col-sm-10">
						  <input type="text" class="form-control" name="ping_slug" value="ping" required autofocus>
						  <p>Here you can rename the ping slug (mysite.com/<strong>ping</strong>/ to something different e.g. <strong>my-hidden-ping</strong>.</p>
						</div>
					</div>
					
					<div class="form-group row">
						<label for="db_prefix" class="col-sm-2 col-form-label">Database Tables Prefix</label>
						<div class="col-sm-10">
						  <input type="text" class="form-control" name="db_prefix" value="<?php echo strtolower( GenerateRandomKey( 3 ) ) ?>_" required autofocus>
						  <p>Here you can set the database table prefix you want to use. Please use only alphanumeric characters or underscore (NO hyphen).</p>
						</div>
					</div>
					
					<div class="form-group row">
						<label for="db_posts" class="col-sm-2 col-form-label">Posts Table Name</label>
						<div class="col-sm-10">
						  <input type="text" class="form-control" name="db_posts" value="posts" required autofocus>
						  <p>By default, the posts table is named as "posts", but you can change it here. Note that you can change it only once.</p>
						</div>
					</div>
					
					<div class="form-group row">
						<label for="db_users" class="col-sm-2 col-form-label">Users Table Name</label>
						<div class="col-sm-10">
						  <input type="text" class="form-control" name="db_users" value="members" required autofocus>
						  <p>By default, the members table is named as "members", but you can change it here. Note that you can change it only once.</p>
						</div>
					</div>
					
					<div class="form-group row">
						<label for="language" class="col-sm-2 col-form-label">Choose your language</label>
						<div class="col-sm-10">
							<select id="language" name="language" class="form-control form-control-lg">
							<?php
								$langs = langs();
								foreach( $langs as $id => $lang ) :
							?>
								<option value="<?php echo $id ?>" <?php echo ( ( $id == DEFAULT_LANGUAGE ) ? 'selected' : '' ) ?>><?php echo $lang['name'] ?> (<?php echo $lang['locale'] ?>) <?php echo ( file_exists( LANGS_DIR . $lang['code'] . '.json' ) ? '[Installed]' : '' ) ?></option>
							<?php endforeach ?>
							</select>
						<p>If there is no language file available, you can still select it as your site's language, but the values will be copied from the 'English' language. You can translate its values or upload a language file for this language later.</p>
					</div>
					</div>

                  <button class="btn btn-block btn-lg bg-blue" type="submit">Finish Installation</button>
				  
				  <input type="hidden" id="dbname" name="dbname" value="<?php echo $_POST['dbname'] ?>">
				  <input type="hidden" id="dbusername" name="dbusername" value="<?php echo $_POST['dbusername'] ?>">
				  <input type="hidden" id="dbpass" name="dbpass" value="<?php echo $_POST['dbpass'] ?>">
				  <input type="hidden" id="dbhost" name="dbhost" value="<?php echo $_POST['dbhost'] ?>">
                </form>
		<?php } elseif ( $step == '0' ) { ?>
                <form id="sign_up" action="<?=$installurl;?>?step=1" method="post">
                    <div class="msg">Enter the database details</div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">link</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="dbhost" value="localhost" placeholder="Database Host (it will usually be 'localhost')" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">build</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="dbname" placeholder="Database Name" required>
                        </div>
                    </div>
					<div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">dashboard</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="dbusername" placeholder="Database Username" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="dbpass" placeholder="Database Password">
                        </div>
                    </div>

                    <button class="btn btn-block btn-lg bg-blue waves-effect" type="submit">Continue</button>

                </form>
	<?php }
		}?>
		   </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>

</html>

<?php 

function dbBuild()
{
	global $email, $siteurl;
	
	//Take care of the language file
	CopyFileLang();
	
	$lang = ( ( !isset( $_POST['language'] ) || empty( $_POST['language'] ) ) ? DEFAULT_LANGUAGE : $_POST['language'] );
	
	$prefix = preg_replace( "/[^a-zA-Z0-9-_]+/", "", $_POST['db_prefix'] );
	
	$siteName = Sanitize ( $_POST['site_name'] );
	
	$postsTable = ( !empty( $_POST['db_posts'] ) ? Sanitize ( $_POST['db_posts'], false ) : 'posts' );
	
	$usersTable = ( !empty( $_POST['db_users'] ) ? Sanitize ( $_POST['db_users'], false ) : 'members' );
	
	$password = Sanitize ( $_POST['password'], false );
	
	$userHash = GenerateRandomKey( 8 );
	
	$userPass = sha1( $password . $userHash );
	
	$dtb = array();
	
	$langData = isset( langs()[$lang] ) ? langs()[$lang] : langs()['us'];
	
	$apiToken = sha1( time() . GenerateRandomKey( 20 ) );
	$apiToken = substr( $apiToken, 0, 20 );

	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "api_obj`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "api_obj` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `is_primary` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `api_limit` int UNSIGNED NOT NULL DEFAULT 0,
			  `items_limit` mediumint UNSIGNED NOT NULL DEFAULT 0,
			  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
			  `last_time_viewed` int UNSIGNED NOT NULL DEFAULT 0,
			  `total_num_views` int UNSIGNED NOT NULL DEFAULT 0,
			  `total_day_views` int UNSIGNED NOT NULL DEFAULT 0,
			  `disabled` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `ip` varchar(255) DEFAULT NULL,
			  `name` varchar(100) NOT NULL,
			  `descr` varchar(500) DEFAULT NULL,
			  `token` varchar(100) NOT NULL,
			  `allow_data` text DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	$dtb[] = "INSERT INTO `" . $prefix . "api_obj` (`id`, `id_site`, `is_primary`, `name`, `token`, `allow_data`) VALUES (1, 1, 1, 'Default', '" . $apiToken . "', 'all');";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "ban_groups`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "ban_groups` (
			  `id_ban_group` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `name` varchar(50) DEFAULT NULL,
			  `ban_time` int UNSIGNED NOT NULL DEFAULT 0,
			  `expire_time` int UNSIGNED NOT NULL DEFAULT 0,
			  `deny_access` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `cannot_login` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `cannot_register` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `cannot_post` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `cannot_comment` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `reason` varchar(255) DEFAULT NULL,
			  `notes` text DEFAULT NULL,
			  PRIMARY KEY (`id_ban_group`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "ban_items`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "ban_items` (
			  `id_ban` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_ban_group` smallint UNSIGNED NOT NULL DEFAULT 0,
			  `id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
			  `ip` varchar(255) DEFAULT NULL,
			  `hostname` varchar(255) DEFAULT NULL,
			  `email_address` varchar(255) DEFAULT NULL,
			  `hits` mediumint UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id_ban`),
			  KEY `ID_GROUP` (`id_ban_group`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	$maint = array();
	
	//Add a temporary image
	$maint['background_image'] = $siteurl . 'inc/tools/theme_files/assets/frontend/img/sample-background.jpg';
	
	$maint = json_encode( $maint );
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "sites`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "sites` (
			  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) DEFAULT NULL,
			  `url` varchar(255) DEFAULT NULL,
			  `is_primary` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `disabled` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `enable_multilang` enum('true','false') NOT NULL DEFAULT 'false',
			  `enable_multiblog` enum('true','false') NOT NULL DEFAULT 'false',
			  `enable_multisite` enum('true','false') NOT NULL DEFAULT 'false',
			  `enable_login_maintenance` enum('true','false') NOT NULL DEFAULT 'false',
			  `external_db` enum('true','false') NOT NULL DEFAULT 'false',
			  `enable_registration` enum('true','false') NOT NULL DEFAULT 'true',
			  `enable_maintenance` enum('true','false') NOT NULL DEFAULT 'false',
			  `maintenance_data` text DEFAULT NULL,
			  `share_data` text DEFAULT NULL,
			  `site_secret` varchar(50) DEFAULT NULL,
			  `cache_hash` varchar(50) DEFAULT NULL,
			  `ping_slash` varchar(50) DEFAULT NULL,
			  `update_hash` varchar(50) DEFAULT NULL,
			  `preview_hash` varchar(50) DEFAULT NULL,
			  `site_ping_url` varchar(250) DEFAULT NULL,
			  `hosted` text DEFAULT NULL,
			  `hosted_data` text DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	$dtb[] = "INSERT INTO `" . $prefix . "sites` (`id`, `title`, `url`, `is_primary`, `maintenance_data`) VALUES (1, '" . $siteName . "', '" . $siteurl . "', 1, '" . $maint . "');";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "tags`;";
	
    $dtb[] = "CREATE TABLE `" . $prefix . "tags` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_lang` int UNSIGNED NOT NULL DEFAULT 0,
			  `id_custom_type` smallint UNSIGNED NOT NULL DEFAULT 0,
			  `id_image` int UNSIGNED NOT NULL DEFAULT 0,
			  `title` varchar(100) NOT NULL,
			  `descr` varchar(500) DEFAULT NULL,
			  `num_items` int UNSIGNED NOT NULL DEFAULT 0,
			  `sef` varchar(50) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `SEF` (`sef`),
			  KEY `CTP` (`id_custom_type`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	$dtb[] = "INSERT INTO " . $prefix . "tags (`id`, `id_lang`, `title`, `num_items`, `sef`) VALUES (1, 1, 'Blog', 1, 'blog');";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "tags_relationships`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "tags_relationships` (
			  `id_relation` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `object_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `taxonomy_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `id_custom_type` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id_relation`),
			  KEY `OBJECT` (`object_id`),
			  KEY `SITE` (`id_site`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	$dtb[] = "INSERT INTO " . $prefix . "tags_relationships (`id_relation`, `object_id`, `taxonomy_id`, `id_site`) VALUES (1, 1, 1, 1);";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "categories`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "categories` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `id_blog` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `id_parent` int UNSIGNED NOT NULL DEFAULT 0,
			  `id_image` int UNSIGNED NOT NULL DEFAULT 0,
			  `id_trans_parent` int UNSIGNED NOT NULL DEFAULT 0,
			  `is_default` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `name` varchar(100) NOT NULL,
			  `sef` varchar(100) NOT NULL,
			  `descr` varchar(500) DEFAULT NULL,
			  `hide_front` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `hide_blog` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `num_items` int UNSIGNED NOT NULL DEFAULT 0,
			  `article_limit` mediumint UNSIGNED NOT NULL DEFAULT 0,
			  `groups_data` text DEFAULT NULL,
			  `cat_color` varchar(15) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `SEF` (`sef`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

	$dtb[] = "INSERT INTO " . $prefix . "categories (`id`, `id_lang`, `id_site`, `is_default`, `name`, `sef`, `num_items`) VALUES (1, 1, 1, 1, 'Uncategorized', 'uncategorized', 1);";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "manufacturers`;";
	
    $dtb[] = "CREATE TABLE `" . $prefix . "manufacturers` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `id_image` int UNSIGNED NOT NULL DEFAULT 0,
			  `title` varchar(100) NOT NULL,
			  `sef` varchar(100) NOT NULL,
			  `descr` varchar(600) DEFAULT NULL,
			  `num_items` int UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id`),
			  KEY SITE (`id_site`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "redirs`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "redirs` (
			  `id` int NOT NULL AUTO_INCREMENT,
			  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `title` varchar(255) DEFAULT NULL,
			  `uri` varchar(150) DEFAULT NULL,
			  `slug` varchar(150) DEFAULT NULL,
			  `target` varchar(300) DEFAULT NULL,
			  `when_matched` varchar(50) DEFAULT NULL,
			  `http_code` varchar(50) DEFAULT NULL,
			  `views` mediumint UNSIGNED NOT NULL DEFAULT 0,
			  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
			  `last_time_viewed` int UNSIGNED NOT NULL DEFAULT 0,
			  `exclude_logs` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `disable_redir` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id`),
			  KEY `SITE` (`id_site`),
			  KEY `URI` (`uri`),
			  KEY `SLUG` (`slug`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "images`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "images` (
		  `id_image` int UNSIGNED NOT NULL AUTO_INCREMENT,
		  `id_parent` int UNSIGNED NOT NULL DEFAULT 0,
		  `id_post` int UNSIGNED NOT NULL DEFAULT 0,
		  `id_attach_post` int UNSIGNED NOT NULL DEFAULT 0,
		  `id_lang` smallint UNSIGNED NOT NULL DEFAULT 0,
		  `id_blog` smallint UNSIGNED NOT NULL DEFAULT 0,
		  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
		  `id_folder` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `filename` varchar(255) DEFAULT NULL,
		  `file_hash` varchar(50) DEFAULT NULL,
		  `width` mediumint UNSIGNED NOT NULL DEFAULT 0,
		  `height` mediumint UNSIGNED NOT NULL DEFAULT 0,
		  `size` int UNSIGNED NOT NULL DEFAULT 0,
		  `file_ext` varchar(20) DEFAULT NULL,
		  `mime_type` varchar(80) DEFAULT 'image',
		  `aproved` tinyint UNSIGNED NOT NULL DEFAULT 1,
		  `img_status` enum('full','cropped') NOT NULL DEFAULT 'full',
		  `img_type` enum('post','thumb','cover','site','user','maintenance') NOT NULL DEFAULT 'post',
		  `title` varchar(255) DEFAULT NULL,
		  `alt` varchar(500) DEFAULT NULL,
		  `descr` varchar(500) DEFAULT NULL,
		  `caption` varchar(500) DEFAULT NULL,
		  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
		  `trans_data` text DEFAULT NULL,
		  `extra_data` text DEFAULT NULL,
		  `external_url` varchar(500) DEFAULT NULL,
		   PRIMARY KEY (`id_image`),
		   KEY `IMGTYPE` (`img_type`,`id_attach_post`),
		   KEY `PARENT` (`id_parent`),
		   KEY `POST` (`id_post`),
		   KEY `USER` (`id_member`),
		   KEY `LANG` (`id_lang`),
		   KEY `BLOG` (`id_blog`),
		   KEY `FOLDER` (`id_folder`),
		   KEY `SITE` (`id_site`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "image_folders`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "image_folders` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `id_parent` int UNSIGNED NOT NULL DEFAULT 0,
			  `is_default` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `name` varchar(100) NOT NULL,
			  `sef` varchar(100) NOT NULL,
			  `descr` varchar(500) DEFAULT NULL,
			  `hide_front` enum('true','false') NOT NULL DEFAULT 'false',
			  `num_items` int UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id`),
			  KEY `SEF` (`sef`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

	$dtb[] = "INSERT INTO " . $prefix . "image_folders (`id`, `id_lang`, `id_site`, `is_default`, `name`, `sef`) VALUES (1, 1, 1, 1, 'Site', 'site');";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "image_galleries`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "image_galleries` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `id_post` int UNSIGNED NOT NULL DEFAULT 0,
			  `name` varchar(100) NOT NULL,
			  `sef` varchar(100) NOT NULL,
			  `descr` varchar(500) DEFAULT NULL,
			  `num_items` int UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id`),
			  KEY `SEF` (`sef`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "image_attachments`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "image_attachments` (
			  `id_attach` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `post_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `image_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `user_id` int UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id_attach`),
			  KEY `OBJECT` (`post_id`),
			  KEY `IMG` (`image_id`),
			  KEY `USER` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . $usersTable . "`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . $usersTable . "` (
		  `id_member` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
		  `id_group` smallint UNSIGNED NOT NULL DEFAULT 0,
		  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `user_name` varchar(80) DEFAULT NULL,
		  `date_registered` int UNSIGNED NOT NULL DEFAULT 0,
		  `num_posts` mediumint UNSIGNED NOT NULL DEFAULT 0,
		  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `last_login` int UNSIGNED NOT NULL DEFAULT 0,
		  `real_name` varchar(255) DEFAULT NULL,
		  `instant_messages` smallint NOT NULL DEFAULT 0,
		  `unread_messages` smallint NOT NULL DEFAULT 0,
		  `new_pm` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `buddy_list` text DEFAULT NULL,
		  `pm_ignore_list` varchar(255) DEFAULT NULL,
		  `pm_prefs` mediumint NOT NULL DEFAULT 0,
		  `mod_prefs` varchar(20) DEFAULT NULL,
		  `message_labels` text DEFAULT NULL,
		  `passwd` varchar(64) DEFAULT NULL,
		  `email_address` varchar(255) DEFAULT NULL,
		  `personal_text` varchar(255) DEFAULT NULL,
		  `gender` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `birthdate` date NOT NULL DEFAULT '0001-01-01',
		  `website_title` varchar(255) DEFAULT NULL,
		  `website_url` varchar(255) DEFAULT NULL,
		  `location` varchar(255) DEFAULT NULL,
		  `hide_email` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `show_online` tinyint UNSIGNED NOT NULL DEFAULT 1,
		  `time_format` varchar(80) DEFAULT NULL,
		  `user_bio` text DEFAULT NULL,
		  `time_offset` float NOT NULL DEFAULT 0,
		  `image_data` text DEFAULT NULL,
		  `pm_email_notify` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `usertitle` varchar(255) DEFAULT NULL,
		  `notify_announcements` tinyint UNSIGNED NOT NULL DEFAULT 1,
		  `notify_regularity` tinyint UNSIGNED NOT NULL DEFAULT 1,
		  `notify_send_body` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `notify_types` tinyint UNSIGNED NOT NULL DEFAULT 2,
		  `member_ip` varchar(255) DEFAULT NULL,
		  `secret_question` varchar(255) DEFAULT NULL,
		  `secret_answer` varchar(64) DEFAULT NULL,
		  `id_theme` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `is_activated` tinyint UNSIGNED NOT NULL DEFAULT 1,
		  `validation_code` varchar(10) DEFAULT NULL,
		  `last_visit` int UNSIGNED NOT NULL DEFAULT 0,
		  `additional_groups` varchar(255) DEFAULT NULL,
		  `password_hash` varchar(255) DEFAULT NULL,
		  `ignore_boards` varchar(255) DEFAULT NULL,
		  `warning` enum('true','false') NOT NULL DEFAULT 'false',
		  `passwd_flood` varchar(20) DEFAULT NULL,
		  `pm_receive_from` tinyint UNSIGNED NOT NULL DEFAULT 1,
		  `num_comments` int UNSIGNED NOT NULL DEFAULT 0,
		  `social_data` text DEFAULT NULL,
		  `trans_data` text DEFAULT NULL,
		  `dashboard_data` text DEFAULT NULL,
		  PRIMARY KEY (`id_member`),
		  KEY `USER_NAME` (`user_name`),
		  KEY `REGISTER` (`date_registered`),
		  KEY `GROUP` (`id_group`),
		  KEY `POSTS` (`num_posts`),
		  KEY `LAST_LOGIN` (`last_login`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

	$dtb[] = "INSERT INTO `" . $prefix . $usersTable . "` (`id_member`, `user_name`, `date_registered`, `id_group`, `last_login`, `real_name`, `passwd`, `email_address`, `member_ip`, `is_activated`, `password_hash`, `id_site`) VALUES
	(1, '" . Sanitize ( $_POST['username'], false ) . "', " . time() . ", 1, " . time() . ", '" . Sanitize ( ucfirst( $_POST['username'] ), false ) . "', '" . $userPass . "', '" . $email . "', '" . GetRealIp() . "', 1, '" . $userHash . "', 1);";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "members_relationships`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "members_relationships` (
			  `id_relation` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_member` int UNSIGNED NOT NULL DEFAULT 0,
			  `id_cloned_member` int UNSIGNED NOT NULL DEFAULT 0,
			  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id_relation`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "post_authors`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "post_authors` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `post_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `user_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id`),
			  KEY `OBJECT` (`post_id`),
			  KEY `USER` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "auth_tokens`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "auth_tokens` (
		`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
		`selector` varchar(12) NOT NULL,
		`token` varchar(64) NOT NULL,
		`userid` int UNSIGNED NOT NULL,
		`expires` datetime,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "password_reset`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "password_reset` (
		`user_id` int NOT NULL,
		`reset_hash` varchar(64) NOT NULL,
		`reset_time` int UNSIGNED NOT NULL DEFAULT 0,
		PRIMARY KEY (`user_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "posts_favorites`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "posts_favorites` (
			  `id_relation` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_folder` int UNSIGNED NOT NULL DEFAULT 0,
			  `post_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `user_id` int UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id_relation`),
			  KEY `OBJECT` (`post_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "posts_subscriptions`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "posts_subscriptions` (
			  `id_relation` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `post_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `user_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `email` varchar(100) DEFAULT NULL,
			  `ip` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id_relation`),
			  KEY `OBJECT` (`post_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "posts_autosaves`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "posts_autosaves` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `post_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `user_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
			  `edited_time` int UNSIGNED NOT NULL DEFAULT 0,
			  `title` varchar(255) DEFAULT NULL,
			  `post` LONGTEXT NOT NULL,
			  `blocks` LONGTEXT DEFAULT NULL,
			  `draft_type` enum('auto','manual') NOT NULL DEFAULT 'auto',
			  PRIMARY KEY (`id`),
			  KEY `OBJECT` (`post_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . $postsTable . "`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . $postsTable . "` (
		  `id_post` int UNSIGNED NOT NULL AUTO_INCREMENT,
		  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `id_blog` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
		  `id_member_updated` mediumint UNSIGNED NOT NULL DEFAULT 0,
		  `id_custom_type` smallint UNSIGNED NOT NULL DEFAULT 0,
		  `id_category` mediumint UNSIGNED NOT NULL DEFAULT 0,
		  `id_sub_category` mediumint UNSIGNED NOT NULL DEFAULT 0,
		  `id_source` smallint UNSIGNED NOT NULL DEFAULT 0,
		  `id_parent` int UNSIGNED NOT NULL DEFAULT 0,
		  `id_page_parent` int UNSIGNED NOT NULL DEFAULT 0,
		  `page_order` smallint UNSIGNED NOT NULL DEFAULT 0,
		  `id_group` smallint UNSIGNED NOT NULL DEFAULT 0,
		  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
		  `edited_time` int UNSIGNED NOT NULL DEFAULT 0,
		  `title` varchar(255) NOT NULL,
		  `description` varchar(500) DEFAULT NULL,
		  `post` LONGTEXT NOT NULL,
		  `content` LONGTEXT DEFAULT NULL,
		  `sef` varchar(150) NOT NULL,
		  `tag_line` varchar(255) DEFAULT NULL,
		  `views` int UNSIGNED NOT NULL DEFAULT 0,
		  `last_time_viewed` int UNSIGNED NOT NULL DEFAULT 0,
		  `post_status` enum('published','draft','scheduled','pending','deleted') NOT NULL DEFAULT 'published',
		  `post_type` enum('post','page') NOT NULL DEFAULT 'post',
		  `poster_ip` varchar(60) DEFAULT NULL,
		  `permissions` text DEFAULT NULL,
		  `update_reason` text DEFAULT NULL,
		  `num_comments` mediumint UNSIGNED NOT NULL DEFAULT 0,
		  `disable_comments` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `cover_img` text DEFAULT NULL,
		  `blocks` LONGTEXT DEFAULT NULL,
		  PRIMARY KEY (`id_post`),
		  UNIQUE KEY `UNIQUE` (`id_post`,`id_member`),
		  KEY `USER` (`id_member`),
		  KEY `LANG` (`id_lang`),
		  KEY `BLOG` (`id_blog`),
		  KEY `SITE` (`id_site`),
		  KEY `TYPE` (`post_type`),
		  KEY `PGPRNT` (`id_page_parent`),
		  KEY `SEF` (`sef`),
		  KEY `STATUS` (`post_status`),
		  KEY `RELATED_IP` (`id_member`,`poster_ip`,`id_post`),
		  KEY `PSTLS` (`post_type`,`post_status`,`id_lang`,`id_site`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

		$dtb[] = "INSERT INTO " . $prefix . $postsTable . " VALUES (1, 1, 0, 1, 1, 0, 0, 1, 0, 0, 0, 0, UNIX_TIMESTAMP(), 0, 'Welcome to TokiCMS', 'If you\'re seeing this article, you have installed <strong>TokiCMS</strong> and it is connected to the database.', '<p>If you\'re seeing this article, you have installed <strong>TokiCMS</strong> and it is connected to the database.</p><p>It is <strong>strongly</strong> suggested that you <a href=\"" . $siteurl . "login/\" title=\"Login\">login</a> right away, then go to the page <em>Settings</em>.</p> <p>Still lost? We\'ll be there to assist you in any way we can.</p><p>Thank you for choosing TokiCMS. We hope you enjoy it as much as we do.</p>', '', 'welcome-to-tokicms', 'Welcome to TokiCMS', 1, UNIX_TIMESTAMP(), 'published', 'post', '127.0.0.1', '', '', 1, 0, '', ''),
		(2, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 0, 'About', 'This is a test page.', '<p>This is a test page.</p><p>Edit this page or delete it.</p>', '', 'about', '', 1, UNIX_TIMESTAMP(), 'published', 'page', '127.0.0.1', '', '', 0, 0, '', '');";

	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "posts_data`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "posts_data` (
			`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_post` int UNSIGNED NOT NULL DEFAULT 0,
			`id_play` smallint UNSIGNED NOT NULL DEFAULT 0,
			`man_id` int UNSIGNED NOT NULL DEFAULT 0,
			`clone_id` int UNSIGNED NOT NULL DEFAULT 0,
			`value1` text NOT NULL COMMENT 'Video Data',
			`value2` text NOT NULL COMMENT 'SEO Data',
			`value3` text NOT NULL COMMENT 'Gallery data',
			`value4` text NOT NULL COMMENT 'Post data',
			`uuid` varchar(300) DEFAULT NULL,
			`title_alias` varchar(300) DEFAULT NULL,
			`prices_title` varchar(300) DEFAULT NULL,
			`blocks` LONGTEXT DEFAULT NULL,
			`external_url` varchar(300) DEFAULT NULL,
			`ext_id` varchar(50) DEFAULT NULL,
			`hide_on_home` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`keep_date` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`last_time_checked` int UNSIGNED NOT NULL DEFAULT 0,
			`last_time_pinged` int UNSIGNED NOT NULL DEFAULT 0,
			`last_time_commented` int UNSIGNED NOT NULL DEFAULT 0,
			`original_import_time` int UNSIGNED NOT NULL DEFAULT 0,
			`times_checked` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`allow_voting` smallint UNSIGNED NOT NULL DEFAULT 0,
			`add_price_num` smallint UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (`id`),
			KEY `POST` (`id_post`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "posts_product_data`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "posts_product_data` (
			`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_post` int UNSIGNED NOT NULL DEFAULT 0,
			`sort_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`model` varchar(100) DEFAULT NULL,
			`sku` varchar(100) DEFAULT NULL,
			`upc` varchar(100) DEFAULT NULL,
			`ean` varchar(100) DEFAULT NULL,
			`jan` varchar(100) DEFAULT NULL,
			`isbn` varchar(100) DEFAULT NULL,
			`mpn` varchar(100) DEFAULT NULL,
			`location` varchar(100) DEFAULT NULL,
			`quantity` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`min_quantity` smallint UNSIGNED NOT NULL DEFAULT 0,
			`subtrack_stock` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`require_shipping` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`avail_time` int UNSIGNED NOT NULL DEFAULT 0,
			`weight_class` enum('kilo','gram','pound','ounce') NOT NULL DEFAULT 'kilo',
			`length_class` enum('centimeter','millimeter','inch') NOT NULL DEFAULT 'centimeter',
			`weight` float DEFAULT NULL,
			`length` float DEFAULT NULL,
			`width` float DEFAULT NULL,
			`height` float DEFAULT NULL,
			`disabled` tinyint UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (`id`),
			KEY `POST` (`id_post`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "post_variations`;";
	
	 $dtb[] = "CREATE TABLE `" . $prefix . "post_variations` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_site` smallint UNSIGNED NOT NULL DEFAULT 0,
			  `id_lang` smallint UNSIGNED NOT NULL DEFAULT 0,
			  `id_category` mediumint UNSIGNED NOT NULL DEFAULT 0,
			  `id_post` int UNSIGNED NOT NULL DEFAULT 0,
			  `title` varchar(255) NOT NULL,
			  `sef` varchar(100) NOT NULL,
			  `description` varchar(500) DEFAULT NULL,
			  `trans_data` text DEFAULT NULL,
			  PRIMARY KEY (id),
			  KEY PST (id_post),
			  KEY SITE (id_site),
			  KEY LANG (id_lang)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "post_variations_items`;";
	
	 $dtb[] = "CREATE TABLE `" . $prefix . "post_variations_items` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_parent` int UNSIGNED NOT NULL DEFAULT 0,
			  `id_post` int UNSIGNED NOT NULL DEFAULT 0,
			  `id_image` int UNSIGNED NOT NULL DEFAULT 0,
			  `var_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `title` varchar(255) NOT NULL,
			  `sef` varchar(100) NOT NULL,
			  `ptitle` varchar(255) DEFAULT NULL,
			  `url` varchar(255) DEFAULT NULL,
			  `sku` varchar(100) DEFAULT NULL,
			  `quantity` mediumint UNSIGNED NOT NULL DEFAULT 0,
			  `subtrack_stock` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `frontend_visibility` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `sale_price` float DEFAULT NULL,
			  `weight` float DEFAULT NULL,
			  `points` float DEFAULT NULL,
			  `subtrack_price` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `subtrack_weight` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `subtrack_points` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (id),
			  KEY PRNT (id_parent)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "post_types`;";
	
    $dtb[] = "CREATE TABLE `" . $prefix . "post_types` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_site` smallint UNSIGNED NOT NULL DEFAULT 0,
			  `id_parent` int UNSIGNED NOT NULL DEFAULT 0,
			  `id_image` int UNSIGNED NOT NULL DEFAULT 0,
			  `type_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `title` varchar(255) NOT NULL,
			  `sef` varchar(100) NOT NULL,
			  `description` varchar(500) DEFAULT NULL,
			  `theme` varchar(50) DEFAULT NULL,
			  `num_items` int UNSIGNED NOT NULL DEFAULT 0,
			  `trans_data` text DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `PARNT` (`id_parent`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "post_types_relationships`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "post_types_relationships` (
			`id_relation` int UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_post_type` int UNSIGNED NOT NULL DEFAULT 0,
			`post_id` int UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (`id_relation`),
			KEY `PSST` (`id_post_type`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "post_attr_group`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "post_attr_group` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` smallint UNSIGNED NOT NULL DEFAULT 0,
	  `id_blog` smallint UNSIGNED NOT NULL DEFAULT 0,
	  `id_lang` smallint UNSIGNED NOT NULL DEFAULT 0,
	  `id_category` smallint UNSIGNED NOT NULL DEFAULT 0,
	  `id_custom_type` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `trans_data` text NOT NULL,
	  `name` varchar(100) NOT NULL,
	  `sef` varchar(100) NOT NULL,
	  `group_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `every_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  PRIMARY KEY (id),
	  KEY SITE (id_site),
	  KEY BLOG (id_blog),
	  KEY LANG (id_lang)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "post_attribute_data`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "post_attribute_data` (
	  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_attr` smallint UNSIGNED NOT NULL DEFAULT 0,
	  `id_post` int UNSIGNED NOT NULL DEFAULT 0,
	  `value` text NOT NULL,
	  PRIMARY KEY (id),
	  KEY ATTRID (id_attr),
	  KEY POST (id_post)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "post_attributes`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "post_attributes` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_group` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `name` varchar(100) NOT NULL,
	  `sef` varchar(100) NOT NULL,
	  `attr_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `trans_data` text NOT NULL,
	  PRIMARY KEY (id),
	  KEY ATTGRP (id_group)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "filters_data`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "filters_data` (
	  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_group` smallint UNSIGNED NOT NULL DEFAULT 0,
	  `filter_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `filter_name` varchar(100) NOT NULL,
	  `trans_data` text DEFAULT NULL,
	  PRIMARY KEY (id),
	  KEY GROUPID (id_group)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "filter_group`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "filter_group` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `name` varchar(100) NOT NULL,
	  `sef` varchar(100) NOT NULL,
	  `group_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `group_data` text DEFAULT NULL,
	  PRIMARY KEY (id),
	  KEY SITE (id_site),
	  KEY LANG (id_lang)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "stores`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "stores` (
	  `id_store` int UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_image` int UNSIGNED NOT NULL DEFAULT 0,
	  `id_type` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_parent` int UNSIGNED NOT NULL DEFAULT 0,
	  `name` varchar(255) NOT NULL,
	  `sef` varchar(255) NOT NULL,
	  `url` varchar(255) NOT NULL,
	  `description` varchar(500) DEFAULT NULL,
	  `post` text DEFAULT NULL,
	  `scrape_as` varchar(15) DEFAULT 'normal',
	  `rotate_ip` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `retrieve_json_data` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `json_data` text NOT NULL,
	  PRIMARY KEY (id_store),
	  KEY STORETYPE (id_type),
	  KEY SITE (id_site)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "stores_data`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "stores_data` (
	  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_store` int UNSIGNED NOT NULL DEFAULT 0,
	  `name` varchar(50) DEFAULT NULL,
	  `key_value` varchar(50) DEFAULT NULL,
	  `reg_data` text DEFAULT NULL,
	  PRIMARY KEY (id),
	  KEY STOREID (id_store)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "store_types`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "store_types` (
			`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` varchar(100) NOT NULL,
			`sef` varchar(100) NOT NULL,
			`descr` varchar(500) DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

	$dtb[] = "INSERT INTO `" . $prefix . "store_types` (`id`, `name`, `sef`) VALUES 
	(1, 'Simple', 'simple'),
	(2, 'eShop', 'eshop'),
	(3, 'Digital Only', 'digital-only'),
	(4, 'Supermarket', 'supermarket'),
	(5, 'Discount Store', 'discount-store'),
	(6, 'Grocery Store', 'grocery-store'),
	(7, 'Marketplace', 'marketplace'),
	(8, 'Vendor', 'vendor'),
	(9, 'Deal or coupon site', 'deal-coupon');";

	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "store_attribute_data`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "store_attribute_data` (
	  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_attr` smallint UNSIGNED NOT NULL DEFAULT 0,
	  `id_store` int UNSIGNED NOT NULL DEFAULT 0,
	  `value` text NOT NULL,
	  PRIMARY KEY (id),
	  KEY ATTRID (id_attr),
	  KEY STOREID (id_store)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "stores_attributes`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "stores_attributes` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `name` varchar(100) NOT NULL,
	  `sef` varchar(100) NOT NULL,
	  `attr_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `trans_data` text NOT NULL,
	  PRIMARY KEY (id),
	  KEY SITE (id_site),
	  KEY LANG (id_lang)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "prices`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "prices` (
		`id_price` int UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_post` int UNSIGNED NOT NULL DEFAULT 0,
		`id_parent` int UNSIGNED NOT NULL DEFAULT 0,
		`id_currency` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`id_store` mediumint UNSIGNED NOT NULL DEFAULT 0,
		`id_tax` smallint UNSIGNED NOT NULL DEFAULT 0,
		`image_id` int UNSIGNED NOT NULL DEFAULT 0,
		`user_id` mediumint UNSIGNED NOT NULL DEFAULT 0,
		`type` enum('normal','coupon') NOT NULL DEFAULT 'normal',
		`coupon_type` enum('coupon','deal','image') NOT NULL DEFAULT 'coupon',
		`title` varchar(255) DEFAULT NULL,
		`content` text DEFAULT NULL,
		`link_text` varchar(50) DEFAULT NULL,
		`extra_text` varchar(50) DEFAULT NULL,
		`time_added` int UNSIGNED NOT NULL DEFAULT 0,
		`last_time_viewed` int UNSIGNED NOT NULL DEFAULT 0,
		`available_since` int UNSIGNED NOT NULL DEFAULT 0,
		`expire_time` int UNSIGNED NOT NULL DEFAULT 0,
		`available_title` varchar(255) DEFAULT NULL,
		`coupon_code` varchar(50) DEFAULT NULL,
		`locale_code` varchar(15) DEFAULT NULL,
		`regular_price` float DEFAULT NULL,
		`sale_price` float DEFAULT NULL,
		`discount_perce` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`discount_title` varchar(255) DEFAULT NULL,
		`main_page_url` varchar(400) DEFAULT NULL,
		`aff_page_url` varchar(400) DEFAULT NULL,
		`views` mediumint UNSIGNED NOT NULL DEFAULT 0,
		`is_featured` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`is_starting_price` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`is_free` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`mask_code` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`pre_order_only` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`likes` int UNSIGNED NOT NULL DEFAULT 0,
		`dislikes` int UNSIGNED NOT NULL DEFAULT 0,
		`pri_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
		PRIMARY KEY (`id_price`),
		KEY POST (id_post),
		KEY STOREID (id_store),
		KEY SITE (id_site)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "price_info`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "price_info` (
		`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_price` int UNSIGNED NOT NULL DEFAULT 0,
		`last_time_updated` int UNSIGNED NOT NULL DEFAULT 0,
		`last_time_checked` int UNSIGNED NOT NULL DEFAULT 0,
		`num_retries` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`not_found` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`in_stock` tinyint UNSIGNED NOT NULL DEFAULT 0,
		PRIMARY KEY (`id`),
		KEY PRICE (id_price)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "price_update_info`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "price_update_info` (
		`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_price` int UNSIGNED NOT NULL DEFAULT 0,
		`time_added` int UNSIGNED NOT NULL DEFAULT 0,
		`price` float DEFAULT NULL,
		`price_before` float DEFAULT NULL,
		PRIMARY KEY (`id`),
		KEY PRICE (id_price)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "comments`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "comments` (
			`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_post` int UNSIGNED NOT NULL DEFAULT 0,
			`id_parent` int UNSIGNED NOT NULL DEFAULT 0,
			`id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`id_blog` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`user_id` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`ext_id`  mediumint UNSIGNED NOT NULL DEFAULT 0,
			`rating` mediumint NOT NULL DEFAULT 0,
			`name` varchar(50) NOT NULL,
			`url` varchar(100) DEFAULT NULL,
			`email` varchar(100) DEFAULT NULL,
			`comment` text DEFAULT NULL,
			`added_time` int UNSIGNED NOT NULL DEFAULT 0,
			`last_checked` int UNSIGNED NOT NULL DEFAULT 0,
			`ip` varchar(100) DEFAULT NULL,
			`status` enum('approved','pending','spam','deleted') NOT NULL DEFAULT 'approved',
			`rating_data` text DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY POST (id_post),
			KEY MEMBER (user_id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	$dtb[] = "INSERT INTO `" . $prefix . "comments` VALUES (1, 1, 0, 1, 1, 0, 0, 0, 'Mr Comment', '', '', '<p>This is a sample comment.</p><p>Edit it or delete it.</p>', UNIX_TIMESTAMP(), 0, '127.0.0.1', 'approved', '');";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "mails`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "mails` (
			`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_parent` int UNSIGNED NOT NULL DEFAULT 0,
			`id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`added_time` int UNSIGNED NOT NULL DEFAULT 0,
			`name` varchar(120) DEFAULT NULL,
			`subject` varchar(250) DEFAULT NULL,
			`email` varchar(120) DEFAULT NULL,
			`post` text DEFAULT NULL,
			`ip` varchar(100) DEFAULT NULL,
			`email` varchar(120) DEFAULT NULL,
			`status` enum('inbox','sent','draft','junk','deleted') NOT NULL DEFAULT 'inbox',
			`default_status` enum('inbox','sent','draft','junk','deleted') NOT NULL DEFAULT 'inbox',
			PRIMARY KEY (`id`),
			KEY SITE (id_site)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "mail_replies`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "mail_replies` (
			`id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`id_mail` int UNSIGNED NOT NULL DEFAULT 0,
			`added_time` int UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (`id_member`,`id_mail`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "mail_forward`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "mail_forward` (
			`id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`id_mail` int UNSIGNED NOT NULL DEFAULT 0,
			`email` varchar(120) DEFAULT NULL,
			`added_time` int UNSIGNED NOT NULL DEFAULT 0,
			`subject` varchar(250) DEFAULT NULL,
			`post` text DEFAULT NULL,
			PRIMARY KEY (`id_member`,`id_mail`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "forms`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "forms` (
	  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_template` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `form_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `form_type` enum('form','table') NOT NULL DEFAULT 'form',
	  `table_type` varchar(50) DEFAULT NULL,
	  `form_pos` varchar(50) DEFAULT NULL,
	  `show_if` varchar(50) DEFAULT NULL,
	  `show_if_option` varchar(50) DEFAULT NULL,
	  `show_if_id` int UNSIGNED NOT NULL DEFAULT 0,
	  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
	  `title` varchar(255) DEFAULT NULL,
	  `descr` text DEFAULT NULL,
	  `disabled` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `groups_data` text DEFAULT NULL,
	  `form_data` text DEFAULT NULL,
	   PRIMARY KEY (id),
	   KEY `SITE` (`id_site`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "form_table_elements`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "form_table_elements` (
	  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_column` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `elem_order` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `elem_id` varchar(50) DEFAULT NULL,
	  `data` text DEFAULT NULL,
	  `style` text DEFAULT NULL,
	  `disabled` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `elem_type` enum('header','cell') NOT NULL DEFAULT 'header',
	   PRIMARY KEY (id),
	   KEY `COLUMID` (`id_column`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "form_elements`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "form_elements` (
	  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_form` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `elem_order` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `elem_id` varchar(50) DEFAULT NULL,
	  `elem_name` varchar(100) DEFAULT NULL,
	  `data` text DEFAULT NULL,
	  `disabled` tinyint UNSIGNED NOT NULL DEFAULT 0,
	   PRIMARY KEY (id),
	   KEY `FORMID` (`id_form`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "form_templates`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "form_templates` (
	  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `template_type` enum('form','table') NOT NULL DEFAULT 'form',
	  `title` varchar(255) DEFAULT NULL,
	  `data` text NOT NULL,
	   PRIMARY KEY (id),
	   KEY `SITE` (`id_site`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	$dtb[] = "INSERT INTO `" . $prefix . "form_templates` (`id`, `id_site`, `title`, `data`) VALUES (1, 1, 'Simple Contact Form', '{\"settings\":{},\"elements\":[{\"id\":\"header\",\"data\":{\"type\":\"h2\",\"value\":\"Contact Us\",\"class\":\"\",\"name\":\"header-a9de4e\"}},{\"id\":\"text-field\",\"data\":{\"label\":\"Name\",\"class\":\"form-control\",\"placeholder\":\"Name\",\"prepend\":\"\",\"append\":\"\",\"limit-length\":\"0\",\"help-text\":\"\",\"name\":\"text-field-e40e99\",\"required\":\"1\"}},{\"id\":\"text-field\",\"data\":{\"label\":\"Subject\",\"class\":\"form-control\",\"placeholder\":\"Subject\",\"prepend\":\"\",\"append\":\"\",\"limit-length\":\"0\",\"help-text\":\"\",\"name\":\"text-field-bdeb5d\",\"required\":\"1\"}},{\"id\":\"text-field\",\"data\":{\"label\":\"Email Address\",\"class\":\"form-control\",\"placeholder\":\"Email Address\",\"prepend\":\"\",\"append\":\"\",\"limit-length\":\"0\",\"help-text\":\"\",\"name\":\"text-field-31c0ab\",\"required\":\"1\"}},{\"id\":\"text-area\",\"data\":{\"label\":\"Your Message\",\"class\":\"form-control\",\"placeholder\":\"Enter your message here\",\"rows\":\"5\",\"limit-length\":\"0\",\"name\":\"text-field-f8de67\",\"help-text\":\"\",\"required\":\"1\"}},{\"id\":\"button\",\"data\":{\"label\":\"Button\",\"button-name\":\"Submit\",\"type\":\"submit\",\"class\":\"btn-default btn\",\"value\":\"\",\"name\":\"button-b1d902\"}}]}');";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "imports`;";

	$dtb[] = "CREATE TABLE `" . $prefix . "imports` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_category` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `id_blog` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `id_custom_type` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `prv_system` varchar(50) DEFAULT NULL,
	  `post_status` varchar(20) DEFAULT NULL,
	  `filename` varchar(500) DEFAULT NULL,
	  `old_url` varchar(500) DEFAULT NULL,
	  `copy_images` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `completed` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
	  `completed_time` int UNSIGNED NOT NULL DEFAULT 0,
	  `file_id` varchar(255) DEFAULT NULL,
	  `file_url` varchar(300) DEFAULT NULL,
	  `extra_data` text DEFAULT NULL,
	   PRIMARY KEY (`id`),
	   KEY `SITE` (`id_site`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "plugins`;";

	$dtb[] = "CREATE TABLE `" . $prefix . "plugins` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `plugin_id` varchar(100) NOT NULL,
	  `name` varchar(100) NOT NULL,
	  `description` text DEFAULT NULL,
	  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
	  `plugin_order` smallint UNSIGNED NOT NULL,
	   PRIMARY KEY (`id`),
	   UNIQUE `UNIQUEINX`(`id_site`, `plugin_id`),
	   KEY `SITE` (`id_site`),
	   KEY `STATUS` (`status`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "plugin_hooks`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "plugin_hooks` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_plugin` smallint UNSIGNED NOT NULL DEFAULT 0,
	  `hook_id` varchar(100) NOT NULL,
	  `function_name` varchar(100) DEFAULT NULL,
	  `file_include` varchar(100) DEFAULT NULL,
	   PRIMARY KEY (`id`),
	   KEY `HOOKID` (`hook_id`),
	   KEY `PLGIN` (`id_plugin`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "playlists`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "playlists` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `source_play_id` varchar(500) DEFAULT NULL,
	  `source_play_url` varchar(500) DEFAULT NULL,
	  `title` varchar(500) DEFAULT NULL,
	  `descr` text DEFAULT NULL,
	  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
	   PRIMARY KEY (id),
	   KEY `SITE` (`id_site`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "links`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "links` (
	  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_post` int UNSIGNED NOT NULL DEFAULT 0,
	  `id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
	  `last_time_viewed` int UNSIGNED NOT NULL DEFAULT 0,
	  `short_link` varchar(100) DEFAULT NULL,
	  `title` varchar(255) DEFAULT NULL,
	  `url` varchar(350) DEFAULT NULL,
	  `descr` varchar(500) DEFAULT NULL,
	  `link_data` text DEFAULT NULL,
	  `trans_data` text DEFAULT NULL,
	  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
	  `num_views` mediumint UNSIGNED NOT NULL DEFAULT 0,
	   PRIMARY KEY (id),
	   KEY `SITE` (`id_site`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "link_categories`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "link_categories` (
			  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			  `id_category` mediumint UNSIGNED NOT NULL DEFAULT 0,
			  `id_parent` mediumint UNSIGNED NOT NULL DEFAULT 0,
			  `name` varchar(250) NOT NULL,
			  `descr` text DEFAULT NULL,
			  `num_items` int UNSIGNED NOT NULL DEFAULT 0,
			  `groups_data` text DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `SITE` (`id_site`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "link_checks`;";

	$dtb[] = "CREATE TABLE `" . $prefix . "link_checks` (
	  `id_check` int UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_post` int UNSIGNED NOT NULL DEFAULT 0,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_comment` int UNSIGNED NOT NULL DEFAULT 0,
	  `url` varchar(500) DEFAULT NULL,
	  `url_status` varchar(50) DEFAULT NULL,
	  `url_response_code` varchar(10) DEFAULT NULL,
	  `link_text` varchar(500) DEFAULT NULL,
	  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
	  `last_checked` int UNSIGNED NOT NULL DEFAULT 0,
	  `times_checked` smallint UNSIGNED NOT NULL DEFAULT 0,
	  `response_headers` text DEFAULT NULL,
	   PRIMARY KEY (`id_check`),
	   KEY `POST` (`id_post`),
	   KEY `COMM` (`id_comment`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "schemas`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "schemas` (
	  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `title` varchar(64) DEFAULT NULL,
	  `type` varchar(64) DEFAULT NULL,
	  `enable_on` varchar(64) DEFAULT NULL,
	  `enable_on_id` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `exclude_from` varchar(64) DEFAULT NULL,
	  `exclude_from_id` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `data` text NOT NULL,
	  `fixed_data` text,
	  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
	   PRIMARY KEY (id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "widgets`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "widgets` (
	  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_ad` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `title` varchar(64) DEFAULT NULL,
	  `type` varchar(64) DEFAULT NULL,
	  `theme` varchar(32) DEFAULT NULL,
	  `theme_pos` varchar(32) NOT NULL DEFAULT 'primary',
	  `enable_on` varchar(64) DEFAULT NULL,
	  `function_name` varchar(64) DEFAULT NULL,
	  `exclude_from` varchar(64) DEFAULT NULL,
	  `build_in` varchar(64) DEFAULT NULL,
	  `num` tinyint UNSIGNED NOT NULL DEFAULT 5,
	  `show_num_posts` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `show_dropdown_list` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `data` text NOT NULL,
	  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
	  `widget_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `disabled` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `groups_data` text DEFAULT NULL,
	   PRIMARY KEY (id),
	   KEY THMPOS (theme_pos),
	   KEY THEME (theme),
	   KEY SITE (id_site),
	   KEY LANG (id_lang)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "ads`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "ads` (
	  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `title` varchar(256) DEFAULT NULL,
	  `type` varchar(24) DEFAULT NULL,
	  `ad_pos` varchar(24) DEFAULT NULL,
	  `ad_align` varchar(24) DEFAULT NULL,
	  `ad_img_url` varchar(255) DEFAULT NULL,
	  `ad_code` text DEFAULT NULL,
	  `exclude_ads` text DEFAULT NULL,
	  `groups_data` text DEFAULT NULL,
	  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
	  `ad_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `width` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `height` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `disabled` tinyint UNSIGNED NOT NULL DEFAULT 0,
	   PRIMARY KEY (id),
	   KEY SITE (id_site),
	   KEY LANG (id_lang)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "social_media`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "social_media` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` varchar(64) DEFAULT NULL,
	  `link` varchar(255) DEFAULT NULL,
	  `alt` varchar(255) DEFAULT NULL,
	  `descr` varchar(500) DEFAULT NULL,
	  `icon` varchar(30) DEFAULT NULL,
	   PRIMARY KEY (id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "auto_sources`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "auto_sources` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
	  `last_checked` int UNSIGNED NOT NULL DEFAULT 0,
	  `last_posts_checked` int UNSIGNED NOT NULL DEFAULT 0,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `user_id` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `id_category` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `id_store mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `title` varchar(64) DEFAULT NULL,
	  `url` varchar(255) DEFAULT NULL,
	  `descr` varchar(500) DEFAULT NULL,
	  `avoid_words` varchar(500) DEFAULT NULL,
	  `required_words` varchar(500) DEFAULT NULL,
	  `banned_words` varchar(500) DEFAULT NULL,
	  `auto_category` varchar(15) DEFAULT NULL,
	  `copy_images` enum('true','false') NOT NULL DEFAULT 'false',
	  `set_first_image_cover` enum('true','false') NOT NULL DEFAULT 'false',
	  `strip_html` enum('true','false') NOT NULL DEFAULT 'false',
	  `remove_images` enum('true','false') NOT NULL DEFAULT 'false',
	  `strip_links` enum('true','false') NOT NULL DEFAULT 'false',
	  `set_original_date` enum('true','false') NOT NULL DEFAULT 'false',
	  `skip_posts_no_images` enum('true','false') NOT NULL DEFAULT 'false',
	  `set_source_link` enum('true','false') NOT NULL DEFAULT 'false',
	  `post_status` enum('published','draft') NOT NULL DEFAULT 'published',
	  `post_type` enum('post','page') NOT NULL DEFAULT 'post',
	  `auto_delete_days` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `max_posts` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `skip_posts_days` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `add_tags` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `post_template` varchar(500) DEFAULT NULL,
	  `source_type` enum('html','xml','rss','multi') NOT NULL DEFAULT 'rss',
	  `custom_data` text NOT NULL,
	  `xml_data` text NOT NULL,
	   PRIMARY KEY (id),
	   KEY SITE (id_site)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "translations`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "translations` (
	  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_lang` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `lang_key` varchar(10) DEFAULT NULL,
	  `lang_code` varchar(10) DEFAULT NULL,
	  `trans_type` varchar(50) DEFAULT NULL,
	  PRIMARY KEY (id),
	  KEY LANG (id_lang),
	  KEY LANGKEY (lang_key),
	  KEY SITE (id_site)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "translation_values`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "translation_values` (
	  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_trans` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `trans_key` varchar(100) DEFAULT NULL,
	  `post` text NOT NULL,
	  PRIMARY KEY (id),
	  KEY TRANS (id_trans)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "blogs`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "blogs` (
	  `id_blog` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `name` varchar(255) DEFAULT NULL,
	  `sef` varchar(100) DEFAULT NULL,
	  `description` text DEFAULT NULL,
	  `slogan` varchar(500) DEFAULT NULL,
	  `num_posts` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `article_limit` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `num_comments` mediumint UNSIGNED NOT NULL DEFAULT 0,
	  `theme` varchar(50) DEFAULT NULL,
	  `unapproved_posts` smallint NOT NULL DEFAULT 0,
	  `unapproved_comments` smallint NOT NULL DEFAULT 0,
	  `frontpage` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `frontpage_shows` enum('posts','page') NOT NULL DEFAULT 'posts',
	  `frontpage_page` int UNSIGNED NOT NULL DEFAULT 0,
	  `news_sitemap` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `enable_rss` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `disabled` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `dont_load_posts` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `hide_sitemap` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `type` varchar(30) NOT NULL DEFAULT 'normal',
	  `redirect` varchar(255) DEFAULT NULL,
	  `groups_data` text DEFAULT NULL,
	  `trans_data` text DEFAULT NULL,
	  `ext_comments` varchar(300) DEFAULT NULL,
	  `custom_home_tmp` varchar(50) DEFAULT NULL,
	  `custom_list_tmp` varchar(50) DEFAULT NULL,
	  `custom_post_tmp` varchar(50) DEFAULT NULL,
	  PRIMARY KEY (id_blog),
	  KEY SITE (id_site),
	  KEY BDIZ (disabled,id_blog),
	  KEY SEF (sef),
	  KEY LANG (id_lang)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "menus`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "menus` (
	  `id_menu` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `title` varchar(255) DEFAULT NULL,
	  `location` varchar(255) DEFAULT NULL,
	  `added_time` int UNSIGNED NOT NULL DEFAULT 0,
	  `edited_time` int UNSIGNED NOT NULL DEFAULT 0,
	  PRIMARY KEY (id_menu),
	  KEY SITE (id_site),
	  KEY LANG (id_lang)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "menu_items`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "menu_items` (
	  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_menu` smallint UNSIGNED NOT NULL DEFAULT 0,
	  `id_item` int UNSIGNED NOT NULL DEFAULT 0,
	  `id_parent` int UNSIGNED NOT NULL DEFAULT 0,
	  `name` varchar(255) DEFAULT NULL,
	  `label` varchar(255) DEFAULT NULL,
	  `type` varchar(50) DEFAULT NULL,
	  `url` varchar(500) DEFAULT NULL,
	  `new_tab` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `item_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  PRIMARY KEY (id),
	  KEY MENU (id_menu)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";


	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "config`;";

	$dtb[] = "CREATE TABLE `" . $prefix . "config` (
		`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
		`variable` varchar(255) DEFAULT NULL,
		`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`config_group` varchar(50) DEFAULT NULL,
		`value` mediumtext NOT NULL,
		PRIMARY KEY (id),
		KEY SITE (id_site),
		KEY CONFIG (config_group)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

	$dtb[] = "INSERT INTO `" . $prefix . "config` (`variable`, `id_site`, `config_group`, `value`) VALUES
		('website_email', 1, 'general', 'info@mydomain.com'),
		('contact_email', 1, 'general', ''),
		('parent_type', 1, 'general', 'normal'),
		('front_page', 1, 'general', 'latest-posts'),
		('front_static_page', 1, 'general', ''),
		('html_editor', 1, 'general', 'tinymce'),
		('editor_data', 1, 'general', '{\"toolbar\":\"formatselect bold italic forecolor backcolor removeformat | bullist numlist table | blockquote alignleft aligncenter alignright | link unlink pagebreak image code\",\"plugins\":\"code autolink image link pagebreak advlist lists textpattern table\",\"tab-size\":\"2\",\"auto-save\":\"60\"}'),
		('enable_archive_page', 1, 'general', 'false'),
		('enable_html5_video_player', 1, 'general', 'false'),
		('enable_cookie_concent', 1, 'general', 'false'),
		('posts_filter', 1, 'general', '/'),
		('categories_filter', 1, 'general', '/category/'),
		('tags_filter', 1, 'general', '/tag/'),
		('site_lang', 1,  'general', 'en'),
		('charset', 1,  'general', 'UTF-8'),
		('cron_hash', 1,  'general', '" . strtolower( GenerateRandomKey( 5 ) ) . "'),
		('article_limit', 1,  'general', '12'),
		('enable_rss', 1, 'general', 'true'),
		('disable_user_login', 1, 'general', 'false'),
		('disable_author_archives', 1, 'general', 'false'),
		('put_spam_in_spam_folder', 1, 'security', 'false'),
		('log_settings', 1, 'general', '{\"disable_javascript_call_scheduled_tasks\":true,\"enable_error_log\":true,\"include_database_query\":false,\"enable_redirection_log\":true,\"enable_moderation_log\":true,\"enable_administration_log\":true,\"enable_profile_edits_log\":true,\"enable_pruning\":false,\"automatically_mark_boards_read\":90,\"automatically_purge_board_information\":365,\"maximum_users_to_process\":200,\"remove_error_log_entries\":200,\"remove_moderation_log_entries\":200,\"remove_ban_hit_log_entries\":200,\"remove_scheduled_task_log_entries\":200,\"remove_redirection_log_entries\":200}'),
		('show_admin_bar', 1, 'general', 'true'),
		('admin_dashboard_data', 1, 'general', '{}'),
		('enable_instantpage', 1, 'performance', 'false'),
		('instantpage_settings', 1, 'performance', '{}'),
		('enable_preloading', 1, 'performance', 'false'),
		('enable_honeypot', 1, 'security', 'true'),
		('num_login_retries', 1, 'security', '3'),
		('num_login_lockout_time', 1, 'security', '30'),
		('enable_registrations', 1, 'general', 'true'),
		('registration_method', 1, 'general', 'email'),
		('send_welcome_email_new_reg', 1, 'general', 'true'),
		('require_accept_reg_agreement', 1, 'general', 'true'),
		('require_accept_privacy_policy', 1, 'general', 'false'),
		('enable_media_embedder', 1, 'general', 'true'),
		('enable_social_auto_publish', 1, 'general', 'false'),
		('enable_auto_menu', 1, 'general', 'true'),
		('share_images_langs', 1, 'general', 'true'),
		('share_images_sites', 1, 'general', 'false'),
		('share_tags_langs', 1, 'general', 'false'),
		('parent_site_shows_everything', 1, 'general', 'false'),
		('add_allow_origin_tag', 1, 'general', 'false'),
		('enable_post_attributes', 1, 'general', 'false'),
		('enable_new_content_time_limit', 1, 'general', 'false'),
		('enable_stats', 1, 'general', 'true'),
		('stats_data', 1, 'general', ''),
		('enable_debug_mode', 1, 'general', 'false'),
		('hide_captcha_logged_in_users', 1, 'security', 'true'),
		('show_captcha_in_forms', 1, 'security', 'everywhere'),
		('notify_the_user_about_remaining_retries', 1, 'security', 'true'),
		('notify_the_user_failed_login', 1, 'security', 'false'),
		('recaptcha_site_key', 1, 'security', ''),
		('recaptcha_secret_key', 1, 'security', ''),
		('search_engine_disallow', 1, 'general', 'false'),
		('language_set_from_code', 1, 'general', 'true'),
		('detect_browser_language', 1, 'general', 'false'),
		('load_jquery_cdn', 1, 'general', 'false'),
		('translate_slugs', 1, 'general', 'false'),
		('hide_default_lang_slug', 1, 'general', 'true'),
		('share_slugs', 1, 'general', 'false'),
		('enable_galleries', 1, 'general', 'false'),
		('enable_forms', 1, 'general', 'false'),
		('enable_sitemap', 1, 'seo', 'true'),
		('notify_search_engines', 1, 'seo', 'true'),
		('enable_news_sitemap', 1, 'seo', 'false'),
		('display_pagination_home', 1, 'general', 'false'),
		('mail_on_comments', 1,  'general', 'false'),
		('comment_repost_timer', 1,  'general', '0'),
		('allow_full_search', 1,  'general', 'false'),
		('blank_icon', 1, 'general', 'false'),
		('enable_comments', 1, 'general', 'true'),
		('comments_data', 1, 'general', '{\"hide_comments\":false,\"allow\":[\"posts\"],\"comments_limit\":0,\"user_only\":\"false\",\"sort_by\":\"older-first\"}'),
		('enable_lazyloader', 1, 'general', 'false'),
		('add_alt_to_images', 1, 'seo', 'true'),
		('allowed_extensions', 1, 'general', 'gif,jpg,jpeg,png'),
		('timezone_set', 1, 'general', 'UTC'),
		('site_image', 1, 'general', ''),
		('privacy_settings', 1, 'general', ''),
		('legal_pages', 1, 'general', ''),
		('contact_page', 1, 'general', ''),
		('theme', 1, 'general', 'clean-blog'),
		('amp_theme', 1, 'general', 'amp'),
		('site_default_image', 1, 'general', ''),
		('site_icon', 1,  'general',''),
		('enable_cache', 1, 'general', 'true'),
		('allow_favorite_posts', 1, 'general', 'false'),
		('allow_favorite_posts_in', 1, 'general', 'everywhere'),
		('allow_favorite_posts_group', 1, 'general', ''),
		('allow_post_notifications', 1, 'general', 'false'),
		('show_subscribers_num', 1, 'general', 'false'),
		('allow_post_notifications_in', 1, 'general', 'everywhere'),
		('allow_notifications_group', 1, 'general', ''),
		('enable_reviews', 1, 'general', 'false'),
		('reviews_allowed_in', 1, 'general', 'everywhere'),
		('allow_reviews_group', 1, 'general', ''),
		('cache_all_visitors', 1, 'general', 'false'),
		('cache_type', 1, 'general', 'normal'),
		('cache_time', 1, 'general', '86400'),
		('gmt_offset', 1, 'general', '0'),
		('force_https', 1, 'general', 'false'),
		('redirect_www', 1, 'general', 'false'),
		('enable_recaptcha', 1, 'security', 'false'),
		('enable_amp', 1, 'general', 'false'),
		('enable_seo', 1, 'general', 'false'),
		('enable_store', 1, 'general', 'false'),
		('enable_forum', 1, 'general', 'false'),
		('enable_autoblog', 1, 'general', 'false'),
		('enable_redirect', 1, 'general', 'false'),
		('enable_marketplace', 1, 'general', 'false'),
		('enable_price_comparison', 1, 'general', 'false'),
		('enable_deals', 1, 'general', 'false'),
		('enable_api', 1, 'general', 'false'),
		('enable_auto_translate', 1, 'tools', 'false'),
		('auto_translate_settings', 1, 'tools', ''),
		('enable_ads', 1, 'general', 'false'),
		('enable_link_manager', 1, 'general', 'false'),
		('link_manager_options', 1, 'general', ''),
		('footer_code', 1, 'general', ''),
		('header_code', 1, 'general', ''),
		('images_root', 1, 'general', ''),
		('images_html', 1, 'general', ''),
		('images_stores_html', 1, 'store', ''),
		('images_stores_root', 1, 'store', ''),
		('default_image', 1, 'general', ''),
		('referrer_policy', 1, 'security', 'false'),
		('enable_robots_txt', 1, 'seo', 'false'),
		('extra_blogs', 1, 'general', ''),
		('child_settings', 1, 'general', ''),
		('video_settings', 1, 'general', ''),
		('rss_settings', 1, 'general', '{\"lang-1\":{\"blog-0\":{\"data\":{\"post_limit\":\"5\",\"header_code\":\"\",\"footer_code\":\"This post {{post-link}} was written for {{site-link}}\"}}}}'),
		('themes_data', 1, 'general', '{}'),
		('plugins_data', 1, 'general', '{}'),
		('robots_txt', 1, 'seo', '{}'),
		('robots_data', 1, 'seo', '{}'),
		('auto_content_data', 1, 'general', '{}'),
		('redirection_data', 1, 'seo', '{}'),
		('news_sitemap_data', 1, 'seo', '{}'),
		('embedder_data', 1, 'general', '{}'),
		('auto_menu', 1, 'general', '{}'),
		('api_keys', 1, 'general', '{}'),
		('trans_data', 1, 'general', '{}'),
		('cdn_data', 1, 'general', '{}'),
		('cron_data', 1,  'general', '{}'),
		('drafts_data', 1,  'general', '{}'),
		('ads_data', 1,  'general', '{}'),
		('auto_social_data', 1,  'general', '{}'),
		('seo_data', 1, 'seo', '{\"title_seperator\":\"vertical-bar\",\"homepage_title_format\":\"{{site-slogan}} {{sep}} {{site-title}} \",\"pages_title_format\":\"{{post-title}} {{sep}} {{site-title}} \",\"blogs_title_format\":\"{{blog-name}} {{sep}} {{site-title}} \",\"posts_title_format\":\"{{post-title}} {{sep}} {{site-title}} \",\"categories_title_format\":\"{{category-title}} Archive {{sep}} {{site-title}} \",\"authors_title_format\":\"{{author-name}} Archive {{sep}} {{site-title}}\",\"tags_title_format\":\"{{tag-title}} Archive {{sep}} {{site-title}} \",\"search_title_format\":\"You searched for: {{search-term}} {{sep}} {{site-title}} \",\"threads_title_format\":\"\",\"products_title_format\":\"\",\"show_pages_search\":\"true\",\"show_posts_search\":\"true\",\"show_tags_search\":\"false\",\"show_categories_search\":\"false\",\"show_products_search\":\"false\",\"show_threads_search\":\"false\",\"show_authors_search\":\"false\",\"google_site_verification\":\"\",\"msvalidate\":\"\",\"yandex_verification\":\"\",\"tracking-codes\":{\"google_analytics_four\":\"\",\"google_analytics_ua\":\"\",\"facebook_pixel_id\":\"\",\"google_tag_manager_id\":\"\"}}'),
		('video_data', 1, 'seo', '{}'),
		('amp_data', 1, 'seo', '{}'),
		('sitemap_data', 1, 'seo', '{}'),
		('schema_data', 1, 'seo', '{}')
	;";

	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "languages`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "languages` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `code` char(4) NOT NULL,
	  `title` varchar(100) NOT NULL,
	  `locale` varchar(30) NOT NULL,
	  `direction` varchar(5) NOT NULL DEFAULT 'ltr',
	  `is_default` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
	  `flagicon` tinytext NOT NULL,
	  `lang_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  PRIMARY KEY (id),
	  KEY CODE (code),
	  KEY SITE (id_site)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

	$dtb[] = "INSERT INTO `" . $prefix . "languages` (`id`, `id_site`, `code`, `title`, `locale`, `direction`, `is_default`, `status`, `flagicon`, `lang_order`) VALUES (1, 1, '" . $langData['code'] . "', '" . $langData['name'] . "', '" . $langData['locale'] . "', 'ltr', 1, 'active', '" . $langData['icon'] . "', 1);";

	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "languages_config`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "languages_config` (
		  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
		  `id_lang` tinyint UNSIGNED NOT NULL DEFAULT 0,
		  `date_format` varchar(15) NOT NULL,
		  `time_format` varchar(15) NOT NULL,
		  `site_name` varchar(80) DEFAULT NULL,
		  `site_description` varchar(400) DEFAULT NULL,
		  `site_slogan` varchar(255) DEFAULT NULL,
		  `footer_text` varchar(400) DEFAULT NULL,
		  `after_content_text` varchar(500) DEFAULT NULL,
		  `ext_comm_system` varchar(80) DEFAULT NULL,
		  `ext_comm_shortname` varchar(80) DEFAULT NULL,
		  `cookie_data` text DEFAULT NULL,
		  `not_found_data` text DEFAULT NULL,
		  `maintance_mode_data` text DEFAULT NULL,
		  `social` text DEFAULT NULL,
		  PRIMARY KEY (id),
		  KEY LANG (id_lang)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

	$dtb[] = "INSERT INTO `" . $prefix . "languages_config` (`id_lang`, `site_name`, `site_slogan`, `site_description`, `date_format`, `time_format`, `footer_text`) VALUES (1, '" . $siteName . "', 'I Blog, Therefore I Am', 'New TokiCMS installation', 'F j, Y', 'h:i:s', 'Copyright {{copyright}} {{current-year}} <a href=\"{{site-url}}\">{{site-title}}</a> ~ {{powered-by-toki-cms}}');";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "membergroups`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "membergroups` (
	  `id_group` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `group_name` varchar(80) DEFAULT NULL,
	  `slug` varchar(80) DEFAULT NULL,
	  `description` text DEFAULT NULL,
	  `min_posts` mediumint NOT NULL DEFAULT -1,
	  `max_messages` smallint UNSIGNED NOT NULL DEFAULT 0,
	  `stars` varchar(255) DEFAULT NULL,
	  `hidden` tinyint UNSIGNED NOT NULL DEFAULT 0,
	  `group_type` enum('system','custom') NOT NULL DEFAULT 'custom',
	  `group_color` varchar(15) DEFAULT NULL,
	  PRIMARY KEY (id_group),
	  KEY MINPOSTS (min_posts)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	$dtb[] = "INSERT INTO " . $prefix . "membergroups (`id_group`, `group_name`, `slug`, `min_posts`, `max_messages`, `group_type`) VALUES
	(1, 'Administrator', 'administrator', -1, 0, 'system'),
	(2, 'Editor', 'editor', -1, 0, 'system'),
	(3, 'Author', 'author', -1, 0, 'system'),
	(4, 'Member', 'member', -1, 0, 'system'),
	(5, 'Guests', 'guests', -1, 0, 'system'),
	(6, 'Search Engines', 'search-engines', -1, 0, 'system');";

	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "membergroup_relation`;";

	$dtb[] = "CREATE TABLE `" . $prefix . "membergroup_relation` (
			  `id_relation` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `id_group` int UNSIGNED NOT NULL DEFAULT 0,
			  `id_site` int UNSIGNED NOT NULL DEFAULT 0,
			  `group_permissions` text DEFAULT NULL,
			  `time_permissions` text DEFAULT NULL,
			   PRIMARY KEY (`id_relation`),
			   KEY SITE (id_site),
			   KEY GROUPID (id_group)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

	$dtb[] = "INSERT INTO " . $prefix . "membergroup_relation (`id_relation`, `id_group`, `id_site`, `group_permissions`) VALUES
	(1, 1, 1, 'all'),
	(2, 2, 1, '[\"create-new-posts\",\"lock-posts\",\"read-comments\",\"post-comments\",\"auto-publish-comments\",\"search-posts\",\"view-site\",\"view-posts\",\"view-dashboard\",\"view-admin-bar\"]'),
	(3, 3, 1, '[\"create-new-posts\",\"manage-own-posts\",\"manage-own-comments\",\"manage-own-posts-comments\",\"post-comments\",\"auto-publish-comments\",\"search-posts\",\"view-site\",\"view-posts\",\"view-dashboard\",\"view-admin-bar\"]'),
	(4, 4, 1, '[\"create-new-posts\",\"manage-own-comments\",\"post-comments\",\"auto-publish-comments\",\"search-posts\",\"view-site\",\"view-posts\"]'),
	(5, 5, 1, '[\"post-comments\",\"search-posts\",\"view-site\",\"view-posts\"]'),
	(6, 6, 1, '[\"view-site\",\"view-lighter-version\",\"view-posts\"]');";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "stats`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "stats` (
		`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`remote_ip` varchar(39) DEFAULT NULL,
		`country` char(2) DEFAULT NULL,
		`language` varchar(255) DEFAULT NULL,
		`domain` varchar(255) DEFAULT NULL,
		`referrer` varchar(512) DEFAULT NULL,
		`search_terms` varchar(255) DEFAULT NULL,
		`user_agent` varchar(255) DEFAULT NULL,
		`platform` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`browser` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`version` varchar(15) DEFAULT NULL,
		`date` date NOT NULL,
		`start_time` time NOT NULL,
		`end_time` time NOT NULL,
		`resource` text DEFAULT NULL,
		`offset` smallint UNSIGNED NOT NULL DEFAULT 0,
		`hits` int UNSIGNED NOT NULL DEFAULT 0,
		PRIMARY KEY (id),
		KEY SITE (id_site),
		KEY DATE (date),
		KEY UA (browser,platform),
		KEY COUNTRY (country)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "stats_archive`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "stats_archive` (
		`yr` smallint UNSIGNED NOT NULL,
		`mo` tinyint UNSIGNED NOT NULL,
		`data` longblob NOT NULL,
		UNIQUE KEY (yr,mo)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "stats_activity`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "stats_activity` (
		`date` date NOT NULL,
		`hits` mediumint UNSIGNED NOT NULL DEFAULT '0',
		`topics` smallint UNSIGNED NOT NULL DEFAULT '0',
		`posts` smallint UNSIGNED NOT NULL DEFAULT '0',
		`registers` smallint UNSIGNED NOT NULL DEFAULT '0',
		`most_on` smallint UNSIGNED NOT NULL DEFAULT '0',
		PRIMARY KEY (date)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "logs`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "logs` (
			`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`user_id` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`title` varchar(300) NOT NULL,
			`descr` text DEFAULT NULL,
			`added_time` int UNSIGNED NOT NULL DEFAULT 0,
			`ip` varchar(100) DEFAULT NULL,
			`type` enum('notfound','redirect','system','user','bot') NOT NULL DEFAULT 'system',
			PRIMARY KEY (`id`),
			KEY SITE (id_site)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "log_boards`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "log_boards` (
			`id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`id_post` int UNSIGNED NOT NULL DEFAULT 0,
			`id_msg` int UNSIGNED NOT NULL DEFAULT 0,
			`added_time` int UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (`id_member`,`id_post`,`id_site`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "log_mark_read`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "log_mark_read` (
			`id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`id_post` int UNSIGNED NOT NULL DEFAULT 0,
			`id_msg` int UNSIGNED NOT NULL DEFAULT 0,
			`added_time` int UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (`id_member`,`id_post`,`id_site`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "log_posts`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "log_posts` (
			`id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`id_post` int UNSIGNED NOT NULL DEFAULT 0,
			`added_time` int UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (`id_member`,`id_post`,`id_site`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "log_comments`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "log_comments` (
			`id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`id_comment` int UNSIGNED NOT NULL DEFAULT 0,
			`added_time` int UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (`id_member`,`id_comment`,`id_site`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "log_log`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "log_log` (
			`id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`id_log` int UNSIGNED NOT NULL DEFAULT 0,
			`added_time` int UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (`id_member`,`id_log`,`id_site`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "log_emails`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "log_emails` (
			`id_log` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_member` mediumint UNSIGNED NOT NULL DEFAULT 0,
			`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
			`id_mail` int UNSIGNED NOT NULL DEFAULT 0,
			`added_time` int UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (`id_member`,`id_mail`,`id_site`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
		
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "log_scheduled_tasks`;";
	
	$dtb[] = "CREATE TABLE `" . $prefix . "log_scheduled_tasks` (
		`id_log` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`id_task` smallint NOT NULL DEFAULT '0',
		`time_run` int NOT NULL DEFAULT '0',
		`time_taken` float NOT NULL DEFAULT '0',
		`added_time` int UNSIGNED NOT NULL DEFAULT 0,
		PRIMARY KEY (id_log),
		KEY SITE (id_site)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "scheduled_tasks`;";	
		
	$dtb[] = "CREATE TABLE `" . $prefix . "scheduled_tasks` (
		`id_task` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_site` tinyint UNSIGNED NOT NULL DEFAULT 0,
		`next_time` int NOT NULL DEFAULT '0',
		`time_offset` int NOT NULL DEFAULT '0',
		`times_run` smallint NOT NULL DEFAULT '0',
		`time_regularity` smallint NOT NULL DEFAULT '0',
		`time_unit` varchar(1) NOT NULL DEFAULT 'h',
		`disabled` tinyint NOT NULL DEFAULT '0',
		`task` varchar(24) NOT NULL DEFAULT '',
		PRIMARY KEY (id_task),
		KEY SITE (id_site)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	$dtb[] = "INSERT INTO `" . $prefix . "scheduled_tasks` (`id_task`, `id_site`, `next_time`, `time_offset`, `time_regularity`, `time_unit`, `disabled`, `task` ) VALUES
	(1, 1, 0, 60, 1, 'd', 0, 'daily-maintenance' ),
	(2, 1, 0, 60, 1, 'd', 0, 'daily-digest' ),
	(3, 1, 0, 0, 1, 'w', 0, 'weekly-digest' ),
	(4, 1, 0, 0, 1, 'w', 0, 'weekly-maintenance' ),
	(5, 1, 0, 0, 1, 'w', 1, 'prune-log-topics' ),
	(6, 1, 0, 0, 1, 'w', 1, 'backup-db' ),
	(7, 1, 0, 60, 1, 'h', 1, 'bot-digest' ),
	(8, 1, 0, 0, 1, 'w', 1, 'mark-boards-as-read' ),
	(9, 1, 0, 5, 1, 'd', 1, 'broken-link-check' );";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "payment_methods`;";
	
    $dtb[] = "CREATE TABLE `" . $prefix . "payment_methods` (
			  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `title` varchar(100) NOT NULL,
			  `sef` varchar(50) NOT NULL,
			  `num_items` int UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
			
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "payment_relationships`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "payment_relationships` (
			  `id_relation` int UNSIGNED NOT NULL AUTO_INCREMENT,
			  `object_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `payment_id` int UNSIGNED NOT NULL DEFAULT 0,
			  `id_store` int UNSIGNED NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id_relation`),
			  KEY `OBJECT` (`object_id`),
			  KEY `STOREID` (`id_store`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	if ( FORCEINSTALL )
		$dtb[] = "DROP TABLE IF EXISTS `" . $prefix . "currencies`;";
		
	$dtb[] = "CREATE TABLE `" . $prefix . "currencies` (
	  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	  `name` varchar(50) DEFAULT NULL,
	  `code` varchar(20) DEFAULT NULL,
	  `symbol` varchar(15) DEFAULT NULL,
	  `format` varchar(25) DEFAULT NULL,
	  `exchange_rate` float NOT NULL DEFAULT 0,
	  PRIMARY KEY (id),
	  KEY CODE (code)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
	
	$dtb[] = "INSERT INTO `" . $prefix . "currencies` (`id`, `name`, `code`, `symbol`, `format`, `exchange_rate`) VALUES
	(1, 'UAE Dirham', 'AED', 'دإ‏', 'دإ‏ 1,0.00', 0),
	(2, 'Afghanistan, Afghani', 'AFN', '؋', '؋1,0.00', 0),
	(3, 'Albania, Lek', 'ALL', 'Lek', '1,0.00Lek', 0),
	(4, 'Armenian Dram', 'AMD', '&#142', '1,0.00 &#1423;', 0),
	(5, 'Netherlands Antillian Guilder', 'ANG', 'ƒ', 'ƒ1,0.00', 0),
	(6, 'Angola, Kwanza', 'AOA', 'Kz', 'Kz1,0.00', 0),
	(7, 'Argentine Peso', 'ARS', '$', '$ 1,0.00', 0),
	(8, 'Australian Dollar', 'AUD', '$', '$1,0.00', 0),
	(9, 'Aruban Guilder', 'AWG', 'ƒ', 'ƒ1,0.00', 0),
	(10, 'Azerbaijanian Manat', 'AZN', 'ман', '1 0,00 ман', 0),
	(11, 'Bosnia and Herzegovina, Convertible Marks', 'BAM', 'КМ', '1,0.00 КМ', 0),
	(12, 'Barbados Dollar', 'BBD', '$', '$1,0.00', 0),
	(13, 'Bangladesh, Taka', 'BDT', '৳', '৳ 1,0.', 0),
	(14, 'Bulgarian Lev', 'BGN', 'лв.', '1 0,00 лв.', 0),
	(15, 'Bahraini Dinar', 'BHD', '.د.', '.د. 1,0.000', 0),
	(16, 'Burundi Franc', 'BIF', 'FBu', '1,0.FBu', 0),
	(17, 'Bermudian Dollar', 'BMD', '$', '$1,0.00', 0),
	(18, 'Brunei Dollar', 'BND', '$', '$1,0.', 0),
	(19, 'Bolivia, Boliviano', 'BOB', 'Bs', 'Bs 1,0.00', 0),
	(20, 'Brazilian Real', 'BRL', 'R$', 'R$ 1,0.00', 0),
	(21, 'Bahamian Dollar', 'BSD', '$', '$1,0.00', 0),
	(22, 'Bhutan, Ngultrum', 'BTN', 'Nu.', 'Nu. 1,0.0', 0),
	(23, 'Botswana, Pula', 'BWP', 'P', 'P1,0.00', 0),
	(24, 'Belarussian Ruble', 'BYN', 'р.', '1 0,00 р.', 0),
	(25, 'Belize Dollar', 'BZD', 'BZ$', 'BZ$1,0.00', 0),
	(26, 'Canadian Dollar', 'CAD', '$', '$1,0.00', 0),
	(27, 'Franc Congolais', 'CDF', 'FC', '1,0.00FC', 0),
	(28, 'Chilean Peso', 'CLP', '$', '$ 1,0.00', 0),
	(29, 'China Yuan Renminbi', 'CNY', '¥', '¥1,0.00', 0),
	(30, 'Colombian Peso', 'COP', '$', '$ 1,0.00', 0),
	(31, 'Costa Rican Colon', 'CRC', '₡', '₡1,0.00', 0),
	(32, 'Cuban Convertible Peso', 'CUC', 'CUC', 'CUC1,0.00', 0),
	(33, 'Cuban Peso', 'CUP', '\$MN', '\$MN1,0.00', 0),
	(34, 'Cape Verde Escudo', 'CVE', '\$', '\$1,0.00', 0),
	(35, 'Czech Koruna', 'CZK', 'Kč', '1 0,00 Kč', 0),
	(36, 'Djibouti Franc', 'DJF', 'Fdj', '1,0.Fdj', 0),
	(37, 'Danish Krone', 'DKK', 'kr.', '1 0,00 kr.', 0),
	(38, 'Dominican Peso', 'DOP', 'RD$', 'RD$1,0.00', 0),
	(39, 'Algerian Dinar', 'DZD', 'د.ج‏', 'د.ج‏ 1,0.00', 0),
	(40, 'Egyptian Pound', 'EGP', 'ج.م', 'ج.م 1,0.00', 0),
	(41, 'Eritrea, Nakfa', 'ERN', 'Nfk', '1,0.00Nfk', 0),
	(42, 'Ethiopian Birr', 'ETB', 'ETB', 'ETB1,0.00', 0),
	(43, 'Euro', 'EUR', '€', '1.0,00 €', 0),
	(44, 'Fiji Dollar', 'FJD', '$', '$1,0.00', 0),
	(45, 'Falkland Islands Pound', 'FKP', '£', '£1,0.00', 0),
	(46, 'Pound Sterling', 'GBP', '£', '£1,0.00', 0),
	(47, 'Georgia, Lari', 'GEL', 'Lari', '1 0,00 Lari', 0),
	(48, 'Ghana Cedi', 'GHS', '₵', '₵1,0.00', 0),
	(49, 'Gibraltar Pound', 'GIP', '£', '£1,0.00', 0),
	(50, 'Gambia, Dalasi', 'GMD', 'D', '1,0.00D', 0),
	(51, 'Guinean franc', 'GNF', 'FG', '1,0.00FG', 0),
	(52, 'Guatemala, Quetzal', 'GTQ', 'Q', 'Q1,0.00', 0),
	(53, 'Guyana Dollar', 'GYD', '$', '$1,0.00', 0),
	(54, 'Hong Kong Dollar', 'HKD', 'HK$', 'HK$1,0.00', 0),
	(55, 'Honduras, Lempira', 'HNL', 'L.', 'L. 1,0.00', 0),
	(56, 'Croatian Kuna', 'HRK', 'kn', '1,0.00 kn', 0),
	(57, 'Haiti, Gourde', 'HTG', 'G', 'G1,0.00', 0),
	(58, 'Hungary, Forint', 'HUF', 'Ft', '1 0,00 Ft', 0),
	(59, 'Indonesia, Rupiah', 'IDR', 'Rp', 'Rp1,0.', 0),
	(60, 'New Israeli Shekel', 'ILS', '₪', '₪ 1,0.00', 0),
	(61, 'Indian Rupee', 'INR', '₹', '1,0.00₹', 0),
	(62, 'Iraqi Dinar', 'IQD', 'د.ع.‏', 'د.ع.‏ 1,0.00', 0),
	(63, 'Iranian Rial', 'IRR', 'Rl', 'Rl 1,0/00', 0),
	(64, 'Iceland Krona', 'ISK', 'kr.', '1,0. kr.', 0),
	(65, 'Jamaican Dollar', 'JMD', 'J$', 'J$1,0.00', 0),
	(66, 'Jordanian Dinar', 'JOD', 'د.ا.‏', 'د.ا.‏ 1,0.000', 0),
	(67, 'Japan, Yen', 'JPY', '¥', '¥1,0.', 0),
	(68, 'Kenyan Shilling', 'KES', 'S', 'S1,0.00', 0),
	(69, 'Kyrgyzstan, Som', 'KGS', 'сом', '1 0-00 сом', 0),
	(70, 'Cambodia, Riel', 'KHR', '៛', '1,0.៛', 0),
	(71, 'Comoro Franc', 'KMF', 'CF', '1,0.00CF', 0),
	(72, 'North Korean Won', 'KPW', '₩', '₩1,0.', 0),
	(73, 'South Korea, Won', 'KRW', '₩', '₩1,0.', 0),
	(74, 'Kuwaiti Dinar', 'KWD', 'دينار', 'دينار‎‎‏ 1,0.00', 0),
	(75, 'Cayman Islands Dollar', 'KYD', '$', '$1,0.00', 0),
	(76, 'Kazakhstan, Tenge', 'KZT', '₸', '₸1 0-00', 0),
	(77, 'Laos, Kip', 'LAK', '₭', '1,0.₭', 0),
	(78, 'Lebanese Pound', 'LBP', 'ل.ل.‏', 'ل.ل.‏ 1,0.00', 0),
	(79, 'Sri Lanka Rupee', 'LKR', '₨', '₨ 1,0.', 0),
	(80, 'Liberian Dollar', 'LRD', '$', '$1,0.00', 0),
	(81, 'Lesotho, Loti', 'LSL', 'M', '1,0.00M', 0),
	(82, 'Libyan Dinar', 'LYD', 'د.ل.‏', 'د.ل.‏1,0.000', 0),
	(83, 'Moroccan Dirham', 'MAD', 'د.م.‏', 'د.م.‏ 1,0.00', 0),
	(84, 'Moldovan Leu', 'MDL', 'lei', '1,0.00 lei', 0),
	(85, 'Malagasy Ariary', 'MGA', 'Ar', 'Ar1,0.', 0),
	(86, 'Macedonia, Denar', 'MKD', 'ден.', '1,0.00 ден.', 0),
	(87, 'Myanmar, Kyat', 'MMK', 'K', 'K1,0.00', 0),
	(88, 'Mongolia, Tugrik', 'MNT', '₮', '₮1 0,00', 0),
	(89, 'Macao, Pataca', 'MOP', 'MOP$', 'MOP$1,0.00', 0),
	(90, 'Mauritania, Ouguiya', 'MRU', 'UM', '1,0.00UM', 0),
	(91, 'Maltese Lira', 'MTL', '₤', '₤1,0.00', 0),
	(92, 'Mauritius Rupee', 'MUR', '₨', '₨1,0.00', 0),
	(93, 'Maldives, Rufiyaa', 'MVR', 'MVR', '1,0.0 MVR', 0),
	(94, 'Malawi, Kwacha', 'MWK', 'MK', 'MK1,0.00', 0),
	(95, 'Mexican Peso', 'MXN', '$', '$1,0.00', 0),
	(96, 'Malaysian Ringgit', 'MYR', 'RM', 'RM1,0.00', 0),
	(97, 'Mozambique Metical', 'MZN', 'MT', 'MT1,0.', 0),
	(98, 'Namibian Dollar', 'NAD', '$', '$1,0.00', 0),
	(99, 'Nigeria, Naira', 'NGN', '₦', '₦1,0.00', 0),
	(100, 'Nicaragua, Cordoba Oro', 'NIO', 'C$', 'C$ 1,0.00', 0),
	(101, 'Norwegian Krone', 'NOK', 'kr', '1.0,00 kr', 0),
	(102, 'Nepalese Rupee', 'NPR', '₨', '₨1,0.00', 0),
	(103, 'New Zealand Dollar', 'NZD', '$', '$1,0.00', 0),
	(104, 'Rial Omani', 'OMR', '&#65020;', '&#65020; 1,0.000', 0),
	(105, 'Panama, Balboa', 'PAB', 'B/.', 'B/. 1,0.00', 0),
	(106, 'Peru, Nuevo Sol', 'PEN', 'S/.', 'S/. 1,0.00', 0),
	(107, 'Papua New Guinea, Kina', 'PGK', 'K', 'K1,0.00', 0),
	(108, 'Philippine Peso', 'PHP', '₱', '₱1,0.00', 0),
	(109, 'Pakistan Rupee', 'PKR', '₨', '₨1,0.00', 0),
	(110, 'Poland, Zloty', 'PLN', 'zł', '1 0,00 zł', 0),
	(111, 'Paraguay, Guarani', 'PYG', '₲', '₲ 1,0.00', 0),
	(112, 'Qatari Rial', 'QAR', 'QR ', 'QR  1,0.00', 0),
	(113, 'Romania, New Leu', 'RON', 'lei', '1,0.00 lei', 0),
	(114, 'Serbian Dinar', 'RSD', 'Дин.', '1,0.00 Дин.', 0),
	(115, 'Russian Ruble', 'RUB', '₽', '1 0,00 ₽', 0),
	(116, 'Rwanda Franc', 'RWF', 'RWF', 'RWF 1 0,00', 0),
	(117, 'Saudi Riyal', 'SAR', 'SR', 'SR 1,0.00', 0),
	(118, 'Solomon Islands Dollar', 'SBD', '$', '$1,0.00', 0),
	(119, 'Seychelles Rupee', 'SCR', '₨', '₨1,0.00', 0),
	(120, 'Sudanese Pound', 'SDG', 'ج.س', '1,0.00 Sd', 0),
	(121, 'Swedish Krona', 'SEK', 'kr', '1 0,00 kr', 0),
	(122, 'Singapore Dollar', 'SGD', '$', '$1,0.00', 0),
	(123, 'Saint Helena Pound', 'SHP', '£', '£1,0.00', 0),
	(124, 'Sierra Leone, Leone', 'SLL', 'Le', 'Le1,0.00', 0),
	(125, 'Somali Shilling', 'SOS', 'S', 'S1,0.00', 0),
	(126, 'Surinam Dollar', 'SRD', '$', '$1,0.00', 0),
	(127, 'South Sudanese pound', 'SSP', 'SS£', 'SS £1,0.00', 0),
	(128, 'Sao Tome and Principe, Dobra', 'STN', 'Db', 'Db1,0.00', 0),
	(129, 'El Salvador Colon', 'SVC', '₡', '₡1,0.00', 0),
	(130, 'Syrian Pound', 'SYP', '£', '£ 1,0.00', 0),
	(131, 'Swaziland, Lilangeni', 'SZL', 'E', 'E1,0.00', 0),
	(132, 'Thailand, Baht', 'THB', '฿', '฿1,0.00', 0),
	(133, 'Tajikistan, Somoni', 'TJS', 'TJS', '1 0;00 TJS', 0),
	(134, 'Turkmenistani New Manat', 'TMT', 'm', '1 0,m', 0),
	(135, 'Tunisian Dinar', 'TND', 'د.ت.‏', 'د.ت.‏ 1,0.000', 0),
	(136, 'Tonga, Paanga', 'TOP', 'T$', 'T$1,0.00', 0),
	(137, 'Turkish Lira', 'TRY', 'TL', '₺1,0.00', 0),
	(138, 'Trinidad and Tobago Dollar', 'TTD', 'TT$', 'TT$1,0.00', 0),
	(139, 'New Taiwan Dollar', 'TWD', 'NT$', 'NT$1,0.00', 0),
	(140, 'Tanzanian Shilling', 'TZS', 'TSh', 'TSh1,0.00', 0),
	(141, 'Ukraine, Hryvnia', 'UAH', '₴', '1 0,00₴', 0),
	(142, 'Uganda Shilling', 'UGX', 'USh', 'USh1,0.00', 0),
	(143, 'US Dollar', 'USD', '\$', '\$1,0.00', 0),
	(144, 'Peso Uruguayo', 'UYU', '\$U', '\$U 1,0.00', 0),
	(145, 'Uzbekistan Sum', 'UZS', 'сўм', '1 0,00 сўм', 0),
	(146, 'Venezuela Bolivares soberano', 'VES', 'Bs. S', 'Bs. S. 1,0.00', 0),
	(147, 'Viet Nam, Dong', 'VND', '₫', '1,0.0 ₫', 0),
	(148, 'Vanuatu, Vatu', 'VUV', 'VT', '1,0.VT', 0),
	(149, 'Samoa, Tala', 'WST', 'WS$', 'WS$1,0.00', 0),
	(150, 'Franc CFA (XAF)', 'XAF', 'F.CFA', '1,0.00 F.CFA', 0),
	(151, 'East Caribbean Dollar', 'XCD', '$', '$1,0.00', 0),
	(152, 'Franc CFA (XOF)', 'XOF', 'F.CFA', '1,0.00 F.CFA', 0),
	(153, 'CFP Franc', 'XPF', 'F', '1,0.00F', 0),
	(154, 'Yemeni Rial', 'YER', '&#65020;', '&#65020; 1,0.00', 0),
	(155, 'South Africa, Rand', 'ZAR', 'R', 'R 1,0.00', 0),
	(156, 'Zambia Kwacha', 'ZMW', 'ZK', 'ZK1,0.00', 0),
	(157, 'Zimbabwean dollar', 'ZWL', '$', '$1,0.00', 0),
	(158, 'Swiss Franc', 'CHF', 'Fr', 'CHF 1,0.00', 0);";
	
	return $dtb;
}

function CopyFileLang()
{
	$langs = langs();
	
	//Stop if the language is the default language
	if ( !empty( $_POST['language'] ) && ( $_POST['language'] == DEFAULT_LANGUAGE ) )
		return;
	
	if ( !isset( $langs[$_POST['language']] ) )
		return;
	
	$code = $langs[$_POST['language']]['code'];
	
	//Also, stop if there is a file for this language
	if ( file_exists( LANGS_DIR . $code . '.json' ) )
		return;
	
	$orFile = LANGS_DIR . DEFAULT_LANGUAGE . '.json';
	
	$file = LANGS_DIR . $code . '.json';
	
	copy( $orFile, $file );
}

function dbLoad() 
{
	$dbh = null;
	
	try {
		$dbh = new PDO
		(
			"mysql:host=" . SERVER . ";dbname=" . DATABASE . ";charset=utf8mb4",
			DBUSERNAME,
			DBPASSWORD
		);
	
		$dbh->query('SET CHARACTER SET utf8mb4');
		$dbh->query('SET NAMES utf8mb4');
		$dbh->query('SET sql_mode=""');
			
		$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	} catch (\PDOException $e) {
		//
	}

	return $dbh;
}

function langs()
{
	//Languages Array
	return array('za' => array( 'lang' => 'za','name' => 'Afrikaans','locale' => 'af','code' => 'af', 'icon' => 'za.png'), 'arab' => array( 'lang' => 'arab','name' => 'العربية','locale' => 'ar','code' => 'ar', 'icon' => 'arab.png'), 'ma' => array( 'lang' => 'ma','name' => 'العربية المغربية','locale' => 'ary','code' => 'ar', 'icon' => 'ma.png'), 'in' => array( 'lang' => 'in','name' => 'తెలుగు','locale' => 'te','code' => 'te', 'icon' => 'in.png'), 'az' => array( 'lang' => 'az','name' => 'گؤنئی آذربایجان','locale' => 'azb','code' => 'az', 'icon' => 'az.png'), 'by' => array( 'lang' => 'by','name' => 'Беларуская мова','locale' => 'bel','code' => 'be', 'icon' => 'by.png'), 'bg' => array( 'lang' => 'bg','name' => 'български','locale' => 'bg_BG','code' => 'bg', 'icon' => 'bg.png'), 'bd' => array( 'lang' => 'bd','name' => 'বাংলা','locale' => 'bn_BD','code' => 'bn', 'icon' => 'bd.png'), 'tibet' => array( 'lang' => 'tibet','name' => 'Tibetan','locale' => 'bo','code' => 'bo', 'icon' => 'tibet.png'), 'ba' => array( 'lang' => 'ba','name' => 'Bosanski','locale' => 'bs_BA','code' => 'bs', 'icon' => 'ba.png'), 'catalonia' => array( 'lang' => 'catalonia','name' => 'Català','locale' => 'ca','code' => 'ca', 'icon' => 'catalonia.png'), 'ph' => array( 'lang' => 'ph','name' => 'Tagalog','locale' => 'tl','code' => 'tl', 'icon' => 'ph.png'), 'kurdistan' => array( 'lang' => 'kurdistan','name' => 'کوردی','locale' => 'ckb','code' => 'ku', 'icon' => 'kurdistan.png'), 'cz' => array( 'lang' => 'cz','name' => 'Čeština','locale' => 'cs_CZ','code' => 'cs', 'icon' => 'cz.png'), 'wales' => array( 'lang' => 'wales','name' => 'Cymraeg','locale' => 'cy','code' => 'cy', 'icon' => 'wales.png'), 'dk' => array( 'lang' => 'dk','name' => 'Dansk','locale' => 'da_DK','code' => 'da', 'icon' => 'dk.png'), 'ch' => array( 'lang' => 'ch','name' => 'Deutsch','locale' => 'de_CH_informal','code' => 'de', 'icon' => 'ch.png'), 'de' => array( 'lang' => 'de','name' => 'Deutsch','locale' => 'de_DE_formal','code' => 'de', 'icon' => 'de.png'), 'bt' => array( 'lang' => 'bt','name' => 'Dzongkha','locale' => 'dzo','code' => 'dz', 'icon' => 'bt.png'), 'gr' => array( 'lang' => 'gr','name' => 'Ελληνικά','locale' => 'el','code' => 'el', 'icon' => 'gr.png'), 'au' => array( 'lang' => 'au','name' => 'English','locale' => 'en_AU','code' => 'en', 'icon' => 'au.png'), 'ca' => array( 'lang' => 'ca','name' => 'English','locale' => 'en_CA','code' => 'en', 'icon' => 'ca.png'), 'gb' => array( 'lang' => 'gb','name' => 'English','locale' => 'en_GB','code' => 'en', 'icon' => 'gb.png'), 'nz' => array( 'lang' => 'nz','name' => 'English','locale' => 'en_NZ','code' => 'en', 'icon' => 'nz.png'), 'us' => array( 'lang' => 'us','name' => 'English','locale' => 'en_US','code' => 'en', 'icon' => 'us.png'), 'za' => array( 'lang' => 'za','name' => 'English','locale' => 'en_ZA','code' => 'en', 'icon' => 'za.png'), 'esperanto' => array( 'lang' => 'esperanto','name' => 'Esperanto','locale' => 'eo','code' => 'eo', 'icon' => 'esperanto.png'), 'ar' => array( 'lang' => 'ar','name' => 'Español','locale' => 'es_AR','code' => 'es', 'icon' => 'ar.png'), 'cl' => array( 'lang' => 'cl','name' => 'Español','locale' => 'es_CL','code' => 'es', 'icon' => 'cl.png'), 'co' => array( 'lang' => 'co','name' => 'Español','locale' => 'es_CO','code' => 'es', 'icon' => 'co.png'), 'cr' => array( 'lang' => 'cr','name' => 'Español','locale' => 'es_CR','code' => 'es', 'icon' => 'cr.png'), 'es' => array( 'lang' => 'es','name' => 'Español','locale' => 'es_ES','code' => 'es', 'icon' => 'es.png'), 'gt' => array( 'lang' => 'gt','name' => 'Español','locale' => 'es_GT','code' => 'es', 'icon' => 'gt.png'), 'mx' => array( 'lang' => 'mx','name' => 'Español','locale' => 'es_MX','code' => 'es', 'icon' => 'mx.png'), 'pe' => array( 'lang' => 'pe','name' => 'Español','locale' => 'es_PE','code' => 'es', 'icon' => 'pe.png'), 've' => array( 'lang' => 've','name' => 'Español','locale' => 'es_VE','code' => 'es', 'icon' => 've.png'), 'ee' => array( 'lang' => 'ee','name' => 'Eesti','locale' => 'et','code' => 'et', 'icon' => 'ee.png'), 'basque' => array( 'lang' => 'basque','name' => 'Euskara','locale' => 'eu','code' => 'eu', 'icon' => 'basque.png'), 'ir' => array( 'lang' => 'ir','name' => 'فارسی','locale' => 'fa_IR','code' => 'fa', 'icon' => 'ir.png'), 'fi' => array( 'lang' => 'fi','name' => 'Suomi','locale' => 'fi','code' => 'fi', 'icon' => 'fi.png'), 'be' => array( 'lang' => 'be','name' => 'Nederlands','locale' => 'nl_BE','code' => 'nl', 'icon' => 'be.png'), 'quebec' => array( 'lang' => 'quebec','name' => 'Français','locale' => 'fr_CA','code' => 'fr', 'icon' => 'quebec.png'), 'fr' => array( 'lang' => 'fr','name' => 'Français','locale' => 'fr_FR','code' => 'fr', 'icon' => 'fr.png'), 'it' => array( 'lang' => 'it','name' => 'Italiano','locale' => 'it_IT','code' => 'it', 'icon' => 'it.png'), 'scotland' => array( 'lang' => 'scotland','name' => 'Gàidhlig','locale' => 'gd','code' => 'gd', 'icon' => 'scotland.png'), 'galicia' => array( 'lang' => 'galicia','name' => 'Galego','locale' => 'gl_ES','code' => 'gl', 'icon' => 'galicia.png'), 'af' => array( 'lang' => 'af','name' => 'پښتو','locale' => 'ps','code' => 'ps', 'icon' => 'af.png'), 'il' => array( 'lang' => 'il','name' => 'עברית','locale' => 'he_IL','code' => 'he', 'icon' => 'il.png'), 'hr' => array( 'lang' => 'hr','name' => 'Hrvatski','locale' => 'hr','code' => 'hr', 'icon' => 'hr.png'), 'hu' => array( 'lang' => 'hu','name' => 'Magyar','locale' => 'hu_HU','code' => 'hu', 'icon' => 'hu.png'), 'am' => array( 'lang' => 'am','name' => 'Հայերեն','locale' => 'hy','code' => 'hy', 'icon' => 'am.png'), 'id' => array( 'lang' => 'id','name' => 'Basa Jawa','locale' => 'jv_ID','code' => 'jv', 'icon' => 'id.png'), 'is' => array( 'lang' => 'is','name' => 'Íslenska','locale' => 'is_IS','code' => 'is', 'icon' => 'is.png'), 'jp' => array( 'lang' => 'jp','name' => '日本語','locale' => 'ja','code' => 'ja', 'icon' => 'jp.png'), 'ge' => array( 'lang' => 'ge','name' => 'ქართული','locale' => 'ka_GE','code' => 'ka', 'icon' => 'ge.png'), 'dz' => array( 'lang' => 'dz','name' => 'Taqbaylit','locale' => 'kab','code' => 'kab', 'icon' => 'dz.png'), 'kz' => array( 'lang' => 'kz','name' => 'Қазақ тілі','locale' => 'kk','code' => 'kk', 'icon' => 'kz.png'), 'kh' => array( 'lang' => 'kh','name' => 'ភាសាខ្មែរ','locale' => 'km','code' => 'km', 'icon' => 'kh.png'), 'kr' => array( 'lang' => 'kr','name' => '한국어','locale' => 'ko_KR','code' => 'ko', 'icon' => 'kr.png'), 'la' => array( 'lang' => 'la','name' => 'ພາສາລາວ','locale' => 'lo','code' => 'lo', 'icon' => 'la.png'), 'lt' => array( 'lang' => 'lt','name' => 'Lietuviškai','locale' => 'lt_LT','code' => 'lt', 'icon' => 'lt.png'), 'lv' => array( 'lang' => 'lv','name' => 'Latviešu valoda','locale' => 'lv','code' => 'lv', 'icon' => 'lv.png'), 'mk' => array( 'lang' => 'mk','name' => 'македонски јазик','locale' => 'mk_MK','code' => 'mk', 'icon' => 'mk.png'), 'mn' => array( 'lang' => 'mn','name' => 'Монгол хэл','locale' => 'mn','code' => 'mn', 'icon' => 'mn.png'), 'my' => array( 'lang' => 'my','name' => 'Bahasa Melayu','locale' => 'ms_MY','code' => 'ms', 'icon' => 'my.png'), 'mm' => array( 'lang' => 'mm','name' => 'Ruáinga','locale' => 'rhg','code' => 'rhg', 'icon' => 'mm.png'), 'no' => array( 'lang' => 'no','name' => 'Norsk Nynorsk','locale' => 'nn_NO','code' => 'nn', 'icon' => 'no.png'), 'np' => array( 'lang' => 'np','name' => 'नेपाली','locale' => 'ne_NP','code' => 'ne', 'icon' => 'np.png'), 'nl' => array( 'lang' => 'nl','name' => 'Nederlands','locale' => 'nl_NL_formal','code' => 'nl', 'icon' => 'nl.png'), 'occitania' => array( 'lang' => 'occitania','name' => 'Occitan','locale' => 'oci','code' => 'oc', 'icon' => 'occitania.png'), 'pl' => array( 'lang' => 'pl','name' => 'Ślōnskŏ gŏdka','locale' => 'szl','code' => 'szl', 'icon' => 'pl.png'), 'br' => array( 'lang' => 'br','name' => 'Português','locale' => 'pt_BR','code' => 'pt', 'icon' => 'br.png'), 'pt' => array( 'lang' => 'pt','name' => 'Português','locale' => 'pt_PT_ao90','code' => 'pt', 'icon' => 'pt.png'), 'ro' => array( 'lang' => 'ro','name' => 'Română','locale' => 'ro_RO','code' => 'ro', 'icon' => 'ro.png'), 'ru' => array( 'lang' => 'ru','name' => 'Татар теле','locale' => 'tt_RU','code' => 'tt', 'icon' => 'ru.png'), 'lk' => array( 'lang' => 'lk','name' => 'සිංහල','locale' => 'si_LK','code' => 'si', 'icon' => 'lk.png'), 'sk' => array( 'lang' => 'sk','name' => 'Slovenčina','locale' => 'sk_SK','code' => 'sk', 'icon' => 'sk.png'), 'si' => array( 'lang' => 'si','name' => 'Slovenščina','locale' => 'sl_SI','code' => 'sl', 'icon' => 'si.png'), 'al' => array( 'lang' => 'al','name' => 'Shqip','locale' => 'sq','code' => 'sq', 'icon' => 'al.png'), 'rs' => array( 'lang' => 'rs','name' => 'Српски језик','locale' => 'sr_RS','code' => 'sr', 'icon' => 'rs.png'), 'se' => array( 'lang' => 'se','name' => 'Svenska','locale' => 'sv_SE','code' => 'sv', 'icon' => 'se.png'), 'pf' => array( 'lang' => 'pf','name' => 'Reo Tahiti','locale' => 'tah','code' => 'ty', 'icon' => 'pf.png'), 'th' => array( 'lang' => 'th','name' => 'ไทย','locale' => 'th','code' => 'th', 'icon' => 'th.png'), 'tr' => array( 'lang' => 'tr','name' => 'Türkçe','locale' => 'tr_TR','code' => 'tr', 'icon' => 'tr.png'), 'cn' => array( 'lang' => 'cn','name' => '中文 (中国)','locale' => 'zh_CN','code' => 'zh', 'icon' => 'cn.png'), 'ua' => array( 'lang' => 'ua','name' => 'Українська','locale' => 'uk','code' => 'uk', 'icon' => 'ua.png'), 'pk' => array( 'lang' => 'pk','name' => 'اردو','locale' => 'ur','code' => 'ur', 'icon' => 'pk.png'), 'uz' => array( 'lang' => 'uz','name' => 'Oʻzbek','locale' => 'uz_UZ','code' => 'uz', 'icon' => 'uz.png'), 'vn' => array( 'lang' => 'vn','name' => 'Tiếng Việt','locale' => 'vi','code' => 'vi', 'icon' => 'vn.png'), 'hk' => array( 'lang' => 'hk','name' => '中文 (香港)','locale' => 'zh_HK','code' => 'zh', 'icon' => 'hk.png'), 'tw' => array( 'lang' => 'tw','name' => '中文 (台灣)','locale' => 'zh_TW','code' => 'zh', 'icon' => 'tw.png') );
}