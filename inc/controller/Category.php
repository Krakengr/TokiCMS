<?php defined('TOKICMS') or die('Hacking attempt...');

class Category extends Controller {
	
	private $noAccess   = false;
	private $page 	    = 1;
	private $blogId 	= 0;
	private $themeData  = [];
	private $blog 		= null;
	private $blogKey	= null;
	private $category	= null;
	private $catKey		= null;
	private $subCat		= null;
	private $subKey		= null;
	private $blogName	= null;
	private $blogDescr	= null;
	private $blogSlogan	= null;
	
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

		if ( !$this->GetCategory() )
		{
			if ( !$this->noAccess )
			{
				Router::SetNotFound();
				$this->setVariable( 'WhereAmI', '404' );
			}
			else
			{
				Router::SetIncludeFile( INC_ROOT . 'no-access.php' );
				Router::SetVariable( 'accessDenied', true );
			}
			
			$this->setVariable( 'Listings', null );
			
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
		$table = ( Router::GetVariable( 'isBlog' ) ? $this->blog['tables'] : null );
		
		$table = ( empty( $table ) ? $this->category['tables'] : $table );
		
		$numItems = ( ( !empty( $table ) && !empty( $table['form_data']['max_items'] ) ) ? $table['form_data']['max_items'] : ( !empty( $this->category['postLimit'] ) ? $this->category['postLimit'] : ( !empty( HOMEPAGE_ITEMS ) ? HOMEPAGE_ITEMS : 20 ) ) );

		$cacheFile = CacheFileName( ( Router::GetVariable( 'isSubCat' ) ? 'subcategory_' . $this->subKey : 'category_' . $this->catKey ) . '-posts', null, $this->lang['lang']['id'], ( Router::GetVariable( 'isBlog' ) ? $this->blogId : null ), $this->page, $numItems, $this->lang['lang']['code'] );
		
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
			
			$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $this->lang['lang']['id'] . ") AND (p.id_blog = " . $this->blogId . ") AND (p." . ( Router::GetVariable( 'isSubCat' ) ? "id_sub_category = " . $this->subCat['id'] : "id_category = " . $this->category['id'] ) . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)";
			
			$query = PostsDefaultQuery( $q, $from . ', ' . $numItems, 'p.added_time DESC' );
			
			//Query: posts
			$tmp = $this->db->from( null, $query )->all();
			
			if ( empty( $tmp ) )
			{
				$log = Settings::LogSettings();
				
				if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
				{
					$errorMessage = ( Router::GetVariable( 'isSubCat' ) ? 'Sub' : '' ) . 'Category posts couldn\'t be fetched (Key: ' . ( Router::GetVariable( 'isSubCat' ) ? $this->subKey : $this->catKey ) . ')';
					
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
				'tableHtml'		=> null,
				'totalItems' 	=> $this->items
			);
			
			$i = 0;
			
			foreach ( $tmp as $p )
			{				
				$data['posts'][$i] = BuildPostVars( $p );
				
				//To build the tables, we have to ask for more data
				if ( !empty( $table ) )
				{
					$data['posts'][$i]['attributes'] = GetPostAttributes( $p['id_post'] );
					$data['posts'][$i]['pricesData'] = GetPricesData( $p['id_post'], 'normal', true, $p );
					$data['posts'][$i]['dealsData']  = GetPricesData( $p['id_post'], 'coupon', true, $p );
				}

				unset( $p );
				
				$i++;
			}
			
			if ( !empty( $table ) )
			{
				$data['tableHtml'] = BuildHtmlTable( $table, $data['posts'] );
			}
			
			WriteOtherCacheFile( $data, $cacheFile );
		}
		
		$posts = null;
		
		if ( !empty( $data['posts'] ) )
		{
			$posts = BuildPosts( $data['posts'] );
			$this->setVariable( 'TableHtml', $data['tableHtml'] );
			$this->setVariable( 'Items', $this->items );
			$this->setVariable( 'ItemsPerPage', $numItems );
			$this->setVariable( 'Table', ( !empty( $table ) ? $table : null ) );
			
			//Set this value to the header, we need it for next-previous tags
			Theme::SetVariable( 'totalItems', $this->items );
			
			//Also set the paginator so it can calculate the number of pages
			Paginator::SetVariable( 'currentPage', $this->page );
			Paginator::SetVariable( 'maxItemsPerPage', $numItems );
			Paginator::SetVariable( 'totalItems', $this->items );
			Paginator::Run();
			
			unset( $data, $table, $numItems );
		}
		
		$this->setVariable( 'Listings', $posts );
	
