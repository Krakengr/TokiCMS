<?php defined('TOKICMS') or die('Hacking attempt...');

//Check user's password
function CheckUserPass( $user, $pass )
{
	if ( $pass === '' )
		return false;
		
	if ( !$user )
		return false;
	
	$db = db();
	
	$userPass = sha1( $pass . $user['password_hash'] );
	
	//TODO: Check this option
	//$check = $db->select( 'id_member' )->from( USERS )->where( 'user_name', $user['user_name'] )->where( 'passwd', $userPass )->where( 'id_site', SITE_ID )->first();

	//Query: member
	$x = $db->from( null, "
	SELECT id_member
	FROM `" . DB_PREFIX . USERS . "`
	WHERE (user_name = :name) AND (passwd = '" . $userPass . "') AND (id_site = " . SITE_ID . ")",
	array( $user['user_name'] => ':name' )
	)->single();
	
	if ( !empty( $x ) )
		return true;
	else
		return false;
}

//Search for a user by its username
function UserByName( $name )
{
	if ( $name === '' )
		return false;
	
	$db = db();
	
	$query = "SELECT id_member, id_group, user_name, num_posts, date_registered, real_name, last_login, password_hash, is_activated, passwd_flood, email_address
	FROM `" . DB_PREFIX . USERS . "`
	WHERE (user_name = :name)";

	//Query: member
	$q = $db->from( null, $query, array( $name => ':name' ) )->single();

	if ( empty( $q ) )
	{
		return false;
	}
	
	return $q;
}

//Search for a user by its email
function UserByEmail( $email )
{
	if ( $email === '' )
		return false;
	
	$db = db();
	
	$query = "SELECT id_member, id_group, user_name, num_posts, date_registered, real_name, last_login, password_hash, is_activated, passwd_flood, email_address
	FROM `" . DB_PREFIX . USERS . "`
	WHERE (email_address = :email)";

	//Query: member
	$q = $db->from( null, $query, array( $email => ':email' ) )->single();

	if ( empty( $q ) )
	{
		return false;
	}
	
	return $q;
}

#####################################################
#
# Get User Details function
#
#####################################################
function GetUserDetails( $key = null, $id = null, $langCode = null, $cache = true )
{
	$db = db();
	
	$langCode = ( $langCode ? $langCode : CurrentLang()['lang']['code'] );
	
	$cacheFile = CacheFileName( 'user_data-' . ( $key ? 'key_' . $key : 'id_' . $id ), null, null, null, null, null, $langCode );

	//Get data from cache
	if ( $cache && ValidCache( $cacheFile ) )
	{
		$q = ReadCache( $cacheFile );
	}
	else
	{
		$db = db();

		$query = "SELECT * FROM `" . DB_PREFIX . USERS . "` WHERE (" . ( $key ? "user_name = :sef" : "id_member = :id" ) . ")" . ( $key ? " AND (id_site = " . SITE_ID . ") AND (is_activated = 1)" : "" );
		
		if ( $key )
		{
			$binds = array( $key => ':sef' );
		}
		else
		{
			$binds = array( $id => ':id' );
		}

		//Query: member
		$q = $db->from( null, $query, $binds )->single();

		if ( empty( $q ) )
			return null;
			
		$q['user_bio'] = CreatePostContent( StripContent( $q['user_bio'] ) );
	
		if ( !empty( $q['social_data'] ) )
		{
			$sc = Json( $q['social_data'] );
			
			if ( !empty( $sc ) && isset( $sc[$langCode] ) )
			{
				$s = $sc[$langCode];
				
				//Set the translated values
				$q['social_data'] = ( !empty( $s ) ? $s : $q['social_data'] );
			}
		}
		
		if ( !empty( $q['image_data'] ) )
		{
			$q['imageData'] = Json( $q['image_data'] );
		}
		
		$q['userUrl'] = BuildUserUrl( $q['user_name'], $langCode );
	
		if ( !empty( $q['trans_data'] ) )
		{
			$tr = Json( $q['trans_data'] );
			
			$q['trans'] = $tr;
			
			if ( !empty( $tr ) && isset( $tr[$langCode] ) )
			{
				$t = $tr[$langCode];
				
				//Set the translated values
				$q['real_name'] = ( !empty( $t['name'] ) ? $t['name'] : $q['real_name'] );
				$q['user_bio'] = ( !empty( $t['bio'] ) ? CreatePostContent( $t['bio'] ) : $q['user_bio'] );
			}
		}
		else
		{
			$q['trans'] = null;
		}
		
		WriteCacheFile( $q, $cacheFile );
	}
	
	return $q;
}

#####################################################
#
# Build User Url function
#
#####################################################
function BuildUserUrl( $key, $langCode )
{
	$url = SITE_URL;
		
	$defaultLangCode = Settings::LangData()['lang']['code'];
		
	$slugTrans = Settings::Trans();

	if ( MULTILANG && !empty( $langCode ) && ( !Settings::IsTrue( 'hide_default_lang_slug' ) || ( Settings::IsTrue( 'hide_default_lang_slug' ) && ( $langCode != $defaultLangCode ) ) ) )
	{
		$url .= $langCode . PS;
	}
		
	$url .= 'author' . PS;

	$url .= $key . PS;

	return $url;
}
