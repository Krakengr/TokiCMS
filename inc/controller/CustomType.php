<?php defined('TOKICMS') or die('Hacking attempt...');

class CustomType extends Controller {
	
	private $noAccess 	= false;
	private $typeKey 	= null;
	private $typeId   	= 0;
	private $page 	  	= 1;
	
    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		if ( !IsAllowedTo( 'view-site' ) )
		{
			//Don't include this file while on login or register
			if ( ( Router::WhereAmI() != 'login' ) || ( Router::WhereAmI() != 'register' ) )
				Router::SetIncludeFile( INC_ROOT . 'no-access.php' );

			$this->view();
			return;
		}
		
		$this->page    	= ( ( Router::GetVariable( 'pageNum' ) > 0 ) ? Router::GetVariable( 'pageNum' ) : 1 );
		$this->typeKey 	= Sanitize( Router::GetVariable( 'slug' ), false );
		$dontLoadPosts 	= $this->getVariable( 'dontLoadPosts' );
		
		if ( !$this->GetCustomType() )
		{
			Router::SetNotFound();
			$this->setVariable( 'WhereAmI', '404' );
			
			$this->view();
			return;
		}
		
		if ( $dontLoadPosts )
		{
			$this->setVariable( 'Listings', null );
		}
		
		else
		{
			$this->GetPosts();
		}
		
		$this->view();
	}
	
	private function GetPosts()
	{
		$numItems = ( !empty( HOMEPAGE_ITEMS ) ? HOMEPAGE_ITEMS : 20 );
		
		$cacheFile = CacheFileName( 'custom-type_' . $this->typeId, null, $this->lang['lang']['id'], null, $this->page, $numItems, $this->lang['lang']['code'] );
		
		//Get data from cache
		if ( ValidOtherCache( $cacheFile ) )
		{
			$data = ReadCache( $cacheFile );
			
			$this->items = $data['totalItems'];
		}
		
		//Get the data and save it to the cache, if needed...
		else
		{
			$xtra = "INNER JOIN `" . DB_PREFIX . "post_types_relationships` AS re ON re.post_id = p.id_post";
			
			$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $this->lang['lang']['id'] . ") AND (re.id_post_type = " . $this->typeId . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL) AND (b.disabled = 0 OR b.disabled IS NULL)";

			$query = PostsDefaultQuery( $q, $numItems, "p.added_time DESC", null, false, $xtra );

			//Query: posts
			$tmp = $this->db->from( null, $query )->all();

			if ( empty( $tmp ) )
			{
				$log = Settings::LogSettings();
				
				if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
				{
					$errorMessage 	= 'Custom Type posts couldn\'t be fetched (Custom Type: ' . $this->typeId . ')';
					$errorData 		= null;
					
					if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
					{
						$errorData = 'Query: ' . PHP_EOL . $query;
					}
				
					Log::Set( $errorMessage, $errorData, $query, 'system' );
				}
				
				$this->setVariable( 'Listings', null );
				
				return null;
			}
			
			$s = GetSettingsData( SITE_ID );
			
			if ( empty( $s ) )
			{
				return null;
			}
			
			// Query: total			
			$this->items = $this->db->from( null, "
			SELECT count(p.id_post) as total
			FROM `" . DB_PREFIX . POSTS . "` as p
			INNER JOIN `" . DB_PREFIX . "post_types_relationships` AS re ON re.post_id = p.id_post
			LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post
			LEFT JOIN  `" . DB_PREFIX . "blogs` 	 as b ON b.id_blog = p.id_blog
			LEFT JOIN  `" . DB_PREFIX . "categories` as c ON c.id = p.id_category
			WHERE " . $q )->total();
			
			$data = array(
				'posts' 		=> array(),
				'totalItems' 	=> $this->items
			);

			foreach ( $tmp as $p )
			{
				$p = array_merge( $p, $s );

				$data['posts'][] = BuildPostVars( $p );
			}

			WriteOtherCacheFile( $data, $cacheFile );
			
			unset( $tmp, $s );
		}
		
		if ( !empty( $data['posts'] ) )
		{
			$posts = BuildPosts( $data['posts'] );
			$this->setVariable( 'Items', $this->items );
			$this->setVariable( 'ItemsPerPage', $numItems );
			
			//Set this value to the header, we need it for next-previous tags
			Theme::SetVariable( 'totalItems', $this->items );
			
			//Also set the paginator so it can calculate the number of pages
			Paginator::SetVariable( 'currentPage', $this->page );
			Paginator::SetVariable( 'maxItemsPerPage', $numItems );
			Paginator::SetVariable( 'totalItems', $this->items );
			Paginator::Run();
		}
		
		$this->setVariable( 'Listings', $posts );
	
		unset( $posts, $data );
	}
	
	private function GetCustomType()
	{		
		$cacheFile = CacheFileName( 'custom-type_' . $this->typeKey, null, $this->lang['lang']['id'] );
		
		//Get data from cache
		if ( ValidCache( $cacheFile ) )
		{
			$data = ReadCache( $cacheFile );
		}
		
		else
		{
			//Query: custom type
			$tmp = $this->db->from( null, "
			SELECT *
			FROM `" . DB_PREFIX . "post_types`
			WHERE (sef = :sef) AND (id_site = " . SITE_ID . ")",
			array( $this->typeKey => ':sef' )
			)->single();

			if ( !$tmp )
			{
				$log = Settings::LogSettings();

				if ( !empty( $log ) && $log['enable_error_log'] && $log['enable_not_found_log'] )
				{
					$query = "SELECT * FROM `" . DB_PREFIX . "post_types` WHERE (sef = '" . $this->typeKey . "') AND (id_site = " . SITE_ID . ")";
					
					$errorData = null;
					
					$errorMessage = 'Custom Post Type couldn\'t be found (Key: ' . $this->typeKey . ')';

					if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
					{
						$errorData = 'Query: ' . PHP_EOL . $query;
					}
					
					Log::Set( $errorMessage, $errorData, $query, 'system' );
				}
			
				return false;
			}
			
			$data = array(
				'id'			=> $tmp['id'],
				'title'		 	=> StripContent( $tmp['title'] ),
				'description' 	=> StripContent( $tmp['description'] ),
				'trans_data'  	=> Json( $tmp['trans_data'] ),
				'image' 	 	=> ( !empty( $tmp['id_image'] ) ? PostImageDetails( $tmp['id_image'], $this->lang['lang']['code'], true ) : null )
			);

			if ( !empty( $data['trans_data'] ) && isset( $data['trans_data'][$this->lang['lang']['code']] ) )
			{
				$data['title'] 		 = StripContent( $data['trans_data'][$this->lang['lang']['code']]['title'] );
				$data['description'] = StripContent( $data['trans_data'][$this->lang['lang']['code']]['description'] );
			}
			
			WriteCacheFile( $data, $cacheFile );
		}

		$this->typeId = $data['id'];
		
		$this->setVariable( 'CustomType', $data );
		
		$this->themeData = array(
			'customTypeTitle' 		=> $data['title'],
			'customTypeSef'			=> $this->typeKey,
			'customTypeDescription' => $data['description']
		);

		return true;
	}
}