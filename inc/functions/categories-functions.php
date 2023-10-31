<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Get the category's data function
#
#####################################################
function GetCategory( $key = null, $id = null, $siteId, $langId = null, $blogId = null, $langKey = null, $cache = true )
{
	$cacheFile = CacheFileName( 'category_data-' . ( $key ? 'key_' . $key : 'id_' . $id ), null, $langId, $blogId, null, null, $langKey, $siteId );

	//Get data from cache
	if ( $cache && ValidCache( $cacheFile ) )
	{
		$data = ReadCache( $cacheFile );
	}
	else
	{
		$db = db();

		$query = "SELECT c.*, la.code as ls, b.sef as blog_sef, cnf.value as hide_lang, cnf2.value as categories_filter, cnf3.value as trans_data, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, ld.code as dlc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_category = c.id AND p.id_lang = c.id_lang AND p.post_status = 'published') as numposts
		FROM `" . DB_PREFIX . "categories` AS c
		INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
		INNER JOIN `" . DB_PREFIX . "languages` as ld ON ld.id_site = c.id_site AND ld.is_default = 1
		INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = c.id_site
		INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site = c.id_site AND cnf.variable = 'hide_default_lang_slug'
		INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = c.id_site AND cnf2.variable = 'categories_filter'
		INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = c.id_site AND cnf3.variable = 'trans_data'
		LEFT  JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
		WHERE (" . ( $key ? "c.sef = :sef" : "c.id = :id" ) . ") AND (c.id_site = " . $siteId . ")" . ( $langId ? " AND (c.id_lang = " . $langId . ")" : "" ) . ( $blogId ? " AND (c.id_blog = " . $blogId . ")" : "" );
		
		if ( $key )
		{
			$binds = array( $key => ':sef' );
		}
		else
		{
			$binds = array( $id => ':id' );
		}

		//Query: category
		$tmp = $db->from( null, $query, $binds )->single();

		if ( !$tmp )
		{
			$log = Settings::LogSettings();
				
			if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
			{
				$errorMessage = 'Category not found in database (' . ( $key ? 'Key: ' . $key : 'Id: ' . $id ) . ')';
					
				if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
				{
					$errorData = 'Query: ' . PHP_EOL . $query;
				}
				
				Log::Set( $errorMessage, $errorData, $param, 'system' );
			}

			return null;
		}
		
		$data = $tmp;
		
		$data['name']  		= StripContent( $data['name'] );
		$data['descr']  	= StripContent( $data['descr'] );
		$data['groups']  	= Json( $data['groups_data'] );
		$data['trans'] 	 	= CategoryTrans( $data, $data['ls'], $data['url'], $langKey );
		$data['catUrl']  	= BuildCategoryUrl( $data, $data['ls'] );
		$data['image'] 	 	= BuildImageArray( $data['id_image'] );
		$data['filters'] 	= GetFilters( $data['id'], $langId );
		$data['postLimit'] 	= $data['article_limit'];
		$data['tables'] 	= GetCategoryBlogTable( $data['id'], $data['id_site'] );
		
		if ( $cache )
		{
			WriteCacheFile( $data, $cacheFile );
		}
	}
	
	return $data;
}

