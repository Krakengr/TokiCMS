<?php defined('TOKICMS') or die('Hacking attempt...');

//
// Common Functions
//

/**
 *
 * @param  Language
 * @return mixed
*/
function __( $str )
{
	global $L;
	
    if( isset( $L[$str] ) )
        return $L[$str];
    
	else 
        return ucfirst( $str );
}

#####################################################
#
# Strip Content function
#
#####################################################
function StripContent( $p )
{
	return stripslashes( html_entity_decode( htmlspecialchars_decode( $p ), ENT_QUOTES, 'UTF-8' ) );
}

#####################################################
#
# Get Registered Slugs function
#
#####################################################
function RegisteredSlugs()
{
	return array( 'page', 'login', 'logout', 'register', 'forgot-password', 'sitemap', 'category', 'tag', 'feed', 'profile', 'search', 'api', 'out', ADMIN_SLUG, PING_SLUG );
}

//Check user's Permissions
function IsAllowedTo( $str )
{
	$Permissions = APP::GetVar( 'Permissions' );
	
	if ( !empty( $Permissions ) )
	{
		if ( !is_array( $Permissions ) && ( $Permissions === 'all' ) )
			return true;

		elseif ( in_array( $str, $Permissions ) )
			return true;
	}
	
	return false;
}

//Get user's Group
function UserGroup()
{
	return APP::GetVar( 'UserGroup' );
}

//Get Current Language Details
function CurrentLang()
{
	return APP::GetVar( 'CurrentLang' );
}

//Get user's ID
function UserId()
{
	return APP::GetVar( 'UserId' );
}

#####################################################
#
# Loads the language function
#
#####################################################
function LoadLang()
{
	// Loads the defined language
	$file = LANG_ROOT . Settings::Get()['site_lang'] . '.json';
	
	if ( file_exists( $file ) )
		return json_decode( file_get_contents( $file ), TRUE );
		
	//Maybe we have a translation? If not, try to load the default language
	else
	{
		//Try to load the default language, to avoid errors
		if ( file_exists( LANG_ROOT . 'en.json' ) )
		{
			return json_decode( file_get_contents( LANG_ROOT . 'en.json' ), TRUE );
		}
			
		else
		{
			//At least we tried
			die( 'Language file "' . Settings::Get()['site_lang'] . '" could not be found' );
		}
	}
}

#####################################################
#
# Get the current site's URL for images function
#
#####################################################
function GetSiteImgUrl()
{
	$html = ( !empty( Settings::Get()['images_html'] ) ? Settings::Get()['images_html'] : SITE_URL . 'uploads' . PS );

	if ( !LOAD_IMAGES_LOCALLY && MULTISITE )
	{
		$query = array(
				'SELECT'	=> 's.id, s.url, c.value',
				'FROM'		=>	DB_PREFIX . "sites as s",
				'WHERE'		=> "is_primary = '1'",
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				'JOINS'		=> array(
					array(
						'LEFT JOIN'	=> DB_PREFIX . 'config AS c',
						'ON'		=> "c.id_site = s.id AND c.variable = 'images_html'"
					)
				)
			);

		$site = Query( $query );

		if ( $site && ( $site['id'] != SITE_ID ) )
		{
			$html = ( !empty( $site['value'] ) ? $site['value'] : $site['url'] . 'uploads' . PS );
		}
	}
	
	return $html;
}

//Get the social media list for current language
function GetSocialMediaList()
{
	$CurrentLang = CurrentLang();
	
	return $CurrentLang['data']['social'];
}

//Check if a setting is enabled
function IsEnabledTo( $str )
{
	global $Post;
	
	$CurrentLang = CurrentLang();
	
	$string = Settings::Get()[$str];

	if ( !empty( $string ) )
	{
		if ( $string === 'everywhere' )
			return true;

		else
		{
			$s = _explode( $string, '::' );
			
			if ( !empty( $s ) )
			{
				if ( $s['target'] == 'blog' )
				{
					if ( $s['id'] != $Post->BlogId() )
						return false;
				}
				
				elseif ( ( $s['target'] == 'lang' ) && ( $s['id'] != $CurrentLang['lang']['id'] ) )
				{
					return false;
				}
			}

			return true;
		}
	}

	return true;
}

