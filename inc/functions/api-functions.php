<?php defined('TOKICMS') or die('Hacking attempt...');

//
// API and remote related Functions
//

#####################################################
#
# Preload function
#
#####################################################
function Preload( $id, $siteId )
{
	$Settings = new Settings( $siteId, false );
	
	if ( !$Settings || !$Settings::IsTrue( 'enable_preloading' ) )
	{
		return;
	}
	
	$db = db();
	
	$p = GetSinglePost( $id, $siteId, false );
	
	if ( !$p || ( $p['postStatus'] !== 'published' ) )
		return;
		
	//Build the homepage files first
	PreloadSite( $siteId );
		
	//Build this post's cache
	PreloadSite( null, $p['postUrl'] );
}

#####################################################
#
# Ping A URL to update the cache file function
#
#####################################################
function PreloadSite( $id = null, $url = null )
{
	if ( !$id && !$url )
		return;
		
	$pingUrl = null;
	
	if ( $id )
	{
		$db 	= db();
		
		$site 	= $db->from( 
		null, 
		"SELECT url
		FROM `" . DB_PREFIX . "sites`
		WHERE (id = " . (int) $id . ")"
		)->single();
			
		if ( $site )
		{
			$pingUrl = $site['url'];
		}
	}
		
	else
	{
		$pingUrl = $url;
	}
		
	if ( empty( $pingUrl ) )
		return;

	return PingSite( $pingUrl );
}
	
#####################################################
#
# Ping Child Site function
#
#####################################################
function PingChildSite( $action, $type = null, $key = null, $siteId = null, $url = null, $time = null )
{
	if ( !$siteId )
		return;

	//Don't continue if this is the parent site
	if ( $siteId == SITE_ID )
		return;
	
	$db 	= db();
		
	$site 	= $db->from( 
	null, 
	"SELECT url, site_secret, site_ping_url, ping_slash
	FROM `" . DB_PREFIX . "sites`
	WHERE (id = " . (int) $siteId . ")"
	)->single();
	
	if ( !$site )
	{
		return;
	}

	$pingUrl  = ( !empty( $site['site_ping_url'] ) ? $site['site_ping_url'] : $site['url'] . $site['ping_slash'] . PS );
		
	$pingUrl .= '?token=' . $site['site_secret'] . '&action=' . $action;
		
	$pingUrl .= ( $type ? '&type=' . $type : '' );
		
	$pingUrl .= ( $key ? '&key=' . $key : '' );

	$pingUrl .= ( $url ? '&url=' . urlencode( $url ) : '' );
		
	$pingUrl .= ( $time ? '&time=' . $time : '' );

	return PingSite( $pingUrl );
}

#####################################################
#
# Update The Remote Post function
#
#####################################################
function AddNewRemotePost( $param )
{
	if ( empty( $param ) )
		return;
	
	//Continue and load the site's settings
	$Settings = new Settings( $param['siteId'], false );

	$hosted = Json( $Settings::Site()['hosted'] );
	
	$host = ( isset( $hosted[$param['langCode']] ) ? $hosted[$param['langCode']] : 'self' );

	if ( $host == 'self' )
		return;

	$data = Json( $Settings::Get()['api_keys'] );
	
	if ( empty( $data ) || !isset( $data[$param['langCode']] ) )
		return;
	
	$data = $data[$param['langCode']];
	
	$post = GetSinglePost( $param['postId'], $param['siteId'], false );
	
	if ( !$post )
		return;
	
	$post = new Post( $post );

	if ( ( $host == 'blogger' ) && !empty( $data['blogger']['api'] ) && !empty( $data['blogger']['oath2'] ) )
	{
		$labels = array();
		
		if ( !$post->IsPage() )
		{
			array_push( $labels, $post->Category()->name );
			
			if ( !empty( $post->SubCategory() ) )
			{
				array_push( $labels, $post->SubCategory()->name );
			}
		}

		$putData = array(
			"kind" 			=> "blogger#post",
			"blog" 			=> array( "id" => $data['blogger']['blog-id'] ),
			"title" 		=> $post->Title(),
			"content" 		=> $post->Content(),
			"publishDate" 	=> $post->Added()->c,
			"labels"		=> $labels
		);

		$req = curl_init();
		
		curl_setopt_array($req, [
			CURLOPT_URL            => 'https://www.googleapis.com/blogger/v3/blogs/' . $data['blogger']['blog-id'] . '/posts/',
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_POSTFIELDS     => json_encode( $putData ),
			CURLOPT_HTTPHEADER     => [ 'Authorization: Bearer ' . $data['blogger']['oath2'], 'Accept: application/json', 'Content-Type: application/json' ],
			CURLOPT_RETURNTRANSFER => true,
		]);

		$response = curl_exec($req);
		
		if ( $response )
		{
			$response = json_decode( $response );
			
			if ( is_object( $response ) && isset( $response->error ) )
			{
				global $Admin;
				
				if ( !is_null( $Admin ) )
				{
					$Admin->SetErrorMessage( sprintf( __( 'blogger-oauth-2-error' ), $response->error->message ) );
				}
			}
			
			if ( is_object( $response ) && isset( $response->id ) )
			{
				UpdateExtPostData( $param['postId'], $response->id, $response->url );
			}
		}
	}
}