#####################################################
#
# Get the category's data function
#
#####################################################
function GetSubCategory( $key = null, $id = null, $siteId, $langId = null, $blogId = null, $langKey = null, $cache = true )
{
	$cacheFile = CacheFileName( 'subcategory_data-' . ( $key ? 'key_' . $key : 'id_' . $id ), null, $langId, $blogId, null, null, $langKey, $siteId );

	//Get data from cache
	if ( $cache && ValidCache( $cacheFile ) )
	{
		$data = ReadCache( $cacheFile );
	}
	else
	{
		$db = db();

		$query = "SELECT c.*, p.sef as parent_sef, p.name as parent_name, la.code as ls, b.sef as blog_sef, cnf.value as hide_lang, cnf2.value as categories_filter, cnf3.value as trans_data, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, ld.code as dlc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_sub_category = c.id AND p.id_lang = c.id_lang AND p.post_status = 'published') as numposts
		FROM `" . DB_PREFIX . "categories` AS c
		INNER JOIN `" . DB_PREFIX . "categories` as p ON p.id = c.id_parent
		INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
		INNER JOIN `" . DB_PREFIX . "languages` as ld ON ld.id_site = c.id_site AND ld.is_default = 1
		INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = c.id_site
		INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site = c.id_site AND cnf.variable = 'hide_default_lang_slug'
		INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = c.id_site AND cnf2.variable = 'categories_filter'
		INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = c.id_site AND cnf3.variable = 'trans_data'
		LEFT  JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
		WHERE (" . ( $key ? "c.sef = :sef" : "c.id = :id" ) . ") AND (c.id_site = " . $siteId . ")" . ( $langId ? " AND (c.id_lang = " . $langId . ")" : "" ) . ( $blogId ? " AND (c.id_blog = " . $blogId . ")" : "" );
		
		if ( $key )
		{
			$binds = array( $key => ':sef' );
		}
		else
		{
			$binds = array( $id => ':id' );
		}

		//Query: category
		$tmp = $db->from( null, $query, $binds )->single();

		if ( !$tmp )
		{
			$log = Settings::LogSettings();
				
			if ( !empty( $log ) && $log['enable_error_log'] && $log['enable_not_found_log'] )
			{
				$errorMessage = '(Sub)Category not found in database (' . ( $key ? 'Key: ' . $key : 'Id: ' . $id ) . ')';
					
				if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
				{
					$errorData = 'Query: ' . PHP_EOL . $query;
				}
				
				Log::Set( $errorMessage, $errorData, $param, 'system' );
			}
			
			return null;
		}
		
		$data = $tmp;
		
		$data['groups']  	= Json( $data['groups_data'] );
		$data['trans'] 	 	= CategoryTrans( $data, $data['ls'], $data['url'], $langKey );
		$data['catUrl']  	= BuildCategoryUrl( $data, $data['ls'], false, true );
		$data['image'] 	 	= BuildImageArray( $data['id_image'] );
		$data['parentUrl']  = BuildCategoryUrl( $data, $data['ls'] );
		$data['parentName'] = StripContent( $data['parent_name'] );
		$data['filters'] 	= GetFilters( $data['id'], $langId );
		$data['postLimit'] 	= $data['article_limit'];
		$data['tables'] 	= GetCategoryBlogTable( $data['id'], $data['id_site'] );
		
		if ( $cache )
		{
			WriteCacheFile( $data, $cacheFile );
		}
	}
	
	return $data;
}