#####################################################
#
# Input Cleaner for $_POST requests
#
#####################################################
function InputCleaner( $data, $return = null )
{
	$data = strip_tags( $data );

	// Fix &entity\n;
	$data = str_replace( array( '&amp;','&lt;','&gt;'), array( '&amp;amp;','&amp;lt;','&amp;gt;' ), $data );
	$data = preg_replace( '/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data );
	$data = preg_replace( '/(&#x*[0-9A-F]+);*/iu', '$1;', $data );
	$data = html_entity_decode( $data, ENT_COMPAT, 'UTF-8' );
	
	$data = preg_replace( '#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data );
	
	$data = preg_replace( '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data );
	
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data );
	
	$data = preg_replace( '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
	
	$data = preg_replace( '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data );
	
	$data = preg_replace( '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data );
	
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data );
	
	$data = preg_replace( '#</*\w+:\w[^>]*+>#i', '', $data );
    
	$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
	
	if ( $data )
		return $data;

	else
		return null;
}

#####################################################
#
# Explode string using delimiter
#
# This function checks if delimiter exists and explodes the results, returns the string as array otherwise
#
#####################################################
function _explode( $string, $delimiter )
{
	if ( strpos( $string, $delimiter ) !== false )
	{
		$data = explode( $delimiter, $string );
		
		return array( 'target' => $data['0'], 'id' => $data['1'] );
	}
	
	return array( 'target' => $string, 'id' => null );
}

//Check if guest is a bot
function IsBot()
{
	return (
		isset($_SERVER['HTTP_USER_AGENT'])
		&& preg_match('/bot|crawl|slurp|spider|mediapartners|yahoo|google|googlebot/i', $_SERVER['HTTP_USER_AGENT'])
	);
}

#####################################################
#
# Check if the current language is the default function
#
#####################################################
function IsTheDefaultLang()
{
	//If there is no multilang mode enabled, this language is the default
	if ( !MULTILANG )
	{
		return true;
	}
	
	$currentLang = CurrentLang();
	$defaultLang = Settings::LangData();
	
	if ( $defaultLang['lang']['id'] != $currentLang['lang']['id'] )
	{
		return false;
	}
	
	return true;
}
#####################################################
#
# Do Various Checks function
#
#####################################################
function DoChecks()
{	
	//Check if we have set to hide URL language information for default language
	HideDefaultLang();
	
	//Check if we have set Force HTTPS
	CheckHttps();
	
	//Check if we have set to add/remove www from the URL
	AddRemoveWww();
	
	//Check if we have set to forward non-mobile to mobile
	RedirectToAMP();
	
	//Check if we have set to Detect browser language
	DetectBrowserLanguage();
}

//Checks if we enabled the "Force Https" mode and redirects to a ssl page
function CheckHttps()
{
	if ( !Settings::IsTrue( 'force_https' ) )
		return;
	
	if ( isset( $_SERVER["HTTPS"] ) && ( $_SERVER["HTTPS"] == 'on' ) )
		return;
	
	$url = 'https://' . $_SERVER["SERVER_NAME"] . $_SERVER['REQUEST_URI'];
	
	//Redirect to the SSL page
	@header('Location: ' . $url, true, 301 );
    exit;
}

#####################################################
#
# Redirect To AMP function
#
# TODO: Remember if user changed lang and stop redirecting
#####################################################
function DetectBrowserLanguage()
{
	if ( Router::GetVariable( 'isAdmin' ) )
		return;
	
	if ( !Settings::IsTrue( 'detect_browser_language' ) )
		return;
	
	//Get the browser's lang
	$lang = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );
	
	//Stop if we don't have any lang to work with
	if ( empty( $lang ) )
		return;

	//Stop if we are in the default lang (or in this lang already)
	if ( ( Router::GetVariable( 'isLang' ) && ( Router::GetVariable( 'langKey' ) == $lang ) ) || ( !Router::GetVariable( 'isLang' ) && ( Settings::Lang()['code'] == $lang ) ) )
		return;
	
	//Get our lang codes array
	$langs = Settings::LangsArray();
	
	if ( in_array( $lang, $langs ) )
	{
		@header( "Location: " . SITE_URL . $lang . PS );
		exit;
	}
	
	return false;
}

#####################################################
#
# Redirect To AMP function
#
#####################################################
function RedirectToAMP()
{
	if ( Router::GetVariable( 'isAmp' ) || !isMobile() || Router::GetVariable( 'isStaticHomePage' ) || ( Router::WhereAmI() != 'post') || empty( Settings::Amp() ) || !isset( Settings::Amp()['redirect_mobile_visitors'] ) || ( isset( Settings::Amp()['redirect_mobile_visitors'] ) && !Settings::Amp()['redirect_mobile_visitors'] ) )
		return;
	
	global $Post;
	
	if ( !$Post || !$Post->HasAmp() )
		return;
	
	@header( "Location: " . $Post->AmpUrl() );
	exit;
}

#####################################################
#
# Add or Remove Www from the URL function
#
#####################################################
function AddRemoveWww()
{
	if ( Router::GetVariable( 'isAdmin' ) )
		return;
	
	//Don't do anything if we are in a lang or we don't want that
	if ( Settings::Get()['redirect_www'] == 'false' )
		return;
	
	$protocol = isset( $_SERVER['HTTPS'] ) && filter_var( $_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN ) ? 'https' : 'http';
	
	if ( Settings::Get()['redirect_www'] == 'to-www' )
	{
		if ( strpos( $_SERVER['HTTP_HOST'], 'www' ) === false ) 
		{
			@header( "Location: $protocol://www." . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301 );
			exit;
		}
	}
	
	elseif ( Settings::Get()['redirect_www'] == 'to-non-www' )
	{
		if ( strpos( $_SERVER['HTTP_HOST'], 'www' ) !== false ) 
		{
			$url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url = str_replace( 'www.', '', $url );
			@header( "Location: " . $url, true, 301 );
			exit;
		}
	}
}

#####################################################
#
# Hide or Show the Default Lang from the URL function
#
#####################################################
function HideDefaultLang()
{
	//Don't check this if we are in admin panel
	if ( Router::GetVariable( 'isAdmin' ) )
		return;

	//Make sure we don't have the default language's code if we don't want that
	if ( Settings::IsTrue( 'hide_default_lang_slug' ) && Router::GetVariable( 'isLang' ) && ( Router::GetVariable( 'langKey' ) == Settings::Lang()['code'] ) )
	{
		@header('Location: ' . SITE_URL );
		exit;
	}
	
	//Prevent any further actions if we want the default lang key to be hidden
	elseif ( Settings::IsTrue( 'hide_default_lang_slug' ) || Router::GetVariable( 'isLang' ) )
		return;
		
	$pageUri = Router::GetVariable( 'url' );

	@header('Location: ' . $pageUri );
	exit;
}

// SESSION TOKEN based on user agent
function SessionToken()
{
	$a = md5(substr(session_id(), 2, 7));
	$b = $_SERVER['HTTP_USER_AGENT'];
	
	return sha1( $a . $b . GetTheHostName( SITE_URL ) );
}

//Generate short key
function generate_short_key( $siteId = SITE_ID )
{	
	//Load the site's settings if needed
	if ( $siteId != SITE_ID )
	{
		$Set = new Settings( $siteId, false );
		
		if ( !$Set )
			return null;
		
		$settings = $Set::LinkSettings();
		
		unset( $Set );
	}
	else
	{
		$settings = Settings::LinkSettings();
	}
		
	$length = ( isset( $settings['short-link-settings']['slug_character_count'] ) ? $settings['short-link-settings']['slug_character_count'] : null );
	
	if ( empty( $length ) || ( $length == 0 ) )
		return '';
	
	$key = '';
	
	$db  = db();
	
	//Loop until we find a "clean" key
	for ( $i = 1; $i < 10; $i++ )
	{
		$shuffle = '1234567890qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPLKJHGFDSAZXCVBNM';
		
		$type = ( !empty( $settings['short-link-settings']['autogenerated_path_type'] ) ? $settings['short-link-settings']['autogenerated_path_type'] : '' );
		
		$case = ( !empty( $settings['short-link-settings']['autogenerated_path_case'] ) ? $settings['short-link-settings']['autogenerated_path_case'] : 'any' );
		
		if ( $type == 'alphabetical' )
		{
			$shuffle = 'AQWERTYUIOPLKJHGFDSAZXCVBNMqwertyuioplkjhgfdsazxcvbnm';
		}
		
		elseif ( $type == 'numeric' )
		{
			$shuffle = '1234567890';
		}
		
		$key = substr( str_shuffle( $shuffle ), $i, $length );
		
		if ( ( $case != 'any' ) && ( $type != 'numeric' ) )
		{
			$key = ( ( $case == 'lowercase' ) ? strtolower( $key ) : strtoupper( $key ) );
		}	
		
		$exists = $db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "links`
		WHERE (id_site = " . (int) $siteId . ") AND (short_link = :link)",
		array( $key => ':link' )
		)->single();
		
		if ( !$exists )
		{
			break;
		}
	}
	
	return $key;
}

//Generate Token
function generate_token( $form, $token = '_token' ) 
{
	$hash = sha1( uniqid( microtime(), true ) . session_id() );  
    $_SESSION[$form.$token] = $hash;
	
    return $hash;
}

//Verify the generated Token
function verify_token( $form, $token = '_token' )
{
	if( !isset( $_SESSION[$form.$token] ) )
	{ 
		return false;
    }
	
	if( !isset( $_POST[$token] ) )
	{
		return false;
    }

	if( !hash_equals( $_SESSION[$form.$token], $_POST[$token] ) )
	{
		return false;
    }
	
	return true;
}

function generate_get_token( $id = null, $num = null )
{
	$a = md5(substr(session_id(), 2, 7));
	$b = $_SERVER['HTTP_USER_AGENT'];
	$s = sha1( $a . $b . ( $id ? $id : '' ) );
	return ( ( $num && is_numeric( $num ) ) ? substr( $s, 0, $num ) : $s );
}

function verify_get_token( $id = null, $num = null )
{
	$nonce = null;
	
	$a = md5(substr(session_id(), 2, 7));
	$b = $_SERVER['HTTP_USER_AGENT'];
	$s = sha1( $a . $b . ( $id ? $id : '' ) );
	$s = ( ( $num && is_numeric( $num ) ) ? substr( $s, 0, $num ) : $s );
	
	return ( ( $nonce && hash_equals( $nonce, $s ) ) ? true : false );
}

function VerifySessionToken( $name, $nonce = null, $time = 60 )
{
	if ( !isset( $_SESSION[$name] ) || empty( $_SESSION[$name] ) )
		return false;
	
	if ( !isset( $_SESSION['cached_unique_tokens'][$name] ) 
		|| empty( $_SESSION['cached_unique_tokens'][$name] ) 
	);
	
	if ( ( $_SESSION['cached_unique_tokens'][$name] + $time ) < time() )
	{
		unset( $_SESSION['cached_unique_tokens'][$name], $_SESSION[$name] );
		return false;
	}

	return hash_equals( $nonce, $_SESSION[$name] );
}

function GenerateSessionToken( $name, $num = 10, $time = 60 )
{
	if ( isset( $_SESSION[$name] ) && !empty( $_SESSION[$name] ) )
	{
		if ( ( isset( $_SESSION['cached_unique_tokens'][$name] ) && !empty( $_SESSION['cached_unique_tokens'][$name] ) && ( $_SESSION['cached_unique_tokens'][$name] + $time ) > time() ) )
			return $_SESSION[$name];
	}
	
	if( function_exists('hash_algos') && in_array( 'sha512', hash_algos() ) )
	{
		$token = hash('sha512', uniqid( microtime(), true ) );
	}
	
	else
	{
		$token = nonce( $name );
	}
	
	if( !isset( $_SESSION['cached_unique_tokens'] ) )
	{
		$_SESSION['cached_unique_tokens'] = array();
	}

	$_SESSION['cached_unique_tokens'][$name] = time();
	
	$token = substr( $token, 0, $num );
	
	$_SESSION[$name] = $token;

	return $token;
}

// generates a secure nonce
function nonce( $str = null, $salt = null, $expires = 86400 )
{
	$salt = ( !$salt ? generate_key( 8 ) : $salt );
	$str = ( !$str ? generate_key( 8 ) : $str );
	return sha1( date('Y-m-d H:i', ceil(time() / $expires ) * $expires) . GetRealIp() . $_SERVER['HTTP_USER_AGENT'] . $salt . $str );
}

// Checks if the form contains this field, returns empty string otherwise
function SafeFormField( $string, $sanitize = false )
{
	//sanitize function removes every code from a string, SafeString keeps a few html tags, like <strong>, <a>, etc...
	return ( $sanitize ? Sanitize( $string, false ) : SafeString( $string ) );
}

//Check if a redirection exists
function CheckRedirect( $sef )
{
	if ( !Settings::IsTrue( 'enable_redirect' ) )
		return false;
	
	$db = db();

	$data = Json( Settings::Get()['redirection_data'] );

	$case_insensitive_matches = ( !empty( $data ) && isset( $data['case_insensitive_matches'] ) ? $data['case_insensitive_matches'] : null );
	
	$ip_logging = ( !empty( $data ) && isset( $data['ip_logging'] ) ? $data['ip_logging'] : null );
	
	if ( $ip_logging && ( $ip_logging == 'full-ip-logging' ) )
		$ip = GetRealIp();
		
	elseif ( $ip_logging && ( $ip_logging == 'anonymize-ip' ) )
		$ip = MaskIp( GetRealIp() );
		
	else
		$ip = 0;

	//TODO
	//$regex = '(?i)\/' . $sef . ( $data['ignore_trailing_slashes'] ? '(\/)?' : '\/' );
	//$q = "SELECT * FROM " . DB_PREFIX . "redirs WHERE uri REGEXP " . $regex . " AND id_site = '" . SITE_ID . "'";
	
	$query = "SELECT *
	FROM `" . DB_PREFIX . "redirs`
	WHERE (id_site = " . SITE_ID . ") AND (disable_redir = 0) AND " . ( $case_insensitive_matches ? 'LOWER(uri)' : 'uri' ) . " LIKE " . ( $case_insensitive_matches ? 'LOWER(:sef)' : ':sef' );
		
	$binds = array( $sef . '%' => ':sef' );

	//Query: redir
	$tmp = $db->from( null, $query, $binds )->single();

	if ( !$tmp )
	{
		//Search for wildcards, for now keep it simple
		$query = "SELECT *
		FROM `" . DB_PREFIX . "redirs`
		WHERE (id_site = " . SITE_ID . ") AND (disable_redir = 0) AND " . ( $case_insensitive_matches ? 'LOWER(uri)' : 'uri' ) . " LIKE " . ( $case_insensitive_matches ? 'LOWER(:sef)' : ':sef' );

		$binds = array( '%(.*)%' => ':sef' );

		//Query: redirs
		$tmp = $db->from( null, $query, $binds )->all();
		
		if ( $tmp )
		{
			$uri = ( $case_insensitive_matches ? strtolower( $_SERVER['REQUEST_URI'] ) : $_SERVER['REQUEST_URI'] );

			$url = Sanitize( $uri, false );

			$base = GetBase();

			if ( $base !== '/' )
			{
				$url = str_replace( $base, '/', $url );
			}
	
			$str = null;
			
			foreach( $tmp as $po )
			{
				$target = $po['target'];
				
				$uriPreg = str_replace( '/', '\/', $po['uri'] );
				
				preg_match("/$uriPreg/", $url, $matches );
				
				if ( !empty( $matches ) )
				{
					$str = preg_replace( "/$uriPreg/", $target, $url );
	
					if ( !empty( $str ) )
					{
						$po['target'] = $str;
						return $po;
					}
				}
			}
		}

		if ( isset( $data['keep_log_redirects_errors'] ) && $data['keep_log_redirects_errors'] )
		{
			Log::Set( sprintf( __( 'error-log-redirected' ), $sef ), $sef, null, 'notfound' );
		}

		return false;
	}
	
	$dbarr = array(
        "last_time_viewed" => time(),
		"views" => "views + 1"
    );

	$db->update( 'redirs' )->where( 'id', $post['id'] )->set( $dbarr );

	//Write the system log
	if ( isset( $data['keep_log_redirects_errors'] ) && $data['keep_log_redirects_errors'] && ( $post['exclude_logs'] == 0 ) )
	{
		Log::Set( sprintf( __( 'error-log-not-found' ), $sef ), '', null, 'redirect' );
	}

	return $post;
}

//Adds the Log to the DB
/*
function SysLogs( $title = '', $descr = '', $slug = '', $ip = 0, $type = 'system', $uuid = 0, $siteid = SITE_ID )
{
	$query = array(
			'INSERT'	=> "id_site, user_id, title, slug, descr, added_time, ip, type",

			'VALUES' 	=> ":site, :user, :title, :slug, :descr, :time, :ip, :type",

			'INTO'		=> DB_PREFIX . "logs",

			'PARAMS' => array( 'NO_PREFIX' => true ),

			'BINDS' => array(
						array( 'PARAM' => ':site', 'VAR' => $siteid, 'FLAG' => 'INT' ),
						array( 'PARAM' => ':user', 'VAR' => $uuid, 'FLAG' => 'INT' ),
						array( 'PARAM' => ':title', 'VAR' => $title, 'FLAG' => 'STR' ),
						array( 'PARAM' => ':slug', 'VAR' => $slug, 'FLAG' => 'STR' ),
						array( 'PARAM' => ':descr', 'VAR' => $descr, 'FLAG' => 'STR' ),
						array( 'PARAM' => ':time', 'VAR' => time(), 'FLAG' => 'INT' ),
						array( 'PARAM' => ':ip', 'VAR' => $ip, 'FLAG' => 'STR' ),
						array( 'PARAM' => ':type', 'VAR' => $type, 'FLAG' => 'STR' )
		)
	);

	return Query( $query, false, false, true );
}
*/

//Set the needed headers here
function SetHeaders()
{	
	if ( Settings::IsTrue( 'add_allow_origin_tag' ) )
	{
		header( 'Access-Control-Allow-Origin: ' . SITE_URL );
	}

	$protocol = 'HTTP/1.0';

    if ( $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1' ) {
        $protocol = 'HTTP/1.1';
    }
	
	header('X-Powered-By: TokiCMS');
	
	$code = (int) Router::GetVariable( 'httpCode' );
	
	if ( Settings::IsTrue( 'enable_maintenance', 'site' ) && !IsAllowedTo( 'admin-site' ) )
	{
		//Tell engines you're busy
		header( $protocol . ' 503 Service Unavailable', true, 503 );
		header( 'Status: 503 Service Temporarily Unavailable' );
		header( 'Retry-After: 3600' ); //1 hour delay, time is in seconds
	}
	
	elseif( Router::GetVariable( 'accessDenied' ) )
	{
		header( $protocol . ' 403 Forbidden', true, 403 );
		header( 'Status: 403 Forbidden' );
	}
	
	else
	{
		header( $protocol . ' ' . $code . ' ' . Router::GetVariable( 'httpMessage' ) );
	}
}

function UpdateRedirHits( $id )
{
	$db = db();
	
	$id = (int) $id;
	
	$dbarr = array(
        "last_time_viewed" => time(),
		"views" => "views + 1"
    );

	$db->update( 'redirs' )->where( 'id', $id )->set( $dbarr );
}

//Check fpr changed slugs in DB
function CheckChangedSlug( $slug )
{
	if ( !Settings::IsTrue( 'enable_redirect' ) )
		return false;
	
	$db = db();
	
	$data = Json( Settings::Get()['redirection_data'] );

	$ip_logging = ( !empty( $data ) && isset( $data['ip_logging'] ) ? $data['ip_logging'] : null );

	if ( $ip_logging && ( $ip_logging == 'full-ip-logging' ) )
		$ip = GetRealIp();

	elseif ( $ip_logging && ( $ip_logging == 'anonymize-ip' ) )
		$ip = MaskIp( GetRealIp() );

	else
		$ip = 0;
	
	//Search for wildcards, for now keep it simple
	$query = "SELECT id, target
	FROM `" . DB_PREFIX . "redirs`
	WHERE (id_site = " . SITE_ID . ") AND (disable_redir = 0) AND (slug = :slug)";

	$binds = array( $slug => ':slug' );

	//Query: redir
	$redir = $db->from( null, $query, $binds )->single();

	if ( !$redir || empty( $redir['target'] ) )
		return false;

	UpdateRedirHits( $redir['id'] );

	//Write the system log
	if ( isset( $data['keep_log_redirects_errors'] ) && $data['keep_log_redirects_errors'] )
	{
		Log::Set( sprintf( __( 'error-log-not-found' ), $slug ), $_SERVER['REQUEST_URI'], null, 'redirect' );
	}

	@header('Location: ' . $redir['target'], true, 301 );
	exit;
}

// Sets an error404, searches redirects or posts on other sites (maybe a post that have been moved to somewhere else)
function Error404() 
{
	$CurrentLang = CurrentLang();
	
	include_once ( FUNCTIONS_ROOT . 'posts-functions.php' );
	include_once ( FUNCTIONS_ROOT . 'categories-functions.php' );
	include_once ( FUNCTIONS_ROOT . 'tags-functions.php' );
	include_once ( CLASSES_ROOT . 'Post.php' );
	
	// Check if there is a redirect first
	if ( Settings::IsTrue( 'enable_redirect' ) )
	{
		$slugToCheck = Sanitize ( Router::GetVariable( 'slug' ), false );
		
		if ( empty( $slugToCheck ) || ( $slugToCheck == '' ) )
			return;

		$redir = CheckRedirect( Sanitize( $_SERVER['REQUEST_URI'], false ) );

		if ( $redir && !$redir['disable_redir'] )
		{
			UpdateRedirHits( $redir['id'] );
			
			if ( $redir['when_matched'] == 'error-404')
				Router::SetNotFound();
			
			elseif ( $redir['when_matched'] == 'redirect-to-random' )
			{
				$post = GetRandPosts( $CurrentLang['lang']['id'], 1 );

				if ( $post && !empty( $post['url'] ) )
				{
					@header('Location: ' . $post['url'] );
					exit;
				}
			}

			elseif ( $redir['when_matched'] == 'redirect-to-url' )
			{
				if ( $redir['http_code'] != 'disable')
				{
					$code = (int) $redir['http_code'];
					
					@header('Location: ' . $redir['target'], true, $code );
					exit;
				}
					
				else
				{
					@header('Location: ' . $redir['target'] );
					exit;
				}
			}
			
			//Add the response code
			if ( $redir['http_code'] != 'disable')
			{
				$code = (int) $redir['http_code'];
				http_response_code( $code );
			}
			
			exit;
		}
		
		//Maybe there is a changed slug?
		else
		{
			CheckChangedSlug( $slugToCheck );
		}
	}

	//Maybe we've moved this post to another site?
	if ( Settings::IsTrue( 'allow_full_search' ) )
	{
		$post = GetPostBySlug( Router::GetVariable( 'slug' ) );
		
		if ( $post && !empty( $post->Url() ) )
		{
			@header('Location: ' . $post->Url(), true, 301 );
			exit;
		}
	}

	//Nothing found it the DB. It's time to show an error
	Router::SetNotFound();

	require_once CONTROLLER_ROOT . 'Noting.php';
}

//Get php server load function
function GetServerLoad()
{
    $load = null;
	
	//windows
    if ( stristr( PHP_OS, 'win' ) )
	{
        $cmd = 'wmic cpu get loadpercentage /all';
        
		@exec( $cmd, $output );
        
		if ( $output )
		{
            foreach( $output as $line )
			{
                if ( $line && preg_match( '/^[0-9]+$/', $line ) )
				{
                    $load = $line;
                    break;
                }
            }
        }
    }
	
	//linux
	else 
	{
		$sys_load = sys_getloadavg();
		$load = $sys_load['0'];
    }
	
    return $load;
}


//Checks if there is a trailing slash
//If not, adds one and redirects to the new URL
function CheckLast()
{		
	$canonic = GetUrl();
	
	if ( strpos( $canonic, '?' ) !== false )
	{
		$c = explode( '?', $canonic );
		
		$canonic = $c['0'];
		
		$last = $canonic[strlen($canonic)-1];
			
		if ($last != '/') 
		{
			$loc = $canonic . PS . '?' . $c['1'];
			
			@header("Location: " . $loc);

			@exit;
		}
		
	}
	
	else
	{
		$last = $canonic[strlen($canonic)-1];
			
		if ($last != '/') 
		{
			$loc = $canonic . PS;

			@header("Location: " . $loc);
					
			@exit;
		}
		
	}
}

// Security checks
function Sec()
{
	$uri = strtolower( $_SERVER['REQUEST_URI'] );

	// Prevent index.php?GLOBALS[foo]=bar and mysql injections
	if ( isset( $_REQUEST['GLOBALS'] ) || isset( $_FILES['GLOBALS'] ) || strlen( $_SERVER['REQUEST_URI'] ) > 450 || ( strpos( $uri, 'eval(' ) !== false ) || ( strpos( $uri, 'concat(' ) !== false ) || ( strpos( $uri, 'union+select' ) !== false ) || ( strpos( $uri, 'base64' ) !== false ) )
	{
		//SysLogs( 'Hacking Attempt', Sanitize( $uri, false ), '', GetRealIp() );
		Log::Set( 'Hacking Attempt', Sanitize( $uri, false ), null, 'redirect' );
		@header("HTTP/1.1 414 Request-URI Too Long");
		@header("Status: 414 Request-URI Too Long");
		@header("Connection: Close");
		@exit;
	}
}

function Json ( $data )
{
	if ( empty( $data ) )
		return array();
	
	if (version_compare(phpversion(), '7.2', '>'))
		return json_decode( $data, true, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE  );
	else
		return json_decode( $data, true, JSON_UNESCAPED_UNICODE );
}

//Sanitize the string
function Sanitize( $string, $clean = true )
{
	$string = SafeString( stripslashes( trim( $string ) ) );
	
	if ( $clean )
		$string = preg_replace( '/[^\sa-zA-Z0-9áéíóúüñÁÉÍÓÚÜÑ\/-=\?]+/u', '', $string );
	
	$string = filter_var ( $string, FILTER_SANITIZE_SPECIAL_CHARS );

	return $string;
}

// Removes bad code, keeps some html tags and converts applicable characters to HTML entities
// for making user input safe for display
// I Could use strip_tags, but strip_tags is never the right function to use for this and it has a lot of problems
function SafeString( $string )
{
	$search = array(
		'@<script[^>]*?>.*?</script>@si',
		'@<head[^>]*?>.*?</head>@siu',
		'@<style[^>]*?>.*?</style>@siu',
		'@<object[^>]*?.*?</object>@siu',
		'@<embed[^>]*?.*?</embed>@siu',
		'@<applet[^>]*?.*?</applet>@siu',
		'@<noframes[^>]*?.*?</noframes>@siu',
		'@<noscript[^>]*?.*?</noscript>@siu',
		'@<noembed[^>]*?.*?</noembed>@siu',
		'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
		'@</?((table)|(th)|(td)|(caption)|(isindex))@iu',
		'@</?((form)|(button)|(fieldset)|(legend)|(input)|(address))@iu',
		'@</?((label)|(select)|(optgroup)|(option))@iu',
		'@</?((frameset)|(frame)|(iframe))@iu',
		'@<![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments
	);
 
    $string = preg_replace($search, '', $string);
	
    return htmlspecialchars( $string, ENT_QUOTES, CHARSET );
}

//Writes data to a file
function WriteCache($data, $cacheFile)
{
	if ( empty( $cacheFile ) )
		return false;
	
	if (!$fp = fopen($cacheFile, 'w')) 
	{
		trigger_error('Error opening cache file');
		return false;
	}
		
	if (!flock($fp, LOCK_EX)) 
	{
		trigger_error('Unable to lock file');
		return false;
	}
		
	if (!fwrite($fp, serialize($data))) 
	{
		trigger_error('Error writing to cache file');
		return false;
	}
		
	flock($fp, LOCK_UN);
	fclose($fp);
}

//Reads data from a file
function ReadCache($cacheFile) 
{
	if (!file_exists($cacheFile)) 
	{
		trigger_error('Invalid cache file');
		return false;
	}
	
	return unserialize( file_get_contents( $cacheFile ) );
}

// Checks if the string contains the "true" word, returns false otherwise
function IsTrue( $string, $form = false )
{
	if ( $form )
		return ( ( isset( $string ) && ( $string == 'true' ) ) ? true : false );
	
	else
		return ( ( $string == 'true' ) ? true : false );
}

// Checks if the string contains the "false" word or is bool (false), returns false otherwise
function IsFalse( $string )
{
	if ( ( $string === 'false' ) || empty( $string ) || !$string )
		return true;

	return false;
}

//Gets the hostname of a URL
function GetTheHostName ( $url )
{
	$url_details = parse_url( $url );

	$host = ( !empty( $url_details['host'] ) ? str_replace( 'www.', '', $url_details['host'] ) : '' );
		
	return $host;
}

//Strip whitespace from a string
function minifyHTML($buffer)
{
	$search = array(
		'/\>[^\S ]+/s', //strip whitespaces after tags, except space
		'/[^\S ]+\</s', //strip whitespaces before tags, except space
		'/(\s)+/s'  // shorten multiple whitespace sequences
	);

	$replace = array(
		'>',
		'<',
		'\\1'
	);
	
	$buffer = preg_replace( $search, $replace, $buffer );

	return $buffer;
}

function CleanUri( $uri )
{
	if ( strpos ( $uri, '?' ) !== false )
	{
		$ur = explode ( '?', $uri );

		$uri = ( !empty( $ur['0'] ) ? $ur['0'] : $uri );
	}
	
	return $uri;
}

function GetUri( $trailing = false, $q = false )
{
	$uri = Sanitize ( $_SERVER['REQUEST_URI'], false );

	if ( $q )
	{
		if ( strpos ( $uri, '?' ) !== false )
		{
			$ur = explode ( '?', $uri );

			$uri = ( !empty( $ur['0'] ) ? $ur['0'] : $uri );
		}
	}
		
	$base = GetBase();

	if ( $base !== '/' )
	{
		if ( $trailing )
			$url = str_replace( $base, '/', $uri );
		
		else
			$url = str_replace( $base, '', $uri );
	}
	
	else
		$url = $uri;

	return $url;
}

function GetBase()
{
	// Base URL
	$base = empty( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
		
	$base = dirname( $base );
		
	if ( strpos( $_SERVER['REQUEST_URI'], $base ) !== 0 )
		$base = '/';
		
	elseif ( $base != DS ) 
	{
		$base = trim( $base, '/' );
		$base = '/' . $base . '/';
			
	} else
		$base = '/';
			
	return $base;
}


//Validate string
function Validate( $string, $type = 'email' )
{
	switch( $type )
	{
		//Validates email
		case 'email':
			return ( filter_var( $string, FILTER_VALIDATE_EMAIL ) ? true : false );
		break;
		
		//Validates URL
		case 'url':
			return ( filter_var( $string, FILTER_VALIDATE_URL ) ? true : false );
		break;
		
		//Validates IP
		case 'ip':
			return ( filter_var( $string, FILTER_VALIDATE_IP ) ? true : false );
		break;
		
		//Validates IPV6
		case 'ipv6':
			return ( filter_var( $string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ? true : false );
		break;
		
		//Validates IPV4
		case 'ipv4':
			return ( filter_var( $string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ? true : false );
		break;
	}
}

//Checks if a string starts with "/"
function FirstTrailCheck( $string )
{
	$string = (string) $string;
	
	if ( version_compare( phpversion(), '8.0', '>' ) )
	{
		if ( !str_starts_with( $string, '/' ) )
			$string = '/' . $string;
	}
	
	else
	{
		if ( substr( $string, 0, 1 ) !== '/' )
			$string = '/' . $string;
	}
	
	return $string;
}

//Adds a trailing slash, if there is not one
function LastTrailCheck( $string )
{
	$string = (string) $string;
		
	$last = $string[strlen($string)-1];

	if ($last != '/')
		$string = $string . '/';
			
	return $string;
}

// Check if a string contains a word
if ( !function_exists( 'str_contains' ) )
{
	function str_contains( $string, $needle )
	{
		return $needle !== '' && mb_strpos($string, $needle) !== false;
	}
}

function IsMobile()
{
	if ( !isset( $_SERVER["HTTP_USER_AGENT"] ) || empty( $_SERVER["HTTP_USER_AGENT"] ) )
		return null;
	
	return preg_match("/(android|iphone|ipod|ipad|avantgo|blackberry|bolt|boost|symbian|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"] );
}

//Gets the current URL
function GetUrl()
{
	return ( ( isset( $_SERVER["HTTPS"] ) && ($_SERVER["HTTPS"] == 'on' ) ) ? 'https' : 'http' ) . '://' . $_SERVER["SERVER_NAME"] . $_SERVER['REQUEST_URI'];
}

function CheckCaptcha ( $response )
{
	if ( ( Settings::Get()['enable_recaptcha'] == 'false' ) || empty( Settings::Get()[ 'recaptcha_site_key'] ) )
		return;
	
	$captcha_url  = 'https://www.google.com/recaptcha/api/siteverify';
		
	$captcha_url .= '?secret=' . urlencode( Settings::Get()[ 'recaptcha_secret_key'] ) . '&response=' . urlencode( $response );
	
	// Make and decode POST request:
	$recaptcha = PingSite( $captcha_url );
	
	if ( empty( $recaptcha ) )
		return false;
	
	//V2
	if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v2' )
	{
		return ( ( isset( $recaptcha['success'] ) && $recaptcha['success'] ) ? true : false );
	}
	
	//V3
	else
	{
		return ( ( isset( $recaptcha['success'] ) && isset( $recaptcha['score'] ) && $recaptcha['success'] && ( $recaptcha['score'] >= 0.5 ) ) ? true : false );
	}
}

#####################################################
#
# Response Code function
#
#####################################################
function ResponseCode( $code )
{
	include ( ARRAYS_ROOT . 'seo-arrays.php');
	
	if ( isset( $urlResponseCodes[$code] ) )
		return $urlResponseCodes[$code]['title'];
	
	return $code;
}

#####################################################
#
# Mask IP function
#
#####################################################
function MaskIp( $ip ) 
{
	$ip = trim( $ip );

	if ( strpos( $ip, ':' ) !== false ) 
	{
		
		$ip = @inet_pton( $ip );
		
		return @inet_ntop( $ip & pack( 'a16', 'ffff:ffff:ffff:ffff::ff00::0000::0000::0000' ) );
	}

	$parts = [];
	
	if ( strlen( $ip ) > 0 )
		$parts = explode( '.', $ip );

	if ( count( $parts ) > 0 )
		$parts[ count( $parts ) - 1 ] = 0;

	return implode( '.', $parts );
}

// Gets the IP
function GetRealIp()
{
	$ip = '';
	
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} 
	
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
	{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	
	else 
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	if ( strpos( $ip, ':' ) !== false ) 
	{
		
		$ip = @inet_pton( $ip );
		
		$ip = @inet_ntop( $ip );
		
		if ( $ip )
			return $ip;
	}
	
	return $ip;
}

/**
*
* Returns a random key mixed letters with numbers
*
* @param int $length The length of the key to return
* @return string
*/
function RandomBytes( $length )
{
	
	if (function_exists( 'random_bytes' ) )
		return random_bytes( $length );
	else
		return GenerateRandomKey($length);
	
	//md5(substr(session_id(), 2, 7));
}

/**
*
* Returns a random key mixed letters with numbers
*
* @param int $length The length of the key to return
* @return string
*/
function GenerateRandomKey( $length, $all = false, $numOnly = false )
{
	if ( $numOnly )
	{
		$sting = '123456789987654321';
	}
	
	else
	{
		$sting = '1234567890qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPLKJHGFDSAMNBVCXZ' . ( $all ? '#$@!%^&*()_-+=}[~|\]' : '' );
	}

	return substr( str_shuffle( $sting ), 0, $length );
}

function GenerateStrongRandomKey( $length )
{
	$key = md5( time() . session_id() . GenerateRandomKey( $length, true ) );
	$key = sha1( md5( sha1( $key ) ) );
	
	return substr( $key, 0, $length );
}

function GetLetter( $string, $length = 1 )
{
	return strtolower( substr( $string, 0, $length ) );
}

// Encodes the contents of a string
function HtmlChars( $str )
{
	return htmlspecialchars( $str, ENT_QUOTES, CHARSET );
}

/**
* Removes invalid XML
*
* @param string $value
* @return string
*/
function StripInvalidXml($value)
{
	$ret = "";
	$current;
		
	if (empty($value)) 
	{
		return $ret;
	}

	$length = strlen($value);
	for ($i=0; $i < $length; $i++)
	{
		$current = ord($value[$i]);
		if (($current == 0x9) ||
				($current == 0xA) ||
				($current == 0xD) ||
				(($current >= 0x20) && ($current <= 0xD7FF)) ||
				(($current >= 0xE000) && ($current <= 0xFFFD)) ||
				(($current >= 0x10000) && ($current <= 0x10FFFF)))
		{
			$ret .= chr($current);
		}
		else
		{
			$ret .= " ";
		}
	}
	
	return $ret;
}

//TODO: https://github.com/PHPMailer/PHPMailer
function SendEmail( $email, $message, $subject )
{
	$headers = "From: " . Settings::Get()['website_email'] . "'\r\n";
	$headers .= "Reply-To: " . Settings::Get()['website_email'] . "'\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

	if ( mail($email, $subject, $message, $headers) )
		return true;
	else
		return false;
}

function RegexReplace ( $txt )
{
	$search 	= array( '{{esc}}', '{{space}}', '{{price}}', 			  '{{char}}',    '{{digit}}', '{{alpha}}' );
	$replace 	= array( '\\', 		'.+', 		 '(\d+(\.|,)?\d+\.?\d*)', '([A-Za-z]+)', '([0-9]+)',  '([A-Za-z0-9]+)' );
	
	return str_replace( $search, $replace, $txt );
}

function thousand( $val )
{
	$thousand =  ( isset ( $val['0']['0'] ) ? $val['0']['0'] : null );
	
	if ($thousand == '!') 
	{
        $thousand = '';
    }
	
	return $thousand;
}

function decimal( $val )
{
	$decimal =  ( isset ( $val['0']['1'] ) ? $val['0']['1'] : null );
	
	return $decimal;
}

function formatPrice( $value, $code = null, $include_symbol = true, $cutZero = false )
{
	$code = $code ?: '1.0,00 €';
	
    $value = preg_replace('/[\s\',!]/', '', $value);

	preg_match_all('/[\s\',.!]/', $code, $separators);

	$valRegex = '/([0-9].*|)[0-9]/';

	$thousand = thousand( $separators );

	$decimal = decimal( $separators );

	preg_match($valRegex, $code, $valFormat);

	$valFormat = ( !empty( $valFormat ) ? $valFormat['0'] : 0 );

	$decimals = $decimal ? strlen(substr(strrchr($valFormat, $decimal), 1)) : 0;

	// Do we have a negative value?
	if ( $negative = $value < 0 ? '-' : '') {
		$value = $value * -1;
	}

	// Format the value
	$value = number_format($value, $decimals, $decimal, $thousand);

	if ( $cutZero ) 
		$value = preg_replace('/(\d+)(,|.)([0-9]{2})00/', "$1$2$3", $value);

	// Apply the formatted measurement
	if ( $include_symbol ) 
	{
		$value = preg_replace( $valRegex, $value, $code);
	}

	// Return value
	return $negative . $value;
}