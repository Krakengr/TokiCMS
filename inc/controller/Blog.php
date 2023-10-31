<?php defined('TOKICMS') or die('Hacking attempt...');

class Blog extends Controller {
	
	private $noAccess   = false;
	private $page 	    = 1;
	private $themeData  = null;
	private $blogName   = null;
	private $blogDescr  = null;
	private $blogSlogan = null;
	private $blog 		= null;
	private $blogKey	= null;
	private $blogId		= null;
	private $staticId	= null;

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

		if ( !$this->GetBlog() )
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
			
			$this->view();
			return;
		}
		
		$Post = null;
		
		if ( ( $this->blog['frontpage_shows'] == 'page' ) && !empty( $this->blog['frontpage_page'] ) )
		{
			$this->staticId = $this->blog['frontpage_page'];
			
			//Load the post directly
			$Post = $this->SinglePost();
		}
		
		if ( $Post )
		{
			//Check if we want Ads in post
			$ads = InPostAds( $Post->Content(), $Post->PostType() );//$this->InPostAds();
			$Post->SetContent( $ads );

			Router::SetWhereAmI( 'post' );
			Router::SetStaticBlogPage(); //Set the homepage as "static"
			$this->setVariable( 'Post', $Post );
			$this->setVariable( 'Listings', null );
			$this->setVariable( 'FrontPageStatic', true );
			
			//Keep this post's views into a file for now
			UpdateFilePostViews ( $Post->PostId() );

			//Make sure admins can't see the light theme
			if ( IsAllowedTo( 'view-lighter-version' ) && !$this->CheckAuth() )
			{
				Router::SetIncludeFile( INC_ROOT . 'light-theme.php' );
			}
			
			$theme = array(
				'postUrl'   		=> $Post->Url(),
				'parentId'  		=> $Post->ParentId(),
				'postId' 			=> $Post->PostId(),
				'postTitle'			=> $Post->Title(),
				'postDate'			=> $Post->Added()->raw,
				'postDateC'			=> $Post->Added()->c,
				'postDescription'	=> $Post->Description(),
				'isPage'			=> $Post->IsPage(),
				'postAuthor'		=> $Post->Author()->name,
				'postTranslations'  => $Post->Translations(),
				'languageKey'		=> $Post->Language()->key,
				'languageName'		=> $Post->Language()->name,
				'categoryUrl'		=> ( $Post->IsPage() ? null : $Post->Category()->url ),
				'categoryId'		=> ( $Post->IsPage() ? null : $Post->Category()->id ),
				'categoryName'		=> ( $Post->IsPage() ? null : $Post->Category()->name ),
				'hasCoverImage'		=> $Post->HasCoverImage(),
				'coverImage'		=> ( !empty( $Post->Cover()['default'] ) ? $Post->Cover()['default'] : null ),
				'blogId' 			=> $this->blog['id_blog'],
				'blogName'			=> $this->blogName,
				'blogDescription'	=> $this->blogDescr,
				'blogSlogan'		=> $this->blogSlogan,
				'blogSef'			=> $this->blog['sef'],
				'blogPosts'			=> $this->blog['num_posts'],
				'blogComments'		=> $this->blog['num_comments'],
				'blogUrl'			=> $this->BlogUrl()
			);
			
			Theme::SetVariable( 'data', $theme );
			
			unset( $theme, $Post );
		}

		else
		{
			$dontLoadPosts = $this->getVariable( 'dontLoadPosts' );
			$this->page    = ( ( Router::GetVariable( 'pageNum' ) > 0 ) ? Router::GetVariable( 'pageNum' ) : 1 );
			
			if ( !$dontLoadPosts && !$this->blog['dont_load_posts'] )
			{
				$this->GetPosts();
			}
			else
			{
				$this->setVariable( 'Listings', null );
			}
			
			Theme::SetVariable( 'data', $this->themeData );
		}
		
		Theme::SetVariable( 'enableRss', $this->blog['enable_rss'] );

		Theme::Build();
		
		$this->view();
	}
	
	private function SinglePost()
	{
		$cacheFile = PostCacheFile( 'post-id-' . $this->staticId, null, $this->lang['lang']['code'] );

		if ( ValidOtherCache( $cacheFile ) )
		{
			$data = ReadCache( $cacheFile );
			UpdateFilePostViews ( $data['id'] );
		}
		
		else
		{
			$data = GetSinglePost( $this->staticId, SITE_ID, false );
			
			if ( $data )
			{
				WriteOtherCacheFile( $data, $cacheFile, true );
			}
		}
		
		return new Post( $data );
	}
	
	private function GetBlog()
	{
		//Make blog data global
		global $Blog;
		
		$this->blogKey  = Sanitize( Router::GetVariable( 'blogKey' ), false );
		
		$this->blog 	= GetBlog( $this->blogKey, null, SITE_ID, $this->lang['lang']['id'] );

		if ( empty( $this->blog ) )
		{
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
			$this->blogName  			= $this->blog['trans'][$this->lang['lang']['code']]['name'];
			$this->blogDescr 			= $this->blog['trans'][$this->lang['lang']['code']]['description'];
			$this->blogSlogan 			= $this->blog['trans'][$this->lang['lang']['code']]['slogan'];
			
			$this->blog['name'] 		= $this->blogName;
			$this->blog['description'] 	= $this->blogDescr;
			$this->blog['slogan'] 	 	= $this->blogSlogan;
		}
		
		else
		{
			$this->blogName   = $this->blog['name'];
			$this->blogDescr  = $this->blog['description'];
			$this->blogSlogan = $this->blog['slogan'];
		}
		
		$Blog 			= $this->blog;
		$this->blogId 	= $this->blog['id_blog'];
		
		$this->setVariable( 'Blog', $this->blog );
		
		//Don't forget the header data
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
	
	private function GetPosts()
	{
		$table = $this->blog['tables'];

		$numItems = ( ( !empty( $table ) && !empty( $table['form_data']['max_items'] ) ) ? $table['form_data']['max_items'] : ( !empty( $this->blog['postLimit'] ) ? $this->blog['postLimit'] : ( !empty( HOMEPAGE_ITEMS ) ? HOMEPAGE_ITEMS : 20 ) ) );
		
		$cacheFile = CacheFileName( 'blog-posts_' . $this->blogKey, null, $this->lang['lang']['id'], $this->blogId, $this->page, $numItems, $this->lang['lang']['code'] );
		
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
			
			$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $this->lang['lang']['id'] . ") AND (p.id_blog = " . $this->blogId . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL) AND (c.hide_blog = 0) AND (su.hide_blog = 0 OR su.hide_blog IS NULL) AND (d.hide_on_home = 0 OR d.hide_on_home IS NULL)";
			
			$query = PostsDefaultQuery( $q, $from . ', ' . $numItems, 'p.added_time DESC', null, false );
			
			//Query: posts
			$tmp = $this->db->from( null, $query )->all();
			
			if ( empty( $tmp ) )
			{
				$log = Settings::LogSettings();
				
				if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
				{
					$errorMessage = 'Blog posts couldn\'t be fetched (Key: ' . $this->blogKey . ')';
					
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
			LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post
			INNER JOIN  `" . DB_PREFIX . "categories` as c ON c.id = p.id_category
			LEFT JOIN  `" . DB_PREFIX . "categories` as su ON su.id = p.id_sub_category
			WHERE " . $q )->total();
			
			$data = array(
				'posts' 		=> array(),
				'tableHtml'		=> null,
				'totalItems' 	=> $this->items
			);
			
			$i = 0;
			
			foreach ( $tmp as $p )
			{
				$p = array_merge( $p, $s );
				
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
	
	#####################################################
	#
	# Add the ads in post function
	#
	#####################################################
	/*
	private function InPostAds()
	{
		if ( !Settings::IsTrue( 'enable_ads' ) )
			return;
	
		$Post 	  = $this->getVariable( 'Post' );
		
		$adMiddle = GetAds( 'post-middle', 1, $Post->PostType() );
		$adTop 	  = GetAds( 'post-beginning', 1, $Post->PostType() );
		$adBottom = GetAds( 'post-end', 1, $Post->PostType() );
	
		$top = $bottom = '';
	
		if ( empty( $adTop ) && empty( $adMiddle ) && empty( $adBottom ) )
			return;
	
		if ( !empty( $adTop ) )
		{
			$top .= PHP_EOL . '<!--top ad-->' . PHP_EOL;

			$top .= '<div style="float:' . $adTop['align'] . ';margin:12px;' . ( ( $adTop['width'] > 0 ) ? 'width:' . $adTop['width'] . 'px;' : '' ) . ( ( $adTop['height'] > 0 ) ? 'height:' . $adTop['height'] . 'px;' : '' ) . ( ( $adTop['type'] == 'dummy' ) ? 'background-color:#33475b;' : '' ) . '">';
						
			if ( $adTop['type'] == 'plain-text' )
				$top .= $adTop['ad_code'];
								
			if ( $adTop['type'] == 'image' )
				$top .= '<img src="' . $adTop['img_url'] . '" align="' . $adTop['align'] . '" />';

			$top .= '</div>' . PHP_EOL;
		}
	
		if ( !empty( $adBottom ) )
		{
			$bottom .= PHP_EOL . '<!--top ad-->' . PHP_EOL;

			$bottom .= '<div style="float:' . $adBottom['align'] . ';margin:12px;' . ( ( $adBottom['width'] > 0 ) ? 'width:' . $adBottom['width'] . 'px;' : '' ) . ( ( $adBottom['height'] > 0 ) ? 'height:' . $adBottom['height'] . 'px;' : '' ) . ( ( $adBottom['type'] == 'dummy' ) ? 'background-color:#33475b;' : '' ) . '">';
						
			if ( $adBottom['type'] == 'plain-text' )
				$bottom .= $adBottom['ad_code'];

			if ( $adBottom['type'] == 'image' )
				$bottom .= '<img src="' . $adBottom['img_url'] . '" align="' . $adBottom['align'] . '" />';

			$bottom .= '</div>' . PHP_EOL;
		}
	
		$temp = $Post->Content();
	
		if ( !empty( $adMiddle ) )
		{
			$count = substr_count( $temp, '</p>' );

			if ( $count >= 6 )
			{
				$half = floor( $count / 2 );

				$content = explode( "</p>", $temp );

				$temp = '';

				$i = 0;

				foreach ( $content as $p ) 
				{
					if ( empty( $p ) )
						continue;

					$i++;

					if ( $i == $half )
					{
						$temp .= PHP_EOL . '<!--middle ad-->' . PHP_EOL;
							
						$temp .= '<div style="float:' . $adMiddle['align'] . ';margin:12px;' . ( ( $adMiddle['width'] > 0 ) ? 'width:' . $adMiddle['width'] . 'px;' : '' ) . ( ( $adMiddle['height'] > 0 ) ? 'height:' . $adMiddle['height'] . 'px;' : '' ) . ( ( $adMiddle['type'] == 'dummy' ) ? 'background-color:#33475b;' : '' ) . '">';
							
						if ( $adMiddle['type'] == 'plain-text' )
							$temp .= $adMiddle['ad_code'];

						if ( $adMiddle['type'] == 'image' )
							$temp .= '<img src="' . $adMiddle['img_url'] . '" align="' . $adMiddle['align'] . '" />';
									
						$temp .= '</div>' . PHP_EOL;
					}
					
					$temp .= trim( $p ) . '</p>' . PHP_EOL;
				}

				unset( $content );
			}
		}

		$content = $top . $temp . $bottom;
	
		//Set the new content
		$Post->SetContent( $content );
		
		//Overwrite the previous Variable
		$this->setVariable( 'Post', $Post );
	
		//Free up some memory
		unset( $adMiddle, $top, $temp, $bottom, $adTop, $adBottom, $content, $Post );
	}*/
}