#####################################################
#
# Get the tables of a category/blog function
#
#####################################################
function GetCategoryBlogTable( $id, $siteId, $cat = true )
{
	$db = db();
	
	//Check if we have tables
	$query = "SELECT id, title, groups_data, form_data, show_if_id
	FROM `" . DB_PREFIX . "forms`
	WHERE (id_site = " . $siteId . ") AND (disabled = 0) AND (form_type = 'table') AND (form_pos = 'posts-archive') AND (show_if = '" . ( $cat ? 'category' : 'blog' ) . "') AND ( show_if_id = 0 OR show_if_id = " . $id . ")";
	
	//Query: form
	$pst = $db->from( null, $query )->single();

	if ( empty( $pst ) )
		return null;
	
	$formData  = ( !empty( $pst['form_data'] ) ? Json( $pst['form_data'] ) : array() );
	$groupData = ( !empty( $pst['groups_data'] ) ? Json( $pst['groups_data'] ) : array() );
	
	//Hide this table if we don't want it into this category/blog
	if ( $cat )
	{
		if ( empty( $pst['show_if_id'] ) && !empty( $formData ) && !empty( $formData['hide_table_if'] ) && ( $formData['hide_table_if'] == 'category' ) && ( $formData['show_table_option'] == 'is-equal' ) && !empty( $formData['hide_target_category'] ) && ( $formData['hide_target_category']['id'] == $id ) )
			return null;
	}
	
	else
	{
		if ( empty( $pst['show_if_id'] ) && !empty( $formData ) && !empty( $formData['hide_table_if'] ) && ( $formData['hide_table_if'] == 'blog' ) && ( $formData['show_table_option'] == 'is-equal' ) && !empty( $formData['hide_target_blog'] ) && ( $formData['hide_target_blog']['id'] == $id ) )
			return null;
	}
	
	$data = array(
		'title' 		=> StripContent( $pst['title'] ),
		'id' 			=> $pst['id'],
		'groups_data'	=> ( !empty( $pst['groups_data'] ) ? Json( $pst['groups_data'] ) : array() ),
		'form_data'		=> $formData,
		'elements'		=> array()
	);
	
	//Get this table's elements
	$query = "SELECT id, data FROM `" . DB_PREFIX . "form_elements`
	WHERE (id_form = " . $pst['id'] . ") AND (disabled = 0) ORDER BY elem_order ASC";
	
	//Query: elements
	$els = $db->from( null, $query )->all();
	
	if ( !empty( $els ) )
	{
		foreach( $els as $el )
		{
			$query = "SELECT id, elem_id, data, style, elem_type
			FROM `" . DB_PREFIX . "form_table_elements`
			WHERE (id_column = " . $el['id'] . ") AND (disabled = 0) ORDER BY elem_order ASC";
			
			//Query: table elements
			$ets = $db->from( null, $query )->all();

			$elData = Json( $el['data'] );

			$data['elements'][$el['id']] = array(
				'head' => array(
					'style' => ( !empty( $elData['header'] ) ? $elData['header'] : array() ),
					'data' 	=> array()
				
				),
				'body' => array(
					'style' => ( !empty( $elData['cell'] ) ? $elData['cell'] : array() ),
					'data' 	=> array()
				)
			);

			if ( !empty( $ets ) )
			{
				foreach( $ets as $et )
				{
					$etData = ( !empty( $et['data'] ) ? Json( $et['data'] ) : array() );
					
					if ( $et['elem_type'] == 'header' )
					{
						$data['elements'][$el['id']]['head']['data'][$et['id']] = array(
							'id' 	=> $et['elem_id'],
							'style' => Json( $et['style'] ),
							'data' 	=> $etData
						);
					}
					
					if ( $et['elem_type'] == 'cell' )
					{
						$data['elements'][$el['id']]['body']['data'][$et['id']] = array(
							'id' 	=> $et['elem_id'],
							'style' => Json( $et['style'] ),
							'data' 	=> $etData
						);
					}
				}
			}
		}
	}

	return $data;
}

#####################################################
#
# Build Category search function
#
#####################################################
function BuildCategorySearch()
{
	//Incude the arrays file
	require ( ARRAYS_ROOT . 'seo-arrays.php');
	
	$arr = array();
	
	foreach( $categoryCustomMetaFormat as $id => $meta )
	{
		$arr[] = '{{' . $id . '}}';
	}
	
	return $arr;
}

