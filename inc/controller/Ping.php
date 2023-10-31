<?php defined('TOKICMS') or die('Hacking attempt...');

class Ping extends Controller {
	
	private $action;
	private $type;
	private $key;
	
    public function process() 
	{	
		$this->setVariable( 'Lang', $this->lang );
		
		$res = array();

		//Check the needed parameters
		if ( !isset( $_GET['token'] ) || !isset( $_GET['action'] ) )
		{
			Log::Set( 'Bad Ping Request', 'Missing GET parameter(s)', $_GET, 'system' );
			$this->Response( 400, 'Bad Request', array( 'message' => 'Missing GET parameter(s).' ) );
		}
		
		$token 			= Sanitize( $_GET['token'], false );
		$this->action 	= Sanitize( $_GET['action'], false );
		$this->type 	= ( isset( $_GET['type'] ) ? Sanitize( $_GET['type'], false ) : null );
		$this->key 		= ( isset( $_GET['key'] ) ? Sanitize( $_GET['key'], false ) : null );

		//Check the TOKEN
		if ( !hash_equals( $token, MAIN_HASH ) )
		{
			Log::Set( 'Unauthorized Ping Request', 'Invalid token:' . $token, $_GET, 'system' );
			$this->Response( 401, 'Unauthorized', array( 'message' => 'Invalid token.' ) );
		}
		
		switch ( $this->action )
		{
			//Rebuild the sitemap files
			case 'build-sitemap':
				BuildSitemap();
				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
			break;
			
			//Delete the "indexnow" hash file
			case 'delete-indexnow-file':
				$sitemapData = Settings::Sitemap();
				
				if ( isset( $sitemapData['indexnow_key'] ) && !empty( $sitemapData['indexnow_key'] ) )
				{
					$file = ROOT . $sitemapData['indexnow_key'] . '.txt';

					if ( file_exists( $file ) )
					{
						@unlink( $file );
					}
				}

				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
			break;
			
			//Check and create the "indexnow" hash file if needed
			case 'check-indexnow-file':
				$sitemapData = Settings::Sitemap();
				
				if ( isset( $sitemapData['indexnow_key'] ) && !empty( $sitemapData['indexnow_key'] ) )
				{
					$indexNowKey = $sitemapData['indexnow_key'];

					$file = ROOT . $indexNowKey . '.txt';

					if ( !file_exists( $file ) )
					{
						file_put_contents( $file, $indexNowKey, LOCK_EX );
					}
				}

				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
			break;
			
			//Update Site's stats
			case 'update-stats':
				UpdateSiteStats();
				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
			break;
			
			//Get Server Load
			case 'server-load':
				$load = GetServerLoad();
				
				if ( empty( $load ) )
				{
					$message = 'CPU load not estimateable (maybe missing rights at Linux or Windows).';
				}
				else
				{
					$message = $load;
				}
				
				$this->Response( 200, 'OK', array( 'message' => $message ) );
			break;
			
			//Delete single file cache
			case 'delete-post-cache':
				
				if ( empty( $this->key ) || !is_numeric( $this->key ) )
				{
					$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );
				}
				
				$query = PostDefaultQuery( "(p.id_site = " . SITE_ID . ") AND (p.id_post = :id) AND (p.post_type = 'post' OR p.post_type = 'page')" );
			
				$binds = array( $this->key => ':id' );
			
				//Query: post
				$tmp = $this->db->from( null, $query, $binds )->single();
				
				if ( !$tmp )
				{
					$this->Response( 204, 'No Content', array( 'message' => 'No Content' ) );
				}
				
				$post = BuildFullPostVars( $tmp );
				
				//If this is the static home page, we have a different cache file
				if (  StaticHomePage( false, $post['id'] ) )
				{
					$cacheFile = PostCacheFile( 'post-id-' . $post['id'], null, $post['language']['key'] );
					
					if ( file_exists( $cacheFile ) )
						@unlink( $cacheFile );
				}
				
				else
				{
					$files = array(
						'cacheFile' 		=> PostCacheFile( $post['sef'], null, $post['language']['key'], null, Settings::Get()['theme'] ),
						'cacheFileAmp' 		=> PostCacheFile( $post['sef'], null, $post['language']['key'], true ),
						'cacheFileComm' 	=> PostCacheFile( $post['sef'], null, $post['language']['key'], null, Settings::Get()['theme'], false, true ),
						'cacheFileStatic' 	=> PostCacheFile( $post['sef'], null, $post['language']['key'], null, Settings::Get()['theme'], true )
					);
			
					foreach( $files as $id => $file )
					{
						if ( file_exists( $file ) )
							@unlink( $file );
					}
			
					//$cacheFileAmp 	= PostCacheFile( $post['sef'], null, $post['language']['key'], true );
					//$cacheFile 		= PostCacheFile( $post['sef'], null, $post['language']['key'], null, Settings::Get()['theme'] );
				}
				
				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
				
			break;
			
			//Theme Position
			case 'theme-position':
				$pos = null;
				
				if ( !empty( ThemeValue( 'widget-position' ) ) )
				{
					$pos = ( isset( ThemeValue( 'widget-position' )['0'] ) ? ThemeValue( 'widget-position' )['0'] : ThemeValue( 'widget-position' ) );
				}
				
				$this->Response( 200, 'OK', array( 'message' => 'Success', 'data' => json_encode( $pos ) ) );
				
			break;
			
			//Sync Image
			case 'sync':
				$this->SyncImage();
			break;
			
			//Delete Image
			case 'delete-image':
			
				if ( empty( $this->key ) || !isset( $_GET['time'] ) || !is_numeric( $_GET['time'] ) )
				{
					$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );
				}
				
