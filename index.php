<?php
/**
 * TOKICMS - open source content management system
 *
 * @package Toki CMS
 * @link https://tokicms.com/
 *
 * @author BadTooth Studio
 * @link https://badtooth.studio/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @version 0.6 Alpha
*/
@session_start();

// Get everything started up
define('TOKICMS', TRUE);
define('TOKI_VERSION', '0.6');
define('TOKI_FULL_VERSION', '0.6 Alpha');

define('DS', DIRECTORY_SEPARATOR);

define('ROOT',  dirname(__FILE__) . DS);

define('INC_ROOT', ROOT . 'inc' . DS);

// First check for the settings file
if ( !file_exists('settings.php') ) 
{
	$loc = ( (!empty($_SERVER['HTTPS'])) ? 'https' : 'http' ) . '://' . $_SERVER["SERVER_NAME"] .  $_SERVER["REQUEST_URI"] . 'install.php';
	@header("Location: " . $loc);
	@exit;
}

//Include the settings file
require ( 'settings.php' );

// Include all function files and do some other stuff
require ( INC_ROOT . 'init.php' );

$App = new App;
$App->process();