#####################################################
#
# Build Category URL function
#
# Builds the url based on category's data and current settings
#
#####################################################
function BuildCategoryUrl( $data, $langCode = null, $skipSlug = false, $child = false )
{
	if ( empty( $data ) )
		return false;
	
	if ( !empty( $data['dlc'] ) )
	{
		$url = LangSlugUrl( $data );
	
		//Add the blog slug
		if ( IsTrue( $data['multiblog'] ) && !empty( $data['blog_sef'] ) )
		{
			$url .= $data['blog_sef'] . PS;
		}
	}
	
	else
	{
		$CurrentLang = CurrentLang();

		$url = SITE_URL;
		
		$langCode = ( $langCode ? $langCode : $CurrentLang['lang']['code'] );
		
		$lKey = ( isset( $data['languageKey'] ) ? $data['languageKey'] : ( isset( $data['ls'] ) ? $data['ls'] : null ) );
		
		//Add the lang slug
		if ( MULTILANG && !empty( $lKey ) && ( !Settings::IsTrue( 'hide_default_lang_slug' ) || ( Settings::IsTrue( 'hide_default_lang_slug' ) && ( $lKey != Settings::LangData()['lang']['code'] ) ) ) )
			$url .= $lKey . PS;
		
		//Add the blog slug
		if ( MULTIBLOG && !empty( $data['blog_sef'] ) )
		{
			$url .= $data['blog_sef'] . PS;
		}
	}
	
	//Add the categories filter
	$url .= ltrim( CatFilter( $langCode, $data ), '/' );

	//Add the slug
	if ( !$skipSlug )
	{
		if ( !$child )
		{
			$url .= $data['sef'] . PS;
		}
		
		elseif ( !empty( $data['parent_sef'] ) )
		{
			$url .= $data['parent_sef'] . PS;
		}
	}
	
	//Add the subcategory if there is one
	$url .= ( ( $child && !$skipSlug && !empty( $data['sub_sef'] ) ) ? $data['sub_sef'] . PS : '' ); 

	return rawurldecode( $url );
}

#####################################################
#
# Return Category Filter function
#
#####################################################
function CatFilter( $langCode = null, $data = null )
{
	if ( !empty( $data ) && !empty( $data['dlc'] ) )
	{
		//We need this value
		$filter = ( !empty( $data['categories_filter'] ) ? $data['categories_filter'] : Settings::Get()['categories_filter'] );
		
		$defaultLang = $data['dlc'];
		
		$langCode = ( $langCode ? $langCode : $data['ls'] );

		if ( IsTrue( $data['multilang'] ) )
		{
			$trans = Json( $data['trans_data'] );
			
			if ( !empty( $trans ) && isset( $trans[$langCode] ) && !empty( $trans[$langCode]['category_filter_trans'] ) )
			{
				$filter = '/' . $trans[$langCode]['category_filter_trans'] . '/';
			}
		}
	}
	
	else
	{
		$filter = Settings::Get()['categories_filter'];
		$defaultLang = Settings::LangData()['lang']['code'];
		$langCode = ( $langCode ? $langCode : CurrentLang()['lang']['code'] );

		if ( MULTILANG )
		{
			$trans = Settings::Trans();

			if ( !empty( $trans ) && isset( $trans[$langCode] ) && !empty( $trans[$langCode]['category_filter_trans'] ) )
			{
				$filter = '/' . $trans[$langCode]['category_filter_trans'] . '/';
			}
		}
	}

	return rawurldecode( $filter );
}

