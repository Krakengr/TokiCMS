<?php defined('TOKICMS') or die('Hacking attempt...');

class Home extends Controller {
	
	private $items 	  = 0;
	private $staticId = 0;
	
    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'view-site' ) )
		{
			//Don't include this file while on login or register
			if ( ( Router::WhereAmI() != 'login' ) || ( Router::WhereAmI() != 'register' ) || ( Router::WhereAmI() != 'forgot-password' ) || ( Router::WhereAmI() != 'recovery' ) )
				Router::SetIncludeFile( INC_ROOT . 'no-access.php' );

			$this->view();
			return;
		}
	
		$Post = null;
		
		//TODO
		//$AuthUser = $this->getVariable( 'AuthUser' );
		
		$dontLoadPosts = $this->getVariable( 'dontLoadPosts' );

		//Check if we have a post as a frontpage
		if ( StaticHomePage() )
		{
			$this->staticId = StaticHomePage( true );
			
			//Load the post directly
			$Post = $this->SinglePost();
		}

		if ( $Post )
		{
			//Check if we want Ads in post
			$ads = InPostAds( $Post->Content(), $Post->PostType() );//$this->InPostAds();
			$Post->SetContent( $ads );

			Router::SetWhereAmI( 'post' );
			Router::SetStaticHomePage(); //Set the homepage as "static"
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
			);
			
			Theme::SetVariable( 'data', $theme );
			
			unset( $theme, $Post );
		}

		else
		{
			if ( $dontLoadPosts )
			{
				$this->setVariable( 'Listings', null );
				$this->view();
				return;
			}
			
			$data = $this->GetPosts();

			$this->setVariable( 'Items', $this->items );
			$this->setVariable( 'ItemsPerPage', HOMEPAGE_ITEMS );
			$this->setVariable( 'Listings', ( !$data ? null : $data ) );

			unset( $data );
			
			Theme::SetVariable( 'totalItems', $this->items );
		}
		
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
	
	#####################################################
	#
	# Get the latest posts function
	#
	#####################################################
	private function GetPosts()
	{
		$cacheFile = CacheFileName( 'posts_home', null, $this->lang['lang']['id'], null, null, HOMEPAGE_ITEMS, $this->lang['lang']['code'] );
		
		//Get data from cache
		if ( ValidOtherCache( $cacheFile ) )
		{
			$data = ReadCache( $cacheFile );
			
			$this->items = $data['totalItems'];
		}
		
		//Get the data and save it to the cache, if needed...
		else
		{
			$numItems = ( !empty( HOMEPAGE_ITEMS ) ? HOMEPAGE_ITEMS : 20 );
			
			$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $this->lang['lang']['id'] . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL) AND (c.hide_front = 0) AND (b.disabled = 0 OR b.disabled IS NULL) AND (b.frontpage = 1 OR b.frontpage IS NULL) AND (d.hide_on_home = 0 OR d.hide_on_home IS NULL)";
			
			$query = PostsDefaultQuery( $q, $numItems, "p.added_time DESC" );

			//Query: posts
			$tmp = $this->db->from( null, $query )->all();
			
			if ( empty( $tmp ) )
			{
				$log = Settings::LogSettings();
				
				if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
				{
					$errorMessage = 'Home posts couldn\'t be fetched (Lang: ' . $this->lang['lang']['code'] . ')';
					
					if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
					{
						$errorData = 'Query: ' . PHP_EOL . $query;
					}
				
					Log::Set( $errorMessage, $errorData, $query, 'system' );
				}
				
				return null;
			}
			
			// Query: total			
			$this->items = $this->db->from( null, "SELECT count(p.id_post) as total
			FROM `" . DB_PREFIX . POSTS . "` as p
			LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post
			LEFT JOIN  `" . DB_PREFIX . "blogs` 	 as b ON b.id_blog = p.id_blog
			LEFT JOIN  `" . DB_PREFIX . "categories` as c ON c.id = p.id_category
			WHERE 1=1 AND " . $q )->total();
			
			$data = array(
				'posts' 		=> array(),
				'totalItems' 	=> $this->items
			);

			foreach ( $tmp as $p )
			{				
				//Get the raw data only
				$data['posts'][] = BuildPostVars( $p );

				unset( $p );
			}

			WriteOtherCacheFile( $data, $cacheFile );
			
			unset( $tmp );
		}
		
		if ( empty( $data['posts'] ) )
		{
			return null;
		}
		
		return BuildPosts( $data['posts'] );
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