#####################################################
#
# Get Single Api function
#
#####################################################
function GetSingleApi( $id )
{
	$db 	= db();
	
	//Get the Api
	$tmp = $db->from( 
	null, 
	"SELECT id, is_primary, disabled, name, descr, allow_data
	FROM `" . DB_PREFIX . "api_obj`
	WHERE (id = " . (int) $id . ")"
	)->single();

	return $tmp;
}

#####################################################
#
# Update The Remote Post function
#
#####################################################
function UpdateRemotePost( $param )
{
	if ( empty( $param ) )
		return;
	
	//Continue and load the site's settings if needed
	$Settings 	= new Settings( $param['siteId'], false );

	$hosted 	= Json( $Settings::Site()['hosted'] );
	
	$host 		= ( isset( $hosted[$param['langCode']] ) ? $hosted[$param['langCode']] : 'self' );

	if ( $host == 'self' )
		return;

	$data = Json( $Settings::Get()['api_keys'] );
	
	if ( empty( $data ) || !isset( $data[$param['langCode']] ) )
		return;
	
	$data = $data[$param['langCode']];
	
	$db   = db();
	
	$post = GetSinglePost( $param['postId'], $param['siteId'], false );
	
	if ( !$post )
		return;
	
	$post = new Post( $post );

	$extId = $post->ExternalId();

	if ( empty( $extId ) )
	{
		AddNewRemotePost( $param );
		return;
	}
	
	if ( ( $host == 'blogger' ) && !empty( $data['blogger']['api'] ) && !empty( $data['blogger']['oath2'] ) )
	{
		$url = 'https://www.googleapis.com/blogger/v3/blogs/' . $data['blogger']['blog-id'] . '/posts/' . $extId . '?key=' . $data['blogger']['api'];
	
		$p = PingSite( $url, true, true );
		
		//Check if this is a valid post
		if ( empty( $p ) )
			return;
		
		$postUrl = $p['url'];
		
		$postUrl = preg_replace('/(.*)([0-9]{4})\/([0-9]{2})\/(.*)\.html/', "$1$2/$3/" . $post->PostSef() . ".html", $postUrl);
		
		$putData = array(
			"kind" 			=> "blogger#post",
			"id" 			=> $extId,
			"blog" 			=> array( "id" => $data['blogger']['blog-id'] ),
			"url"			=> $postUrl,
			"selfLink" 		=> 'https://www.googleapis.com/blogger/v3/blogs/' . $data['blogger']['blog-id'] . '/posts/' . $extId,
			"title" 		=> $post->Title(),
			"content" 		=> $post->Content(),
			"publishDate" 	=> $post->Added()->c,
		);

		$req = curl_init();
		
		curl_setopt_array($req, [
			CURLOPT_URL            => 'https://www.googleapis.com/blogger/v3/blogs/' . $data['blogger']['blog-id'] . '/posts/' . $extId,
			CURLOPT_CUSTOMREQUEST  => "PUT",
			CURLOPT_POSTFIELDS     => json_encode( $putData ),
			CURLOPT_HTTPHEADER     => [ 'Authorization: Bearer ' . $data['blogger']['oath2'], 'Accept: application/json', 'Content-Type: application/json' ],
			CURLOPT_RETURNTRANSFER => true,
		]);

		$response = curl_exec($req);

		if ( $response )
		{
			$response = json_decode( $response );

			if ( is_object( $response ) && isset( $response->error ) )
			{
				global $Admin;
				
				if ( !is_null( $Admin ) )
				{
					$Admin->SetErrorMessage( sprintf( __( 'blogger-oauth-2-error' ), $response->error->message ) );
				}
			}
			
			if ( is_object( $response ) && isset( $response->id ) )
			{
				UpdateExtPostData( $param['postId'], $response->id, $response->url );
			}
		}
	}
}

#####################################################
#
# Update External Post Data function
#
#####################################################
function UpdateExtPostData( $id, $extId, $url )
{
	$db   = db();
	
	$dbarr = array(
		"external_url" 	=> $url,
		"ext_id" 		=> $extId
	);
	
	$q = $db->update( 'posts_data' )->where( 'id_post', $id )->set( $dbarr );

	return $q;
}

#####################################################
#
# Pings a site/Url
#
#####################################################
function PingSite( $url, $get_data = true, $json = true, $get_response = false )
{
    if( $url == null )
		return false;
	
	$headers = array();

    $ch = curl_init( $url );  

    curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );  
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );  
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );  
	
	if ( $json )
	{
		$headers = array( "Accept: application/json" );

		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
    }
	
	if ( $get_response )
	{
		curl_setopt( $ch, CURLOPT_HEADERFUNCTION, function( $curl, $header ) use ( &$headers )
			{
				$len = strlen( $header );
				
				$header = explode(':', $header, 2);

				if ( count( $header ) < 2 )
					return $len;

				$headers[strtolower( trim( $header['0'] ) )][] = trim( $header['1'] );
				
				return $len;
			}
		);
	}
	
	$data = curl_exec( $ch );  
    
	$httpcode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );  

	curl_close( $ch );
	
	if ( $get_data )
	{
		if ( $json )
			return json_decode( $data, true );
		
		else
			return $data;
	}

	if ( $get_response )
	{
		$header = array(
				'code' 		=> $httpcode,
				'headers' 	=> $headers
		);
		
		return $header;
	}
    
	if( $httpcode >= 200 && $httpcode < 300 )
		return true;
	
	else
		return false;
}