#####################################################
#
# Get Category Translations function
#
#####################################################
function CategoryTrans( $data, $langCode = null, $url = null, $locale = null, $otherLang = null )
{
	$db = db();

	$trans = array();
	
	$langCode = ( $langCode ? $langCode : ( isset( $data['ls'] ) ? $data['ls'] : CurrentLang()['lang']['code'] ) );
	
	$multiBlog = ( isset( $data['multiblog'] ) ? IsTrue( $data['multiblog'] ) : MULTIBLOG );

	if ( $otherLang )
	{
		//This is the lang info we have got
		$trans[$otherLang['code']] = array(
			'url' 	=> BuildCategoryUrl( $data, $otherLang['code'], false, $child ),
			'lang' 	=> $otherLang['locale'],
			'id'	=> $data['id_lang']
		);
	}
	
	else
	{
		$catSlug = ltrim( CatFilter( $langCode, $data ), '/' );

		//This is the current's lang info
		$trans[$langCode] = array(
			'url' 	=> $url . ( $multiBlog && !empty( $data['blog_sef'] ) ? $data['blog_sef'] . PS : '' ) . $catSlug . $data['sef'] . PS,
			'lang' 	=> $locale,
			'id'	=> $data['id_lang']
		);
	}

	//Is this a child category? We have to do some work here
	if ( !empty( $data['id_trans_parent'] ) )
	{
		//Check if we have a valid parent
		$query = "SELECT c.sef, c.id, c.id_lang, b.sef as blog_sef, la.code as ls, la.locale as lc
		FROM `" . DB_PREFIX . "categories` AS c
		INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
		LEFT  JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
		WHERE (c.id = " . (int) $data['id_trans_parent'] . ")
		LIMIT 1";
	
		//Query: parent
		$parent = $db->from( null, $query )->single();
		
		if ( !empty( $parent ) )
		{
			$url = BuildCategoryUrl( $parent, $parent['ls'] );
			
			$trans[$parent['ls']] = array (
					'url' => $url,
					'id' => $parent['id'],
					'langID' => $parent['id_lang'],
					'lang' => $parent['lc']
			);
		}
		
		//Now check for other categories that have the same parent
		$query = "SELECT c.sef, c.id, c.id_lang, b.sef as blog_sef, p.sef as parent_sef,
		p.name as parent_name, la.code as ls, la.locale as lc
		FROM `" . DB_PREFIX . "categories` AS c
		INNER JOIN `" . DB_PREFIX . "categories` as p ON p.id = c.id_parent
		INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
		LEFT  JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
		WHERE (c.id = " . (int) $data['id_trans_parent'] . ")";
	
		//Query: childs
		$childs = $db->from( null, $query )->all();

		if ( $childs )
		{
			foreach( $childs as $child )
			{
				$url = BuildCategoryUrl( $child, $child['ls'], false, true );
			
				$trans[$child['ls']] = array (
					'url' => $url,
					'id' => $child['id'],
					'langID' => $child['id_lang'],
					'lang' => $child['lc']
				);
			}
		}
	}
	
	//This will be easier
	else
	{
		$query = "SELECT c.sef, c.id, c.id_lang, b.sef as blog_sef, p.sef as parent_sef,
		p.name as parent_name, la.code as ls, la.locale as lc
		FROM `" . DB_PREFIX . "categories` AS c
		INNER JOIN `" . DB_PREFIX . "categories` as p ON p.id = c.id_parent
		INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
		LEFT  JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
		WHERE (c.id_trans_parent = " . (int) $data['id'] . ")";
	
		//Query: childs
		$childs = $db->from( null, $query )->all();

		if ( $childs )
		{
			foreach( $childs as $child )
			{
				$url = BuildCategoryUrl( $child, $child['ls'], false, true );
				
				$trans[$child['ls']] = array (
					'url' => $url,
					'id' => $child['id'],
					'langID' => $child['id_lang'],
					'lang' => $child['lc']
				);
			}
		}
	}

	return $trans;
}

//This function will check if the language is in the array
//and return the array for any use.
//I will do a DB search here in furure releases
function GetCategoryArray()
{
	$cats = Cats();

	if ( !Router::GetVariable( 'categoryKey' ) )
		return null;
	
	$catKey = Router::GetVariable( 'categoryKey' );
	$langKey = Router::GetVariable( 'langKey' );
	$blogKey = Router::GetVariable( 'blogKey' );
	
	$cat = array();
	
	if ( !$Router->IsLang() )
	{
		if ( Router::GetVariable( 'isBlog' ) && isset( $cats[Settings::Lang()['code']][$blogKey][$catKey]) )
			$cat = $cats[Settings::Lang()['code']][$blogKey][$catKey];
		
		elseif ( !Router::GetVariable( 'isBlog' ) && isset( $cats[Settings::Lang()['code']]['orphanCats'][$catKey]) )
			$cat = $cats[Settings::Lang()['code']]['orphanCats'][$catKey];
	}
	else
	{
		if ( Router::GetVariable( 'isBlog' ) && isset( $cats[$langKey][$blogKey][$catKey]) )
			$cat = $cats[$langKey][$blogKey][$catKey];
		
		elseif ( !Router::GetVariable( 'isBlog' ) && isset( $cats[$langKey]['orphanCats'][$catKey]) )
			$cat = $cats[$langKey]['orphanCats'][$catKey];
	}

	return $cat;
}