				$filename = urldecode( $this->key );
				
				$file = FolderRootByDate( $_GET['time'] ) . $filename;
				
				if ( !file_exists( $file ) )
				{
					$this->Response( 204, 'No Content', array( 'message' => 'Nothing Found', 'url' => null ) );
				}

				@unlink( $file );

				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
			break;
			
			//Delete Images and its childs
			case 'delete-images':
			
				if ( empty( $this->key ) || !is_numeric( $this->key ) )
				{
					$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );
				}
				
				DeleteImage( $this->key );

				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
			break;
			
			//Delete Child Images Only
			case 'delete-childs':
			
				if ( !isset( $_GET['time'] ) || !is_numeric( $_GET['time'] ) || empty( $this->key ) || !is_numeric( $this->key ) )
				{
					$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );
				}
				
				$d = DeleteChildImages( $this->key, (int) $_GET['time'] );
				
				if ( isset( $d['ok'] ) )
					$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
				else
					$this->Response( (int) $d['num'], $d['title'], array( 'message' => $d['message'] ) );
			break;
			
			//Update Views
			case 'update-views':
				UpdatePostsViews();
				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
			break;
			
			//Get the themes
			case 'get-themes':
			
				if ( empty( $this->type ) )
					$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );
				
				$themes = GetThemes ( $this->type );
				$this->Response( 200, 'OK', array( 'message' => 'Success', 'data' => $themes ) );
			break;
			
			//Update "Update" Hash
			case 'update-hash-update':

				if ( !empty( UPDATE_HASH ) )
				{
					$this->db->update( 'sites' )->where( 'id', SITE_ID )->set( "update_hash", UPDATE_HASH );
				}

				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
			break;
			
			//Update Ping Slash
			case 'update-ping-slash':

				if ( !empty( PING_SLUG ) )
				{
					$this->db->update( 'sites' )->where( 'id', SITE_ID )->set( "ping_slash", PING_SLUG );
				}

				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
			break;
			
			//Check online status
			case 'check':
				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
			break;
			
			//Clean Cache(s)
			case 'clean-cache':
				$this->CleanCache();
			break;
			
			//Fully Clean Cache(s)
			case 'clean-caches':
				DelFullCache();
				$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
			break;
			
			//Clean datafile
			case 'clean-datafile':
				if ( empty( $this->key ) )
					$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );
				
				$file = DB_DATA_ROOT . $this->key . '.php';
				
				if ( file_exists( $file ) )
				{
					@unlink( $file );
					$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
				}
				
				$this->Response( 204, 'No Content', array( 'message' => 'Nothing Found', 'url' => null ) );
			break;

			default:
				$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );
		}
		
		exit;
	}
	
	//Clean Cache
	private function CleanCache()
	{
		if ( $this->type == 'all' )
		{
			DelCacheFiles( null, true );
		}
			
		elseif ( $this->type != 'post' )
		{
			DelCacheFiles( $this->type );
		}

		elseif ( ( $this->type == 'post' ) && isset( $_GET['key'] ) )
		{
			DelCacheFiles( null, true );

			$file = Sanitize( $_GET['key'], false );
			
			$cacheFile = PostCacheFile( $file );
			
			if ( file_exists( $cacheFile ) )
				@unlink( $cacheFile );
		}
		
		else
			$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );

		$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
	}
	
	//Sync Child Images By ID
	private function SyncImageById()
	{
		if ( empty( $this->key ) || !is_numeric( $this->key ) )
			$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );
		
		$imageId = (int) $this->key;

		//Let's get this image's info
		//Query: image
		$img = $this->db->from( null, "
		SELECT added_time, id_site, filename
		FROM `" . DB_PREFIX . "images`
		WHERE (id_image = :id)",
		array( $imageId => ':id' )
		)->single();
		
		if ( !$img )
		{
			$this->Response( 204, 'No Content', array( 'message' => 'Nothing Found', 'url' => null ) );
		}
		
		//Query: site
		$site = $this->db->from( null, "
		SELECT id, url
		FROM `" . DB_PREFIX . "sites`
		WHERE (is_primary = 1)"
		)->single();

		if ( !$site )
		{
			$this->Response( 204, 'No Content', array( 'message' => 'Nothing Found', 'url' => null ) );
		}
			
		$S = new Settings( $site['id'], false );
			
		$imageFolder = ( !empty( $S::Get()['images_html'] ) ? $S::Get()['images_html'] : $site['url'] . 'uploads/' );
		
		unset( $S );
		
		$targetRoot = FolderRootByDate( $img['added_time'] );
		
		$imageFolder = FolderRootByDate( $img['added_time'], $imageFolder );
		
		$imgs = $this->db->from( null, "
		SELECT filename
		FROM `" . DB_PREFIX . "images`
		WHERE (id_parent = " . $imageId . ")"
		)->all();

		if ( empty( $imgs ) )
		{
			$this->Response( 204, 'No Content', array( 'message' => 'Nothing Found', 'url' => null ) );
		}
		
		foreach ( $imgs as $img )
		{
			//Copy the file
			if ( !file_exists( $targetRoot . $img['filename'] ) ) 
			{
				CopyRemoteFile( $imageFolder . $img['filename'], $targetRoot . $img['filename'], true );
			}
		}
		
		$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
	}

	//Sync Image
	private function SyncImage()
	{
		//Maybe we want to copy an image by its ID
		if ( !empty( $this->key ) )
		{
			$this->SyncImageById();
			return;
		}

		if ( !isset( $_GET['url'] ) || !isset( $_GET['time'] ) || !is_numeric( $_GET['time'] ) )
			$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );
		
		$imageUri = urldecode( $_GET['url'] );
		
		$info = pathinfo( $imageUri );
		
		if ( empty( $info ) )
			$this->Response( 204, 'No Content', array( 'message' => 'Image URL Error', 'url' => null ) );
		
		$allowed = AllowedExt();
		
		//Make sure we allow the extension
		if ( empty( $allowed ) || empty( $info['extension'] ) || ( !empty( $info['extension'] ) && !in_array( $info['extension'], $allowed ) ) )
			$this->Response( 403, 'Forbidden', array( 'message' => 'Image extension is now allowed', 'url' => null ) );
		
		$folder = FolderRootByDate( $_GET['time'] );
	
		$url = FolderUrlByDate( $_GET['time'] );
		
		$filename = $info['filename'] . '.' . $info['extension'];
	
		$imgRoot = $folder . $filename;
		
		//Is this file in our folder?
		if ( file_exists( $imgRoot ) )
			$this->Response( 200, 'OK', array( 'message' => 'Success', 'url' => $url . $filename ) );
		
		//Copy the file
		$copy = CopyRemoteFile( $imageUri, $imgRoot, true );
		
		//Recheck for the file, it should be there
		if ( $copy && file_exists( $imgRoot ) && ( filesize( $imgRoot ) > 500 ) )
		{
			$this->Response( 200, 'OK', array( 'message' => 'Success', 'url' => $url . $filename ) );
		}
		
		else
		{
			@unlink( $imgRoot );
			$this->Response( 204, 'No Content', array( 'message' => 'Error: File could not be copied', 'url' => null ) );
		}
	}
	
	//Return the response
	private function Response( $code = 200, $message = 'OK', $data = array() )
	{
		header( 'HTTP/1.1 ' . $code . ' ' . $message );
		header( 'Access-Control-Allow-Origin: *' );//TODO
		header( 'Access-Control-Allow-Methods: GET ');
		header( 'Content-Type: application/json; charset=UTF-8' );
		
		echo json_encode( $data );
		
		exit;
	}
}