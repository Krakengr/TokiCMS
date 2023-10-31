<?php defined('TOKICMS') or die('Hacking attempt...');

class Tag extends Controller {
	
	private $page 	    = 1;
	private $tagKey 	= null;
	private $themeData  = [];
	private $tag		= null;
	
    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		if ( !IsAllowedTo( 'view-site' ) )
		{
			//Don't include this file while on login or register
			if ( Router::WhereAmI() != 'login' )
				Router::SetIncludeFile( INC_ROOT . 'no-access.php' );

			$this->view();
			return;
		}
		
		if ( !$this->GetTag() )
		{
			$this->view();
			return;
		}
		
		$dontLoadPosts = $this->getVariable( 'dontLoadPosts' );
		
		$this->page    = ( ( Router::GetVariable( 'pageNum' ) > 0 ) ? Router::GetVariable( 'pageNum' ) : 1 );

		if ( $dontLoadPosts )
		{
			$this->setVariable( 'Listings', null );
		}
		
		else
		{
			$this->GetPosts();
		}
		
		Theme::SetVariable( 'data', $this->themeData );
		Theme::Build();
		
		$this->view();
	}
	
	private function GetPosts()
	{
		$numItems = ( !empty( HOMEPAGE_ITEMS ) ? HOMEPAGE_ITEMS : 20 );
		
		$cacheFile = CacheFileName( 'tag_' . $this->tagKey, null, $this->lang['lang']['id'], null, $this->page, $numItems, $this->lang['lang']['code'] );
		
		//Get the data from the cache, if is valid
		if ( ValidOtherCache( $cacheFile ) )
		{
			$data = ReadCache( $cacheFile );
			
			$this->items = $data['totalItems'];
		}
		
		//Get the data and save it to the cache, if needed...
		else
		{
			$from = ( ( $this->page * $numItems ) - $numItems );
			
			$xtra = "INNER JOIN `" . DB_PREFIX . "tags_relationships` AS re ON re.object_id = p.id_post";
			
			$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $this->lang['lang']['id'] . ") AND (re.taxonomy_id = " . $this->tag['id'] . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL) AND (b.disabled = 0 OR b.disabled IS NULL)";
			
			$query = PostsDefaultQuery( $q, $numItems, "p.added_time DESC", null, false, $xtra );

			//Query: posts
			$tmp = $this->db->from( null, $query )->all();
			
			
			if ( empty( $tmp ) )
			{
				$log = Settings::LogSettings();
				
				if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
				{
					$errorMessage = 'Tag posts couldn\'t be fetched (Key: ' . $this->tagKey . ')';
					
					if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
					{
						$errorData = 'Query: ' . PHP_EOL . $query;
					}
				
					Log::Set( $errorMessage, $errorData, $param, 'system' );
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
			INNER JOIN  `" . DB_PREFIX . "tags_relationships` AS re ON re.object_id = p.id_post
			LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post
			LEFT JOIN  `" . DB_PREFIX . "blogs` 	 as b ON b.id_blog = p.id_blog
			LEFT JOIN  `" . DB_PREFIX . "categories` as c ON c.id = p.id_category
			WHERE " . $q
			)->total();

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
		
		$posts = null;
		
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
	
	private function GetTag()
	{
		global $Tag;
		
		$this->tagKey = Sanitize( Router::GetVariable( 'tagKey' ), false );
		
		$this->tag = $Tag = GetTag( $this->tagKey, null, SITE_ID, $this->lang['lang']['id'], $this->lang['lang']['code'] );

		if ( empty( $this->tag ) )
		{
			Router::SetNotFound();
			$this->setVariable( 'WhereAmI', '404' );
			return false;
		}
		
		$this->themeData = array(
			'tagName' 			=> $this->tag['title'],
			'tagSef'			=> $this->tag['sef'],
			'tagDescription' 	=> $this->tag['descr'],
			'tagFilter'			=> $this->tag['tags_filter'],
			'tagUrl'			=> $this->tag['tagUrl']
		);
		
		return true;
	}
}