#####################################################
#
# Get Categories based in Multiblog function
#
#####################################################
function Cats( $siteId = SITE_ID, $cache = true )
{
	if ( $siteId == SITE_ID )
	{
		$multi = MULTIBLOG;
	}
	
	else
	{
		$db = db();
		
		$tmp = $db->from( null,
		"SELECT enable_multiblog
		FROM `" . DB_PREFIX . "sites`
		WHERE (id = " . $siteId . ")"
		)->single();
		
		$multi = ( ( $tmp && ( $tmp['enable_multiblog'] == 'true' ) ) ? true : false );
	}
	
	if ( $multi )
		$cats = BlogCats( $siteId, $cache );
	
	else
		$cats = GetCats( $siteId, $cache );

	return $cats;
}

#####################################################
#
# Get Categories function
#
#####################################################
function GetCats( $siteId = SITE_ID, $cache = true )
{
	$CurrentLang = CurrentLang();
	
	$cacheFile = CacheFileName( 'categories', null, null, null, null, null, null, $siteId );

	if ( $cache && ValidCache( $cacheFile ) )
	{
		$categories = readCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		$categories = array();

		//Get the languages
		$langList = $db->from( null,
		"SELECT id, code, title, locale
		FROM `" . DB_PREFIX . "languages`
		WHERE (id_site = " . $siteId . ")"
		)->all();
		
		if ( empty( $langList ) )
		{
			return null;
		}
		
		foreach( $langList as $lang )
		{
			$langArr = array(
				'langName' 		=> $lang['title'],
				'langCode' 		=> $lang['code'],
				'langLocale' 	=> $lang['locale'],
				'langId' 		=> $lang['id']
			);
			
			$query = "
			SELECT c.*, la.code as ls, cnf.value as hide_lang, cnf2.value as categories_filter, cnf3.value as trans_data, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, ld.code as dlc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_category = c.id AND p.id_lang = c.id_lang AND p.post_status = 'published') as items
			FROM `" . DB_PREFIX . "categories` AS c
			INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
			INNER JOIN `" . DB_PREFIX . "languages` as ld ON ld.id_site = c.id_site AND ld.is_default = 1
			INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = c.id_site
			INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site = c.id_site AND cnf.variable = 'hide_default_lang_slug'
			INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = c.id_site AND cnf2.variable = 'categories_filter'
			INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = c.id_site AND cnf3.variable = 'trans_data'
			WHERE 1=1 AND (c.id_parent = 0) AND (c.id_lang = " . $lang['id'] . ") AND (c.id_blog = 0)
			ORDER BY c.name ASC";
	
			//Query: categories (no blogs)
			$cats = $db->from( null, $query )->all();

			if ( $cats )
			{
				foreach ( $cats as $cat )
				{
					//We need these strings as null
					$cat['bs'] = null;
					$cat['bn'] = null;
					
					$catUrl = BuildCategoryUrl( $cat, $lang['code'] );
					
					$trans = CategoryTrans( $cat, $lang['code'], $cat['url'], $cat['ls'] );

					$categories[$lang['code']][$cat['sef']] = array(
							'id' 			=> $cat['id'],
							'name' 			=> stripslashes( $cat['name'] ),
							'description' 	=> stripslashes( $cat['descr'] ),
							'groups' 		=> ( !empty( $cat['groups_data'] ) ? Json( $cat['groups_data'] ) : null ),
							'url' 			=> $catUrl,
							'items' 		=> $cat['items'],
							'isDefault' 	=> $cat['is_default'],
							'trans' 		=> $trans,
							'langData' 		=> $langArr,
							'childs' 		=> array()
					);
					
					//Now get the subcategories, if any
					$query = "
					SELECT c.*, p.sef as parent_sef, p.name as parent_name, la.code as ls, cnf.value as hide_lang, cnf2.value as categories_filter, cnf3.value as trans_data, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, ld.code as dlc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_sub_category = c.id AND p.id_lang = c.id_lang AND p.post_status = 'published') as items
					FROM `" . DB_PREFIX . "categories` AS c
					INNER JOIN `" . DB_PREFIX . "categories` as p ON p.id = c.id_parent
					INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
					INNER JOIN `" . DB_PREFIX . "languages` as ld ON ld.id_site = c.id_site AND ld.is_default = 1
					INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = c.id_site
					INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site = c.id_site AND cnf.variable = 'hide_default_lang_slug'
					INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = c.id_site AND cnf2.variable = 'categories_filter'
					INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = c.id_site AND cnf3.variable = 'trans_data'
					WHERE 1=1 AND (c.id_parent = " . $cat['id'] . ") AND (c.id_lang = " . $lang['id'] . ") AND (c.id_blog = 0)
					ORDER BY c.name ASC";
			
					//Query: subcategories (no blogs)
					$subs = $db->from( null, $query )->all();
					
					if ( $subs )
					{
						foreach ( $subs as $sub )
						{
							$trans = CategoryTrans( $sub, $lang['code'], $cat['url'], $cat['ls'] );

							$categories[$lang['code']][$cat['sef']]['childs'][$sub['sef']] = array(
									'id' => $sub['id'],
									'name' => stripslashes( $sub['name'] ),
									'description' => stripslashes( $sub['descr'] ),
									'items' => $sub['items'],
									'groups' => ( !empty( $sub['groups_data'] ) ? Json( $sub['groups_data'] ) : null ),
									'url' => $catUrl . $sub['sef'] . PS,
									'trans' => $trans
							);
						}
					}
				}
			}
		}
		
		if ( $cache )
		{
			WriteCacheFile( $categories, $cacheFile );
		}
	}
	
	return $categories;
}

