<?php defined('TOKICMS') or die('Hacking attempt...');

class Search extends Controller {
	
	private $searchTerm;
	private $page  = 1;
	private $items = 0;
	
    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		if ( !IsAllowedTo( 'view-site' ) || !IsAllowedTo( 'search-posts' ) )
		{
			//Don't include this file while on login or register
			if ( !IsAllowedTo( 'view-site' ) )
			{
				if ( Router::WhereAmI() != 'login' )
					Router::SetIncludeFile( INC_ROOT . 'no-access.php' );
			}
			
			elseif ( !IsAllowedTo( 'search-posts' ) )
			{
				Router::SetNotFound();
				$this->setVariable( 'WhereAmI', '404' );
			}

			$this->view();
			return;
		}
		
		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || !isset( $_POST['s'] ) )
		{
			$url = Router::GetVariable( 'siteRealUrl' );

			@header('Location: ' . $url );
			exit;
		}
		
		$this->searchTerm = Sanitize( $_POST['s'], false );
		$this->page    = ( ( Router::GetVariable( 'pageNum' ) > 0 ) ? Router::GetVariable( 'pageNum' ) : 1 );
		
		$data = $this->GetPosts();

		$this->setVariable( 'Items', $this->items );
		$this->setVariable( 'ItemsPerPage', HOMEPAGE_ITEMS );
		$this->setVariable( 'Listings', ( !$data ? null : $data ) );
		
		Paginator::SetVariable( 'currentPage', $this->page );
		Paginator::SetVariable( 'maxItemsPerPage', HOMEPAGE_ITEMS );
		
		//Set the pages to paginator
		Paginator::SetVariable( 'totalItems', $this->items );

		//Calculate the pages
		Paginator::Run();
		
		Theme::SetVariable( 'totalItems', $this->items );
		
		Theme::Build();

		unset( $data );

		$this->view();
	}
	
	private function GetPosts()
	{
		$from = ( ( $this->page * HOMEPAGE_ITEMS ) - HOMEPAGE_ITEMS );
		
		$search = '%' . $this->searchTerm . '%';

		$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $this->lang['lang']['id'] . ") AND (p.title LIKE '" . $search . "' OR p.post LIKE '" . $search . "') AND (p.post_type = 'post') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL) AND (b.disabled = 0 OR b.disabled IS NULL)";

		$query = PostsDefaultQuery( $q, $from . ', ' . HOMEPAGE_ITEMS, 'p.added_time DESC', null, false );

		//Query: posts
		$tmp = $this->db->from( null, $query )->all();
			
		if ( empty( $tmp ) )
		{
			$log = Settings::LogSettings();
				
			if ( !empty( $log ) && $log['enable_error_log'] && $log['enable_not_found_log'] )
			{
				$errorMessage = 'Search results couldn\'t be fetched (Term: "' . $this->searchTerm . '")';
					
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
		LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post
		LEFT JOIN  `" . DB_PREFIX . "blogs` 	 as b ON b.id_blog = p.id_blog
		LEFT JOIN  `" . DB_PREFIX . "categories` as c ON c.id = p.id_category
		WHERE " . $q
		)->total();
			
		$i = 0;
		
		$data = array();
		
		foreach ( $tmp as $p )
		{
			$p = array_merge( $p, $s );
			
			$data[$i] = BuildPostVars( $p );
		
			$i++;
		}
		
		return BuildPosts( $data );
	}
}