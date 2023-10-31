<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Get the blog's data function
#
#####################################################
function GetBlog( $key = null, $id = null, $siteId, $langId = null, $langKey = null, $cache = true )
{
	$cacheFile = CACHE_ROOT . 'content' . PS . 'blog_data-' . ( $key ? 'key_' . $key : 'id_' . $id ) . ( $langId ? '-langid_' . $langId : '' ) . ( $langKey ? '-langkey_' . $langKey : '' ) . '-siteid_' . $siteId;
		
	$cacheFile .= '-' . sha1( $cacheFile . CACHE_HASH ) . '.php';

	//Get data from cache
	if ( $cache && ValidCache( $cacheFile ) )
	{
		$blog = ReadCache( $cacheFile );
	}
	else
	{
		$db = db();

		$query = "SELECT b.*, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_blog = b.id_blog AND p.id_lang = b.id_lang AND p.post_status = 'published') as numposts, (SELECT COUNT(id) FROM `" . DB_PREFIX . "comments` as cm WHERE cm.id_blog = b.id_blog AND cm.id_lang = b.id_lang AND cm.status = 'approved') as numcomm
		FROM `" . DB_PREFIX . "blogs` AS b
		WHERE (" . ( $key ? "b.sef = :sef" : "b.id_blog = :id" ) . ") AND (b.id_site = " . $siteId . ")" . ( $langId ? " AND (b.id_lang = " . $langId . " OR b.id_lang = 0)" : "" ) . " AND (b.disabled = 0 OR b.disabled IS NULL)";
		
		if ( $key )
		{
			$binds = array( $key => ':sef' );
		}
		else
		{
			$binds = array( $id => ':id' );
		}

		//Query: blog
		$tmp = $db->from( null, $query, $binds )->single();
		
		if ( !$tmp )
		{
			if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
			{
				$errorMessage = 'Blog not found in database (' . ( $key ? 'Key: ' . $key : 'Id: ' . $id ) . ')';
					
				if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
				{
					$errorData = 'Query: ' . PHP_EOL . $query;
				}
				
				Log::Set( $errorMessage, $errorData, $param, 'system' );
			}
			
			return null;
		}
		
		$blog = $tmp;
		
		$blog['trans'] 		  = Json( $blog['trans_data'] );
		//$blog['cats'] 		  = BuildBlogCats( $tmp, $langId );
		$blog['groups'] 	  = Json( $blog['groups_data'] );
		$blog['postTemplate'] = $blog['custom_post_tmp'];
		$blog['listTemplate'] = $blog['custom_list_tmp'];
		$blog['homeTemplate'] = $blog['custom_home_tmp'];
		$blog['postLimit'] 	  = $blog['article_limit'];
		$blog['tables'] 	  = GetCategoryBlogTable( $blog['id_blog'], $blog['id_site'], false );

		WriteCacheFile( $blog, $cacheFile );
	}
	
	return $blog;
}

function BuildBlogCats( $blog, $langId = null )
{
	$db = db();
	
	$query = "SELECT c.*, la.code as ls, la.locale as lc, b.sef as bs
	FROM `" . DB_PREFIX . "categories` AS c
	INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
	LEFT JOIN  `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
	WHERE (c.id_parent = 0) AND (c.id_blog = " . $blog['id_blog'] . ")" . ( $langId ? " AND (c.id_lang = " . $langId . ")" : "" );

	//Query: categories
	$cats = $db->from( null, $query )->all();

	if ( !$cats )
		return null;
		
	$data = array();
	
	$query = "SELECT code, locale FROM `" . DB_PREFIX . "languages`	WHERE (id = " . $langId . ")";
		
	$lang = $db->from( null, $query )->single();

	foreach ( $cats as $cat )
	{
		$catUrl = BuildCategoryUrl( $cat, $lang['code'] );
		$trans = CategoryTrans( $cat, $lang['code'], Router::GetVariable( 'siteRealUrl' ), $lang['locale'] );
			
		$data[$cat['sef']] = array(
					'id' => $cat['id'],
					'name' => stripslashes( $cat['name'] ),
					'description' => stripslashes( $cat['descr'] ),
					'url' => $catUrl,
					'items' => $cat['num_items'],
					'trans' => $trans,
					'childs' => array()
		);
			
		//Get the subcategories, if any
		$query = "SELECT c.*, la.code as ls, la.locale as lc
		FROM `" . DB_PREFIX . "categories` AS c
		INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
		WHERE (c.id_parent = " . $cat['id'] . ")";

		//Query: subcategories
		$subs = $db->from( null, $query )->all();

		if ( $subs )
		{
			foreach ( $subs as $sub )
			{
				$trans = CategoryTrans( $sub, $lang['code'], Router::GetVariable( 'siteRealUrl' ), $lang['locale'] );
					
				$data[$cat['sef']]['childs'][$sub['sef']] = array(
							'id' => $sub['id'],
							'name' => stripslashes( $sub['name'] ),
							'description' => stripslashes( $sub['descr'] ),
							'trans' => $trans,
							'items' => $sub['num_items'],
							'url' => $catUrl . $sub['sef'] . PS
				);
			}
		}
	}

	return $data;
}

#####################################################
#
# Get the blog's schemas function
#
#####################################################
function GetBlogSchemas( $_Blog )
{
	return;
	if ( empty( $_Blog ) || ( Router::GetVariable( 'whereAmI' ) != 'post' ) )
		return '';
	
	$blogs = Settings::BlogsFullArray();

	if ( ( Settings::Get()['store_blog'] != 'disable' ) && ( isset( $blogs[Settings::Get()['store_blog']] ) ) )
		$storeBlogId = $blogs[Settings::Get()['store_blog']]['id_blog'];
	
	else
		$storeBlogId = null;
		
	if ( ( Settings::Get()['forum_blog'] != 'disable' ) && ( isset( $blogs[Settings::Get()['forum_blog']] ) ) )
		$forumBlogId = $blogs[Settings::Get()['forum_blog']]['id_blog'];
	
	else
		$forumBlogId = null;

	$where = "(";
		
	$where .= "enable_on = '" . ( ( $storeBlogId && ( $_Blog['id_blog'] == $storeBlogId ) ) ? 'all-products' : ( ( $forumBlogId && ( $_Blog['id_blog'] == $forumBlogId ) ) ? 'all-threads' : 'blog' ) ). "'";
		
	$where .= ( ( !$storeBlogId && !$forumBlogId ) ? " AND enable_on_id = '" . $_Blog['id_blog'] . "'" : '' );
		
	$where .= ")";

	$where .= " AND NOT (";
		
	$where .= "exclude_from = '" . ( ( $storeBlogId && ( $_Blog['id_blog'] == $storeBlogId ) ) ? 'all-products' : ( ( $forumBlogId && ( $_Blog['id_blog'] == $forumBlogId ) ) ? 'all-threads' : 'blog' ) ) . "'";
		
	$where .= ( ( !$storeBlogId && !$forumBlogId ) ? " AND exclude_from_id = '" . $_Blog['id_blog'] . "'" : '' );

	$where .= ")";

	$query = array(
			'SELECT'	=>  "id, title, type, data",
					
			'FROM'		=> DB_PREFIX . "schemas",
					
			'WHERE'		=> "id_site = :id AND " . $where,
					
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
					
			'BINDS' 	=> array(
								array( 'PARAM' => ':id', 'VAR' => SITE_ID, 'FLAG' => 'INT' )
			)
	);

	return Query( $query, true );
}