#####################################################
#
# Get Categories Based on their Blogs function
#
#####################################################
function BlogCats( $siteId = SITE_ID, $cache = true )
{
	if ( $siteId == SITE_ID )
	{
		$multi = MULTIBLOG;
	}
	
	else
	{
		$db = db();
		
		$tmp = $db->from( null,
		"SELECT enable_multiblog
		FROM `" . DB_PREFIX . "sites`
		WHERE (id = " . $siteId . ")"
		)->single();
		
		$multi = ( ( $tmp && ( $tmp['enable_multiblog'] == 'true' ) ) ? true : false );
	}
	
	if ( !$multi )
	{
		return GetCats( $siteId, $cache );
	}

	$cacheFile = CacheFileName( 'blog-categories', null, null, null, null, null, null, $siteId );
	
	if ( $cache && ValidCache( $cacheFile ) )
	{
		$categories = readCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		$categories = array();
		
		//Get the cats, but don't cache 'em
		$noBlogCats = GetCats( $siteId, false );
		
		//Get the languages
		$langList = $db->from( null,
		"SELECT id, code, title, locale
		FROM `" . DB_PREFIX . "languages`
		WHERE (id_site = " . $siteId . ")"
		)->all();
		
		if ( empty( $langList ) )
		{
			return $noBlogCats;
		}

		foreach( $langList as $lang )
		{
			$langArr = array(
				'langName' 		=> $lang['title'],
				'langCode' 		=> $lang['code'],
				'langLocale' 	=> $lang['locale'],
				'langId' 		=> $lang['id']
			);
		
			//Now get the blogs
			$blogs = $db->from( 
			null, 
			"SELECT id_blog, sef, name
			FROM `" . DB_PREFIX . "blogs`
			WHERE (id_site = " . (int) $siteId . ") AND (id_lang = " . $lang['id'] . " OR id_lang = 0)
			ORDER BY name ASC"
			)->all();
			
			if ( empty( $blogs ) )
			{
				continue;
			}
			
			foreach( $blogs as $blog )
			{
				$blogArr = array(
					'blogName' 		=> $blog['name'],
					'blogKey' 		=> $blog['sef'],
					'blogId' 		=> $blog['id_blog']
				);
				
				$query = "SELECT c.*, la.code as ls, b.sef as blog_sef, cnf.value as hide_lang, cnf2.value as categories_filter, cnf3.value as trans_data, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, ld.code as dlc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_category = c.id AND p.id_lang = c.id_lang AND p.post_status = 'published') as items
				FROM `" . DB_PREFIX . "categories` AS c
				INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
				INNER JOIN `" . DB_PREFIX . "languages` as ld ON ld.id_site = c.id_site AND ld.is_default = 1
				INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = c.id_site
				INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site = c.id_site AND cnf.variable = 'hide_default_lang_slug'
				INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = c.id_site AND cnf2.variable = 'categories_filter'
				INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = c.id_site AND cnf3.variable = 'trans_data'
				LEFT  JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
				WHERE (c.id_parent = 0) AND (c.id_site = " . $siteId . ") AND (c.id_lang = " . $lang['id'] . ") AND (c.id_blog = " . $blog['id_blog'] . ") ORDER BY c.name ASC";

				//Query: categories
				$cats = $db->from( null, $query )->all();

				if ( $cats )
				{
					foreach ( $cats as $cat )
					{
						$categories[$lang['code']][$blog['sef']][$cat['sef']] = array(
							'id' 			=> $cat['id'],
							'items' 		=> $cat['items'],
							'name' 			=> stripslashes( $cat['name'] ),
							'description' 	=> stripslashes( $cat['descr'] ),
							'url' 			=> BuildCategoryUrl( $cat, $cat['ls'] ),
							'trans' 		=> CategoryTrans( $cat, $cat['ls'], $cat['url'], $cat['ls'] ),
							'image' 		=> BuildImageArray( $cat['id_image'] ),
							'groups' 		=> ( !empty( $cat['groups_data'] ) ? Json( $cat['groups_data'] ) : null ),
							'langData' 		=> $langArr,
							'blogData' 		=> $blogArr,
							'childs' 		=> array()
						);
							
						//Get the subcategories, if any
						$query = "SELECT c.*, la.code as ls, b.sef as blog_sef, cnf.value as hide_lang, cnf2.value as categories_filter, cnf3.value as trans_data, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, ld.code as dlc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_category = c.id AND p.id_lang = c.id_lang AND p.post_status = 'published') as items
						FROM `" . DB_PREFIX . "categories` AS c
						INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
						INNER JOIN `" . DB_PREFIX . "languages` as ld ON ld.id_site = c.id_site AND ld.is_default = 1
						INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = c.id_site
						INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site = c.id_site AND cnf.variable = 'hide_default_lang_slug'
						INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = c.id_site AND cnf2.variable = 'categories_filter'
						INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = c.id_site AND cnf3.variable = 'trans_data'
						LEFT  JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
						WHERE (c.id_parent = " . $cat['id'] . ") AND (c.id_lang = " . $lang['id'] . ") AND (c.id_blog = " . $blog['id_blog'] . ")
						ORDER BY c.name ASC";

						//Query: categories
						$subs = $db->from( null, $query )->all();

						if ( $subs )
						{
							foreach ( $subs as $sub )
							{
								$categories[$lang['code']][$blog['sef']][$cat['sef']]['childs'][$sub['sef']] = array(
									'id' => $sub['id'],
									'name' => stripslashes( $sub['name'] ),
									'description' => stripslashes( $sub['descr'] ),
									'trans' => CategoryTrans( $sub, $sub['ls'], $sub['url'], $sub['ls'] ),
									'items' => $sub['items'],
									'groups' => ( !empty( $sub['groups_data'] ) ? Json( $sub['groups_data'] ) : null ),
									'url' => BuildCategoryUrl( $sub, $sub['ls'], false, true ),
									'parentUrl' => BuildCategoryUrl( $sub, $sub['ls'] ),
									'image' => BuildImageArray( $sub['id_image'] )
								);
							}
						}
						
					}
				}
			}
			
			//We also need the orphan categories
			if ( isset( $noBlogCats[$lang['code']] ) )
			{
				$categories[$lang['code']]['orphanCats'] = $noBlogCats[$lang['code']];
			}
		}

		//Cache the data only if we don't have DEBUG MODE enabled
		if ( $cache )
			WriteCacheFile( $categories, $cacheFile );
	}
	
	return $categories;
}