		unset( $posts );
	}
	
	private function GetCategory()
	{
		//Make the data global
		global $Category, $Blog;
		
		if ( Router::GetVariable( 'isBlog' ) && !$this->GetBlog() )
		{
			return false;
		}
		
		$this->catKey = Sanitize( Router::GetVariable( 'categoryKey' ), false );
		
		$this->category = $Category = GetCategory( $this->catKey, null, SITE_ID, $this->lang['lang']['id'], ( Router::GetVariable( 'isBlog' ) ? $this->blog['id_blog'] : null ), $this->lang['lang']['code'] );
		
		if ( empty( $this->category ) )
		{
			return false;
		}
		
		if ( !IsAllowedTo( 'admin-site' ) && !empty( $this->category['groups'] ) )
		{
			$UserGroup = UserGroup();

			if ( !in_array( $UserGroup, $this->category['groups'] ) )
			{
				$this->noAccess = true;
				return false;
			}
		}
		
		if ( Router::GetVariable( 'isSubCat' ) && !$this->GetSubCategory() )
		{
			return false;
		}
		
		$this->setVariable( 'Category', $this->category );
		
		$arr = array(
			'categoryId'  	=> $this->category['id'],
			'categoryName'  => $this->category['name'],
			'categoryDescr' => $this->category['descr'],
			'categoryUrl'   => $this->category['catUrl']
		);

		$this->themeData = array_merge( $this->themeData, $arr );

		return true;
	}
	
	private function GetSubCategory()
	{
		//Make SubCategory data global
		global $SubCategory;
		
		$this->subKey = Sanitize( Router::GetVariable( 'subCategoryKey' ), false );
		
		$this->subCat = $SubCategory = GetSubCategory( $this->subKey, null, SITE_ID, $this->lang['lang']['id'], ( Router::GetVariable( 'isBlog' ) ? $this->blog['id_blog'] : null ), $this->lang['lang']['code'] );
		
		if ( empty( $this->subCat ) )
		{
			return false;
		}
		
		if ( !IsAllowedTo( 'admin-site' ) && !empty( $this->subCat['groups'] ) )
		{
			$UserGroup = UserGroup();

			if ( !in_array( $UserGroup, $this->subCat['groups'] ) )
			{
				$this->noAccess = true;
				return false;
			}
		}
		
		$this->setVariable( 'SubCategory', $this->subCat );
		
		$arr = array(
			'subCategoryId'    => $this->subCat['id'],
			'subCategoryName'  => $this->subCat['name'],
			'subCategoryDescr' => $this->subCat['descr'],
			'subCategoryUrl'   => $this->subCat['catUrl']
		);
		
		$this->themeData = array_merge( $this->themeData, $arr );
		
		return true;
	}
	
	private function GetBlog()
	{
		if ( !Router::GetVariable( 'isBlog' ) )
			return;
		
		$this->blogKey 	= Sanitize( Router::GetVariable( 'blogKey' ), false );
		
		$this->blog 	= GetBlog( $this->blogKey, null, SITE_ID, $this->lang['lang']['id'] );

		if ( empty( $this->blog ) )
		{
			$log = Settings::LogSettings();
				
			if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
			{
				$errorMessage = 'Blog data couldn\'t be fetched (Key: ' . $this->blogKey . ', Lang: ' . $this->lang['lang']['id'] . ')';

				Log::Set( $errorMessage, $errorData, $param, 'system' );
			}
				
			return false;
		}		
		
		//Check if we have selected to redirect this whole blog
		if ( !empty( $this->blog['redirect'] ) )
		{
			@header("Location: " . $this->blog['redirect'], true, 301 );
			@exit;
		}
		
		if ( !empty( $this->blog['groups'] ) )
		{
			if ( !IsAllowedTo( 'admin-site' ) && !in_array( $this->userGroup, $this->blog['groups'] ) )
			{
				$this->noAccess = true;
				return false;
			}
		}
		
		//If this blog has translation data, set its correct values
		if ( !empty( $this->blog['trans'] ) && !empty( $this->blog['trans'][$this->lang['lang']['code']] ) )
		{
			$this->blogName   = $Blog['name'] 		 = $this->blog['trans'][$this->lang['lang']['code']]['name'];
			$this->blogDescr  = $Blog['description'] = $this->blog['trans'][$this->lang['lang']['code']]['description'];
			$this->blogSlogan = $Blog['slogan'] 	 = $this->blog['trans'][$this->lang['lang']['code']]['slogan'];
		}
		
		else
		{
			$this->blogName   = $this->blog['name'];
			$this->blogDescr  = $this->blog['description'];
			$this->blogSlogan = $this->blog['slogan'];
		}
		
		$this->blogId = $this->blog['id_blog'];
		
		$this->setVariable( 'Blog', $this->blog );
		
		$this->themeData = array(
			'blogId' 			=> $this->blog['id_blog'],
			'blogName'			=> $this->blogName,
			'blogDescription'	=> $this->blogDescr,
			'blogSlogan'		=> $this->blogSlogan,
			'blogSef'			=> $this->blog['sef'],
			'blogPosts'			=> $this->blog['num_posts'],
			'blogComments'		=> $this->blog['num_comments'],
			'blogUrl'			=> $this->BlogUrl()
		);
		
		return true;
	}
	
	#####################################################
	#
	# Builds the slug based on language settings function
	#
	#####################################################
	private function BlogUrl()
	{
		$url = SITE_URL;

		$defaultLang = Settings::LangData()['lang']['code'];
		$langCode 	 = $this->lang['lang']['code'];
		
		//Add the lang slug
		if ( MULTILANG && ( !Settings::IsTrue( 'hide_default_lang_slug' ) || 
		( Settings::IsTrue( 'hide_default_lang_slug' ) && ( $langCode != $defaultLang ) ) ) )
		{
			$url .= $langCode . PS;
		}
		
		$url .= $this->blog['sef'] . PS;

		return $url;
	}
}