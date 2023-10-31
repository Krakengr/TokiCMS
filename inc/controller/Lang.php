<?php defined('TOKICMS') or die('Hacking attempt...');

class Lang extends Controller {
	
	private $page;
	
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
		
		$dontLoadPosts = $this->getVariable( 'dontLoadPosts' );
		$this->page    = ( ( Router::GetVariable( 'pageNum' ) > 0 ) ? Router::GetVariable( 'pageNum' ) : 1 );

		$Post 		   = null;

		//Check if we have a post as a frontpage
		if ( StaticHomePage() )
		{
			//We need to find the page we want to load, based on its parent ID
			$_page = $this->GetPageId();
			
			if ( !empty( $_page ) )
			{
				$Post = GetSinglePost( $_page['id_post'] );
			}
		}

		if ( $Post )
		{
			//Check if we want Ads in post
			InPostAds();
			
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
		}
		
		else
		{
			if ( $dontLoadPosts )
			{
				$this->setVariable( 'Listings', null );
				$this->view();
				return;
			}
			
			$this->GetPosts();
		}
		
		$this->view();
	}
	
	private function GetPosts()
	{
		$numItems = ( !empty( HOMEPAGE_ITEMS ) ? HOMEPAGE_ITEMS : 20 );
		
		$cacheFile = CacheFileName( 'posts_by_lang', null, $this->lang['lang']['id'], null, $this->page, $numItems, $this->lang['lang']['code'] );

		//Get data from cache
		if ( ValidOtherCache( $cacheFile ) )
		{
			$data = ReadCache( $cacheFile );
			
			$this->items = $data['totalItems'];
		}
		
		//Get the data and save it to the cache, if needed...
		else
		{
			$from = ( ( $this->page * $numItems ) - $numItems );
			
			$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $this->lang['lang']['id'] . ") AND (p.id_blog = 0) AND (p.post_type = 'post') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)";
			
			$query = "SELECT p.id_post, p.id_blog, p.id_site, p.id_parent, p.added_time, p.id_member, p.id_lang, p.title, COALESCE(p.description, SUBSTRING(p.post, 1, 180)) AS description, p.disable_comments, p.sef, p.views, p.num_comments, p.post_type, p.post_status, p.cover_img, p.post, p.content, c.name AS cat_name, c.sef AS cat_sef, c.id AS cat_id, c.cat_color, su.name AS sub_name, su.sef AS sub_sef, su.id AS sub_id, su.cat_color AS sub_color, b.sef AS blog_sef, b.name AS blog_name, b.trans_data AS blog_trans, b.groups_data AS blog_groups, u.real_name as real_name, u.user_name, u.image_data as user_img, u.trans_data, la.code AS ls, la.title AS lt, la.locale AS ll, la.flagicon, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, s.enable_multisite as multisite, s.title as st, d.value1 as extra_val, d.ext_id, d.external_url as ext_url, d.last_time_commented as lstc, ld.id as dlid, ld.code as dlc, ld.title as dlt, ld.locale as dll, lc.date_format, lc.time_format, cnf.value as hide_lang, cnf2.value as enable_comments, cnf3.value as disable_author_archives, cnf4.value as comments_data, (SELECT COUNT(id) FROM `" . DB_PREFIX . "comments` as cm WHERE cm.id_post = p.id_post AND cm.status = 'approved') as numcomm
			FROM `" . DB_PREFIX . POSTS . "` AS p
			INNER JOIN `" . DB_PREFIX . "languages`  as la ON la.id = p.id_lang
			INNER JOIN `" . DB_PREFIX . "languages_config` as lc ON lc.id_lang = p.id_lang
			INNER JOIN `" . DB_PREFIX . USERS . "`   as u ON u.id_member = p.id_member
			INNER JOIN `" . DB_PREFIX . "sites` 	 as s ON s.id = p.id_site
			INNER JOIN `" . DB_PREFIX . "config` 	 as cnf ON cnf.id_site = p.id_site AND cnf.variable = 'hide_default_lang_slug'
			INNER JOIN `" . DB_PREFIX . "config` 	 as cnf2 ON cnf2.id_site = p.id_site AND cnf2.variable = 'enable_comments'
			INNER JOIN `" . DB_PREFIX . "config` 	 as cnf3 ON cnf3.id_site = p.id_site AND cnf3.variable = 'disable_author_archives'
			INNER JOIN `" . DB_PREFIX . "config` 	 as cnf4 ON cnf4.id_site = p.id_site AND cnf4.variable = 'comments_data'
			INNER JOIN `" . DB_PREFIX . "languages`  as ld ON ld.id_site = p.id_site AND ld.is_default = 1
			LEFT JOIN  `" . DB_PREFIX . "categories` as c ON c.id = p.id_category
			LEFT JOIN  `" . DB_PREFIX . "categories` as su ON su.id = p.id_sub_category
			LEFT JOIN  `" . DB_PREFIX . "blogs` 	 as b ON b.id_blog = p.id_blog
			LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post
			WHERE 1=1 AND " . $q . " AND (c.hide_front = 0) AND (b.disabled = 0 OR b.disabled IS NULL) AND (b.frontpage = 1 OR b.frontpage IS NULL)
			GROUP BY p.id_post
			ORDER BY p.added_time DESC LIMIT " . $from . ', ' . $numItems;
			
			//Query: posts
			$tmp = $this->db->from( null, $query )->all();

			if ( empty( $tmp ) )
			{
				$log = Settings::LogSettings();
				
				if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
				{
					$errorMessage = 'Browse (lang) posts couldn\'t be fetched (Lang: ' . $this->lang['lang']['code'] . ')';
					
					if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
					{
						$errorData = 'Query: ' . PHP_EOL . $query;
					}
				
					Log::Set( $errorMessage, $errorData, $param, 'system' );
				}
				
				return null;
			}
			
			// Query: total
			$this->items = $this->db->from( null, "SELECT count(p.id_post) as total FROM `" . DB_PREFIX . POSTS . "` as p LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post WHERE 1=1 AND " . $q )->total();
			
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
	}
	
	private function GetPageId()
	{
		//If we have enabled lang code in url for all the languages, we must be sure that this is the correct language
		if( Settings::Lang()['id'] == $this->lang['lang']['id'] )
			return $this->lang['lang']['id'];
		
		$staticId = StaticHomePage( true );
		
		$cacheFile = CACHE_ROOT . 'home_page-' . $this->lang['lang']['code'] . '-'
					. md5 ( 'home_page-' . $this->lang['lang']['code'] . CACHE_HASH ) . '.php';

		if ( ValidCache( $cacheFile ) )
		{
			$page = readCache( $cacheFile );
		}
		else
		{
			$query = array(
				'SELECT'	=>  "id_post, id_parent, title, sef, cover_img",

				'WHERE' 	=> "id_parent = :id AND id_site = :site AND id_lang = :lang",

				'FROM'		=> DB_PREFIX . POSTS,

				'PARAMS' => array( 'NO_PREFIX' => true ),

				'LIMIT'		=> 1,

				'BINDS' 	=> array(
							array(
									'PARAM' => ':id',
									'VAR' => $staticId,
									'FLAG' => 'INT'
							),
							array(
									'PARAM' => ':site',
									'VAR' => SITE_ID,
									'FLAG' => 'INT'
							),
							array(
									'PARAM' => ':lang',
									'VAR' => $this->lang['lang']['id'],
									'FLAG' => 'INT'
							)
				)
			);
		
			$page = Query( $query );

			if ( !$page )
			{
				return null;
			}

			WriteCacheFile( $page, $cacheFile );
		}
		
		return $page;
	}
}