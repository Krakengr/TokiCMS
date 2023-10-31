<?php defined('TOKICMS') or die('Hacking attempt...');

class Author extends Controller {
	
	private $user;
	private $page = 1;
	private $themeData  = [];
	
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
		
		if ( !$this->GetUser() )
		{
			$this->view();
			return;
		}
		
		$this->page    = ( ( Router::GetVariable( 'pageNum' ) > 0 ) ? Router::GetVariable( 'pageNum' ) : 1 );
		
		$dontLoadPosts = $this->getVariable( 'dontLoadPosts' );
	
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
		
		$cacheFile = CacheFileName( 'userposts-id_' . $this->user['id_member'], null, $this->lang['lang']['id'], null, $this->page, $numItems, $this->lang['lang']['code'] );
		
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
			
			$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $this->lang['lang']['id'] . ") AND (p.id_member = " . $this->user['id_member'] . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)";
			
			$query = PostsDefaultQuery( $q, $from . ', ' . $numItems, 'p.added_time DESC', 'p.id_post' );
			
			//Query: posts
			$tmp = $this->db->from( null, $query )->all();
			
			if ( empty( $tmp ) )
			{
				$log = Settings::LogSettings();
				
				if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
				{
					$errorMessage = 'User posts couldn\'t be fetched (User: ' . $this->user['user_name'] . ')';
					
					if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
					{
						$errorData = 'Query: ' . PHP_EOL . $query;
					}
				
					Log::Set( $errorMessage, $errorData, $param, 'system' );
				}
				
				$this->setVariable( 'Listings', null );
				return null;
			}
			
			// Query: total
			$this->items = $this->db->from( null, "SELECT count(p.id_post) as total FROM `" . DB_PREFIX . POSTS . "` as p LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post WHERE 1=1 AND " . $q )->total();
			
			$data = array(
				'posts' 		=> array(),
				'totalItems' 	=> $this->items
			);
			
			$i = 0;
			
			foreach ( $tmp as $p )
			{				
				$data['posts'][$i] = BuildPostVars( $p );
				
				$i++;
			}
			
			WriteOtherCacheFile( $data, $cacheFile );
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
			
			unset( $data, $numItems );
		}
		
		$this->setVariable( 'Listings', $posts );
	
		unset( $posts );
	}

	private function GetUser()
	{
		$this->user = GetUserDetails( Router::GetVariable( 'authorKey' ), null, $this->lang['lang']['code'] );
		
		if ( !$this->user )
		{
			Router::SetNotFound();
			$this->setVariable( 'WhereAmI', '404' );
			return false;
		}
		
		$this->themeData = array(
			'userId' 			=> $this->user['id_member'],
			'userGroupId' 		=> $this->user['id_group'],
			'userName' 			=> $this->user['real_name'],
			'userBio' 			=> $this->user['user_bio'],
			'userSocial' 		=> $this->user['social_data']
		);

		return true;
	}
}