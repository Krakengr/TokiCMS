<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Build the full post array function
#
#####################################################
function BuildFullPostVars( $post )
{
	$data 	= BuildPostVars( $post );
	
	$hasAmp = $data['hasAmp'];
	
	$isAmp  = Router::GetVariable( 'isAmp' );

	$comm  	= ( $data['commentsEnabled'] ? GetPostComments( $post ) : array() );
	
	$extra 	= GetDataXtraPost( $post['id_post'] );
	$prices = GetPricesData( $post['id_post'], 'normal', true, $post );
	$deals  = GetPricesData( $post['id_post'], 'coupon', true, $post );
	
	$arr = array(
		'content'	 		=> CreatePostContent( $data['postRaw'], $data['title'], false, $post ),
		//Save some time and create the AMP content only if it's necessary
		'ampContent' 		=> ( $hasAmp && $isAmp ? CreatePostContent( $data['postRaw'], $data['title'], true, $post ) : null ),
		'ampCoverImage' 	=> ( $hasAmp && $isAmp ? BuildAmpCoverImageHtml( $data ) : null ),
		//'coverImageHtml' 	=> BuildCoverImageHtml( $data ),
		'subs'	 			=> PostSubs( $post ),
		'comments'	 		=> $comm,
		'author' 			=> BuildAuthorData( $post, true ),
		'video'				=> VideoDataBuild( $post ),
		'customTypes'		=> GetCustomAssocs( $post['id_post'] ),
		'images'			=> GetPostImages( $post['id_post'] ),
		'rating'			=> PostRating( $post ),
		'catGroups'			=> ( !empty( $post['cat_groups'] ) ? Json( $post['cat_groups'] ) : null ),
		'blogGroups'		=> ( !empty( $post['blog_groups'] ) ? Json( $post['blog_groups'] ) : null ),
		'subCatGroups'		=> ( !empty( $post['sub_groups'] ) ? Json( $post['sub_groups'] ) : null ),
		'trans'	 			=> PostTrans( $post, $data['postUrl'] ),
		'tags'	 			=> GetPostTags( $post ),
		'tagsHtml'	 		=> BuildPostTagsHtml( $post ),
		'previous'			=> ( $data['isPage'] ? null : GetNextPrevPost( $post ) ),
		'next'				=> ( $data['isPage'] ? null : GetNextPrevPost( $post, false ) ),
		'relatedPosts'		=> ( $data['isPage'] ? null : GetRelatedPosts( $post ) ),
		'topPosts'			=> ( $data['isPage'] ? null : GetTopPosts( $post ) ),
		'hasPrices'			=> ( !empty( $prices ) ? true : false ),
		'hasDeals'			=> ( !empty( $deals ) ? true : false ),
		'pricesData'		=> $prices,
		'dealsData'			=> $deals,
		'xtraData'			=> $extra,
		'blocksData'		=> GetBlocksData( $post['id_post'] ),
		'attributes'		=> GetPostAttributes( $post['id_post'] ),
		'variations'		=> GetPostVariations( $post['id_post'] ),
		'schemas'			=> BuildPostSchemas( $post ),
		'schemaData'		=> ( !empty( $post['schema_data'] ) ? Json( $post['schema_data'] ) : null ),
		'postData' 			=> ( isset( $extra['postData'] ) ? $extra['postData'] : null ),
	);

	return array_merge( $data, $arr );
}

#####################################################
#
# Build the post array function
#
#####################################################
function BuildPostVars( $post )
{
	$siteUrl 	= ( !empty( $post['url'] ) ? $post['url'] : SITE_URL );
	$format 	= ( !empty( $post['date_format'] ) ? $post['date_format'] : CurrentLang()['data']['date_format'] );
	$format    .= ( !empty( $post['time_format'] ) ? ' ' . $post['time_format'] : '' );
	
	if ( !empty( $post['comments_data'] ) )
	{
		$c = Json( $post['comments_data'] );
		
		$hideComments = ( !empty( $c['hide_comments'] ) ? $c['hide_comments'] : false );
		
		if ( !empty( $c['allow'] ) )
		{
			$commentsAllowed = false;
			
			if ( ( $post['post_type'] == 'page' ) && in_array( 'pages', $c['allow'] ) )
				$commentsAllowed = true;
		
			if ( ( $post['post_type'] == 'post' ) && in_array( 'posts', $c['allow'] ) )
				$commentsAllowed = true;
		}
	}
	
	else
	{
		$hideComments = false;
		$commentsAllowed = true;
	}

	$data = array(
		'id' => $post['id_post'],
		'title' => StripContent( $post['title'] ),
		'subTitle' => null,
		'titleEncoded' => urlencode( stripslashes( $post['title'] ) ),
		'description' => ( !empty( $post['description'] ) ? stripslashes( $post['description'] ) : ( isset( $post['post'] ) ? generateDescr ( $post['post'] ) : '' ) ),
		'sef' => $post['sef'],
		'postType' => $post['post_type'],
		'post' => ( !empty( $post['content'] ) ? $post['content'] : null ),
		'postStatus' => $post['post_status'],
		'hideOnHome' => ( isset( $post['hide_home'] ) ? $post['hide_home'] : 0 ),
		'parentId' => ( isset( $post['id_parent'] ) ? $post['id_parent'] : 0 ),
		'pageParentId' => 0,
		'pageParentUrl' => null,
		'pageParentTitle' => null,
		'pageParentSef' => null,
		'pageOrder' => ( isset( $post['page_order'] ) ? $post['page_order'] : 0 ),
		'postUrl' => null,
		'ampUrl'  => null,
		'urlEncoded' => null,
		'catGroups'	=> array(),
		'blogGroups' => array(),
		'subCatGroups' => array(),
		//'canEditPost' => CanEditPost( $post ),
		'blog'	=> array(),
		'site'	=> array(),
		'added'	=> array(),
		'category'	=> array(),
		'subcategory' => array(),
		'updated' => array(),
		'lastCommented' => ( !empty ( $post['lstc'] ) ? date( $format, $post['lstc'] ) : null ),
		'lastCommentedNice' => ( !empty ( $post['lstc'] ) ? niceTime( $post['lstc'] ) : null ),
		'video' => array(),
		'author' => BuildAuthorData( $post ),
		'otherAuthors' => array(),//TODO
		'language' => array(),
		'readingTime' => ReadingTime( StripContent( $post['post'] ) ),
		'views' => $post['views'],
		'customTypes' => array(),
		'disableComments' => ( isset( $post['disable_comments '] ) ? $post['disable_comments '] : 0 ),
		'externalUrl' => ( isset( $post['ext_url'] ) ? $post['ext_url'] : null ),
		'extId' => ( isset( $post['ext_id'] ) ? $post['ext_id'] : null ),
		'customId' => ( isset( $post['id_custom_type'] ) ? $post['id_custom_type'] : 0 ),
		'hasCoverImage' => ( ( isset( $post['cover_img'] ) && !empty( $post['cover_img'] ) ) ? true : false ),
		'coverImage' => ( ( isset( $post['cover_img'] ) && !empty( $post['cover_img'] ) ) ? Json( $post['cover_img'] ) : array() ),
		'hideComments' => $hideComments,
		'commentsEnabled' => ( IsTrue( $post['enable_comments'] ) && !$hideComments && $commentsAllowed ),
		'commentsCount'	=> $post['numcomm'],
		'comments'	=> array(),
		'hasComments' => ( $post['numcomm'] > 0 ),
		'canComment' => ( IsTrue( $post['enable_comments'] ) && $commentsAllowed && empty( $post['disable_comments'] ) ),
		'coverSrc' => null,
		'variations' => array(),
		'isPage' => ( $post['post_type'] == 'page' ),
		'postRaw' => ( isset( $post['post'] ) ? StripContent( $post['post'] ) : null ),
		'ampUrl' => null,
		'hasAmp' => false,
	);

	//Add this here to avoid errors
	$data['video'] = array(
		'fromContent' => false,
		'embed' => null,
		'url' => null
	);
		
	//This data is only for the blog browsing. It will be overwritten in single post mode
	if ( isset( $post['extra_val'] ) && !empty( $post['extra_val'] ) )
	{
		$temp = Json( $post['extra_val'] );
			
		if ( !empty( $temp ) )
		{
			$data['video'] = array(
				'fromContent' => false,
				'url' => $temp['video_url']
			);

			unset( $temp );
		}
	}
		
	//Add the blog data
	if ( IsTrue( $post['multiblog'] ) && !empty( $post['blog_sef'] ) )
	{
		$blogUrl = ( !empty( $post['blog_sef'] ) ? LangSlugUrl( $post ) . $post['blog_sef'] . PS : null );
			
		$data['blog'] = array(
			'name' => ( !empty( $post['blog_name'] ) ? $post['blog_name'] : null ),
			'sef' => ( !empty( $post['blog_sef'] ) ? $post['blog_sef'] : null ),
			'groups' => ( !empty( $post['blog_groups'] ) ? Json( $post['blog_sef'] ) : null ),
			'trans' => ( !empty( $post['blog_trans'] ) ? Json( $post['blog_trans'] ) : null ),
			'url' => $blogUrl,
			'postTemplate' => ( !empty( $post['bpt'] ) ? LangSlugUrl( $post ) . $post['bpt'] . PS : null ),
			'id' => $post['id_blog'],
			'html' => '<a href="' . $blogUrl . '" title="' . htmlspecialchars( $post['blog_name'] ) . '">' . $post['blog_name'] . '</a>'
		);
			
		if ( !empty( $data['blog']['trans'] ) && isset( $data['blog']['trans'][$post['ls']]['name'] ) )
		{
			$data['blog']['name'] = $data['blog']['trans'][$post['ls']]['name'];
		}
		
		$blogSef = $post['blog_sef'];
	}
	else
	{
		$blogSef = null;
		
		$data['blog'] = array(
			'name' => null,
			'sef' => null,
			'groups' => null,
			'trans' => null,
			'url' => null,
			'postTemplate' => null,
			'id' => 0,
			'html' => null
		);
	}
		
	//Add the site data
	$data['site'] = array(
		'name' 	=> ( isset( $post['st'] ) ? $post['st'] : null ),
		'url' 	=> ( isset( $post['url'] ) ? $post['url'] : $siteUrl ),
		'image' => ( isset( $post['site_image'] ) ? Json( $post['site_image'] ) : array() ),
		'id' 	=> ( isset( $post['id_site'] ) ? $post['id_site'] : SITE_ID )
	);

	//Add the language data
	$data['language'] = array(
		'id' => $post['id_lang'],
		'name' => $post['lt'],
		'key' => $post['ls'],
		'locale' => ( isset( $post['ll'] ) ? $post['ll'] : null ),
		'flag' => ( isset( $post['flagicon'] ) ? $post['flagicon'] : null ),
	);

	$data['added'] = array(
		'time' => date( $format, $post['added_time'] ),
		'nice' => niceTime( $post['added_time'] ),
		'raw' => $post['added_time'],
		'r' => date ( 'r', $post['added_time'] ),
		'c' => date( 'c', $post['added_time'] )
	);
		
	if ( !empty( $post['last_update'] ) )
	{
		$data['updated'] = array(
			'time' => date( $format, $post['last_update'] ),
			'nice' => niceTime( $post['last_update'] ),
			'raw' => $post['last_update'],
			'r' => date ( 'r', $post['last_update'] ),
			'c' => date( 'c', $post['last_update'] )
		);
	}
		
	//Build the url
	$postUrl 			= BuildPostUrl( $post );
	$data['postUrl'] 	= $postUrl;
	$data['urlEncoded'] = urlencode( $postUrl );
	
	//If this is a child page, set the correct URL
	if ( ( $post['post_type'] === 'page' ) && !empty( $post['id_page_parent'] ) && !empty( $post['parent_sef'] ) )
	{
		$data['pageParentUrl'] 		= BuildPostUrl( $post, true ) . $post['parent_sef'] . PS;
		$data['postUrl']			= BuildPostUrl( $post, false, true );
		$data['pageParentId'] 		= $post['id_page_parent'];
		$data['pageParentTitle'] 	= StripContent( $post['parent_title'] );
		$data['pageParentSef'] 		= $post['parent_sef'];
	}
	
	//Add the category and subcategory into the array
	if ( $post['post_type'] !== 'page' )
	{
		$catUrl = LangSlugUrl( $post ) . ( $blogSef ? $blogSef . PS : '' );
		
		//Add the categories filter
		$catUrl .= ltrim( CatFilter( $post['ls'] ), '/' ) . $post['cat_sef'] . PS;
		
		if ( !empty( $post['cat_id'] ) )
		{
			$data['category'] = array(
					'id' => $post['cat_id'],
					'key' => $post['cat_sef'],
					'color' => ( isset( $post['cat_color'] ) ? $post['cat_color'] : null ),
					'name' => stripslashes( $post['cat_name'] ),
					'url' => $catUrl,
					'html' => '<a href="' . $catUrl . '" ' . ( !empty( $post['cat_color'] ) ? ' style="color: ' . $post['cat_color'] . '"' : '' ) . 'title="' . htmlspecialchars( stripslashes( $post['cat_name'] ) ) . '">' . stripslashes( $post['cat_name'] ) . '</a>'
			);
		}
			
		if ( !empty( $post['sub_id'] ) )
		{
			$data['subcategory'] = array(
					'id' => $post['sub_id'],
					'key' => $post['sub_sef'],
					'color' => ( isset( $post['sub_color'] ) ? $post['sub_color'] : null ),
					'name' => stripslashes( $post['sub_name'] ),
					'url' => $catUrl . $post['sub_sef'] . PS,
					'html' => '<a href="' . $catUrl . $post['sub_sef'] . PS . '" ' . ( !empty( $post['sub_color'] ) ? ' style="color: ' . $post['sub_color'] . '"' : '' ) . 'title="' . htmlspecialchars( stripslashes( $post['sub_name'] ) ) . '">' . stripslashes( $post['sub_name'] ) . '</a>'
			);
		}
		
		$a = ( isset( $post['amp_data'] ) ? Json( $post['amp_data'] ) : null );
		
		$hasAmp = false;
		
		if ( isset( $post['enable_amp'] ) && IsTrue( $post['enable_amp'] ) && !empty( $a['content_types'] ) )
		{
			if ( ( $post['post_type'] == 'page' ) && in_array( 'pages', $a['content_types'] ) )
				$hasAmp = true;
			
			if ( ( $post['post_type'] == 'post' ) && in_array( 'posts', $a['content_types'] ) )
				$hasAmp = true;
		}
		
		$data['ampUrl'] = ( $hasAmp ? $postUrl . 'amp' . PS : null );
		$data['hasAmp']	= $hasAmp;
	}

	//Build the Cover Image's src set
	$coverSrc = BuildCoverSrc( $data );
	$data['coverSrc'] = $coverSrc;
		
	$data['subTitle'] = ( !empty( $data['postData'] ) && isset ( $data['postData']['subtitle'] )
			? html_entity_decode( $data['postData']['subtitle'] ) : null );

	$data['coverImageHtml'] = BuildCoverImageHtml( $data );

	return $data;
}

#####################################################
#
# Check for video data in the Content function
#
#####################################################
function CheckVideoContent( $post )
{
	$db = db();
	
	if ( empty( $post['video_data'] ) )
	{
		return null;
	}
	
	$videoSettings = Json( $post['video_data'] );
	
	$siteId		   = ( !empty( $post['id_site'] ) ? $post['id_site'] : SITE_ID );
		
	if ( empty( $videoSettings ) || !isset( $videoSettings['enable_indexation_videos'] ) || !$videoSettings['enable_indexation_videos'] )
		return null;

	$p = StripContent( $post['post'] );

	$data = $temp = array();

	//Get only one video
	preg_match('/\[video.+id="([0-9]+)".*]/iU', $p, $matches );

	if ( !empty( $matches ) )
	{
		$id = $matches['1'];
		
		$query = "SELECT filename, title, added_time, caption, external_url, mime_type, extra_data, file_ext
		FROM `" . DB_PREFIX . "images` WHERE (id_image = " . (int) $id . ")";
	
		//Query: image/video
		$vid = $db->from( null, $query )->single();

		if ( !empty( $vid ) )
		{
			$temp['source'] = 'generic';
			$temp['id'] = '';
				
			if ( empty( $vid['external_url'] ) )
			{
				$query = "SELECT value FROM `" . DB_PREFIX . "config` WHERE (id_site = " . $siteId . ") AND (variable = 'images_html')";

				//Query: images html
				$tmp = $db->from( null, $query )->single();

				$html = ( !empty( $tmp['value'] ) ? $tmp['value'] : SITE_URL . 'uploads' . PS );

				$temp['url'] = FolderUrlByDate( $vid['added_time'], $html ) . $vid['filename'];
				
				$query = "SELECT enable_multisite, share_data FROM `" . DB_PREFIX . "sites` WHERE (id = " . $siteId . ")";

				//Query: site settings
				$tmp = $db->from( null, $query )->single();
					
				$json = Json( $tmp['share_data'] );
					
				$locally = ( !empty( $json['sync_uploads'] ) ? $json['sync_uploads'] : false );
					
				if ( !$locally && IsTrue( $tmp['enable_multisite'] ) && ( $siteId != SITE_ID ) )
				{
					$query = "SELECT s.id, s.url, c.value FROM `" . DB_PREFIX . "sites` as s 
					LEFT JOIN `" . DB_PREFIX . "config` as c ON c.id_site = s.id AND c.variable = 'images_html'
					WHERE (s.is_primary = 1)";

					//Query: default site settings
					$site = $db->from( null, $query )->single();
		
					if ( $site )
					{
						$html = ( !empty( $site['value'] ) ? $site['value'] : $site['url'] . 'uploads' . PS );

						$temp['url'] = FolderUrlByDate( $vid['added_time'], $html ) . $vid['filename'];
					}
				}
			}
			
			else
			{
				$temp['url'] = $vid['external_url'];
			}
		}

		else
		{
			//Get only one youtube video
			preg_match('#(?<!"|>)https?:\/\/(www\.)?youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)(?<!"|<)#i', $p, $matches );
		
			if ( !empty( $matches ) )
			{
				$temp = array(
					'source'  => 'youtube',
					'url'  	  => $matches['1'],
					'id'  	  => $matches['3']
				);
			}
		}
	
		if ( !empty( $temp ) )
		{
			$args = array(
					'source'  => $temp['source'],
					'url'  	  => $temp['url'],
					'id'  	  => $temp['id']
			);

			$data['url'] 			= $temp['url'];
			$data['embed'] 			= IFrame( $args, false );
			$data['amp'] 			= IFrame( $args, true );
			$data['fromContent'] 	= true;
		}
	}
	
	return $data;
}

#####################################################
#
# Deletes a price data from the DB function
#
#####################################################
function DeletePrice( $id )
{
	$db = db();
	
	//Delete this price
	$db->delete( 'prices' )->where( "id_price", $id )->run();

	//Delete any other data this price may have
	$db->delete( 'price_info' )->where( "id_price", $id )->run();

	$db->delete( 'price_update_info' )->where( "id_price", $id )->run();
}

#####################################################
#
# Get Post By Slug function
#
#####################################################
function GetPostBySlug( $slug, $cache = true )
{
	$db 	= db();

	$query = PostDefaultQuery( "(p.id_site = " . SITE_ID . ") AND (p.sef = :sef) AND (p.post_type = 'post' OR p.post_type = 'page') AND (p.post_status = 'published') AND (b.disabled = 0 OR b.disabled IS NULL)" );

	//Query: post
	$tmp = $db->from( null, $query, array( $slug => ':sef' ) )->single();

	if ( empty( $tmp ) )
	{
		return null;
	}

	$s = GetSettingsData( $tmp['id_site'] );

	if ( empty( $s ) )
	{
		return null;
	}

	$tmp = array_merge( $tmp, $s );
	
	$data = BuildFullPostVars( $tmp );
	
	return new Post( $data );
}

#####################################################
#
# Get Single Post By Id function
#
#####################################################
function GetSinglePost( $id, $siteId = null, $cache = true, $full = false, $all = false, $getFull = false, $top = false, $rel = false, $nexpre = false )
{	
	try
	{
		$post 					= new GetPost;
		$post->id 				= (int) $id;
		$post->siteId 			= $siteId;
		$post->cache 			= $cache;
		$post->buildFullArr 	= $getFull;
		$post->build 			= $full;
		$post->anyStatus 		= $all;
		$post->getTopPosts 		= $top;
		$post->getRelatedPosts 	= $rel;
		$post->getNextPrevPosts = $nexpre;

		return $post->GetPost();
	}

	catch( Exception $e )
	{
		//
	}
	
	$log = Settings::LogSettings();

	if ( !empty( $log ) && $log['enable_error_log'] && $log['enable_not_found_log'] )
	{
		$errorMessage = 'Post couldn\'t be fetched (id: ' . $id . ')';

		Log::Set( $errorMessage, null, null, 'system' );
	}

	return null;

	/*
		
	$id = (int) $id;
	
	$cacheFile = CacheFileName( 'single-post_' . $id, null, null, null, null, null, null, ( $siteId ? $siteId : SITE_ID ) );
	
	//Get the data from the cache, if is valid
	if ( $cache && ValidOtherCache( $cacheFile ) )
	{
		$data = ReadCache( $cacheFile );
	}
		
	//Get the data and save it to the cache, if needed...
	else
	{
		$db = db();
		$query = PostDefaultQuery( ( $siteId ? "(p.id_site = " . $siteId . ") AND " : "" ) . "(p.id_post = :id) AND (p.post_type = 'post' OR p.post_type = 'page')" . ( !$all ? " AND (p.post_status = 'published')" : "" ) . " AND (b.disabled = 0 OR b.disabled IS NULL)" );

		$binds = array( $id => ':id' );

		//Query: post
		$tmp = $db->from( null, $query, $binds )->single();
		
		if ( empty( $tmp ) )
		{
			$log = Settings::LogSettings();

			if ( !empty( $log ) && $log['enable_error_log'] && $log['enable_not_found_log'] )
			{
				$errorMessage = 'Post couldn\'t be fetched (id: ' . $id . ')';

				if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
				{
					$errorData = 'Query: ' . PHP_EOL . $query;
				}
				
				Log::Set( $errorMessage, $errorData, $query, 'system' );
			}
			
			return null;
		}
		
		$s = GetSettingsData( $tmp['id_site'] );
		
		if ( empty( $s ) )
		{
			return null;
		}
		
		$tmp = array_merge( $tmp, $s );
		
		$data = BuildFullPostVars( $tmp );
		var_dump( $data );exit;
		if ( $cache )
		{
			WriteOtherCacheFile( $data, $cacheFile );
		}
	}
	
	return ( $full ? new Post( $data ) : $data );*/
}

#####################################################
#
# Get some settings needed for post building function
#
#####################################################
function GetSettingsData( $siteId )
{
	$db = db();
	
	$siteId = (int) $siteId;
	
	return $db->from( null,
	"SELECT s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, s.enable_multisite as multisite, s.title as st,
	cnf.value as hide_lang, cnf2.value as enable_comments, cnf3.value as disable_author_archives, cnf4.value as comments_data, cnf5.value as enable_amp, cnf6.value as amp_data, cnf7.value as enable_lazyloader, cnf8.value as seo_data, cnf9.value as post_not, cnf10.value as enable_reviews, cnf11.value as video_data, cnf12.value as site_image, cnf13.value as schema_data
	FROM `" . DB_PREFIX . "sites` AS s
	INNER JOIN `" . DB_PREFIX . "config` as cnf   ON cnf.id_site   = " . $siteId . " AND cnf.variable   = 'hide_default_lang_slug'
	INNER JOIN `" . DB_PREFIX . "config` as cnf2  ON cnf2.id_site  = " . $siteId . " AND cnf2.variable  = 'enable_comments'
	INNER JOIN `" . DB_PREFIX . "config` as cnf3  ON cnf3.id_site  = " . $siteId . " AND cnf3.variable  = 'disable_author_archives'
	INNER JOIN `" . DB_PREFIX . "config` as cnf4  ON cnf4.id_site  = " . $siteId . " AND cnf4.variable  = 'comments_data'
	INNER JOIN `" . DB_PREFIX . "config` as cnf5  ON cnf5.id_site  = " . $siteId . " AND cnf5.variable  = 'enable_amp'
	INNER JOIN `" . DB_PREFIX . "config` as cnf6  ON cnf6.id_site  = " . $siteId . " AND cnf6.variable  = 'amp_data'
	INNER JOIN `" . DB_PREFIX . "config` as cnf7  ON cnf7.id_site  = " . $siteId . " AND cnf7.variable  = 'enable_lazyloader'
	INNER JOIN `" . DB_PREFIX . "config` as cnf8  ON cnf8.id_site  = " . $siteId . " AND cnf8.variable  = 'seo_data'
	INNER JOIN `" . DB_PREFIX . "config` as cnf9  ON cnf9.id_site  = " . $siteId . " AND cnf9.variable  = 'allow_post_notifications'
	INNER JOIN `" . DB_PREFIX . "config` as cnf10 ON cnf10.id_site = " . $siteId . " AND cnf10.variable = 'enable_reviews'
	INNER JOIN `" . DB_PREFIX . "config` as cnf11 ON cnf11.id_site = " . $siteId . " AND cnf11.variable = 'video_data'
	INNER JOIN `" . DB_PREFIX . "config` as cnf12 ON cnf12.id_site = " . $siteId . " AND cnf12.variable = 'site_image'
	INNER JOIN `" . DB_PREFIX . "config` as cnf13 ON cnf13.id_site = " . $siteId . " AND cnf13.variable = 'schema_data'
	WHERE (s.id = " . $siteId . ")"
	)->single();
}

#####################################################
#
# Build the video data function
#
#####################################################
function VideoDataBuild( $post )
{
	$data = array();
	
	if ( isset( $post['xtraData']['video'] ) && !empty( $post['xtraData']['video'] ) )
	{
		$temp = $post['xtraData']['video'];
		
		$data = array(
			'id' 			=> $temp['videoID'],
			'playlist'		=> $temp['playlistId'],
			'url' 			=> $temp['videoUrl'],
			'fromContent'   => false,
			'embed' 		=> ( isset( $temp['embed_code'] ) ? html_entity_decode( trim( $temp['embed_code'] ) ) : null ),
		);

		if ( empty( $data['embed'] ) && !empty( $temp['videoID'] ) )
		{
			$args = array(
					'source'  => 'generic',
					'width'   => $temp['videoWidth'],
					'height'  => $temp['videoHeight'],
					'id'  	  => $temp['videoID'],
					'url'  	  => 'https://www.youtube.com/embed/' . $temp['videoID'] //Only Youtube for now
			);
			
			$data['embed'] = IFrame( $args, false );
			$data['amp'] = IFrame( $args, true );
			$data['fromContent'] = false;
		}
		
		unset( $temp );
	}
		
	else 
	{
		$data = CheckVideoContent( $post );
	}
	
	return $data;
}
	
#####################################################
#
# Get Post Rating function
#
#####################################################
function PostRating( $post )
{
	if ( !IsTrue( $post['enable_reviews'] ) )
		return null;

	$db = db();
	
	//Query: rating
	$tmp = $db->from( null, "
	SELECT ROUND(AVG(rating), 1) as numRating
	FROM `" . DB_PREFIX . "comments`
	WHERE (id_post = " . $post['id_post'] . ")"
	)->single();
	
	return ( !empty( $tmp ) ? $tmp['numRating'] : null );
}

#####################################################
#
# Get Post Translations function
#
#####################################################
function PostTrans( $post, $url )
{
	$db = db();
	
	$data = array();

	$data[$post['ls']] = array(
		'url' 	=> $url,
		'id' 	=> $post['id_post'],
		'title' => $post['title'],
		'lang' 	=> $post['ll']
	);

	//Is this a child post? We have to do some work here
	if ( !empty( $post['id_parent'] ) )
	{
		$query = "SELECT p.sef, p.title, p.id_post, b.sef as blog_sef, la.code as ls, la.locale as lc
			FROM `" . DB_PREFIX . POSTS . "` as p
			INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = p.id_lang
			LEFT JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = p.id_blog
			WHERE 1=1 AND (p.id_post = " . $post['id_parent'] . ") AND (p.post_type = '" . $post['post_type'] . "') AND (p.post_status = 'published')";
			
		//Query: post parent
		$parent = $db->from( null, $query )->single();
		
		if ( !empty( $parent ) )
		{
			$url = ( ( isset( $post['url'] ) && !empty( $post['url'] ) ) ? $post['url'] : SITE_URL );
			
			if ( IsTrue( $post['multilang'] ) && !empty( $parent['ls'] ) && ( !IsTrue( $post['hide_lang'] ) || ( IsTrue( $post['hide_lang'] ) && ( $parent['ls'] != $post['ls'] ) ) ) )
				$url .= $parent['ls'] . PS;
			
			if ( !StaticHomePage( false, $parent['id_post'] ) )
			{
				$url .= ( IsTrue( $post['multiblog'] ) && !empty( $parent['blog_sef'] ) ? $parent['blog_sef'] . PS : '' ) . ltrim( PostFilter( $parent['ls'] ), '/' ) . $parent['sef'] . PS;
			}
			
			$data[$parent['ls']] = array (
					'url' => $url,
					'id' => $parent['id_post'],
					'title' => $parent['title'],
					'lang' => $parent['lc']
			);
		}
		
		//Now check for other posts that have the same parent
		$query = "SELECT p.sef, p.title, p.id_post, b.sef as blog_sef, la.code as ls, la.locale as lc
			FROM `" . DB_PREFIX . POSTS . "` as p
			INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = p.id_lang
			LEFT JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = p.id_blog
			WHERE 1=1 AND (p.id_parent = " . $post['id_parent'] . ") AND (p.post_type = '" . $post['post_type'] . "') AND (p.post_status = 'published') AND (p.id_post != " . $post['id_post'] . ")";

		//Query: childs
		$childs = $db->from( null, $query )->all();
		
		if ( $childs )
		{
			foreach( $childs as $child )
			{
				$url = ( ( isset( $post['url'] ) && !empty( $post['url'] ) ) ? $post['url'] : SITE_URL );
			
				if ( IsTrue( $post['multilang'] ) && !empty( $child['ls'] ) && ( !IsTrue( $post['hide_lang'] ) 
					|| ( IsTrue( $post['hide_lang'] ) && ( $child['ls'] != $post['ls'] ) ) ) 
				)
				{
					$url .= $child['ls'] . PS;
				}
					
				if ( !StaticHomePage( false, $post['id_parent'] ) )
				{
				
					$url .= ( IsTrue( $post['multiblog'] ) && !empty( $child['blog_sef'] ) ? $child['blog_sef'] . PS : '' ) . ltrim( PostFilter( $child['ls'] ), '/' ) . $child['sef'] . PS;
				}
			
				$data[$child['ls']] = array (
					'url' 	=> $url,
					'id' 	=> $child['id_post'],
					'title' => $child['title'],
					'lang'	=> $child['lc']
				);
			}
		}
	}
	//This will be easier
	else
	{
		//Now check for other posts that have the same parent
		$query = "SELECT p.sef, p.title, p.id_post, b.sef as blog_sef, la.code as ls, la.locale as lc
			FROM `" . DB_PREFIX . POSTS . "` as p
			INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = p.id_lang
			LEFT JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = p.id_blog
			WHERE 1=1 AND (p.id_parent = " . $post['id_post'] . ") AND (p.post_type = '" . $post['post_type'] . "') AND (p.post_status = 'published')";

		//Query: childs
		$childs = $db->from( null, $query )->all();
		
		if ( $childs )
		{
			foreach( $childs as $child )
			{
				$url = ( ( isset( $post['url'] ) && !empty( $post['url'] ) ) ? $post['url'] : SITE_URL );
			
				if ( IsTrue( $post['multilang'] ) && !empty( $child['ls'] ) && ( !IsTrue( $post['hide_lang'] ) 
					|| ( IsTrue( $post['hide_lang'] ) && ( $child['ls'] != $post['ls'] ) ) ) 
				)
				{
					$url .= $child['ls'] . PS;
				}
				
				if ( !StaticHomePage( false, $post['id_post'] ) )
				{
					$url .= ( IsTrue( $post['multiblog'] ) && !empty( $child['blog_sef'] ) ? $child['blog_sef'] . PS : '' ) . ltrim( PostFilter( $child['ls'] ), '/' ) . $child['sef'] . PS;
				}
				
				$data[$child['ls']] = array (
					'url' 	=> $url,
					'id' 	=> $child['id_post'],
					'lang' 	=> $child['lc']
				);
			}
		}
	}

	return $data;
}

#####################################################
#
# Get Post Variations function
#
#####################################################
function GetPostVariations( $postId )
{
	$db = db();
	
	//First check if we have an item with this id and get its parent	
	//Query: parent
	$v = $db->from( null, "
	SELECT id_parent
	FROM `" . DB_PREFIX . "post_variations_items`
	WHERE (id_post = " . $postId . ")"
	)->single();

	if( !$v ) 
		return null;
	
	//Now get the info of this parent
	$query = "SELECT id, title, sef, description, trans_data FROM `" . DB_PREFIX . "post_variations` WHERE (id = " . $v['id_parent'] . ")";
	
	//Query: variations
	$p = $db->from( null, $query )->single();
	
	if( !$p ) 
		return null;
	
	$data = array(
		'id' 		 	=> $p['id'],
		'title' 	 	=> StripContent( $p['title'] ),
		'sef' 	 	  	=> StripContent( $p['sef'] ),
		'description' 	=> StripContent( $p['description'] ),
		'trans' 		=> Json( $p['trans_data'] ),
		'variations'  	=> array()	
	);
	
	//Now get every item from this parent
	$query = "SELECT * FROM `" . DB_PREFIX . "post_variations_items` WHERE (id_parent = " . $v['id_parent'] . ") ORDER BY var_order ASC";
	
	//Query: variations
	$vars = $db->from( null, $query )->all();

	if ( !$vars )
		return $data;
	
	foreach ( $vars as $var )
	{
		$data['variations'][] = array(
			'id'					=> $var['id'],
			'order' 				=> $var['var_order'],
			'imageId'				=> $var['id_image'],
			'postId'				=> $var['id_post'],
			'parentId'				=> $var['id_parent'],
			'sku'					=> $var['sku'],
			'quantity'				=> $var['quantity'],
			'subtrackStock'			=> $var['subtrack_stock'],
			'frontendVisibility'	=> $var['frontend_visibility'],
			'salePrice'				=> $var['sale_price'],
			'weight'				=> $var['weight'],
			'points'				=> $var['points'],
			'subtrackPrice'			=> $var['subtrack_price'],
			'subtrackWeight'		=> $var['subtrack_weight'],
			'subtrackPoints'		=> $var['subtrack_points'],
			'title'					=> StripContent( $var['title'] ),
			'postTitle'				=> StripContent( $var['ptitle'] ),
			'sef'					=> StripContent( $var['sef'] ),
			'url'					=> StripContent( $var['url'] )
		);
	}

	return $data;
}

#####################################################
#
# Get Post Attribute function
#
#####################################################
function GetPostAttributes( $postId )
{
	$db = db();
	
	$query = "SELECT a.id, a.value, a.id_attr, g.id as gid, g.name as gname, p.name, p.trans_data
	FROM `" . DB_PREFIX . "post_attribute_data` AS a
	LEFT JOIN `" . DB_PREFIX . "post_attributes` as p ON p.id = a.id_attr
	LEFT JOIN `" . DB_PREFIX . "post_attr_group` as g ON g.id = p.id_group
	WHERE 1=1 AND (a.id_post = " . $postId . ") ORDER BY g.group_order ASC";
			
	//Query: attributes
	$a = $db->from( null, $query )->all();

	if ( !$a )
		return null;
	
	$data = array();
	
	foreach( $a as $at )
	{
		$data[$at['id_attr']] = array(
			'id' => $at['id'],
			'groupId' => $at['gid'],
			'attrId' => $at['id_attr'],
			'value' => StripContent( $at['value'] ),
			'name' => StripContent( $at['name'] ),
			'group' => StripContent( $at['gname'] ),
			'trans' => ( !empty( $at['trans_data'] ) ? Json( $at['trans_data'] ) : null ),
		);
	}

	return $data;
}

#####################################################
#
# Get Post Images function
#
#####################################################
function GetPostImages( $postId )
{
	$db = db();
	
	$query = "SELECT * FROM `" . DB_PREFIX . "images` WHERE 1=1 AND (id_post = " . $postId . ") AND (img_type = 'post') AND (aproved = 1)";

	//Query: images
	$imgs = $db->from( null, $query )->all();
	
	if ( empty( $imgs ) )
		return false;
	
	foreach( $imgs as $img )
	{
		$data[] = array(
			'id' => $img['id_image'],
			'filename' => stripslashes( $img['filename'] ),
			'title' => stripslashes( $img['title'] ),
			'alt' => stripslashes( $img['alt'] ),
			'descr' => stripslashes( $img['descr'] ),
			'caption' => stripslashes( $img['caption'] ),
			'width' => $img['width'],
			'height' => $img['height'],
			'size' => $img['size'],
			'imageUrl' => ( !empty( $img['external_url'] ) ? $img['external_url'] : FolderUrlByDate( $img['added_time'] ) . stripslashes( $img['filename'] ) )
		);
	}

	return $data;
}

#####################################################
#
# Get Blocks Data function
#
#####################################################
function GetBlocksData( $postId )
{
	$db = db();
	
	$data = null;
		
	//Query: Blocks data
	$x = $db->from( null, "
	SELECT blocks
	FROM `" . DB_PREFIX . POSTS . "`
	WHERE (id_post = " . $postId . ")"
	)->single();

	if ( !$x )
		return $data;

	if ( !empty( $x['blocks'] ) )
	{
		$temp = Json( $x['blocks'] );
		
		if ( !empty( $temp ) && isset( $temp['blocks'] ) )
		{
			$data = $temp['blocks'];
		}
	}

	return $data;
}

#####################################################
#
# Build the post's schemas function
#
# Do it here, so we can cache the results
#
#####################################################
function BuildPostSchemas( $post )
{
	//TODO
	return null;
	
	$data = GetPostSchema( $post );

	if ( empty( $data ) )
		return '';

	//include ( ARRAYS_ROOT . 'seo-arrays.php');

	$schemas = array();
	
	$html = '';

	foreach( $data as $schema )
	{
		//$i = 0;
		
		//$scmData = Json( $schema['data'] );
		//$scmXtraData = $scmData['custom-data'];
		
		//$scmData = $scmData['data'];
		
		$schemas[] = ReturnSchemaByType( $post, $schema );
	}
	
	//print_r($schemas);
	//exit;
	//return $html;
}

#####################################################
#
# Get certain image function
#
#####################################################
function GetFullImage( $id, $langCode = null, $siteId = null )
{
	$db = db();
	
	$coverImg = array();
	
	$langCode = ( $langCode ? $langCode : CurrentLang()['lang']['code'] );
	
	$siteId	  = ( $siteId ? $siteId : SITE_ID );
	
	$query = "SELECT id_image, filename, width, height, size, mime_type, added_time, trans_data, id_parent
	FROM `" . DB_PREFIX . "images` WHERE (id_image = " . $id . ")";

	//Query: image
	$_img = $db->from( null, $query )->single();

	if ( !$_img )
		return null;
	
	//If this image has parent, we need its parent...
	if ( !empty( $_img['id_parent'] ) )
	{
		$query = "SELECT id_image, filename, width, height, size, mime_type, added_time, trans_data
		FROM `" . DB_PREFIX . "images` WHERE (id_image = " . $_img['id_parent'] . ")";

		//Query: image
		$_img = $db->from( null, $query )->single();
		
		if ( !$_img )
			return null;
	}
	
	$query = "SELECT value FROM `" . DB_PREFIX . "config` WHERE (id_site = " . $siteId . ") AND (variable = 'images_html')";

	//Query: images html
	$tmp = $db->from( null, $query )->single();
	
	$html = ( !empty( $tmp['value'] ) ? $tmp['value'] : SITE_URL . 'uploads' . PS );
	
	$imageUrl = FolderUrlByDate( $_img['added_time'], $html ) . $_img['filename'];
	
	$imgData = Json( $_img['trans_data'] );
			
	$imgData = ( !empty( $imgData ) && isset( $imgData[$langCode] ) ? $imgData[$langCode] : null );
	
	$query = "SELECT enable_multisite, share_data FROM `" . DB_PREFIX . "sites` WHERE (id = " . $siteId . ")";

	//Query: site settings
	$tmp = $db->from( null, $query )->single();
	
	$json = Json( $tmp['share_data'] );
	
	$locally = ( !empty( $json['sync_uploads'] ) ? $json['sync_uploads'] : false );

	if ( !$locally && IsTrue( $tmp['enable_multisite'] ) && ( $siteId != SITE_ID ) )
	{
		$query = "SELECT s.id, s.url, c.value FROM `" . DB_PREFIX . "sites` as s 
		LEFT JOIN `" . DB_PREFIX . "config` as c ON c.id_site = s.id AND c.variable = 'images_html'
		WHERE (s.is_primary = 1)";

		//Query: default site settings
		$site = $db->from( null, $query )->single();

		if ( $site )
		{
			$html = ( !empty( $site['value'] ) ? $site['value'] : $site['url'] . 'uploads' . PS );

			$imageUrl = FolderUrlByDate( $_img['added_time'], $html ) . $_img['filename'];
		}
	}

	$coverImg = array(
			'imageId' => $_img['id_image'],
			'mimeType' => $_img['mime_type'],
			'imageFilename' => $_img['filename'],
			'imageCaption' 	=> ( $imgData ? StripContent( $imgData['caption'] ) : '' ),
			'imageTitle' 	=> ( $imgData ? StripContent( $imgData['title'] ) : '' ),
			'imageAlt' 		=> ( $imgData ? StripContent( $imgData['alt'] ) : '' ),
			'imageDescr' 	=> ( $imgData ? StripContent( $imgData['descr'] ) : '' ),
			'sizes' => array(
					'default' => array(
						'imgSize' => $_img['size'],
						'imageWidth' => $_img['width'],
						'imageHeight' => $_img['height'],
						'imageUrl' => $imageUrl
					),

					$_img['width'] => array(
						'imgSize' => $_img['size'],
						'imageWidth' => $_img['width'],
						'imageHeight' => $_img['height'],
						'imageUrl' => $imageUrl
					)
			)
	);
	
	$query = "SELECT id_image, filename, width, height, size
	FROM `" . DB_PREFIX . "images` WHERE (id_parent = " . $_img['id_image'] . ") ORDER BY width ASC";

	//Query: child images
	$imgs = $db->from( null, $query )->all();

	if ( $imgs )
	{
		foreach( $imgs as $img )
		{
			$imgUrl = FolderUrlByDate( $_img['added_time'], $html ) . $img['filename'];

			$coverImg['sizes'][$img['width']] = array(
							'imageFilename' => $img['filename'],
							'imageWidth' => $img['width'],
							'imageHeight' => $img['height'],
							'imageUrl' => $imgUrl,
							'imgSize' => $img['size'],
							'imageId' => $img['id_image']
			);
		}
	}

	return $coverImg;
}

#####################################################
#
# Replaces the [price*], [posts*] shortcode in content function
#
#####################################################
function ReplaceOtherShortCode( $p, $post = null )
{
	//In post link
	$p = preg_replace_callback('/\[interlink.+id="([0-9]+)".+target="(.*)".+descr="(.*)".+prices="(.*)".*]/i', function( $m ) use ( $post )
	{
		$id 	= $m['1'];
		$target = '_' . ( !empty( $m['2'] ) ? $m['2'] : 'self' );
		$descr 	= ( ( $m['3'] == 'true' ) ? true : false );
		$prices = ( ( $m['4'] == 'true' ) ? true : false );
		
		$pst = GetSinglePost( $id, null, false, false, false, false );

		if ( !$pst )
			return null;
		
		$description 	= ( $descr ? '<p>' . $pst['description'] . '</p>' : '' );
		$description   .= ( $prices ? PriceListHtml( $id, $post ) : '' );

		$image			= ( !empty( $pst['coverImage']['default']['imageUrl'] ) ? $pst['coverImage']['default']['imageUrl'] : null );
		
		$siteName		= ( !empty( $pst['site']['name'] ) ? $pst['site']['name'] : '' );
		
		$html = TextLinkEmbed( $pst['title'], $description, $pst['postUrl'], $target, $image, $siteName );
		
		unset( $pst );
		
		return $html;
	}, $p );
	
	
	//Price List
	$p = preg_replace_callback('/\[price-list.+id="([0-9]+)"]/i', function( $m ) use ( $post )
	{
		if ( !empty( $m ) )
		{
			$id = $m['1'];

			$html = PriceListHtml( $id, $post );
			
			return $html;
		}
		
		return null;
	}, $p );
	
	//Google Map
	$p = preg_replace_callback('/\[g-map.+width="([0-9]+)".+height="([0-9]+)".+marker="(.*)".+zoom="([0-9]+)".+title="(.*)".+css="(.*)"]/i', function( $m ) use ( $post )
	{
		if ( !empty( $m ) )
		{
			$html = '<iframe width="' . $m['1'] . '" height="' . $m['2'] . '" src="//maps.google.com/maps?q=' . urlencode( $m['3'] ) . '&amp;output=embed" title="' . htmlspecialchars( $m['4'] ) . '"' . ( !empty( $m['5'] ) ? ' style="' . $m['5'] . '"' : '' ) . '></iframe>';
			
			return $html;
		}
		
		return null;
	}, $p );
	
	//Best List
	$p = preg_replace_callback('/\[best-price.+id="([0-9]+)"]/i', function( $m ) use ( $post )
	{
		if ( !empty( $m ) )
		{
			$id = $m['1'];
			
			$p = GetTopPriceData( $id, 'normal', $post );

			if ( empty( $pr ) )
				return null;

			$html = '<p><a href="' . $p['outUrl'] . '" target="_blank" rel="nofollow">' . $p['title'] . ' - ' . $p['storeName'];

			if ( $p['salePriceRaw'] > 0 )
			{
				$html .= ' - ';
						
				if ( $p['notFound'] )
				{
					$html .= '<del title="Not Found">';
				}
						
				if ( $p['startingPrice'] )
				{
					$html .= __( 'from' ) . ' ';
				}
						
				$html .= $p['priceFixed'];
						
				if ( $p['notFound'] )
				{
					$html .= '</del>';
				}

			}

			$html .= '</a></p>';
			
			return $html;
		}
		
		return null;
	}, $p );
	
	//Single Price
	$p = preg_replace_callback('/\[price.+id="([0-9]+)"]/i', function( $m ) use ( $post )
	{
		if ( !empty( $m ) )
		{
			$id = $m['1'];
			
			$p = GetSinglePricesData( $id, $post );

			if ( !$p )
				return null;
			
			$html = '<p><a href="' . $p['outUrl'] . '" target="_blank" rel="nofollow">' . $p['title'] . ' - ' . $p['storeName'];
			
			if ( $p['salePriceRaw'] > 0 )
			{
				$html .= ' - ';
				
				if ( $p['notFound'] )
				{
					$html .= '<del title="Not Found">';
				}
				
				if ( $p['startingPrice'] )
				{
					$html .= __( 'from' ) . ' ';
				}
				
				$html .= $p['priceFixed'];
				
				if ( $p['notFound'] )
				{
					$html .= '</del>';
				}

			}
			
			$html .= '</a></p>';

			return $html;
		}
		
		return null;
	}, $p );

	return $p;
}


#####################################################
#
# Replaces the [video] shortcode in content function
#
#####################################################
function ReplaceVideosShortCode( $p, $amp = false, $post = null )
{
	$db = db();
	
	$p = preg_replace_callback('/\[video.+id="([0-9]+)".*]/iU', function( $m ) use ( $amp, $post, $db )
	{
		if ( !empty( $m ) )
		{
			$id = $m['1'];
			
			$siteId = ( !empty( $post['id_site'] ) ? $post['id_site'] : SITE_ID );
			
			$query = "SELECT filename, title, added_time, caption, external_url, mime_type, extra_data, file_ext
			FROM `" . DB_PREFIX . "images` WHERE (id_image = " . (int) $id . ")";
			
			//Query: video
			$vid = $db->from( null, $query )->single();

			if ( !empty( $vid ) )
			{
				if ( empty( $vid['external_url'] ) )
				{
					$query = "SELECT value FROM `" . DB_PREFIX . "config` WHERE (id_site = " . $siteId . ") AND (variable = 'images_html')";

					//Query: images html
					$tmp = $db->from( null, $query )->single();
					
					$html = ( !empty( $tmp['value'] ) ? $tmp['value'] : SITE_URL . 'uploads' . PS );
		
					$fileUrl = FolderUrlByDate( $vid['added_time'], $html ) . $vid['filename'];
					
					$query = "SELECT enable_multisite, share_data FROM `" . DB_PREFIX . "sites` WHERE (id = " . $siteId . ")";

					//Query: site settings
					$tmp = $db->from( null, $query )->single();
					
					$json = Json( $tmp['share_data'] );
					
					$locally = ( !empty( $json['sync_uploads'] ) ? $json['sync_uploads'] : false );

					if ( !$locally && IsTrue( $tmp['enable_multisite'] ) && ( $siteId != SITE_ID ) )
					{
						$query = "SELECT s.id, s.url, c.value FROM `" . DB_PREFIX . "sites` as s 
						LEFT JOIN `" . DB_PREFIX . "config` as c ON c.id_site = s.id AND c.variable = 'images_html'
						WHERE (s.is_primary = 1)";

						//Query: default site settings
						$site = $db->from( null, $query )->single();

						if ( $site )
						{
							$html = ( !empty( $site['value'] ) ? $site['value'] : $site['url'] . 'uploads' . PS );

							$imageUrl = FolderUrlByDate( $_img['added_time'], $html ) . $_img['filename'];
						}
					}
				}
				else
				{
					$fileUrl = $vid['external_url'];
				}
				
				$themeValues = ( !empty( ThemeValue( 'theme-media' ) ) ? ThemeValue( 'theme-media' ) : null );
				$themeValues = ( isset( $themeValues['0']['theme-video'] ) ? $themeValues['0']['theme-video'] : ( isset( $themeValues['theme-video'] ) ? $themeValues['theme-video'] : null ) );

				$html = $iframe = '';
				
				$xtraData = ( !empty( $vid['extra_data'] ) ? Json( $vid['extra_data'] ) : null );
				
				$thumpUrl = ( isset( $xtraData['videoThumbnailUrl'] ) ? $xtraData['videoThumbnailUrl'] : null );
				
				if ( $amp )
				{
					$ampWidth = 600;
					$ampHeight = 400;
	
					$embed = '<amp-iframe width="' . $ampWidth . '" height="' . $ampHeight . '"' . PHP_EOL;
					$embed .= 'sandbox="allow-scripts allow-same-origin"' . PHP_EOL;
					$embed .= 'layout="responsive"' . PHP_EOL;
					$embed .= 'frameborder="0"' . PHP_EOL;
					$embed .= 'src="' . $fileUrl . '">' . PHP_EOL;
					
					if ( $thumpUrl )
					{
						$embed .= '<amp-img layout="fill" src="' . $thumpUrl . '" placeholder></amp-img>' . PHP_EOL;
					}
					
					$embed .= '</amp-iframe>';
				}
				else
				{
					if ( !empty( $vid['caption'] ) ) 
					{
						$html .= '<figure';
						
						if ( !empty( $themeValues ) && isset( $themeValues['figure_class'] ) )
						{
							$html .= ' class="' . sprintf( $themeValues['figure_class'], $align ) . '"';
						}

						if ( !empty( $themeValues ) && isset( $themeValues['figure_id'] ) )
						{
							$html .= ' id="' . sprintf( $themeValues['figure_id'], $id ) . '"';
						}
					
						$html .= '>';
					}
					
					if ( empty( $xtraData ) )
					{
						$arr = array( 
							'title' => $vid['title'],
							'autoplay' => false,
							'controls' => array(
								'play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'settings', 'fullscreen'
							)						
						);
					}
					else
					{
						$arr = array( 
								'title' => $vid['title'],
								'autoplay' => ( ( isset( $xtraData['autoplay'] ) && $xtraData['autoplay'] ) ? true : false ),
								'controls' => array(),
								'settings' => array()
						);
						
						if ( isset( $xtraData['speed'] ) && $xtraData['speed'] )
						{
							array_push( $arr['settings'], 'speed' );
						}
						
						if ( isset( $xtraData['loop'] ) && $xtraData['loop'] )
						{
							array_push( $arr['settings'], 'loop' );
						}
						
						if ( isset( $xtraData['playLargeVideo'] ) && $xtraData['playLargeVideo'] )
						{
							array_push( $arr['controls'], 'play-large' );
						}
						
						if ( isset( $xtraData['playVideo'] ) && $xtraData['playVideo'] )
						{
							array_push( $arr['controls'], 'play' );
						}
						
						if ( isset( $xtraData['videoProgress'] ) && $xtraData['videoProgress'] )
						{
							array_push( $arr['controls'], 'progress' );
						}
						
						if ( isset( $xtraData['currentTime'] ) && $xtraData['currentTime'] )
						{
							array_push( $arr['controls'], 'current-time' );
						}
						
						if ( isset( $xtraData['mute'] ) && $xtraData['mute'] )
						{
							array_push( $arr['controls'], 'mute' );
						}
						
						if ( isset( $xtraData['volume'] ) && $xtraData['volume'] )
						{
							array_push( $arr['controls'], 'volume' );
						}
						
						if ( isset( $xtraData['fileSettings'] ) && $xtraData['fileSettings'] )
						{
							array_push( $arr['controls'], 'settings' );
						}
						
						if ( isset( $xtraData['fullscreen'] ) && $xtraData['fullscreen'] )
						{
							array_push( $arr['controls'], 'fullscreen' );
						}
					}
					
					$iframe .= '
					<video controls crossorigin playsinline' . ( $thumpUrl ? ' poster="' . $thumpUrl . '"' : '' ) . ' data-plyr-config=\'' . json_encode( $arr ) . '\'>
						<source src="' . $fileUrl . '" type="video/' . $vid['file_ext'] . '" ';
						
						if ( !empty( $themeValues ) && isset( $themeValues['iframe_class'] ) )
							$iframe .= 'class="' . $themeValues['iframe_class'] . '" ';

						$iframe .= 'size="576">
					</video>';
					
					if ( !empty( $themeValues ) && isset( $themeValues['video_wrap'] ) )
					{
						$html .= sprintf( $themeValues['video_wrap'], $iframe );
					}
					else
					{
						$html .= $iframe;
					}
					
					if ( !empty( $vid['caption'] ) ) 
					{
						$html .= '<figcaption';
						
						if ( !empty( $themeValues ) && isset( $themeValues['caption_class'] ) )
						{
							$html .= ' class="' . sprintf( $themeValues['caption_class'], $align ) . '"';
						}
						
						if ( !empty( $themeValues ) && isset( $themeValues['caption_id'] ) )
						{
							$html .= ' id="' . sprintf( $themeValues['caption_id'], $id ) . '"';
						}
						
						$html .= '>' . html_entity_decode( $vid['caption'] ) . '</figcaption></figure>';
					}
					
					if ( !empty( $themeValues ) && isset( $themeValues['wrap'] ) )
					{
						$html = sprintf( $themeValues['wrap'], $html );
					}
				}

			}
			
			return $html;
		}
		
		return null;
	}, $p );

	return $p;
}

#####################################################
#
# Returns the html post tags function
#
#####################################################
function BuildPostTagsHtml( $post )
{
	$tags = GetPostTags( $post );

	if ( empty( $tags ) )
	{
		return null;
	}
	
	$themeValues = ( !empty( ThemeValue( 'theme-tags' ) ) ? ThemeValue( 'theme-tags' ) : null );
	$themeValues = ( isset( $themeValues['0'] ) ? $themeValues['0'] : $themeValues );

	//This function works with the theme's values. If this value is empty, don't continue
	if ( empty( $themeValues ) )
	{
		return null;
	}
	
	$_tags = '';
	
	$num = count( $tags );
	
	$i = 0;
	
	foreach( $tags as $tag )
	{
		$i++;
		
		if ( !empty( $themeValues['tag_wrap'] ) )
		{
			$_tags .= sprintf( $themeValues['tag_wrap'], $tag['url'], $tag['name'] );
		}
		
		else
		{
			$_tags .= '<a href="' . $tag['url'] . '" rel="tag"' . ( $class ? ' class="' . $class . '"' : '' ) . '>' . $tag['name'] . '</a>';
		}
		
		$_tags .= ( ( !empty( $themeValues['tag_sep'] ) && ( $i < $num ) ) ? $themeValues['tag_sep'] : '' );
	}

	if ( !empty( $themeValues['tags_wrap'] ) )
	{
		$html = sprintf( $themeValues['tags_wrap'], $_tags );
	}
	
	else
		$html = $_tags;
	
	return $html;
}

#####################################################
#
# Return the html cover image function
#
#####################################################
function BuildCoverImageHtml( $post )
{
	if ( empty( $post['coverImage'] ) )
	{
		return null;
	}
	
	$class = '';

	$themeValues = ( !empty( ThemeValue( 'theme-image' ) ) ? ThemeValue( 'theme-image' ) : null );
	$themeValues = ( isset( $themeValues['0'] ) ? $themeValues['0'] : $themeValues );
	
	$decoding	= ( ( !empty( $themeValues ) && !empty( $themeValues['decoding'] ) ) ? $themeValues['decoding'] : false );
	$fullPicTag	= ( ( !empty( $themeValues ) && !empty( $themeValues['full_tag'] ) ) ? true : false );
	$hasLazy	= ( ( !empty( $themeValues ) && !empty( $themeValues['has_lazy_mode'] ) ) ? true : false );
	$lazy		= Settings::IsTrue( 'enable_lazyloader' );
	
	$arr = $post['coverImage'];
	
	if ( !empty( $themeValues ) && isset( $themeValues['cover_class'] ) )
	{
		$class .= ( $lazy ? 'lazyload ' : '' ) . sprintf( $themeValues['cover_class'], 'center', ( !empty( $arr['default']['imageId'] ) ? $arr['default']['imageId'] : $post['id'] ) );
	}

	$maxWidth  = ( ( !empty( $themeValues ) && !empty( $themeValues['max_width'] ) ) ? $themeValues['max_width'] : null );
	$maxHeight = ( ( !empty( $themeValues ) && !empty( $themeValues['max_height'] ) ) ? $themeValues['max_height'] : null );

	$caption = ( ( isset( $arr['default']['imageCaption'] ) && !empty( $arr['default']['imageCaption'] ) ) ? $arr['default']['imageCaption'] : null );

	$num = count( $arr );
	
	$html = '';
	
	$srcset = ( !empty( $post['coverSrc']['srcset'] ) ? $post['coverSrc']['srcset'] : null );
	$sizes = ( !empty( $post['coverSrc']['sizes'] ) ? $post['coverSrc']['sizes'] : null );
	
	$imageWidth = ( isset( $arr['default']['imageWidth'] ) ? $arr['default']['imageWidth'] : ( isset( $arr['default']['width'] ) ? $arr['default']['width'] : '' ) );

	if ( $fullPicTag )
	{
		$html .= '<picture' . ( !empty( $class ) ? ' class="' . $class . '"' : '' ) . '>';
		
		$html .= '<source type="image/' . $arr['default']['mimeType'] . '"' . ( $srcset ? ' srcset="' . $srcset . '"' : '' ) . ( $sizes ? ' sizes="' . $sizes . '"' : '' );

		
		$html .= ' />';
	}

	$html .= '<img ';

	$alt = ( ( isset( $arr['default']['imageAlt'] ) && !empty( $arr['default']['imageAlt'] ) ) ? htmlspecialchars( $arr['default']['imageAlt'] ) : '' );

	$width = ( is_numeric( $maxWidth ) ? $maxWidth : ( isset( $arr['default']['imageWidth'] ) ? $arr['default']['imageWidth'] : ( isset( $arr['default']['width'] ) ? $arr['default']['width'] : null ) ) ); 
	
	$height = ( is_numeric( $maxHeight ) ? $maxHeight : ( isset( $arr['default']['imageHeight'] ) ? $arr['default']['imageHeight'] : ( isset( $arr['default']['height'] ) ? $arr['default']['height'] : null ) ) ); 
	
	$html .= ( !empty( $class ) ? 'class="' . $class . '" ' : '' ) . ( $width ? 'width="' . $width . '" ' : '' ) . ( $height ? 'height="' . $height . '" ' : '' ) . ( ( $lazy && !$hasLazy ) ? 'data-' : '' ) . 'src="' . $arr['default']['imageUrl'] . '" alt="' . $alt . '"';
	
	$html .= ( $srcset ? ' srcset="' . $srcset . '"' : '' );
	
	$html .= ( $sizes ? ' sizes="' . $sizes . '"' : '' );

	if ( $lazy || $hasLazy )
		$html .= ' loading="lazy"';
		
	if ( $decoding )
		$html .= ' decoding="' . $decoding . '"';
	
	if ( !empty( $themeValues ) && isset( $themeValues['cover_style'] ) )
	{
		$html .= ' style="' . $themeValues['cover_style'] . '"';
	}

	$html .= ' />';

	if ( $fullPicTag )
		$html .= '</picture>';
	
	if ( !empty( $themeValues ) && !empty( $themeValues['cover_wrap'] ) )
	{
		$html = sprintf( $themeValues['cover_wrap'], $html );
	}

	return $html;
}

#####################################################
#
# Replaces the [image] shortcode in content function
#
#####################################################
function ReplaceImagesShortCode( $p, $amp = false, $post = null )
{
	$p = preg_replace_callback('/\[image.+id="([0-9]+)".+width="([0-9]+)".+align="(.*)".*]/i', function( $m ) use ( $amp, $post )
	{
		if ( !empty( $m ) )
		{
			$id = $m['1'];
			$width = $m['2'];
			$align = $m['3'];

			$imgData = GetFullImage( $id, ( !empty( $post['ls'] ) ? $post['ls'] : null ), ( !empty( $post['id_site'] ) ? $post['id_site'] : null ) );

			$themeValues = ( !empty( ThemeValue( 'theme-media' ) ) ? ThemeValue( 'theme-media' ) : null );
			$themeValues = ( !empty( ThemeValue( 'theme-image' ) ) ? ThemeValue( 'theme-image' ) : $themeValues );
			$themeValues = ( !empty( $themeValues['0'] ) ? $themeValues['0'] : $themeValues );
			$hasLazy	= ( ( !empty( $themeValues ) && !empty( $themeValues['has_lazy_mode'] ) ) ? true : false );
			$lazy		= Settings::IsTrue( 'enable_lazyloader' );
			
			$html = $srcset = $sizes = $img = '';

			if ( !empty( $imgData ) )
			{
				$url = ( !empty( $imgData['sizes']['default'] ) ? $imgData['sizes']['default']['imageUrl'] : '#' );
				
				if ( is_numeric( $width ) && !empty( $imgData['sizes'][$width] ) )
				{
					$_data = $imgData['sizes'][$width];
				}
				else
				{
					//We could search for the ID here, maybe in later releases...
					//
					//
					$_data = $imgData['sizes']['default'];
				}

				if ( count( $imgData['sizes'] ) > 2 )
				{
					$t = $i = 0;
					
					//We need the correct number of sizes
					foreach( $imgData['sizes'] as $_size => $size )
					{
						if ( $_size == 'default' )
							continue;
						
						if ( is_numeric( $width ) && ( $size['imageWidth'] == $width ) )
							continue;
						
						$t++;
					}
					
					if ( $t > 1 )
					{
						foreach( $imgData['sizes'] as $_size => $size )
						{
							if ( $_size == 'default' )
								continue;
							
							if ( is_numeric( $width ) && ( $size['imageWidth'] == $width ) )
								continue;

							$srcset .= $size['imageUrl'] . ' ' . $size['imageWidth'] . 'w';
							
							$i++;
							
							if ( $i < $t )
								$srcset .= ', ';
						}
					}
				}

				if ( !empty( $imgData['imageCaption'] ) )
				{
					$html .= '<figure';
					
					if ( !empty( $themeValues ) && !empty( $themeValues['figure_class'] ) )
					{
						$html .= ' class="' . sprintf( $themeValues['figure_class'], $align ) . '"';
					}

					if ( !empty( $themeValues ) && !empty( $themeValues['figure_id'] ) )
					{
						$html .= ' id="' . sprintf( $themeValues['figure_id'], $id ) . '"';
					}
					
					$html .= '>';
				}

				if ( $amp )
				{
					$img .= '<amp-img width="' . $_data['imageWidth'] . '" height="' . $_data['imageHeight'] . '"' . PHP_EOL;
					$img .= 'src="' . $_data['imageUrl'] . '"' . PHP_EOL;
					$img .= 'layout="responsive"' . PHP_EOL;
					$img .= 'srcset="' . $srcset . '"' . PHP_EOL;
					$img .= 'alt="' . htmlspecialchars( html_entity_decode( $imgData['imageAlt'] ) ) . '"></amp-img>';
				}
				
				else
				{
					$img .= '<img width="' . $_data['imageWidth'] . '" height="' . $_data['imageHeight'] . '" ';
					
					$img .= 'src="' . $_data['imageUrl'] . '"';

					$img .= ' alt="' . htmlspecialchars( html_entity_decode( $imgData['imageAlt'] ) ) . '"';
					
					if ( ( !empty( $post['enable_lazyloader'] ) && IsTrue( $post['enable_lazyloader'] ) ) || $hasLazy )
					{
						$img .= ' loading="lazy"';
					}

					if ( !empty( $themeValues ) && !empty( $themeValues['image_class'] ) )
					{
						$img .= ' class="' . sprintf( $themeValues['image_class'], $align, $id ) . '"';
					}
					
					if ( !empty( $themeValues ) && !empty( $themeValues['cover_style'] ) )
					{
						$img .= ' style="' . $themeValues['cover_style'] . '"';
					}
					
					if ( !empty( $imgData['imageTitle'] ) )
					{
						$img .= ' title="' . htmlspecialchars( html_entity_decode( $imgData['imageTitle'] ) ) . '"';
					}
					
					if ( !empty( $srcset ) )
					{
						$img .= ' srcset="' . $srcset . '"';
						
						//Just a default value here, maybe do an auto calculation later
						$img .= ' sizes="(min-width: 1215px) 1140px,(min-width: 995px) 920px,(min-width: 775px) 700px, 440px"';
					}
						
						
					$img .= '>';
				}
				
				if ( !empty( $themeValues ) && !empty( $themeValues['image_wrap'] ) )
				{
					$html .= sprintf( $themeValues['image_wrap'], $img );
				}
				
				else
				{
					$html .= $img;
				}

				if ( !empty( $imgData['imageCaption'] ) )
				{
					$html .= '<figcaption';
					
					if ( !empty( $themeValues ) && !empty( $themeValues['caption_class'] ) )
					{
						$html .= ' class="' . sprintf( $themeValues['caption_class'], $align ) . '"';
					}
					
					if ( !empty( $themeValues ) && !empty( $themeValues['caption_id'] ) )
					{
						$html .= ' id="' . sprintf( $themeValues['caption_id'], $id ) . '"';
					}

					$html .= '>' . html_entity_decode( $imgData['imageCaption'] ) . '</figcaption></figure>' . PHP_EOL;
				}
			}
			
			if ( !empty( $themeValues ) && !empty( $themeValues['wrap'] ) )
			{
				$html = sprintf( $themeValues['wrap'], $html );
			}
			
			return $html;
		}
		
		return null;
	}, $p );

	return $p;
}

#####################################################
#
# Returns the reading time in minutes function
#
#####################################################
function ReadingTime( $content )
{
	$word = str_word_count( strip_tags( URLify( $content ) ) );
	
	$m = floor( $word / 270 );
	
	$est = __( 'reading-time' ) . ': ' . $m . ( $m == 1 ? __( 'minute' ) : __( 'minutes' ) );
	
	$est = ( $m == 0 ) ? __( 'reading-time' ) . ': ' . __( 'less-than-a-minute' ) : $est;

	return $est;
}

#####################################################
#
# Create the cover image html for AMP function
#
#####################################################
function BuildAmpCoverImageHtml( $post )
{
	if ( empty( $post['coverImage'] ) )
	{
		return null;
	}
	
	$amp = '<amp-img';
	$amp .= ' alt="' . ( !empty( $post['coverImage']['default']['imageDescr'] ) ? htmlspecialchars( $post['coverImage']['default']['imageDescr'] ) : '' ) . '"' . PHP_EOL;
	
	$amp .= 'src="' . $post['coverImage']['default']['imageUrl'] . '"' . PHP_EOL;
	$amp .= 'width="' . $post['coverImage']['default']['imageWidth'] . '"' . PHP_EOL;
	$amp .= 'height="' . $post['coverImage']['default']['imageHeight'] . '"' . PHP_EOL;
	$amp .= 'layout="responsive"' . PHP_EOL;
	$amp .= 'srcset="';
	
	$total = count( $post['coverImage'] );
	$i = 1;
	
	foreach( $post['coverImage'] as $size => $img )
	{
		$amp .= $img['imageUrl'] . ' ' . $img['imageWidth'] . 'w';
		
		if ( $i < $total )
		{
			$amp .= ',' . PHP_EOL;
		}
		
		$i++;
	}
	
	$amp .= '">
	</amp-img>';
	
	return $amp;
}

#####################################################
#
# Get Schemas function
#
#####################################################
function GetPostSchema( $post )
{
	//Don't continue if we don't want SEO or SCHEMAS
	if ( !Settings::IsTrue( 'enable_seo' ) || !isset( Settings::Seo()['enable_schema_markup'] ) || !Settings::Seo()['enable_schema_markup'] )
		return '';
	
	if ( MULTIBLOG && !empty( $post['id_blog'] ) )
		return GetBlogSchemas( array( 'id_blog' => $post['id_blog'] ) );
	
	else
		return GetPostSchemas( $post );
}

#####################################################
#
# Get Xtra Content function
#
#####################################################
function GetDataXtraPost( $postId )
{
	$db = db();
	
	$query = "SELECT d.*, m.title
	FROM `" . DB_PREFIX . "posts_data` AS d
	LEFT JOIN `" . DB_PREFIX . "manufacturers` as m ON m.id = d.man_id
	WHERE 1=1 AND (d.id_post = " . $postId . ")";
			
	//Query: post data
	$tmp = $db->from( null, $query )->single();

	if ( empty( $tmp ) )
		return null;

	$data = array();
	
	if ( !empty( $tmp['man_id'] ) )
	{
		$data['manufacturer'] = array( 'id' => $tmp['man_id'], 'title' => StripContent( $tmp['title'] ) );
	}
	else
	{
		$data['manufacturer'] = null;
	}
	
	//value1 is the video data
	if ( !empty( $tmp['value1'] ) )
	{
		$temp = Json( $tmp['value1'] );

		if ( !empty( $temp ) )
		{
			if ( !empty( $temp['video_url'] ) )
				parse_str( parse_url( $temp['video_url'], PHP_URL_QUERY ), $videoVars );
			else
				$videoVars = null;

			$data['video'] = array(
					'playlistId' 	 => ( isset( $temp['id_playlist'] ) ? $temp['id_playlist'] : null ),
					'videoUrl' 		 =>  ( isset( $temp['video_url'] ) ? $temp['video_url'] : null ),
					'embedCode' 	 =>  ( isset( $temp['embed_code'] ) ? html_entity_decode( $temp['embed_code'] ) : null ),
					'videoID' 		 =>  ( isset( $videoVars['v'] ) ? $videoVars['v'] : null ),
					'familyFriendly' =>  ( isset( $temp['family_friendly'] ) ? $temp['family_friendly'] : false ),
					'durationRaw' 	 =>  ( isset( $temp['duration'] ) ? $temp['duration'] : null ),
					'duration' 		 =>  ( isset( $temp['duration'] ) ? FormatDuration( $temp['duration'] ) : null ),
					'videoHeight'	 =>  ( isset( $temp['video_height'] ) ? $temp['video_height'] : null ),
					'videoWidth' 	 =>  ( isset( $temp['video_width'] ) ? $temp['video_width'] : null ),
					'playlistUrl' 	 =>  ( isset( $temp['playlist_url'] ) ? $temp['playlist_url'] : null ),
			);
		}
	}
	
	//value2 is the SEO data
	if ( !empty( $tmp['value2'] ) )
	{
		$temp = Json( $tmp['value2'] );

		if ( !empty( $temp ) )
		{
			$data['seo']['seo'] = $temp['seo'];
			$data['seo']['graph'] = $temp['graph'];
		}
	}
	
	//value3 is the Gallery data
	if ( !empty( $tmp['value3'] ) )
	{	
		$temp = Json( $tmp['value3'] );
		
		if ( !empty( $temp ) )
		{
			$gallery = array();
			
			foreach( $temp as $idImg )
			{
				$query = "SELECT filename, title, alt, descr, caption, added_time, width, height
				FROM `" . DB_PREFIX . "images`
				WHERE 1=1 AND (id_image = " . $idImg . ")";
			
				//Query: image
				$gImg = $db->from( null, $query )->single();
	
				if ( !empty( $gImg ) )
				{
					$gallery[$idImg] = array(
									'url' => FolderUrlByDate( $gImg['added_time'] ) . stripslashes( $gImg['filename'] ),
									'title' => $gImg['title'],
									'alt' => $gImg['alt'],
									'descr' => $gImg['descr'],
									'caption' => $gImg['caption'],
									'added_time' => $gImg['added_time'],
									'width' => $gImg['width'],
									'height' => $gImg['height'],
									'childs' => array()
					);
					
					$query = "SELECT filename, width, height FROM `" . DB_PREFIX . "images`
					WHERE 1=1 AND (id_parent = " . $idImg . ")";
			
					//Query: images
					$gChildImg = $db->from( null, $query )->all();

					if ( !empty( $gChildImg ) )
					{
						foreach( $gChildImg as $gChild )
						{
							$gallery[$idImg]['childs'][] = array(
											'url' => FolderUrlByDate( $gImg['added_time'] ) . stripslashes( $gChild['filename'] ),
											'width' => $gChild['width'],
											'height' => $gChild['height'],
							);
						}
					}
				}
			}
			
			$data['gallery'] = $gallery;
		}
	}
	
	//value4 is the Post data
	if ( !empty( $tmp['value4'] ) )
	{	
		$data['postData'] = Json( $tmp['value4'] );
	}
	
	$data['addPriceNum'] = ( !empty( $tmp['add_price_num'] ) ? true : false );
	$data['allowVoting'] = ( !empty( $tmp['allow_voting'] ) ? true : false );
	$data['pricesTitle'] = StripContent( $tmp['prices_title'] );

	return $data;
}

#####################################################
#
# Get Post Subscribers function
#
#####################################################
function PostSubs( $post )
{
	if ( !IsTrue( $post['post_not'] ) )
		return null;
	
	$db = db();
	
	// Query: total subscribers
	return $db->from( null, " SELECT count(*) as total FROM `" . DB_PREFIX . "posts_subscriptions` WHERE 1=1 AND post_id = " . $post['id_post'] )->total();	
}

#####################################################
#
# Get Post Comment function
#
#####################################################
function GetPostComments( $post )
{
	$db = db();
	
	$co = ( !empty( $post['comments_data'] ) ? Json( $post['comments_data'] ) : null );
	
	if ( !empty( $co ) && !empty( $co['sort_by'] ) )
	{
		if ( $co['sort_by'] == 'older-first' )
		{
			$sort_by = "co.added_time ASC";
		}
		
		elseif ( $co['sort_by'] == 'newer-first' )
		{
			$sort_by = "co.added_time DESC";
		}
		
		else
		{
			$sort_by = "co.id DESC";
		}
	}
	
	else
	{
		$sort_by = "co.added_time DESC";
	}
	
	$query = "SELECT co.*, COALESCE(u.real_name, u.user_name) AS user_name, u.image_data
	FROM `" . DB_PREFIX . "comments` AS co
	LEFT JOIN `" . DB_PREFIX . USERS . "`   as u ON u.id_member = co.user_id
	WHERE 1=1 AND (co.id_post = " . $post['id_post'] . ")
	GROUP BY co.id
	ORDER BY " . $sort_by;
			
	//Query: comments
	$tmp = $db->from( null, $query )->all();
	
	if ( !$tmp )
	{
		return null;
	}

	$data = $imageData = array();

	foreach ( $tmp as $c )
	{
		$dt = Json( $c['rating_data'] );
		
		$image = TOOLS_HTML . 'theme_files/assets/frontend/img/default-fallback-image.png';
		
		if ( !empty( $c['image_data'] ) )
		{
			$imageData = Json( $c['image_data'] );
			
			if ( !empty( $imageData ) && isset( $imageData['default'] ) )
			{
				$image = $imageData['default']['imageUrl'];
			}
		}
		
		$data[] = array(
			'id' 		=> $c['id'],
			'status' 	=> $c['status'],
			'parentId' 	=> $c['id_parent'],
			'userId' 	=> $c['user_id'],
			'ip' 		=> $c['ip'],
			'imageData' => $imageData,
			'imageUrl' 	=> $image,
			'url'		=> $c['url'],
			'email'		=> $c['email'],
			'time'		=> postDate( $c['added_time'], false ),
			'niceTime'	=> niceTime( $c['added_time'] ),
			'timeRaw'	=> $c['added_time'],
			'name'		=> ( !empty( $c['user_name'] ) ? $c['user_name'] : $c['name'] ),
			'rTime'		=> date( 'r', $c['added_time'] ),
			'timeC'		=> postDate( $c['added_time'], true ),
			'rating'	=> $c['rating'],
			'comment'	=> CreatePostContent( $c['comment'], $post['title'], false, $post, true ),
			'reviewPos'	=> ( !empty( $dt ) ? CreatePostContent( $dt['pos'], $post['title'], false, $post, true ) : null ),
			'reviewNeg' => ( !empty( $dt ) ? CreatePostContent( $dt['neg'], $post['title'], false, $post, true ) : null )
		);
	}

	return $data;
}

#####################################################
#
# Get Post Tags function
#
#####################################################
function GetPostTags( $post )
{
	$id_custom_type = ( isset( $post['id_custom_type'] ) ? $post['id_custom_type'] : 0 );
	
	$tags = GetTheTags ( $post['id_post'], null, null, false, 'id', null, $id_custom_type );
	
	$data = array();

	if ( !empty( $tags ) )
	{
		foreach( $tags as $id => $tag )
			$data[] = array( 
				'name' => stripslashes( $tag['name'] ), 
				'sef' => $tag['sef'],
				'url' => LangSlugUrl( $post ) . ltrim( TagFilter( $post['ls'] ), '/' ) . $tag['sef'] . PS
			);
		
		unset( $tags );
	}
	
	return $data;
}

#####################################################
#
# Create Post Content function
#
#####################################################
function CreatePostContent( $p, $title = '', $amp = false, $post = null, $isComment = false )
{
	$themeValues = ( !empty( ThemeValue( 'theme-image' ) ) ? ThemeValue( 'theme-image' ) : $themeValues );
	$themeValues = ( !empty( $themeValues['0'] ) ? $themeValues['0'] : $themeValues );
	$hasLazy	 = ( ( !empty( $themeValues ) && !empty( $themeValues['has_lazy_mode'] ) ) ? true : false );
	$lazy		 = ( !empty( $post['enable_lazyloader'] ) && IsTrue( $post['enable_lazyloader'] ) );
	
	$p = StripContent( $p );
	
	$hasBlocks = false;
	
	$hasBlocks = ( !empty( $post['id_post'] ) ? GetBlocksData( $post['id_post'] ) : null );

	if ( !$hasBlocks )
	{
		$p = Parsedown( $p ); //Markdown Support
		$p = ReplaceImagesShortCode( $p, $amp, $post ); //Replace image shortcode
		$p = ReplaceVideosShortCode( $p, $amp, $post ); //Replace videos shortcode
		$p = ReplaceOtherShortCode( $p, $post ); //Replace prices,posts etc. shortcode
		$p = wpautop( $p ); //WordPress Content Support
	}
	else
	{
		$p = GetBlocksHtmlData( $post['id_post'], $post );
	}
	
	$p = CreateEmbed( $p, $amp, $isComment );
	$p = replaceCaptionImage( $p, $amp ); //WordPress Support

	if ( !$amp )
	{
		$seoSettings = ( !empty( $post['seo_data'] ) ? Json( $post['seo_data'] ) : null );

		if ( !empty( $seoSettings['add_alt_on_images'] ) && $seoSettings['add_alt_on_images'] )
			$p = AddAltTagToImages( $p, $title );
		
		$p = EditLinks( $p );

		if ( $lazy && !$hasLazy )
			$p = AddLazyLoader( $p );
	}
	
	else
	{
		//Clean scripts and do a generic clean (AMP)
		$p = AmpClean( $p ); 
		
		//Convert blogger's DIVs
		$p = BloggerClean( $p );

		//Convert Images (AMP)
		$p = FormatAmpImages( $p ); 
	}
	
	//Replace double paragraphs
	$p = str_replace( array( '<p><p>', '</p></p>' ), array( '<p>', '</p>' ), $p );
	$p = preg_replace( '|<p>\s*<p>|', "<p>", $p );
	$p = preg_replace( '|</p>\s*</p>|', "</p>", $p );

	return $p;
}

#####################################################
#
# Build the author array function
#
#####################################################
function BuildAuthorData( $post, $single = false  )
{
	$trans = ( IsTrue( $post['multilang'] ) && !empty( $post['trans_data'] ) ? Json( $post['trans_data'] ) : null );
	
	$langCode = $post['ls'];
	
	$userName = ( !empty( $trans ) && isset( $trans[$langCode] ) ? $trans[$langCode]['name'] : ( !empty( $post['real_name'] ) ? $post['real_name'] : $post['user_name'] ) );

	$userUrl = ( !IsTrue( $post['disable_author_archives'] ) ? LangSlugUrl( $post ) . 'author' . PS . $post['user_name'] . PS : '#' );
		
	$userHtml = '<a href="' . $userUrl . '" title="' . htmlspecialchars( $userName ) . '">' . $userName . '</a>';

	$data = array(
		'name' 			=> StripContent( $userName ),
		'user_name' 	=> $post['user_name'],
		'url' 			=> $userUrl,
		'html' 			=> $userHtml,
		'bio'			=> '',
		'coverImg'		=> UserImg( $post ),
		'coverSrcSet'	=> UserImgSrc( $post ),
		'social'		=> array(),
		'image'			=> array(),
		'id' 			=> $post['id_member']
	);

	if ( $single )
	{
		$details = GetUserDetails( null, $post['id_member'] );
			
		if ( !empty( $details ) )
		{
			$data['bio'] 	= $details['user_bio'];
			$data['social'] = $details['social_data'];
			$data['image'] 	= ( isset( $details['imageData'] ) ? $details['imageData'] : null );
		}
	}

	return $data;
}

#####################################################
#
# Build User Image Array function
#
#####################################################
function UserImg( $post )
{
	$userImage = array (
		'default' => 
			array(
				'id' => 0,
				'imageFilename' => null,
				'imageTitle' => null,
				'imageWidth' => null,
				'imageHeight' => null,
				'imageSize' => null,
				'mimeType' => null,
				'imageUrl' =>'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII='
			)
	);

	if ( isset( $post['user_img'] ) && !empty( $post['user_img'] ) )
	{
		$userImageArr = Json( $post['user_img'] );
			
		$userImage = ( $userImageArr ? $userImageArr : $userImage );
	}
		
	return $userImage;
}

#####################################################
#
# Builds Full Cover Image Array function
#
#####################################################
function UserImgSrc( $post )
{
	$array = array(
		'srcset' => '',
		'sizes' => '',
		'srcFull' => ''
	);
		
	if ( !isset( $post['user_img'] ) || empty( $post['user_img'] ) )
		return $array;
		
	$arr = Json( $post['user_img'] );
			
	if ( empty( $arr ) )
		return $array;
		
	$num = count( $arr );
	
	$set = $sizes = '';
		
	$coverFull = 'srcset="';

	$i = 0;
		
	$imageWidth = ( isset( $arr['default']['imageWidth'] ) ? $arr['default']['imageWidth'] : 0 );
	
	foreach( $arr as $_ar => $ar )
	{
		$i++;
			
		$coverFull 	.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
		$set 		.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
			
		if ( $i < $num )
		{
			$coverFull  .= ', ' . PHP_EOL;
			$set 		.= ', ' . PHP_EOL;
		}
	}
		
	if ( !empty( $imageWidth ) ) 
	{
		$coverFull .= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px"';
			
		$sizes .=  '(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px';
	}

	if ( $num > 1 )
	{
		$i = 0;
			
		$set = $sizes = '';
		
		$coverFull = 'srcset="';
		
		foreach( $arr  as $_ar => $ar )
		{
			$i++;
				
			$coverFull 	.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
			$set 		.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
				
			if ( $i < $num )
			{
				$coverFull  .= ', ' . PHP_EOL;
				$set 		.= ', ' . PHP_EOL;
			}
		}
			
		if ( !empty( $imageWidth ) ) 
		{
			$coverFull .= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px"';
			$sizes .= '(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px';
		}
	}
		
	return array(
		'srcset' => $set,
		'sizes' => $sizes,
		'srcFull' => $coverFull
	);
}

#####################################################
#
# Builds the slug based on language settings function
#
#####################################################
function LangSlugUrl( $post )
{
	$url = ( ( isset( $post['url'] ) && !empty( $post['url'] ) ) ? $post['url'] : SITE_URL );

	$defaultLang = $post['dlc'];
	
	if ( IsTrue( $post['multilang'] )  && !empty( $post['ls'] ) 
		&& ( !IsTrue( $post['hide_lang'] ) || ( IsTrue( $post['hide_lang'] ) && ( $post['ls'] != $defaultLang ) ) )
	)
		$url .= $post['ls'] . PS;
		
	return $url;
}

#####################################################
#
# Build Post URL function
#
# Builds the url based on post's data and current settings
#
#####################################################
function BuildPostUrl( $post, $skipSlug = false, $isChild = false )
{
	$url = LangSlugUrl( $post );

	//If this post is the static homepage, return the site's url
	if ( StaticHomePage( false, $post['id_post'] ) || ( !empty( $post['id_parent'] ) && StaticHomePage( false, $post['id_parent'] ) ) )
		return rawurldecode( $url );

	//Add the blog slug
	$url .= ( IsTrue( $post['multiblog'] ) && !empty( $post['blog_sef'] ) ? $post['blog_sef'] . PS : '' );

	//Add the posts filter
	if ( $post['post_type'] !== 'page' )
		$url .= ltrim( PostFilter( $post['ls'] ), '/' );
	
	//Add the post slug
	if ( !$skipSlug && !$isChild )
	{
		$url .= $post['sef'] . PS;
	}
	
	//Add the parent slug
	if ( !$skipSlug && $isChild && !empty( $post['parent_sef'] ) )
	{
		$url .= $post['parent_sef'] . PS . $post['sef'] . PS;
	}
		
	//If this post has an external URL, return this
	if ( isset( $post['ext_url'] ) && !empty( $post['ext_url'] ) )
	{
		$url = $post['ext_url'];
	}

	return rawurldecode( $url );
}

#####################################################
#
# Builds Full Cover Image Array function
#
#####################################################
function BuildCoverSrc( $data )
{
	//Create this array to avoid any errors later
	$array = array(
		'srcset' 	=> null,
		'sizes' 	=> null,
		'srcFull' 	=> null
	);
		
	if ( empty( $data['coverImage'] ) )
		return $array;
		
	$arr = $data['coverImage'];

	$num = count( $arr );
	
	$set = $sizes = '';
	$coverFull = 'srcset="';

	$i = 0;
		
	$imageWidth = ( isset( $data['coverImage']['default']['imageWidth'] ) ? $data['coverImage']['default']['imageWidth'] : 0 );
	
	foreach( $arr as $_ar => $ar )
	{
		$i++;
			
		$coverFull 	.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
		$set 		.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
			
		if ( $i < $num )
		{
			$coverFull  .= ', ' . PHP_EOL;
			$set 		.= ', ' . PHP_EOL;
		}
	}
		
	if ( !empty( $imageWidth ) ) 
	{
		$coverFull 	.= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px"';
		$sizes 		.= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px"';
	}

	if ( $num > 1 )
	{
		$i = 0;
			
		$set = $sizes = '';
		
		$coverFull = 'srcset="';
		
		foreach( $arr  as $_ar => $ar )
		{
			$i++;
				
			$coverFull 	.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
			$set 		.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
				
			if ( $i < $num )
			{
				$coverFull  .= ', ' . PHP_EOL;
				$set 		.= ', ' . PHP_EOL;
			}
		}
			
		if ( !empty( $imageWidth ) ) 
		{
			$coverFull 	.= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px"';
			$sizes 		.= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px"';
		}
	}
		
	return array(
		'srcset' => $set,
		'sizes' => $sizes,
		'srcFull' => $coverFull
	);
}

#####################################################
#
# Clean the post's content Function
#
#####################################################
function CleanContent( $c )
{
	$content = htmlspecialchars( $c );
	$content = ReplaceSpecialChars( $content );
	$content = replaceCaptionImage( $content );

	return $content;
}

#####################################################
#
# Get top Price function
#
#####################################################
function GetTopPriceData( $id, $type = 'normal', $post = null )
{
	$db = db();
	
	$query = "SELECT id_price
	FROM `" . DB_PREFIX . 'prices' . "`
	WHERE (id_post = " . (int) $id . ") AND (type = :type)
	ORDER BY sale_price ASC
	LIMIT 1";

	//Query: price
	$tmp = $db->from( null, $query, [ $type => ':type' ] )->single();
	
	if ( !$tmp )
	{
		return null;
	}
	
	return GetSinglePricesData( $tmp['id_price'], $post );
}

#####################################################
#
# Get the Next and/or Previous Post function
#
#####################################################
function GetNextPrevPost( $post, $previous = true )
{
	$db = db();
	
	$blogId = ( isset( $post['id_blog'] ) ? $post['id_blog'] : 0 );
	$langId = ( isset( $post['id_lang'] ) ? $post['id_lang'] : 0 );
	
	$q = "(p.id_post " . ( $previous ? '<' : '>' ) . " " . $post['id_post'] . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (p.id_blog = " . $blogId . ") AND (p.id_lang = " . $langId . ")";
		
	$query = PostsDefaultQuery( $q, 1, "p.id_post " . ( $previous ? 'DESC' : 'ASC' ), null, false );
	
	//Query: post
	$tmp = $db->from( null, $query )->single();
	
	if ( !$tmp )
	{
		return null;
	}
	
	$s = GetSettingsData( $post['id_site'] );
		
	if ( empty( $s ) )
	{
		return null;
	}

	$tmp = array_merge( $tmp, $s );
	
	return BuildPostVars( $tmp );
}

#####################################################
#
# Gets Assoc Tags function
#
#####################################################
function GetTheTags ( $post_id, $table = null, $table2 = null, $arr_only = false, $id = 'id', $idLang = null, $typeID = null )
{
	$db = db();
	
	$tagTable = ( $table ? $table : DB_PREFIX . 'tags' );
	
	$query = "SELECT * FROM `" . ( $table2 ? $table2 : DB_PREFIX . 'tags_relationships' ) . "`
	WHERE (object_id = " . (int) $post_id . ")";

	//Query: tags
	$tmp = $db->from( null, $query )->all();
	
	if ( empty( $tmp ) )
		return false;

	$tags = array();
	
	foreach ( $tmp as $t )
	{
		$query = "SELECT id, sef, title, num_items FROM `" . DB_PREFIX . "tags` WHERE (id = " . $t['taxonomy_id'] . ")";
			
		//Query: tag
		$tag = $db->from( null, $query )->single();
	
		if ( !empty( $tag ) )
		{
			$tags[] = array( 'id' => $tag['id'], 'name' => $tag['title'], 'sef' => $tag['sef'], 'numItems' => $tag['num_items'] );

			unset( $tag );
		}
	}
		
	if ( $arr_only )
	{
		$ts = $tags;
			
		$tags = array();
			
		foreach( $ts as $a => $t )
			$tags[] = $t['name'];
	}

	return $tags;
}

#####################################################
#
# Get Post Prices function
#
#####################################################
function GetPricesData( $id, $type = 'normal', $orderByPrice = true, $post = null )
{
	$db = db();
	
	$query = "SELECT p.*, s.name as st, po.title as ppt, la.code as cd,
	c.name as cu, c.code as cc, c.symbol as cs, c.format as cf, c.exchange_rate as cr,
	pi.last_time_updated as lu,	pi.last_time_checked as lc,	pi.num_retries as lr, pi.not_found as ln, pi.in_stock as ls
	FROM `" . DB_PREFIX . "prices` AS p
	INNER JOIN `" . DB_PREFIX . "stores` AS s ON s.id_store = p.id_store
	INNER JOIN `" . DB_PREFIX . POSTS . "` as po ON p.id_post = po.id_post
	INNER JOIN `" . DB_PREFIX . "languages` AS la ON la.id = po.id_lang
	INNER JOIN `" . DB_PREFIX . "currencies` AS c ON c.id = p.id_currency
	LEFT JOIN `" . DB_PREFIX . "price_info` AS pi ON pi.id_price = p.id_price
	WHERE 1=1 AND (p.id_post = " . $id . ") AND (type = '" . $type . "')
	GROUP BY p.id_price
	ORDER BY p." . ( $orderByPrice ? 'sale_price' : 'pri_order' ) . " ASC";

	//Query: prices
	$tmp = $db->from( null, $query )->all();
	
	if ( !$tmp )
		return null;
	
	$amp = Router::GetVariable( 'isAmp' );
	
	$i = 0;
	
	$data = array();
	
	foreach( $tmp as $p )
	{
		$i++;
		
		$data[$i] = GetSinglePricesData( $p['id_price'] );
	}

	return $data;
}

#####################################################
#
# Get Single Price function
#
#####################################################
function GetSinglePricesData( $id, $post = null )
{
	$db = db();
	
	$amp = Router::GetVariable( 'isAmp' );
	
	$query = "SELECT p.*, s.name as st, po.title as ppt, la.code as cd,
	c.name as cu, c.code as cc, c.symbol as cs, c.format as cf, c.exchange_rate as cr,
	pi.last_time_updated as lu,	pi.last_time_checked as lc,	pi.num_retries as lr, pi.not_found as ln, pi.in_stock as ls
	FROM `" . DB_PREFIX . "prices` AS p
	INNER JOIN `" . DB_PREFIX . "stores` AS s ON s.id_store = p.id_store
	INNER JOIN `" . DB_PREFIX . POSTS . "` as po ON p.id_post = po.id_post
	INNER JOIN `" . DB_PREFIX . "languages` AS la ON la.id = po.id_lang
	INNER JOIN `" . DB_PREFIX . "currencies` AS c ON c.id = p.id_currency
	LEFT JOIN `" . DB_PREFIX . "price_info` AS pi ON pi.id_price = p.id_price
	WHERE (p.id_price = " . $id . ")";
	
	//Query: price
	$p = $db->from( null, $query )->single();

	if ( !$p )
		return null;
	
	$data = array();

	$pst = StripContent( $p['content'] );
	$pst = Parsedown( $pst ); //Markdown Support
	$pst = ReplaceImagesShortCode( $pst, $amp, $post ); //Replace image shortcode
	$pst = ReplaceVideosShortCode( $pst, $amp, $post ); //Replace videos shortcode
	$pst = wpautop( $pst );
	
	$pst = CreateEmbed( $pst, $amp );
		
	$data = array(
			'title' => ( !empty( $p['title'] ) ? StripContent( $p['title'] ) : StripContent( $p['ppt'] ) ),
			'discount' => $p['discount_perce'],
			'discountFixed' => ( ( $p['discount_perce'] > 0 ) ? $p['discount_perce'] . '%' : null ),
			'discountTitle' => ( !empty( $p['discount_title'] ) ? StripContent( $p['discount_title'] ) : null ),
			'couponCode' => ( !empty( $p['coupon_code'] ) ? StripContent( $p['coupon_code'] ) : null ),
			'couponType' => $p['coupon_type'],
			'regPriceRaw' => $p['regular_price'],
			'salePriceRaw' => $p['sale_price'],
			'localeCode' => ( !empty( $p['locale_code'] ) ? StripContent( $p['locale_code'] ) : null ),
			'priceFixed' => ( !empty( $p['sale_price'] ) ? formatPrice( $p['sale_price'], $p['cf'] ) : null ),
			'regPriceFixed' => ( !empty( $p['regular_price'] ) ? formatPrice( $p['regular_price'], $p['cf'] ) : null ),
			'linkText' => ( !empty( $p['link_text'] ) ? StripContent( $p['link_text'] ) : __( 'visit' ) ),
			'post' => $pst,
			'extraText' => ( !empty( $p['extra_text'] ) ? StripContent( $p['extra_text'] ) : '' ),
			'startingPrice' => ( !empty( $p['is_starting_price'] ) ? true : false ),
			'likes' => $p['likes'],
			'outUrl' => $post['url'] . 'out' . PS . $p['id_price'] . PS,
			'id' => $p['id_price'],
			'dislikes' => $p['dislikes'],
			'currencyName' => $p['cu'],
			'currencyCode' => $p['cc'],
			'currencySymbol' => $p['cs'],
			'currencyId' => $p['id_currency'],
			'url' => $p['main_page_url'],
			'aff' => $p['aff_page_url'],
			'views' => $p['views'],
			'expireTime' => $p['expire_time'],
			'dealExpired' => ( ( !empty( $p['expire_time'] ) && ( $p['expire_time'] < time() ) ) ? true : false ),
			'expireTimeFixed' => ( !empty( $p['expire_time'] ) ? postDate( $p['expire_time'] ) : null ),
			'availSince' => $p['available_since'],
			'availSinceFixed' => ( !empty( $p['available_since'] ) ? postDate( $p['available_since'] ) : null ),
			'timeAdded' => $p['time_added'],
			'timeViewed' => $p['last_time_viewed'],
			'timeUpdated' => $p['lu'],
			'timeChecked' => $p['lc'],
			'numRetries' => $p['lr'],
			'timeCheckedFixed' => ( !empty( $p['lc'] ) ? postDate( $p['lc'] ) : null ),
			'timeUpdatedFixed' => ( !empty( $p['lu'] ) ? postDate( $p['lu'] ) : null ),
			'timeViewedFixed' => ( !empty( $p['last_time_viewed'] ) ? postDate( $p['last_time_viewed'] ) : null ),
			'timeAddedFixed' => postDate( $p['time_added'] ),
			'storeName' => StripContent( $p['st'] ),
			'storeId' => $p['id_store'],
			'couponCode' => ( !empty( $p['coupon_code'] ) ? StripContent( $p['coupon_code'] ) : '' ),
			'availTitle' => ( !empty( $p['available_title'] ) ? StripContent( $p['available_title'] ) : '' ),
			'inStock' => ( $p['ls'] ? true : false ),
			'notFound' => ( $p['ln'] ? true : false ),
			'isFeatured' => ( $p['is_featured'] ? true : false ),
			'isFree' => ( $p['is_free'] ? true : false ),
			'maskCode' => ( $p['mask_code'] ? true : false ),
			'preOrder' => ( $p['pre_order_only'] ? true : false ),
			'coverImage' => array(),
			'history' => array()
	);
		
	if ( !empty( $p['image_id'] ) )
	{
		$data['coverImage'] = PostImageDetails( $p['image_id'], $p['cd'], true );
	}
	
	$query = "SELECT time_added, price, price_before FROM `" . DB_PREFIX . "price_update_info`
	WHERE 1=1 AND (id_price = " . $id . ") ORDER BY time_added ASC";

	//Query: info
	$h = $db->from( null, $query )->all();
		
	if ( !empty( $h ) )
	{
		foreach ( $h as $s )
		{
			$data['history'] = array(
					'dateRaw' => $s['time_added'],
					'dateFixed' => postDate( $s['time_added'] ),
					'priceRaw' => $s['price'],
					'priceFixed' => ( !empty( $s['price'] ) ? formatPrice( $s['price'], $p['cf'] ) : null ),
					'regPriceRaw' => $s['price_before'],
					'regPriceFixed' => ( !empty( $s['price_before'] ) ? formatPrice( $s['price_before'], $p['cf'] ) : null )
			);
		}
	}

	return $data;
}

#####################################################
#
# Get Related Posts function
#
#####################################################
function GetRelatedPosts( $post, $limit = 10 )
{
	$db = db();
	
	$blogId = ( isset( $post['id_blog'] ) ? $post['id_blog'] : 0 );
	$langId = ( isset( $post['id_lang'] ) ? $post['id_lang'] : 0 );
	$catId = ( isset( $post['id_category'] ) ? $post['id_category'] : 0 );
	
	$q = "(p.id_post != " . $post['id_post'] . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (p.id_blog = " . $blogId . ") AND (p.id_lang = " . $langId . ") AND (p.id_category = " . $catId . ")";
		
	$query = PostsDefaultQuery( $q, $limit, "p.id_post DESC", null, false );

	//Query: posts
	$tmp = $db->from( null, $query )->all();
	
	if ( !$tmp )
	{
		return null;
	}
	
	$s = GetSettingsData( $post['id_site'] );
		
	if ( empty( $s ) )
	{
		return null;
	}
	
	$data = array();
	
	foreach ( $tmp as $p )
	{			
		$p = array_merge( $p, $s );
		
		$data[] = BuildPostVars( $p );
	}
	
	return $data;
}

#####################################################
#
# Get Top Posts function
#
#####################################################
function GetTopPosts( $post, $limit = 10  )
{
	$db = db();
	
	$postId = ( isset( $post['id_post'] ) ? $post['id_post'] : 0 );
	$blogId = ( isset( $post['id_blog'] ) ? $post['id_blog'] : 0 );
	$langId = ( isset( $post['id_lang'] ) ? $post['id_lang'] : 0 );
	
	$q = "(p.post_type = 'post') AND (p.post_status = 'published') AND (p.id_blog = " . $blogId . ") AND (p.id_lang = " . $langId . ")";
		
	$query = PostsDefaultQuery( $q, $limit, "p.views DESC", null, false );

	//Query: posts
	$tmp = $db->from( null, $query )->all();
	
	if ( !$tmp )
	{
		return null;
	}
	
	$s = GetSettingsData( $post['id_site'] );
		
	if ( empty( $s ) )
	{
		return null;
	}
	
	$data = array();
	
	foreach ( $tmp as $p )
	{
		$p = array_merge( $p, $s );
		
		$data[] = BuildPostVars( $p );
	}
	
	return $data;
}

#####################################################
#
# Get Random Posts function
#
#####################################################
function GetRandPosts( $post, $limit = 10 )
{
	$db = db();
	
	$postId = ( isset( $post['id_post'] ) ? $post['id_post'] : 0 );
	$blogId = ( isset( $post['id_blog'] ) ? $post['id_blog'] : 0 );
	$langId = ( isset( $post['id_lang'] ) ? $post['id_lang'] : 0 );
	$catId = ( isset( $post['id_category'] ) ? $post['id_category'] : 0 );
	
	$q = "(p.id_post != " . $post['id_post'] . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (p.id_blog = " . $blogId . ") AND (p.id_lang = " . $langId . ") AND (p.id_category = " . $catId . ")";
		
	$query = PostsDefaultQuery( $q, $limit, "RAND()", null, false );

	//Query: posts
	$tmp = $db->from( null, $query )->all();
	
	if ( !$tmp )
	{
		return null;
	}
	
	$s = GetSettingsData( $post['id_site'] );
		
	if ( empty( $s ) )
	{
		return null;
	}
	
	$data = array();
	
	foreach ( $tmp as $p )
	{
		$p = array_merge( $p, $s );
		
		$data[] = BuildPostVars( $p );
	}
	
	return $data;
}

#####################################################
#
# Get Post Custom Types function
#
#####################################################
function GetCustomAssocs( $post_id )
{
	$db = db();
	
	$query = "SELECT id_post_type FROM `" . DB_PREFIX . "post_types_relationships` WHERE (post_id = " . $post_id . ")";
	
	//Query: post types
	$cus = $db->from( null, $query )->all();

	if( !$cus ) 
		return null;
	
	$data = array();
	
	foreach ( $cus as $c )
	{
		$query = "SELECT * FROM `" . DB_PREFIX . "post_types` WHERE (id = " . $c['id_post_type'] . ")";
	
		//Query: post type
		$cu = $db->from( null, $query )->single();
/*
		$query = array(
			'SELECT'	=> '*',
			'FROM'		=>	DB_PREFIX . "post_types",
			'WHERE'		=> 'id = :id',
			'ORDER'		=> 'type_order ASC',
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
			'BINDS' 	=> array(
				array( 'PARAM' => ':id', 'VAR' => $c['id_post_type'], 'FLAG' => 'INT' )
			)
		);
*/
		if ( $cu )
		{
			$data[$cu['id']] = array(
				'id'  	  	  => $cu['id'],
				'title' 	  => StripContent( $cu['title'] ),
				'sef' 	  	  => StripContent( $cu['sef'] ),
				'description' => StripContent( $cu['description'] ),
				'trans_data'  => Json( $cu['trans_data'] ),
				'image'  	  => $cu['id_image'],
				'tags'		  => GetCusAssocTags( $post_id, $cu['id'] )
			);

			unset( $cu );
		}
	}

	unset( $cus );
	
	return $data;
}

#####################################################
#
# Post Image Details function
#
#####################################################
function PostImageDetails( $id, $langCode = null, $noPost = false )
{
	$db = db();
	
	$data = array();
	
	$langCode = ( $langCode ? $langCode : CurrentLang()['lang']['code'] );
	
	if ( $noPost )
	{
		$query = "SELECT * FROM `" . DB_PREFIX . "images` WHERE (id_image = " . $id . ")";
	}
	else
	{
		$query = "SELECT at.id_attach, im.id_image, im.filename, im.width, im.height, im.size, im.mime_type, im.added_time, im.external_url, im.trans_data
		FROM `" . DB_PREFIX . "images` AS im
		LEFT JOIN `" . DB_PREFIX . "image_attachments` AS at ON at.image_id = im.id_image
		WHERE (at.post_id = " . $id . ")";
	}

	//Query: image
	$_img = $db->from( null, $query )->single();

	if ( !$_img )
		return $data;
	
	$imgData = Json( $_img['trans_data'] );

	$imgData = ( !empty( $imgData ) && isset( $imgData[$langCode] ) ? $imgData[$langCode] : null );

	$data['default'] = array(
			'imageWidth' 	=> $_img['width'],
			'imageHeight' 	=> $_img['height'],
			'imageUrl' 		=> ( !empty( $_img['external_url'] ) ? $_img['external_url'] : FolderUrlByDate( $_img['added_time'] ) . $_img['filename'] ),
			'imageId' 		=> $_img['id_image'],
			'imageFilename' => $_img['filename'],
			'mimeType'		=> $_img['mime_type'],
			'imageCaption' 	=> ( $imgData ? htmlspecialchars( $imgData['caption'], ENT_QUOTES ) : '' ),
			'imageTitle' 	=> ( $imgData ? htmlspecialchars( $imgData['title'], ENT_QUOTES ) : '' ),
			'imageAlt' 		=> ( $imgData ? htmlspecialchars( $imgData['alt'], ENT_QUOTES ) : '' ),
			'imageDescr' 	=> ( $imgData ? htmlspecialchars( $imgData['descr'], ENT_QUOTES ) : '' ),
			'imageHtml' 	=> array()
	);
	
	$query = "SELECT id_image, filename, width, height, size, mime_type
	FROM `" . DB_PREFIX . "images` WHERE (id_parent = " . $_img['id_image'] . ") ORDER BY width ASC";
	
	//Query: images
	$imgs = $db->from( null, $query )->all();
	
	if ( !empty( $imgs ) )
	{
		foreach( $imgs as $img )
		{
			$data[$img['width']] = array(
					'imageWidth' 	=> $img['width'],
					'imageHeight' 	=> $img['height'],
					'imageUrl' 		=> FolderUrlByDate( $_img['added_time'] ) . $img['filename'],
					'imageId' 		=> $img['id_image'],
					'mimeType' 		=> $_img['mime_type'],
					'imageFilename' => $img['filename'],
					'imageCaption' 	=> ( $imgData ? htmlspecialchars( $imgData['caption'], ENT_QUOTES ) : '' ),
					'imageTitle' 	=> ( $imgData ? htmlspecialchars( $imgData['title'], ENT_QUOTES ) : '' ),
					'imageAlt' 		=> ( $imgData ? htmlspecialchars( $imgData['alt'], ENT_QUOTES ) : '' ),
					'imageDescr' 	=> ( $imgData ? htmlspecialchars( $imgData['descr'], ENT_QUOTES ) : '' )
			);
		}
	}
	
	$temp = BuildImgSrc( $data );
	
	$data['default']['imageHtml'] = $temp;

	return $data;
}

#####################################################
#
# Builds Full Cover Image Array function
#
#####################################################
function BuildImgSrc( $arr )
{
	$array = array(
		'srcset' => '',
		'sizes' => '',
		'srcFull' => ''
	);
		
	if ( empty( $arr ) )
		return array();
		
		
	$num = count( $arr );
	
	$set = $sizes = '';
		
	$coverFull = 'srcset="';

	$i = 0;
		
	$imageWidth = ( isset( $arr['default']['imageWidth'] ) ? $arr['default']['imageWidth'] : 0 );
	
	foreach( $arr as $_ar => $ar )
	{
		$i++;
			
		$coverFull 	.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
		$set 		.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
			
		if ( $i < $num )
		{
			$coverFull  .= ', ' . PHP_EOL;
			$set 		.= ', ' . PHP_EOL;
		}
	}
		
	if ( !empty( $imageWidth ) ) 
	{
		$coverFull .= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px"';
			
		$sizes .=  '(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px';
	}

	if ( $num > 1 )
	{
		$i = 0;
			
		$set = $sizes = '';
		
		$coverFull = 'srcset="';
		
		foreach( $arr  as $_ar => $ar )
		{
			$i++;
				
			$coverFull 	.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
			$set 		.= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : '0' ) . 'w';
				
			if ( $i < $num )
			{
				$coverFull  .= ', ' . PHP_EOL;
				$set 		.= ', ' . PHP_EOL;
			}
		}
			
		if ( !empty( $imageWidth ) ) 
		{
			$coverFull .= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px"';
			$sizes .= '(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px';
		}
	}
		
	return array(
		'srcset' => $set,
		'sizes' => $sizes,
		'srcFull' => $coverFull
	);
}

#####################################################
#
# Build the object data function
#
#####################################################
function BuildPosts( $data )
{		
	if ( empty( $data ) )
		return null;

	$posts = array();

	foreach ( $data as $p )
	{
		$posts[] = new Post( $p );
	}

	return $posts;
}

#####################################################
#
# Link posts with custom types
#
#####################################################
function CheckCustomTypes( $id, $arr )
{
	$db = db();
	
	if ( !empty( $arr ) )
	{
		foreach( $arr as $cusId => $cus )
		{
			if ( is_array( $cus ) )
			{
				$cus = $cusId;
			}

			//Get any tags from this post
			$tags = GetAssocTags( $id, $cus, true );
			
			$ex = $db->from( 
			null, 
			"SELECT id_relation
			FROM `" . DB_PREFIX . "post_types_relationships`
			WHERE (post_id = " . $id . ") AND (id_post_type = " . $cus . ")"
			)->single();

			if( !$ex && !empty( $tags ) )
			{
				//Insert this relation
				$dbarr = array(
					"post_id" 		=> $id,
					"id_post_type" 	=> $cus
				);
					
				$db->insert( 'post_types_relationships' )->set( $dbarr );
			}
			
			//It there are no tags available, unlink this post from this custom type
			if ( $ex && empty( $tags ) )
			{
				$db->delete( 'post_types_relationships' )->where( "post_id", $id )->run();
			}
		}
	}
	else
	{
		//Delete any relation
		$db->delete( 'post_types_relationships' )->where( "post_id", $id )->run();
	}
}

#####################################################
#
# Post's variation function
#
#####################################################
function Variations( $param )
{
	if ( empty( $param['variations'] ) )
		return;
		
	$parentId 	= ( ( isset( $param['variationParent'] ) && !empty( $param['variationParent']['id'] ) ) ? $param['variationParent']['id'] : null );
		
	$langCode 	= GetLangKey( $param['langId'] );
	
	$db 		= db();
		
	if ( empty( $parentId ) )
	{
		$slug = CreateSlug( $param['variationParent']['title'], true );
		
		$dbarr = array(
			"id_post" 	=> $param['postId'],
			"id_site" 	=> $param['siteId'],
			"id_lang" 	=> $param['langId'],
			"title" 	=> $param['variationParent']['title'],
			"sef" 		=> $slug
		);

		$pId = $db->insert( 'post_variations' )->set( $dbarr, null, true );
	}
	else
	{
		$pId = $parentId;
			
		$slug = CreateSlug( $param['variationParent']['title'], true );
			
		$dbarr = array(
			"title" => $param['variationParent']['title'],
			"sef" 	=> $slug
		);

		$db->update( 'post_variations' )->where( 'id', $pId )->set( $dbarr );
	}		
		
	foreach( $param['variations'] as $p_ => $var )
	{
		$slug = CreateSlug( $var['title'], true );
			
		$tmp = GetSinglePost( $p_, null, false );
			
		$pTitle = ( $tmp ? $tmp['title'] 	: '' );
		$url    = ( $tmp ? $tmp['postUrl'] 	: '' );
			
		//First check if have this variation
		$v = $db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "post_variations_items`
		WHERE (id_parent = " . $pId . ") AND (id_post = " . $p_ . ")"
		)->single();
			
		if ( $v )
		{
			$dbarr = array(
				"title"	 	=> $var['title'],
				"sef" 		=> $slug,
				"var_order" => $var['order'],
				"ptitle" 	=> $pTitle,
				"url" 		=> $url
			);

			$db->update( 'post_variations_items' )->where( 'id', $v['id'] )->set( $dbarr );
		}
			
		else
		{
			$dbarr = array(
				"title"	 	=> $var['title'],
				"sef" 		=> $slug,
				"id_post" 	=> $p_,
				"id_parent" => $pId,
				"var_order" => $var['order'],
				"ptitle" 	=> $pTitle,
				"url" 		=> $url
			);
				
			$db->insert( 'post_variations_items' )->set( $dbarr );
		}
	}
}

#####################################################
#
# Grab External Images Function
#
#####################################################
function GetExternalImagesFromContent( $content )
{
	preg_match_all('/(<figure>)?<img.+src=[\'"]([^\'"]+)[\'"].*>(<figcaption>(.*)<\/figcaption><\/figure>)?/iU', $content, $matches, PREG_SET_ORDER );
	//TODO
	var_dump( $matches );exit;

		if ( empty( $matches ) )
			return $content;

		$local = $Admin->ImageUpladDir( SITE_ID );

		$root = ( !empty( $local ) ? $local['root'] : null );
		
		$share = ( !$Admin->IsDefaultSite() ? $Admin->ImageUpladDir( $postSiteId ) : null );
		var_dump( $share );exit;
		$folder = FolderRootByDate( $postDate, $root );

		foreach( $matches as $match )
		{
			$caption = ( ( isset( $match['1'] ) && !empty( $match['1'] ) ) ? htmlspecialchars( $match['1'] ) : null );

			if ( strpos( $match['2'], '?' ) !== false ) 
			{
				$img = explode('?', $match['2']);
				$img = $img['0'];
			} 

			else
				$img = $match['2'];
			
			if ( empty( $img ) )
				continue;
			
			$info = pathinfo( $img );
			
			if ( empty( $info ) || !isset( $info['extension'] ) || empty( $info['extension'] ) )
			{
				continue;
			}
			
			$allowed = AllowedExt();

			//Make sure we allow the extension
			if ( empty( $allowed ) || !in_array( $info['extension'], $allowed ) )
			{
				continue;
			}

			$sefName = URLify( $info['filename'] );

			$fileName = $sefName . '.' . $info['extension'];
			
			$format = null;
			
			//Check if we have this image already
			$query = array(
					'SELECT'	=>  'id_image, width',

					'FROM'		=> DB_PREFIX . 'images',

					'WHERE'		=> "filename = :name",

					'PARAMS' 	=> array( 'NO_PREFIX' => true ),

					'BINDS' 	=> array(
								array( 'PARAM' => ':name', 'VAR' => $fileName, 'FLAG' => 'STR' )
					)
			);

			$imgData = Query( $query );
			
			if ( $imgData )
			{
				$format = '[image id="' . $imgData['id_image'] . '" width="' . $imgData['width'] . '" align="center"]';
			}
			else
			{
				$fileName = CopyImage( $img, $folder );
				
				$imgRoot = $folder . $fileName;
	
				if ( !$fileName || !file_exists( $imgRoot ) )
				{
					continue;
				}
				
				list( $width, $height ) = getimagesize( $imgRoot );
				
				$imgUrl = ( !empty( $local ) ? FolderUrlByDate( $postDate, $local['html'] ) : FolderUrlByDate( $postDate ) ) . $fileName;
	
				$imageID = addDbImage( $fileName, $folder, $postSiteId, $Admin->UserID(), 'post', 'full', 0, $postDate, null, $postLangId, $id );

				if ( $imageID )
				{
					//If we have child site(s), ask them to copy the image
					if ( !empty( $share ) && isset( $share['share'] ) && $share['share'] )
					{
						$Admin->PingChildSite( 'sync', 'image', null, $postSiteId, $imgUrl, $postDate );
					}

					//Create the smaller images
					CreateChildImgs( $imgRoot, $imageID, $postDate, $folder, $postSiteId, 0, 0, 'post' );
				}
				
				//Check it the image exists in the folder
				//Useful if we have changed our share settings for a site
				//Better do it here to avoid any delay to the frontend 
				CheckImageExists( $imageID );
				
				$format = '[image id="' . $imageID . '" width="' . $width . '" align="center"]';
			}
			
			if ( $format )
			{
				$content = str_replace( $match['0'], $format, $content );
			}
		}
		
		return $content;
	}

#####################################################
#
# Get Single Form function
#
#####################################################
function GetSingleForm( $id )
{
	$db = db();
	
	// Get the form
	$form = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "forms`
	WHERE (id = " . (int) $id . ")"
	)->single();
	
	if ( !$form )
		return null;
	
	$data = array(
		'id' 			=> $form['id'],
		'name' 			=> $form['title'],
		'disabled' 		=> $form['disabled'],
		'type' 			=> $form['form_type'],
		'table-type'	=> $form['table_type'],
		'pos' 			=> $form['form_pos'],
		'show-if' 		=> $form['show_if'],
		'show-id' 		=> $form['show_if_id'],
		'groups' 		=> Json( $form['groups_data'] ),
		'data' 			=> Json( $form['form_data'] ),
		'elements' 		=> array()
	);
	
	$elems = $db->from( 
	null, 
	"SELECT id, data, disabled, elem_id, elem_order, elem_name
	FROM `" . DB_PREFIX . "form_elements`
	WHERE (id_form = " . (int) $id . ") AND (disabled = 0)
	ORDER BY elem_order ASC"
	)->all();
	
	if ( $elems )
	{
		foreach( $elems as $elem )
		{
			$tableElememts = array();
			
			if ( $form['form_type'] == 'table' )
			{
				$table_els = $db->from( 
				null, 
				"SELECT id, data, style, elem_id, elem_order, elem_type
				FROM `" . DB_PREFIX . "form_table_elements`
				WHERE (id_column = " . $elem['id'] . ") AND (disabled = 0)
				ORDER BY elem_order ASC"
				)->all();

				if ( $table_els )
				{
					foreach( $table_els as $table_el )
					{
						$tableElememts[$table_el['elem_type']][] = array(
							'id' 		=> $table_el['id'],
							'elementId' => $table_el['elem_id'],
							'columnId'  => $elem['id'],
							'order' 	=> $table_el['elem_order'],
							'data'		=> Json( $table_el['data'] ),
							'style'		=> Json( $table_el['style'] )
						);
					}
				}
			}

			$data['elements'][$elem['id']] = array(
				'id' 		=> $elem['id'],
				'elementId' => $elem['elem_id'],
				'order' 	=> $elem['elem_order'],
				'name' 		=> StripContent( $elem['elem_name'] ),
				'data'		=> Json( $elem['data'] ),
				'elements' 	=> $tableElememts
			);
		}
	}

	return $data;
}

#####################################################
#
# Update Posts Stats function
#
#####################################################
function UpdatePostStats( $args, $new = false )
{
	$db = db();
	
	if ( $new )
	{
		if ( $args['blogId'] > 0 )
		{
			//$db->update( "blogs" )->where( 'id_blog', $args['blogId'] )->set( "num_posts", array( "num_posts", "1", "+" ) );
			$db->update( "blogs" )->where( "id_blog", $args['blogId'] )->increase( "num_posts" );
		}
		
		//$db->update( "categories" )->where( 'id', $args['categoryId'] )->set( "num_items", array( "num_items", "1", "+" ) );
		$db->update( "categories" )->where( "id", $args['categoryId'] )->increase( "num_items" );
		
		if ( isset( $args['subCategoryId'] ) && ( $args['subCategoryId'] > 0 ) )
		{
			//$db->update( "categories" )->where( 'id', $args['subCategoryId'] )->set( "num_items", array( "num_items", "1", "+" ) );
			$db->update( "categories" )->where( "id", $args['subCategoryId'] )->increase( "num_items" );
		}
	}
	
	elseif ( !empty( $args['oldCategoryId'] ) )
	{
		if ( $args['categoryId'] != $args['oldCategoryId'] )
		{
			//$db->update( "categories" )->where( 'id', $args['oldCategoryId'] )->set( "num_items", array( "num_items", "1", "-" ) );			
			//$db->update( "categories" )->where( 'id', $args['categoryId'] )->set( "num_items", array( "num_items", "1", "+" ) );
			
			$db->update( "categories" )->where( "id", $args['oldCategoryId'] )->decrease( "num_items" );
			$db->update( "categories" )->where( "id", $args['categoryId'] )->increase( "num_items" );
		}
		
		if ( ( ( $args['subCategoryId'] > 0 ) || ( $args['oldSubCategoryId'] > 0 ) ) && ( $args['subCategoryId'] != $args['oldSubCategoryId'] ) )
		{
			if ( $args['oldSubCategoryId'] > 0 )
			{
				//$db->update( "categories" )->where( 'id', $args['oldSubCategoryId'] )->set( "num_items", array( "num_items", "1", "-" ) );
				$db->update( "categories" )->where( "id", $args['oldSubCategoryId'] )->decrease( "num_items" );
			}
			
			if ( $args['subCategoryId'] > 0 )
			{
				//$db->update( "categories" )->where( 'id', $args['subCategoryId'] )->set( "num_items", array( "num_items", "1", "+" ) );
				$db->update( "categories" )->where( "id", $args['subCategoryId'] )->increase( "num_items" );
			}
		}
	}
}


//TODO: CHECK AND REMOVE THE FOLLOWING FUNCTIONS



#####################################################
#
# Gets content from external page function
#
#####################################################
function GetContentFromUrl( $arr, $siteId, $date = null, $title = null, $descr = null, $url = null )
{
	$db 			= db();
	$Bot 			= new Bot;
	$img 			= new Image( $siteId );
	$options 		= array();
	$date			= ( $date ? $date : time() );
	$url 			= ( !empty( $url ) ? $url : $arr['url'] );
	$custom 		= Json( $arr['custom_data'] );
	$regex  		= ( !empty( $custom['regex'] ) ? $custom['regex'] : null );
	$search 		= ( !empty( $custom['search_replace'] ) ? $custom['search_replace'] : null );
	$customFields 	= ( !empty( $regex['custom_fields'] ) ? $regex['custom_fields'] : null );
	$skip			= ( ( $arr['skip_posts_days'] > 0 ) ? ( $arr['skip_posts_days'] * 86400 ) : null );
	$catData		= array();
	$autoCats		= false;

	$options['randomIp']['value'] 			= ( isset( $regex['rotate_ip_address'] ) ? $regex['rotate_ip_address'] : null );
	$options['crawlAsGoogleBot']['value'] 	= ( isset( $regex['crawl_as'] ) ? $regex['crawl_as'] : 'normal' );
	
	//Get some data from the category
	if ( !empty( $arr['id_category'] ) )
	{
		$catData = $db->from( 
		null, 
		"SELECT id_lang, id_blog, id_parent
		FROM `" . DB_PREFIX . "categories`
		WHERE (id = " . $arr['id_category'] . ") AND (id_site = " . $arr['id_site'] . ")"
		)->single();

		if ( !$catData )
		{
			return;
		}
	}

	elseif ( !empty( $arr['auto_category'] ) )
	{
		$catData 				= array();
		$autoCats 				= true;
		$catData['id_category'] = 0;
		
		$s = _explode( $arr['auto_category'], '::' );
		
		if ( empty( $s ) )
		{
			return;
		}
		
		if ( $s['target'] == 'lang' )
		{
			$catData['id_blog'] = 0;
			$catData['id_lang'] = $s['id'];
		}

		else if ( $s['target'] == 'blog' )
		{
			$catData['id_blog'] = $s['id'];
			
			$x = $db->from( 
			null, 
			"SELECT id_lang
			FROM `" . DB_PREFIX . "blogs`
			WHERE (id_blog = " . $s['id'] . ")"
			)->single();
			
			if ( $x && ( $x['id_lang'] > 0 ) )
			{
				$catData['id_lang'] = $x['id_lang'];
			}
			
			else
			{
				$x = $db->from( 
				null, 
				"SELECT id
				FROM `" . DB_PREFIX . "languages`
				WHERE (id_site = " . $siteId . ") AND (is_default = 1)"
				)->single();
				
				$catData['id_lang'] = $x['id'];
			}
		}
	}

	else
	{
		return;
	}

	$Bot->options 	= $options;
	$copyImg		= IsTrue( $arr['copy_images'] );
	$firstAsCover 	= IsTrue( $arr['set_first_image_cover'] );
	$setSource 		= IsTrue( $arr['set_source_link'] );
	$skipNoImg 		= IsTrue( $arr['skip_posts_no_images'] );
	$template		= $arr['post_template'];
	$status			= $arr['post_status'];
	$type			= $arr['post_type'];
	$userId			= $arr['user_id'];
	$siteId			= $arr['id_site'];
	$langId			= ( !empty( $catData['id_lang'] ) ? $catData['id_lang'] : 0 );
	$blogId			= ( !empty( $catData['id_blog'] ) ? $catData['id_blog'] : 0 );
	$catId			= ( empty( $catData['id_parent'] ) ? $arr['id_category'] : $catData['id_parent'] );
	$subCatId		= ( empty( $catData['id_parent'] ) ? 0 : $arr['id_category'] );
	$addTitleTags	= (int) $arr['add_tags'];
	$avoid			= trim( strtolower( $arr['avoid_words'] ) );
	$have			= trim( strtolower( $arr['required_words'] ) );
	$haveWords 		= null;
	$avoidWords 	= null;
	$langCode 		= ( !empty( $langId ) ? GetLangKey( $langId ) : null );

	if ( !empty( $avoid ) && str_contains( $avoid, ',' ) )
	{
		$avoidWords = explode( ',', $avoid );
	}

	if ( !empty( $have ) && str_contains( $have, ',' ) )
	{
		$haveWords = explode( ',', $have );
	}

	$str = '<br><em><p><strong>';

	if ( !IsTrue( $arr['strip_links'] ) )
	{
		$str .= '<a>';
	}

	if ( !IsTrue( $arr['remove_images'] ) )
	{
		$str .= '<img>';
	}

	if ( !IsTrue( $arr['strip_html'] ) )
	{
		$str .= '<ul><li><quote><blockqoute><code><embed>';
	}

	//Set generic info for images
	$img->userId 		= $userId;
	$img->langId 		= $langId;
	$img->isExternal 	= true;

	$postDate 		= ( !IsTrue( $arr['set_original_date'] ) ? time() : $date );
	$postUpdated	= 0;
	$postTitle		= $title;
	$postDescr		= $descr;
	$postContent	= $postDescr;
	$postAlias		= '';
	$postSubtitle	= '';
	$xtraPostData	= '';
	$postImg		= null;
	$postHas		= ( !empty( $have ) ? false : true );
	$postContains	= false;
	$postTags		= array();
	$uri			= preg_replace( '/((\?|&amp;|&#038;)utm_source=.*)?((&amp;|&#038;)utm_campaign=.*)?(&amp;|&#038;)?/', '', $url );
	$tags			= array();
	$postImages		= array();
	$tempTitle 		= strtolower( $postTitle );
	$Bot->url 		= $uri;
	$uuid 			= md5( $uri );

	//Check if this post exists
	$ex = $db->from( 
	null, 
	"SELECT id, id_post
	FROM `" . DB_PREFIX . "posts_data`
	WHERE (uuid = :uuid)",
	array( $uuid => ':uuid' )
	)->single();

	if ( $ex )
	{
		return;
	}

	//Let's see if there is a word we don't want (or do?)
	$postContains 	= ( ( empty( $avoid ) && empty( $avoidWords ) ) ? false : SearchForWord( $avoid, $avoidWords, $tempTitle ) );

	//Do the same for "have" words
	$postHas 		= ( ( empty( $have ) && empty( $haveWords ) ) ? true : SearchForWord( $have, $haveWords, $tempTitle ) );

	if ( $postContains || !$postHas || empty( $regex ) )
	{
		return;
	}

	try
	{
		$Bot->process();

		if ( $Bot->status == 200 )
		{
			if ( !empty( $regex['regex_title'] ) )
			{
				$title 		= $Bot->match( $regex['regex_title'] );
				$postTitle	= ( !empty( $title ) ? StripContent( $title ) : $postTitle );
			}

			if ( !empty( $regex['regex_image'] ) )
			{
				$im 		= $Bot->match( $regex['regex_image'] );
				$postImg	= ( !empty( $im ) ? StripContent( $im ) : $postImg );
			}

			$coverImg = ( !empty( $postImg ) ? $postImg : null );

			if ( !empty( $regex['regex_descr'] ) )
			{
				$descr 		= $Bot->match( $regex['regex_descr'] );
				$postDescr	= ( !empty( $descr ) ? StripContent( $descr ) : $postDescr );
			}

			if ( !empty( $regex['regex_content'] ) )
			{
				$con 			= $Bot->match( $regex['regex_content'] );
				$postContent	= ( !empty( $con ) ? StripContent( $con ) : $postContent );
			}

			if ( $copyImg )
			{
				$postImages = SearchContentImages( $postContent, $firstAsCover );
			}

			//Skip this post if no images found
			if ( $skipNoImg && empty( $postImages['1'] ) )
			{
				return;
			}

			if ( !empty( $search ) )
			{
				foreach( $search as $re )
				{
					$postContent = str_replace( $re['search'], $re['replace'], $postContent );
				}
			}

			if ( $type == 'post' )
			{
				if ( !empty( $regex['regex_tags'] ) )
				{
					$tags = $Bot->match( $regex['regex_tags'] );
				}

				//The previous match found nothing. Check the container
				if ( empty( $tags ) && !empty( $regex['regex_tags'] && !empty( $regex['regex_tags_container'] ) ) )
				{
					$tagC = $Bot->match( $regex['regex_tags_container'] );

					if ( !empty( $tagC ) )
					{
						$tags = $Bot->match( $regex['regex_tags'], $tagC );
					}
				}

				//Add the tags from the title
				if ( ( $addTitleTags > 0 ) && empty( $tags ) )
				{
					$i 		= 0;
					$tags 	= array();
					$tags_ 	= preg_split("/[\s]+/", $postTitle, -1, PREG_SPLIT_NO_EMPTY );

					if ( !empty( $tags_ ) )
					{
						foreach( $tags_ as $tag )
						{
							if ( $i >= $addTitleTags )
							{
								break;
							}

							$str = preg_replace( '/[\W]/', '', $tag );

							if ( strlen( $str ) > 2 )
							{
								$tags[] = $str;
								$i++;
							}
						}
					}
				}

				if ( !empty( $tags ) )
				{
					if ( is_array( $tags ) )
					{
						foreach( $tags as $tag )
						{
							$postTags[] = array( 'value' => $tag );
						}
					}

					//This is for only one tag
					elseif ( is_string( $tags ) )
					{
						$postTags[] = array( 'value' => $tags );
					}
				}
			}

			if ( !empty( $customFields ) )
			{
				foreach( $customFields as $cus )
				{
					$s = _explode( $cus['field'], '::' );

					if ( $s['target'] == 'pre' )
					{
						if ( $s['id'] == 'updated' )
						{
							$updt 			= $Bot->match( $cus['value'] );
							$postUpdated	= ( !empty( $updt ) ? @strtotime( $updt ) : 0 );
						}

						if ( $s['id'] == 'alias' )
						{
							$alias 		= $Bot->match( $cus['value'] );
							$postAlias	= ( !empty( $alias ) ? StripContent( $alias ) : '' );
						}

						if ( $s['id'] == 'subtitle' )
						{
							$subtitle 		= $Bot->match( $cus['value'] );
							$postSubtitle	= ( !empty( $subtitle ) ? StripContent( $subtitle ) : '' );

							$xtraPostData = array(
								'subtitle' => $postSubtitle
							);
						}
					}
				}
			}

			//Add the post
			$dbarr = array(
				"id_site" 			=> $siteId,
				"id_lang" 			=> $langId,
				"id_blog" 			=> $blogId,
				"title" 			=> $postTitle,
				"post" 				=> '',
				"description" 		=> $postDescr,
				"post_status" 		=> $status,
				"added_time" 		=> $postDate,
				"edited_time" 		=> $postUpdated,
				"sef" 				=> '',
				"id_category" 		=> $catId,
				"post_type" 		=> $type,
				"poster_ip" 		=> GetRealIp(),
				"id_member" 		=> $userId,
				"id_sub_category" 	=> $subCatId
			);
	
			$postId = $db->insert( POSTS )->set( $dbarr, null, true );

			if ( !$postId )
			{
				return;
			}

			$img->time 		= $postDate;
			$img->postId 	= $postId;
		
			$slug = SetShortSef( POSTS, 'id_post', 'sef', CreateSlug( $postTitle, true ), $postId );
		
			$db->update( POSTS )->where( 'id_post', $postId )->set( "sef", $slug );

			//Check for any images in the content and update it
			if ( !empty( $postImages ) && $copyImg )
			{
				$postContent = ReplaceContentImages( $postContent, $postImages, $siteId, $langId, $postId, $postDate );
			}

			$postContent = trim( strip_tags( $postContent, $str ) );

			if ( !empty( $template ) )
			{
				$search 		= array ( '{{title}}', '{{description}}', '{{content}}', '{{source-url}}', '{{image-url}}', '{{more}}' );

				$replace 		= array ( $postTitle, $postDescr, $postContent, $uri, $this->coverImg, '<!--more-->' );

				$postContent 	= trim( str_replace( $search, $replace, $postContent ) );

			}

			if ( $setSource )
			{
				$postContent .= '
				<p><a href="' . $uri . '" target="_blank" rel="noopener nofollow">' . __( 'source' ) . '</a></p>';
			}

			$db->update( POSTS )->where( 'id_post', $postId )->set( "post", $postContent );

			if ( $blogId > 0 )
			{
				$db->update( "blogs" )->where( "id_blog", $blogId )->increase( "num_posts" );
			}

			if ( $type == 'post' )
			{
				if ( $catId > 0 )
				{
					$db->update( "categories" )->where( "id", $catId )->increase( "num_items" );
				}

				if ( $subCatId > 0 )
				{
					$db->update( "categories" )->where( "id", $subCatId )->increase( "num_items" );
				}

				if ( !empty( $postTags ) )
				{
					AddTags( $postTags, $postId, $langId, $siteId, 0 );
				}
			}

			$dbarr = array(
				"id_post" 				=> $postId,
				"uuid" 					=> $uuid,
				"original_import_time" 	=> time(),
				"ext_id" 				=> $arr['id'],
				"title_alias" 			=> $postAlias,
				"value1" 				=> '',
				"value2" 				=> '',
				"value3" 				=> '',
				"value4" 				=> $xtraPostData
			);
			
			//Add the post's data
			$db->insert( "posts_data" )->set( $dbarr );

			//Add the post product data
			$db->insert( "posts_product_data" )->set( array( "id_post" => $postId ) );

			//Set the cover image
			if ( !empty( $coverImg ) )
			{
				$img->imgFile = $coverImg;
				$img->GetImage( true );
			}
			
			$coverImg = null;

			//Update the cover_img value
			$coverImg = PostImageDetails( $postId, $langCode );

			if ( !empty( $coverImg ) )
			{
				$db->update( POSTS )->where( 'id_post', $postId )->set( "cover_img", json_encode( $coverImg, JSON_UNESCAPED_UNICODE ) );
			}

			//Add the custom data as post attributes
			if ( !empty( $customFields ) )
			{
				foreach( $customFields as $cus )
				{
					$s = _explode( $cus['field'], '::' );

					if ( ( $s['target'] == 'att' ) && is_numeric( $s['id'] ) )
					{
						$val 	= $Bot->match( $cus['value'] );
						$value	= ( !empty( $val ) ? StripContent( $val ) : null );

						if ( !empty( $value ) )
						{
							$dbarr = array(
								"id_post" 	=> $postId,
								"id_attr" 	=> $s['id'],
								"value" 	=> $value
							);

							$db->insert( "post_attribute_data" )->set( $dbarr );
						}
					}
				}
			}
		}
	}

	catch (\Exception $e)
	{
		return;
	}
	
	return true;
}

#####################################################
#
# Replace any images found in content function
#
#####################################################
function ReplaceContentImages( $content, $imgs, $siteId, $langId, $postId, $time = null )
{
	$db 				= db();
	$img 				= new Image( $siteId );
	$img->time 			= ( !empty( $time ) ? $time : time() );
	$img->postId 		= $postId;
	$img->langId 		= $langId;
	$img->isExternal 	= true;
	
	if ( !empty( $imgs['1'] ) )
	{
		foreach( $imgs['1'] as $key => $img )
		{
			$img->imgFile = $img;

			$imageId = $img->GetImage();

			if ( !empty( $imageId ) && is_numeric( $imageId ) )
			{
				$imData = $db->from( 
				null, 
				"SELECT width
				FROM `" . DB_PREFIX . "images`
				WHERE (id_image = " . $imageId . ")"
				)->single();
					
				if ( $imData )
				{
					$newImg = '[image id="' . $imageId . '" width="' . $imData['width'] . '" align="center"]';

					$content = str_replace( $imgs['0'][$key], $newImg, $content );
				}
			}
		}
	}

	return $content;
}

#####################################################
#
# Search for images in content function
#
#####################################################
function SearchContentImages( $content, $firstAsCover = false )
{
	preg_match_all('/<figure.+>?<img.+src=[\'"]([^\'"]+)[\'"].*><\/figure>?/i', $content, $matches);

	if ( empty( $matches ) || empty( $matches['1'] ) )
		return null;

	return $matches;
}

#####################################################
#
# Search for a word function
#
#####################################################
function SearchForWord( $string, $array = null, $text, $whatToReturn = true )
{
	$return = null;
		
	if ( !empty( $string ) || !empty( $array ) )
	{
		if ( !empty( $string ) && str_contains( $text, $string ) )
		{
			$return = $whatToReturn;
		}

		else
		{
			if ( is_array( $array ) )
			{
				foreach( $array as $word )
				{
					if ( empty( $word ) )
					{
						continue;
					}

					if ( str_contains( $text, strtolower( $word ) ) )
					{
						$return = $whatToReturn;
						break;
					}
				}
			}

			else
			{
				if ( str_contains( $text, strtolower( $array ) ) )
				{
					$return = $whatToReturn;
				}
			}
		}
	}
		
	return $return;
}


#####################################################
#
# Cover Image function
#
#####################################################
function PostCoverImage( $args )
{
	if ( empty( $args ) )
		return null;
	
	$db = db();
		
	$share = ImageUpladDir( $args['siteId'] );

	if ( !empty( $share ) && isset( $share['share'] ) && $share['share'] )
	{
		$imgUrl = ( !empty( $share['html'] ) ? $share['html'] : null );
			
		$coverImg = array();
		
		$_img = $db->from( null, "
		SELECT at.id_attach, im.id_image, im.filename, im.width, im.height, im.size, im.mime_type, im.added_time, im.external_url, im.trans_data
		FROM `" . DB_PREFIX . "images` AS im
		LEFT JOIN `" . DB_PREFIX . "image_attachments` AS at ON at.image_id = im.id_image
		WHERE (at.post_id = " . $args['postId'] . ")"
		)->single();
	
		if ( !$_img )
			return null;
			
		if ( !empty( $_img['external_url'] ) )
		{
			$imageUrl = $_img['external_url'];
			$isExternal = true;
		}
		else
		{
			$orImgUrl = FolderUrlByDate( $_img['added_time'], $imgUrl ) . $_img['filename'];
				
			$isExternal = false;
				
			$ping = PingChildSite( 'sync', 'image', null, $args['siteId'], $orImgUrl, $_img['added_time'] );

			if ( !empty( $ping ) && isset( $ping['message'] ) && ( $ping['message'] == 'Success' ) && !empty( $ping['url'] ) )
				$imageUrl = $ping['url'];
				
			else
				$imageUrl = $orImgUrl;
		}
			
		$imgData = Json( $_img['trans_data'] );
			
		$imgData = ( !empty( $imgData ) && isset( $imgData[$args['langCode']] ) ? $imgData[$args['langCode']] : null );
			
		$coverImg['default'] = array(
					'imageWidth' 	=> $_img['width'],
					'imageHeight' 	=> $_img['height'],
					'imageUrl' 		=> $imageUrl,
					'isExternal' 	=> $isExternal,
					'imageId' 		=> $_img['id_image'],
					'imageFilename' => $_img['filename'],
					'mimeType' 		=> $_img['mime_type'],
					'imageCaption' 	=> ( $imgData ? $imgData['caption'] : '' ),
					'imageTitle' 	=> ( $imgData ? $imgData['title'] : '' ),
					'imageAlt' 		=> ( $imgData ? $imgData['alt'] : '' ),
					'imageDescr' 	=> ( $imgData ? $imgData['descr'] : '' )
		);
		
		$imgs = $db->from( 
		null, 
		"SELECT id_image, filename, width, height, size, mime_type
		FROM `" . DB_PREFIX . "images`
		WHERE (id_parent = " . $_img['id_image'] . ")
		ORDER BY width ASC"
		)->all();

		if ( $imgs )
		{
			foreach( $imgs as $img )
			{
				$orImgUrl = FolderUrlByDate( $_img['added_time'], $imgUrl ) . $img['filename'];
			
				$ping = PingChildSite( 'sync', 'image', null, $args['siteId'], $orImgUrl, $_img['added_time'] );
			
				if ( !empty( $ping ) && isset( $ping['message'] ) && ( $ping['message'] == 'Success' ) && !empty( $ping['url'] ) )
					$imageUrl = $ping['url'];
					
				else
					$imageUrl = $orImgUrl;
			
				$coverImg[$img['width']] = array(
						'imageWidth' => $img['width'],
						'imageHeight' => $img['height'],
						'imageUrl' => $imageUrl,
						'imageId' => $img['id_image'],
						'mimeType' => $_img['mime_type'],
						'imageFilename' => $img['filename'],
						'imageCaption' 	=> ( $imgData ? $imgData['caption'] : '' ),
						'imageTitle' 	=> ( $imgData ? $imgData['title'] : '' ),
						'imageAlt' 		=> ( $imgData ? $imgData['alt'] : '' ),
						'imageDescr' 	=> ( $imgData ? $imgData['descr'] : '' )
				);
			}
		}
	}
		
	else
		$coverImg = PostImageDetails( $args['postId'], ( isset( $args['langCode'] ) ? $args['langCode'] : null ) );

	return ( !empty( $coverImg ) ? $coverImg : array() );
}

#####################################################
#
# Add an image as cover based on parameters function
#
#####################################################
function CoverImage( $param )
{
	if ( !isset( $param['image'] ) || empty( $param['image'] ) )
	{
		return;
	}
		
	$db = db();
	
	//Add this image as external
	if ( !isset( $param['copyImage'] ) || empty( $param['copyImage'] ) )
	{
		$imgId = addExternalImage( $param['image'], $param['postId'], $param['langId'], $param['blogId'], $param['siteId'], $param['userId'], 'cover' );
	}

	else
	{
		$imgId = CopyCoverImage( $param );
	}

	//Create the array data
	if ( !empty( $imgId ) )
	{
		$imgArr = BuildImageArray( $imgId );

		if ( !empty( $imgArr ) )
		{
			$db->update( POSTS )->where( 'id_post', $param['postId'] )->set( "cover_img", json_encode( $imgArr, JSON_UNESCAPED_UNICODE ) );
		}
	}
}

#####################################################
#
# Create an image based on parameters function
#
#####################################################
function CopyCoverImage( $param = array(), $cover = true )
{
	if ( empty( $param ) || !isset( $param['image'] ) || empty( $param['image'] ) || !isset( $param['copyImage'] ) || !$param['copyImage'] )
		return;
		
	$name = pathinfo( $param['image'] );
	
	if ( empty( $name ) || empty( $name['extension'] ) )
	{
		return;
	}
	
	$allowed = AllowedExt( $param['siteId'] );
	
	//Make sure we allow the extension
	if ( empty( $allowed ) || !in_array( $name['extension'], $allowed ) )
	{
		return;
	}
		
	$time = $param['postDate'];
		
	$local = ImageUpladDir( SITE_ID );
	
	$root = ( !empty( $local ) ? $local['root'] : null );
	
	$folder = FolderRootByDate( $time, $root );
	
	$mime = GetMimeType( $param['image'] );
		
	$targetName = ( isset( $param['fileName'] ) && !empty( $param['fileName'] ) ? $param['fileName'] : null );
	
	$fileName = CopyImage( $param['image'], $folder, $targetName, true, $mime );

	$imgRoot = $folder . $fileName;
	
	//We couldn't copy the file
	if ( !$fileName || !file_exists( $imgRoot ) )
	{
		return null;
	}
		
	//Or maybe the file is empty, don't keep it
	if ( filesize( $imgRoot ) < 100 )
	{
		@unlink( $imgRoot );
		return null;
	}
		
	list( $width, $height ) = @getimagesize( $imgRoot );
		
	//Set the image's url
	$imgUrl = ( !empty( $local ) ? FolderUrlByDate( $time, $local['html'] ) : FolderUrlByDate( $time ) ) . $fileName;
	
	$imageID = addDbImage( $fileName, $folder, $param['siteId'], $param['userId'], $param['postType'], 'full', 0, $time, null, $param['langId'], $param['postId'], $cover );
		
	if ( $imageID )
	{
		$share = ImageUpladDir( $param['siteId'] );

		//If we have child site(s), ask them to copy the image
		if ( !empty( $share ) && isset( $share['share'] ) && $share['share'] )
		{
			PingChildSite( 'sync', 'image', null, $param['siteId'], $imgUrl, $time );
		}
			
		if ( $mime == 'image' )
		{
			//Create the smaller images
			CreateChildImgs( $imgRoot, $imageID, $time, $folder, $param['siteId'], 0, 0, $param['postType'] );
		}

		return $imageID;
	}
		
	return null;
}

#####################################################
#
# Post Cover Image function
#
#####################################################
function PostImage( $args )
{
	if ( empty( $args ) )
		return null;
	
	$folder = FolderRootByDate( $args['postDate'] );
		
	$code = $args['langCode'];
	
	$db = db();

	//Now we can set the extrernal image, if we have one
	if ( isset( $args['externalImage'] ) && !empty( $args['externalImage'] ) )
	{
		//Maybe we have already an image as cover, so let's delete this key
		DelCoverImage( $args['postId'] );

		//We can copy this image locally, if this is what we want
		if ( isset( $args['copyRemoteImage'] ) && $args['copyRemoteImage'] )
		{
			$copy = CopyImage( $args['externalImage'], $folder );

			if( $copy )
			{
				//If we previously had set this image as external for this post, delete this image as we are going to copy it locally
				$db->delete( 'images' )->where( "id_post", $args['postId'] )->where( "external_url", $args['externalImage'] )->run();
				
				//Now check if we have this image already
				$imgExists = $db->from( 
				null, 
				"SELECT id_image
				FROM `" . DB_PREFIX . "images`
				WHERE (filename = :name)",
				array( $copy => ':name' )
				)->single();
						
				if ( !$imgExists )
				{
					//Add the image
					$imageID = addLocalImage( $copy, $args['postId'], $args['langId'], $args['blogId'], $args['siteId'], $args['userId'], 'cover', $args['title'], $args['postDate'], true );
						
					if ( $imageID )
					{
						$sizes = array( '75', '50', '25' );
							
						$imgRoot = $folder . $copy;
							
						$name = pathinfo( $imgRoot );
				
						//Create three smaller copies from the original image
						foreach( $sizes as $_size )
						{
							//Calculate the img size
							$size = CalculateImgSize( $imgRoot, $_size );
								
							if ( empty( $size ) )
								continue;

							$newImg = $name['filename'] . '-' . $size['f'] . '.' . $name['extension'];
								
							CreateImage( $imgRoot, $folder . $newImg, array( 'w' => $size['w'], 'h' => $size['h'] ) );

							if ( file_exists( $folder . $newImg ) ) 
								addDbImage( $newImg, $folder, $args['siteId'], 0, 'post', 'cropped', 0, null, $imageID, $args['langId'] );
						}
					}						
				}
			}

			//We couldn't grab the image, keep it as external
			else
				addExternalImage( $args['externalImage'], $args['postId'], $args['langId'], $args['blogId'], $args['siteId'], $args['userId'], 'cover', $args['title'], $args['postDate'] );
		}

		else
		{
			//First check if we have this image already
			$data = $db->from( 
			null, 
			"SELECT id_image
			FROM `" . DB_PREFIX . "images`
			WHERE (id_post = " . $args['postId'] . ") AND (external_url = :url)",
			array( $args['externalImage'] => ':url' )
			)->single();

			//We don't so add it
			if ( !$data )
			{
				addExternalImage( $args['externalImage'], $args['postId'], $args['langId'], $args['blogId'], $args['siteId'], $args['userId'], 'cover', null, $args['postDate'] );
			}
				
			//We've found the image, update it
			else
			{
				AddImageAsCover( $args['postId'], $data['id_image'], $args['userId'] );
			}
		}
	}

	//We've had an image but we are no longer want it?
	else
	{
		//If this field is empty, means that we don't longer want the cover image
		//So we should no longer have it as cover
		if ( !isset( $args['coverImageId'] ) || empty( $args['coverImageId'] ) )
		{
			DelCoverImage( $args['postId'] );
		}

		//We added an image? Check it and added it if necessary
		elseif ( isset( $args['coverImageId'] ) && !empty( $args['coverImageId'] ) )
		{
			//Check if we already have a cover image for this post
			$data = $db->from( 
			null, 
			"SELECT id_attach, image_id
			FROM `" . DB_PREFIX . "image_attachments`
			WHERE (post_id = " . $args['postId'] . ")"
			)->single();

			//If we have an image, check if is the same image, otherwise edit the previous image
			if ( $data && ( $data['image_id'] != $args['coverImageId'] ) )
			{
				$dbarr = array(
					"image_id" 	 => $args['coverImageId'],
					"user_id" 	=> $args['userId']
				);
					
				$db->update( "image_attachments" )->where( 'id_attach', $data['id_attach'] )->set( $dbarr );
			}
				
			else
			{
				$dbarr = array(
					"post_id" 	=> $args['postId'],
					"image_id" 	=> $args['coverImageId'],
					"user_id" 	=> $args['userId']
				);
					
				$db->insert( 'image_attachments' )->set( $dbarr );
			}
		}
	}
}
	
//Add/Update an image as cover
function AddImageAsCover( $postId, $imgId, $userId )
{
	$db = db();
	
	$id = null;
	
	$ex = $db->from( 
	null, 
	"SELECT id_attach
	FROM `" . DB_PREFIX . "image_attachments`
	WHERE (post_id = " . (int) $postId . ")"
	)->single();

	if ( !$ex )
	{
		$dbarr = array(
			"post_id" 	=> $postId,
			"image_id" 	=> $imgId,
			"user_id" 	=> $userId
		);

		$q = $db->insert( 'image_attachments' )->set( $dbarr );

		if ( $q )
		{
			$id = $db->lastId();
		}
	}
		
	else
	{
		$dbarr = array(
			"image_id" 	=> $imgId,
			"user_id" 	=> $userId
		);
			
		$q = $db->update( "image_attachments" )->where( 'id_attach', $ex['id_attach'] )->set( $dbarr );
	}
	
	return $q;
}

#####################################################
#
# Add NEw Content function
#
#####################################################
# TODO
function AddNewPost( $args )
{
	$userId = $Admin->UserID();
	$defLang = $Admin->DefaultLang()['id'];
	$lang = $_POST['post_lang_id'];
	$site = $_POST['post_site_id'];
	$blog = ( ( isset( $_POST['blogId'] ) && !empty( $_POST['blogId'] ) ) ? (int) $_POST['blogId'] : $_POST['post_blog_id'] );
		
	//Grab the data from the POST
	$postType = ( isset( $_POST['postType'] ) ? $_POST['postType'] : 'post' );
	$postFormat = ( isset( $_POST['postFormat'] ) ? $_POST['postFormat'] : 0 );
	$categoryId = 0;
	$subCategoryId = 0;
		
	if ( ( $postType != 'page' ) && isset ( $_POST['category'] ) )
	{
		$cat = _explode( $_POST['category'], '::' );
			
		if ( $cat['target'] == 'cat' )
		{
			$categoryId = ( isset( $cat['id'] ) ? (int) $cat['id'] : 0 );
				
			$query = array(
						'SELECT' =>  "id_site, id_lang, id_blog",
							
						'FROM'	=> DB_PREFIX . 'categories',
							
						'PARAMS' => array( 'NO_PREFIX' => true ),
							
						'WHERE' => "id = :id",
							
						'BINDS'	=> array(
								array( 'PARAM' => ':id', 'VAR' => $categoryId, 'FLAG' => 'INT' )
						)
			);

			$nfo = Query( $query );
				
			if ( $nfo )
			{
				$lang = $nfo['id_lang'];
				$site = $nfo['id_site'];
				$blog = $nfo['id_blog'];
			}
		}
			
		else
		{
			$subCategoryId = ( isset( $cat['id'] ) ? (int) $cat['id'] : 0 );
				
			$query = array(
					'SELECT' =>  "id_parent, id_site, id_lang, id_blog",
					'FROM'	=> DB_PREFIX . 'categories',
					'PARAMS' => array( 'NO_PREFIX' => true ),
					'WHERE' => "id = :id",
					'BINDS'	=> array(
							array( 'PARAM' => ':id', 'VAR' => $subCategoryId['id'], 'FLAG' => 'INT' )
					)
			);

			$nfo = Query( $query );
				
			if ( $nfo )
			{
				$categoryId = $nfo['id_parent'];
				$lang = $nfo['id_lang'];
				$site = $nfo['id_site'];
				$blog = $nfo['id_blog'];
			}
		}
	}
		
		//Set the post's date
		$postDate = ( isset( $_POST['date'] ) && !empty( $_POST['date'] ) ? $_POST['date'] . ' ' . 
					( isset( $_POST['hoursPublished'] ) && !empty( $_POST['hoursPublished'] ) ? $_POST['hoursPublished'] : '00' ) . ':' .
					( isset( $_POST['minutesPublished'] ) && !empty( $_POST['minutesPublished'] ) ? $_POST['minutesPublished'] : '00' ) . ':00'
					: null 
		);
			
		$postDate = ( $postDate ? strtotime( $postDate ) : time() );
		
		$temp = ( ( isset( $_POST['slug'] ) && !empty( $_POST['slug'] ) ) ? $_POST['slug'] : $_POST['title'] );
		
		$slug = SetShortSef( POSTS, 'id_post', 'sef', CreateSlug( $temp, true ) );
		
		$query = array(
			'INSERT'	=> "id_site, id_lang, id_blog, title, post, description, post_status, added_time, sef, id_category, post_type, id_custom_type, poster_ip, id_member, id_sub_category",

			'VALUES' 	=> ":id_site, :id_lang, :id_blog, :title, :post, :description, :status, :added, :sef, :category, :type, :custom_type, :poster_ip, :id_member, :sub",
			
			'INTO'		=> DB_PREFIX . POSTS,
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
			'BINDS' 	=> array(
								array( 'PARAM' => ':id_site', 'VAR' => $site, 'FLAG' => 'INT' ),
								array( 'PARAM' => ':id_lang', 'VAR' => $lang, 'FLAG' => 'INT' ),
								array( 'PARAM' => ':id_blog', 'VAR' => $blog, 'FLAG' => 'INT' ),
								array( 'PARAM' => ':title', 'VAR' => $_POST['title'], 'FLAG' => 'STR' ),
								array( 'PARAM' => ':post', 'VAR' => $_POST['content'], 'FLAG' => 'STR' ),
								array( 'PARAM' => ':description', 'VAR' => $_POST['description'], 'FLAG' => 'STR' ),
								array( 'PARAM' => ':status', 'VAR' => 'draft', 'FLAG' => 'STR' ),
								array( 'PARAM' => ':added', 'VAR' => $postDate, 'FLAG' => 'INT' ),
								array( 'PARAM' => ':sef', 'VAR' => $slug, 'FLAG' => 'STR' ),
								array( 'PARAM' => ':category', 'VAR' => $categoryId, 'FLAG' => 'INT' ),
								array( 'PARAM' => ':type', 'VAR' => $postType, 'FLAG' => 'STR' ),
								array( 'PARAM' => ':custom_type', 'VAR' => $postFormat, 'FLAG' => 'INT' ),
								array( 'PARAM' => ':poster_ip', 'VAR' => GetRealIp(), 'FLAG' => 'STR' ),
								array( 'PARAM' => ':id_member', 'VAR' => $userId, 'FLAG' => 'INT' ),
								array( 'PARAM' => ':sub', 'VAR' => $subCategoryId, 'FLAG' => 'INT' )
				)
		);

		$id = Query( $query, false, false, false, false, true );

		if ( !$id )
		{
			$Admin->SetAdminMessage( __( 'post-add-error' ) );
			return;
		}
		
		if ( $categoryId > 0 )
		{
			$query = array(
					'UPDATE' 	=> DB_PREFIX . "categories",
					'SET'		=> "num_items = num_items + 1",
					'WHERE'		=> "id = :id",
						
					'PARAMS' 	=> array( 'NO_PREFIX' => true ),
						
					'BINDS' 	=> array(
								array( 'PARAM' => ':id', 'VAR' => $categoryId, 'FLAG' => 'INT' )
					)
			);

			Query( $query, false, false, true );
		}
		
		// Add the tags, if the post is not a page of course
		if ( $postType != 'page' )
		{
			$tags = ( !empty( $_POST['tag'] ) ? json_decode( $_POST['tag'], true ) : null );
			
			if ( !empty( $tags ) )
			{
				AddTags ( $tags, $id, $lang, $site, $postFormat );
			}
		}
		
		//Add the post data
		$query = array(
			'INSERT'	=> "id_post, value1, value2, value3, value4",
			'VALUES' 	=> ":post, '', '', '', ''",
			'INTO'		=> DB_PREFIX . "posts_data",
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
			'BINDS' 	=> array(
							array( 'PARAM' => ':post', 'VAR' => $id, 'FLAG' => 'INT' )
			)
		);

		Query( $query, false, false, true );
}

#####################################################
#
# Edit Content URLs function
#
#####################################################
function EditLinks( $post )
{
	$post = preg_replace_callback('/<a(.*)?href=[\'"]([^\'"]+)[\'"](.*)?>([^<]*)<\/a>/iU', function( $m ) 
	{
		$settings = Settings::LinkSettings();

		//External link
		if ( GetTheHostName ( $m['2'] ) != GetTheHostName ( SITE_URL ) )
		{
			$settings = ( ( !isset( $settings['external-link-settings'] ) || empty( $settings['external-link-settings'] ) ) ? null : $settings['external-link-settings'] );

			if ( empty( $settings ) || !$settings['enable_settings'] || empty( $m['4'] ) )
				return $m['0'];

			else
			{
				$link = '<a href="' . $m['2'] . '"';
				
				if ( $settings['open_links_new_tab'] )
				{
					$link .= ' target="_blank"';
				}
				
				if ( !empty( $settings['css_class'] ) )
				{
					$link .= ' class="' . StripContent( $settings['css_class'] ) . '"';
				}
				
				if ( $settings['nofollow_links'] )
				{
					$link .= ' rel="nofollow';
				
					if ( !empty( $settings['add_rel'] ) )
					{
						foreach( $settings['add_rel'] as $rel )
						{
							$link .= ' ' . $rel;
						}
					}
					
					$link .= '"';
				}
				else
				{
					if ( !empty( $settings['add_rel'] ) )
					{
						$link .= ' rel="';
						
						$count = count( $settings['add_rel'] );
						$i = 0;
						
						foreach( $settings['add_rel'] as $rel )
						{
							$i++;
							$link .= $rel . ( ( $count > $i ) ? ' ' : '' );
						}

						$link .= '"';
					}
				}

				$link .= '>' . $m['4'] . '</a>';
			}
		}
		
		else
		{
			$settings = ( ( !isset( $settings['internal-link-settings'] ) || empty( $settings['internal-link-settings'] ) ) ? null : $settings['internal-link-settings'] );

			if ( empty( $settings ) || !$settings['enable_settings'] || empty( $m['4'] ) )
				return $m['0'];
			
			$link = '<a href="' . $m['2'] . '"';
				
			if ( $settings['open_links_new_tab'] )
			{
				$link .= ' target="_blank"';
			}
			
			if ( !empty( $settings['css_class'] ) )
			{
				$link .= ' class="' . StripContent( $settings['css_class'] ) . '"';
			}
				
			if ( $settings['nofollow_links'] )
			{
				$link .= ' rel="nofollow';
				
				if ( !empty( $settings['add_rel'] ) )
				{
					foreach( $settings['add_rel'] as $rel )
					{
						$link .= ' ' . $rel;
					}
				}
					
				$link .= '"';
			}
			else
			{
				if ( !empty( $settings['add_rel'] ) )
				{
					$link .= ' rel="';
					
					$count = count( $settings['add_rel'] );
					$i = 0;
					
					foreach( $settings['add_rel'] as $rel )
					{
						$i++;
						$link .= $rel . ( ( $count > $i ) ? ' ' : '' );
					}
						
					$link .= '"';
				}
			}
	
			$link .= '>' . $m['4'] . '</a>';
		}

		return $link;
		
	}, $post);

	return $post;
}

#####################################################
#
# Get The posts function
#
#####################################################
function GetPosts( $type = 'latest', $items = HOMEPAGE_ITEMS, $langId = null, $blogId = null, $catId = null, $subId = null, $cache = true )
{
	$langId = ( $langId ? (int) $langId : CurrentLang()['lang']['id'] );
	$catId 	= ( $catId 	? (int) $catId : 0 );
	$subId 	= ( $subId 	? (int) $subId : 0 );
	$order	= ( ( $type == 'latest' ) ? 'p.added_time DESC' : ( ( $type == 'top' ) ? 'p.views DESC' : ( ( $type == 'title' ) ? 'p.title ASC' : 'p.id_post DESC' ) ) );
	
	$cacheFile = CacheFileName( 'custom-posts_' . $type, null, $langId, $blogId, null, $items );

	//Get data from cache
	if ( $cache && ValidOtherCache( $cacheFile ) )
	{
		$data = ReadCache( $cacheFile );
	}
		
	//Get the data and save it to the cache, if needed...
	else
	{
		$db = db();
		
		$query = "SELECT p.id_post, p.id_blog, p.id_site, p.id_parent, p.added_time, p.id_member, p.id_lang, p.title, COALESCE(p.description, SUBSTRING(p.post, 1, 180)) AS description, p.disable_comments, p.sef, p.views, p.num_comments, p.post_type, p.post_status, p.cover_img, p.post, p.content, c.name AS cat_name, c.sef AS cat_sef, c.id AS cat_id, c.cat_color, su.name AS sub_name, su.sef AS sub_sef, su.id AS sub_id, su.cat_color AS sub_color, u.real_name as real_name, u.user_name, u.image_data as user_img, b.sef AS blog_sef, b.name AS blog_name, b.trans_data AS blog_trans, b.groups_data AS blog_groups, u.trans_data, la.code AS ls, la.title AS lt, la.locale AS ll, la.flagicon, d.value1 as extra_val, d.ext_id, d.external_url as ext_url, d.last_time_commented as lstc, ld.id as dlid, ld.code as dlc, ld.title as dlt, ld.locale as dll, lc.date_format, lc.time_format, (SELECT COUNT(id) FROM `" . DB_PREFIX . "comments` as cm WHERE cm.id_post = p.id_post AND cm.status = 'approved') as numcomm
		FROM `" . DB_PREFIX . POSTS . "` AS p
		INNER JOIN `" . DB_PREFIX . "languages`  as la ON la.id = p.id_lang
		INNER JOIN `" . DB_PREFIX . "languages_config` as lc ON lc.id_lang = p.id_lang
		INNER JOIN `" . DB_PREFIX . USERS . "`   as u ON u.id_member = p.id_member
		INNER JOIN `" . DB_PREFIX . "languages`  as ld ON ld.id_site = p.id_site AND ld.is_default = 1
		LEFT JOIN  `" . DB_PREFIX . "categories` as c ON c.id = p.id_category
		LEFT JOIN  `" . DB_PREFIX . "categories` as su ON su.id = p.id_sub_category
		LEFT JOIN  `" . DB_PREFIX . "blogs` 	 as b ON b.id_blog = p.id_blog
		LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post
		WHERE 1=1 AND (p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $langId . ")" . ( !empty( $blogId ) ? " AND (p.id_blog = " . $blogId . ")" : "" ) . " AND (p.post_type = 'post') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL) AND (d.hide_on_home = 0 OR d.hide_on_home IS NULL)" . ( !empty( $blogId ) ? " AND (b.disabled = 0 OR b.disabled IS NULL)" : "" ) . ( !empty( $catId ) ? " AND (p.id_category = " . $catId . ")" : "" ) . ( !empty( $subId ) ? " AND (p.id_sub_category = " . $subId . ")" : "" ) . "
		ORDER BY " . $order . " LIMIT " . $items;

		$tmp = $db->from( null, $query )->all();
		
		if ( !$tmp )
		{
			return null;
		}
		
		$s = GetSettingsData( SITE_ID );
		
		if ( !$s )
		{
			return null;
		}
		
		$data = array();
		
		foreach ( $tmp as $p )
		{				
			//Get the raw data only
			$p = array_merge( $p, $s );
			
			$data[] = BuildPostVars( $p );
		}
		
		if ( $cache )
		{
			WriteOtherCacheFile( $data, $cacheFile );
		}
	}
	
	return BuildPosts( $data );
}

#####################################################
#
# Get Feed Content function
#
#####################################################
function GetFeed( $url, $limit = 10 )
{
	require_once ( TOOLS_ROOT . 'simplepie' . DS . 'autoloader.php' );
	
	$feed = new SimplePie();
	
	$feed->set_feed_url( $url );
	
	$feed->set_cache_location( CACHE_ROOT );
	
	$success = $feed->init();

	$start 	= ( ( $limit == 0 ) ? null : 0 );
	$limit 	= ( ( $limit == 0 ) ? null : $limit );
	
	if ( !$success )
		return null;

	$feed->handle_content_type();
	
	$arr = array();
	
	if ( $feed->get_items( $start, $limit ) )
	{
		foreach ( $feed->get_items() as $item )
		{
			//$enclosure = $item->get_enclosure();

			$arr[] = array(
				'dateUnix' 	=> strtotime( $item->get_date( 'd-m-Y H:i:s' ) ),
				'date' 		=> $item->get_date( 'd-m-Y H:i:s' ),
				'url' 		=> $item->get_permalink(),
				'title' 	=> $item->get_title(),
				'descr' 	=> $item->get_description(),
				'content' 	=> $item->get_content(),
			);
		}
	}

	return $arr;
}

#####################################################
#
# Get the code of a language function
#
#####################################################
function GetLandCodeById( $id )
{
	$id = (int) $id;
	
	$code = ( isset( Settings::AllLangsById()[$id] ) ? Settings::AllLangsById()[$id]['lang']['code'] : Settings::LangData()['lang']['code'] );
	
	return $code;
}

#####################################################
#
# Get Lamg Key By Id function
#
#####################################################
function GetLangKey( $id  )
{
	$db = db();
	
	//Query: language
	$q = $db->from( null, "
	SELECT code
	FROM `" . DB_PREFIX . "languages`
	WHERE (id = " . $id . ")"
	)->single();
	
	return ( $q ? $q['code'] : GetLandCodeById( $id ) );
}

#####################################################
#
# Markdown Parse Content function
#
#####################################################
function Parsedown( $text, $safe = false ) 
{
	require_once (TOOLS_ROOT . 'parsedown' . DS . 'Parsedown.php');
	require_once (TOOLS_ROOT . 'parsedown' . DS . 'parsedown-extra' . DS . 'ParsedownExtra.php');
	
	//$Parsedown = new Parsedown();
	$Parsedown = new ParsedownExtra();
	$Parsedown->setUrlsLinked( false );
	$Parsedown->setBreaksEnabled( false );
	$Parsedown->setSafeMode( $safe );

	return $Parsedown->text( $text );
}

#####################################################
#
# Return Post Filter function
#
#####################################################
function PostFilter( $langCode = null )
{
	$CurrentLang = CurrentLang();
	$filter = Settings::Get()['posts_filter'];
	$defaultLang = Settings::LangData()['lang']['code'];
	$langCode = ( $langCode ? $langCode : $CurrentLang['lang']['code'] );
	
	if ( MULTILANG )//&& ( $defaultLang != $langCode ) )
	{
		$trans = Settings::Trans();

		if ( !empty( $trans ) && isset( $trans[$langCode] ) && !empty( $trans[$langCode]['post_filter_trans'] ) )
			$filter = '/' . $trans[$langCode]['post_filter_trans'] . '/';
	}

	return rawurldecode( $filter );
}

#####################################################
#
# Check and Correct a post's URL function
#
#####################################################
function CheckPostUrl( $data )
{
	if ( empty( $data ) )
		return;
	
	if ( Router::GetVariable( 'isBlog' ) )
	{
		global $Blog;
		
		$blogId = ( !empty( $Blog ) ? $Blog['id_blog'] : null );
	}
	else
		$blogId = null;

	$redirect = false;

	$CurrentLang = CurrentLang();

	$lang = Settings::LangData();

	$langId = $CurrentLang['lang']['id'];
	
	$postType = ( isset( $data['postType'] ) ? $data['postType'] : ( isset( $data['post_type'] ) ? $data['post_type'] : null ) );
	$postUrl = $data['postURL'];

	//We can't continue without these values
	if ( empty( $postType ) || empty( $postUrl ) )
		return;

	//If the post is from another language, send it there
	if ( MULTILANG && ( $data['languageId'] != $langId ) )
	{
		$redirect = true;
	}
	
	//If the URL gives a blog, but the site has multiblog disabled, send it to the parent site
	//This shouldn't be true, but better safe than sorry
	if ( !MULTIBLOG && Router::GetVariable( 'isBlog' ) )
	{
		$redirect = true;
	}
		
	//If the post belongs to a blog but we try to access it from somewhere else, send 'em there
	if ( MULTIBLOG && !empty( $data['blogID'] ) && !empty( $blogId ) && ( $data['blogID'] != $blogId ) )
	{
		$redirect = true;
	}
	
	//If the post belongs to a blog, but the URL doesn't provide one, correct it now
	if ( MULTIBLOG && !empty( $data['blogSef'] ) && !empty( $data['blogID'] ) && !Router::GetVariable( 'isBlog' ) )
	{
		$redirect = true;
	}
	
	//What if we try to load a page inside a blog but this page doesn't belong there?
	if ( MULTIBLOG && empty( $data['blogID'] ) && Router::GetVariable( 'isBlog' ) )
	{
		$redirect = true;
	}

	//If the post is being called as a page, correct it now
	if ( ( Settings::Get()['posts_filter'] !== '/' ) && ( $postType == 'post' ) && ( Router::GetVariable( 'postStatus' ) == 'page' ) )
	{
		$redirect = true;
	}

	//... the same goes for the pages, but not for the static page we set as homepage
	if ( ( Settings::Get()['posts_filter'] !== '/' ) && ( $postType == 'page' ) && ( Router::GetVariable( 'postStatus' ) == 'post' ) )
	{
		if ( !StaticHomePage( false, $data['id'] ) || !StaticHomePage( false, $data['parentId'] ) )
		{
			$redirect = true;
		}
	}
	
	if ( $redirect )
	{
		@header("Location: " . $postUrl, true, 301 );
		@exit;
	}
	
	return;
}

#####################################################
#
# Check if the post has AMP function disabled
#
#####################################################
function CheckPostAmpStatus()
{
	global $Post;

	//Don't do anything if we are not in amp mode
	if ( !Router::GetVariable( 'isAmp' ) || !$Post )
		return;
	
	if ( !is_object( $Post ) && is_array( $Post ) )
	{
		if ( !empty( Settings::Amp()['content_types'] ) )
		{
			$types = Settings::Amp()['content_types'];
			
			if ( ( $Post['postType'] == 'page' ) && in_array( 'pages', $types ) )
				return;
			
			elseif ( ( $Post['postType'] == 'post' ) && in_array( 'posts', $types ) )
				return;
		}
		
		$url = $Post['postURL'];
	}
	
	elseif ( is_object( $Post ) )
	{
		if ( $Post->HasAmp() )
			return;
		
		$url = $Post->PostUrl();
	}
	
	@header("Location: " . $url, true, 301 );
	@exit;
}

#####################################################
#
# Get the post's schemas function
#
#####################################################
function GetPostSchemas( $post )
{
	if ( empty( $post ) || !is_array( $post ) )
		return '';

	$where = "(";
	
	if ( $post['post_type'] == 'page' )
		$where .= "enable_on = 'all-pages'" . ( empty( $post['id_blog'] ) ? " OR enable_on = 'orphan-pages'" : '' );
	
	else
		$where .= "enable_on = 'all-posts'" . ( empty( $post['id_blog'] ) ? " OR enable_on = 'orphan-posts'" : '' );

	$where .= ")";

	$where .= " AND NOT (";
	
	if ( $post['post_type'] == 'page' )
		$where .= "exclude_from = 'all-pages'" . ( empty( $post['id_blog'] ) ? " OR exclude_from = 'orphan-pages'" : '' );
	
	else
		$where .= "exclude_from = 'all-posts'" . ( empty( $post['id_blog'] ) ? " OR exclude_from = 'orphan-posts'" : '' );
	
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

function ReturnSchemaByType( $post, $schema )
{
	global $L;
	
	include ( ARRAYS_ROOT . 'seo-arrays.php');
	
	$scmData = Json( $schema['data'] );
	$scmXtraData = $scmData['custom-data'];
		
	$scmData = $scmData['data'];

	$schemaArray = array(
				'@context' => 'https://schema.org',
				'@type' => ( 
							isset( $schemaTypesArray[$scmData['article-type']] ) ? $schemaTypesArray[$scmData['article-type']]['property'] : 
							( isset( $schemaArticleTypesArray[$scmData['article-type']] ) ? $schemaArticleTypesArray[$scmData['article-type']]['property'] : '' )
				)
	);
	
	//TODO: Build a function to automate this
	
	//Course Schema
	if ( $schema['type'] == 'course' )
	{
		$schemaArray['name'] = GetSchemaValue( 'title', $post, $schema );
		$schemaArray['description'] = GetSchemaValue( 'description', $post, $schema );
		$schemaArray['courseCode'] = GetSchemaValue( 'course-code', $post, $schema );
			
		if ( !empty( $scmData['rating'] ) )
		{
			$schemaArray['aggregateRating'] = array(
											'@type' => 'AggregateRating',
											'ratingValue' => GetSchemaValue( 'rating-value', $post, $schema ),
											'reviewCount' => GetSchemaValue( 'review-count', $post, $schema )
			);
		}
			
		if ( !empty( $scmData['course-provider'] ) )
		{
			$schemaArray['provider'] = array(
				'@type' => 'AggregateRating',
				'name' => GetSchemaValue( 'rating-value', $post, $schema ),
				'url' => GetSchemaValue( 'review-count', $post, $schema )
			);
			
		}
	}
	
	return $schemaArray;
}

/*
<script type="application/ld+json">{"@context":"https://schema.org","@type":"Course","name":"Ο ΚΥΡ ΑΝΤΩΝΗΣ - ΚΑΖΑΝΤΖΙΔΗΣ ΜΑΡΙΝΕΛΛΑ - ΧΑΤΖΙΔΑΚΗΣ LYRICS","courseCode":"derssderss","description":"Ο ΚΥΡ ΑΝΤΩΝΗΣ - ΚΑΖΑΝΤΖΙΔΗΣ ΜΑΡΙΝΕΛΛΑ - ΧΑΤΖΙΔΑΚΗΣ LYRICS","hasCourseInstance":[{"@type":"CourseInstance","name":"Ο ΚΥΡ ΑΝΤΩΝΗΣ - ΚΑΖΑΝΤΖΙΔΗΣ ΜΑΡΙΝΕΛΛΑ - ΧΑΤΖΙΔΑΚΗΣ LYRICS","description":"Ο ΚΥΡ ΑΝΤΩΝΗΣ - ΚΑΖΑΝΤΖΙΔΗΣ ΜΑΡΙΝΕΛΛΑ - ΧΑΤΖΙΔΑΚΗΣ LYRICS","courseMode":"Ο ΚΥΡ ΑΝΤΩΝΗΣ - ΚΑΖΑΝΤΖΙΔΗΣ ΜΑΡΙΝΕΛΛΑ - ΧΑΤΖΙΔΑΚΗΣ LYRICS","eventStatus":"EventScheduled","eventAttendanceMode":"OfflineEventAttendanceMode","startDate":"2021-06-15T13:56:24+0000","endDate":"2021-11-07T18:10:01","location":{"@type":"Place","name":"Ο ΚΥΡ ΑΝΤΩΝΗΣ - ΚΑΖΑΝΤΖΙΔΗΣ ΜΑΡΙΝΕΛΛΑ - ΧΑΤΖΙΔΑΚΗΣ LYRICS","address":"Ο ΚΥΡ ΑΝΤΩΝΗΣ - ΚΑΖΑΝΤΖΙΔΗΣ ΜΑΡΙΝΕΛΛΑ - ΧΑΤΖΙΔΑΚΗΣ LYRICS"},"organizer":{"@type":"Organization","name":"Ο ΚΥΡ ΑΝΤΩΝΗΣ - ΚΑΖΑΝΤΖΙΔΗΣ ΜΑΡΙΝΕΛΛΑ - ΧΑΤΖΙΔΑΚΗΣ LYRICS","url":"Ο ΚΥΡ ΑΝΤΩΝΗΣ - ΚΑΖΑΝΤΖΙΔΗΣ ΜΑΡΙΝΕΛΛΑ - ΧΑΤΖΙΔΑΚΗΣ LYRICS"},"offers":{"@type":"Offer","price":"100","priceCurrency":"BTC","url":"https://raspberrypi/wordpress/2021/06/%ce%bf-%ce%ba%cf%85%cf%81-%ce%b1%ce%bd%cf%84%cf%89%ce%bd%ce%b7%cf%83-%ce%ba%ce%b1%ce%b6%ce%b1%ce%bd%cf%84%ce%b6%ce%b9%ce%b4%ce%b7%cf%83-%ce%bc%ce%b1%cf%81%ce%b9%ce%bd%ce%b5%ce%bb%ce%bb%ce%b1/","validFrom":"2021-06-15T13:56:24","availability":"OutOfStock"},"performer":{"@type":"Person","name":"ddd"}}],"provider":{"@type":"Organization","name":"Ο ΚΥΡ ΑΝΤΩΝΗΣ - ΚΑΖΑΝΤΖΙΔΗΣ ΜΑΡΙΝΕΛΛΑ - ΧΑΤΖΙΔΑΚΗΣ LYRICS","sameAs":"https://raspberrypi/wordpress/2021/06/%ce%bf-%ce%ba%cf%85%cf%81-%ce%b1%ce%bd%cf%84%cf%89%ce%bd%ce%b7%cf%83-%ce%ba%ce%b1%ce%b6%ce%b1%ce%bd%cf%84%ce%b6%ce%b9%ce%b4%ce%b7%cf%83-%ce%bc%ce%b1%cf%81%ce%b9%ce%bd%ce%b5%ce%bb%ce%bb%ce%b1/"},"aggregateRating":{"@type":"AggregateRating","ratingValue":"2.4","reviewCount":"10"}}</script><!-- / Schema optimized by Schema Pro --><!-- Schema optimized by Schema Pro -->
*/

#####################################################
#
# Get the value of a schema field function
#
#####################################################
function GetSchemaValue( $value, $post, $schema )
{
	return;
	global $L, $Lang, $router;

	include ( ARRAYS_ROOT . 'seo-arrays.php');
	
	if ( Settings::IsTrue( 'blank_icon' ) )
		$siteImage = 'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=';
		
	elseif ( isset( Settings::Get()['siteImage'] ) && !empty( Settings::Get()['siteImage'] ) )
		$siteImage = Settings::Get()['siteImage']['default']['url'];
	
	else
		$siteImage = null;
	
	//Set the default data
	$postData = array(
				'site-title' => array( 'value' => $Lang['data']['site_name'] ),
				'site-slogan' => array( 'value' => $Lang['data']['site_slogan'] ),
				'site-url' => array( 'value' => $router->Url() ),
				'site-logo' => array( 'value' => $siteImage ),
				'post-title' => array( 'value' => htmlspecialchars( $post['title'] ) ),
				'post-content' => array( 'value' => htmlspecialchars( $post['post'] ) ),
				'post-description' => array( 'value' => htmlspecialchars( $post['description'] ) ),
				'post-url' => array( 'value' => BuildPostUrl( $post, SITE_URL, false, $Lang['lang']['code'] ) ),
				'author-name' => array( 'value' => htmlspecialchars( $post['real_name'] ) ),
				'post-featured-image' => array( 'value' => ( !empty( $post['external_url'] ) ? $post['external_url'] : FolderUrlByDate( $post['img_added'] ) . $post['img'] ) ),
				'post-author-image' => array( 'value' => null ),
				'publish-date' => array( 'value' => postDate ( $post['added_time'], false, $Lang['data']['date_format'] ) ),
				'last-modified-date' => array( 'value' => postDate ( $post['edited_time'], false, $Lang['data']['date_format'] ) ),	
	);
	
	$scmData = Json( $schema['data'] );
	$scmXtraData = $scmData['custom-data'];
		
	$scmData = $scmData['data'];
	
	$val = $scmData[$value];

	if ( !isset( $val ) || empty( $val ) )
		return null;
	
	if ( isset( $postData[$val] ) )
		return $postData[$val]['value'];
	
	if ( ( ( $val == 'custom-text' ) || ( $val == 'custom-number' ) || ( $val == 'custom-date' ) ) && isset( $scmXtraData[$val] ) )
		return $scmXtraData[$val];
	
	return null;
}

#####################################################
#
# Get User Profile Image function
#
#####################################################
function GetUserImage( $userId, $single = false )
{
	$db = db();
	
	// Get the post Keys
	$img = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "images`
	WHERE (id_member = " . (int) $userId . ") AND (img_type = 'user')"
	)->single();

	if ( empty( $img ) )
	{
		return array(
						'id' => 0,
						'filename' => '',
						'title' => '',
						'width' => 0,
						'height' => 0,
						'size' => 0,
						'imageUrl' => 'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII='
			);
	}
	
	$data[] = array(
					'id' => $img['id_image'],
					'filename' => stripslashes( $img['filename'] ),
					'title' => stripslashes( $img['title'] ),
					'width' => $img['width'],
					'height' => $img['height'],
					'size' => $img['size'],
					'imageUrl' => FolderUrlByDate( $img['added_time'] ) . stripslashes( $img['filename'] )
	);
	
	if ( !$single )
	{
		$imgs = $db->from( 
		null, 
		"SELECT id_image, filename, title, width, height, size
		FROM `" . DB_PREFIX . "images`
		WHERE (id_parent = " . $img['id_image'] . ") AND (img_type = 'user')
		ORDER BY width ASC"
		)->all();
		
		if ( $imgs )
		{
			$addedTime = $img['added_time'];
			
			foreach( $imgs as $img )
			{
				$data[] = array(
								'id' => $img['id_image'],
								'filename' => stripslashes( $img['filename'] ),
								'title' => stripslashes( $img['title'] ),
								'width' => $img['width'],
								'height' => $img['height'],
								'size' => $img['size'],
								'imageUrl' => FolderUrlByDate( $addedTime ) . stripslashes( $img['filename'] )
					);
			}
		}
	}
	
	return $data;
}

#####################################################
#
# Get HTML content and convert it to Blocks Data function
#
#####################################################
function ConvertToBlocks( $p )
{
	$post = array();
	
	$p = StripContent( $p );
	$p = wpautop( $p );

	preg_match_all('/\<p.*>(.*)<\/p>/iU', $p, $matches, PREG_SET_ORDER );
	
	if ( !empty( $matches ) )
	{
		foreach ( $matches as $_p )
		{
			$_p = $_p['1'];
			
			preg_match( '/<iframe.*>/iU', $_p, $t );
			preg_match( '/<img.*>/iU', $_p, $t1 );
			preg_match( '/\[image.*\]/iU', $_p, $t2 );
			preg_match( '/\[video.*\]/iU', $_p, $t3 );
			preg_match( '/\[caption.*\].*\[\/caption\]/iU', $_p, $t4 );
			
			if ( !empty( $t ) || !empty( $t1 ) || !empty( $t2 ) || !empty( $t3 ) || !empty( $t4 ) )
				continue;
			
			$post[] = array(
					'id' => GenerateRandomKey( 10 ),
					'type' => 'paragraph',
					'data' => array(
						'text' => $_p
					)
			
			);
		}
		
	}
	
	preg_match_all('/\<code.*>(.*)<\/code>/iU', $p, $matches, PREG_SET_ORDER );
	
	if ( !empty( $matches ) )
	{
		foreach ( $matches as $_p )
		{
			$_p = $_p['1'];
			
			$post[] = array(
					'id' => GenerateRandomKey( 10 ),
					'type' => 'code',
					'data' => array(
						'code' => $_p
					)
			
			);
			
		}
		
	}
	
	preg_match_all('/\<h(.*)>(.*)<\/h.*>/iU', $p, $matches, PREG_SET_ORDER );

	if ( !empty( $matches ) )
	{
		foreach ( $matches as $_p )
		{			
			$post[] = array(
					'id' => GenerateRandomKey( 10 ),
					'type' => 'header',
					'data' => array(
						'text' => $_p['2'],
						'level' => $_p['1']
					)
			
			);
		}
		
	}

	preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/iU', $p, $photz );
	
	if ( !empty( $photz ) )
	{
		foreach ( $photz['1'] as $im )
		{
			$post[] = array(
					'id' => GenerateRandomKey( 10 ),
					'type' => 'image',
					'data' => array(
						'url' => $im,
						'width' => 980,
						'caption' => '',
						'imageId' => '',
						'align' => 'center'
					)
			
			);
		}
	}
	
	//preg_match_all('/\[caption.+width="(.*)".*\]<img.+src=[\'"]([^\'"]+)[\'"].*>(.*)\[\/caption\]/iU', $p, $matches, PREG_SET_ORDER );
	
	preg_match_all( '/<iframe.+src=[\'"]([^\'"]+)[\'"].*>/iU', $p, $matches, PREG_SET_ORDER );
	
	if ( !empty( $matches ) )
	{
		foreach ( $matches as $m )
		{
			$post[] = array(
					'id' => GenerateRandomKey( 10 ),
					'type' => 'embed',
					'data' => array(
						'service' => '',
						'source' => $m['1'],
						'embed' => $m['1'],
						'height' => 480,
						'width' => 680,
						'caption' => ''
					)
			
			);
		}
	}
	
	//print_r($post );exit;

	return $post;
}

#####################################################
#
# Get Blocks Data and Convert it to HTML function
#
#####################################################
function GetBlocksHtmlData( $postId, $post = null )
{
	$data = GetBlocksData( $postId );
	
	if ( !$data )
		return null;
	
	$html = '';
	//print_r($data);
	foreach( $data as $block )
	{
		if ( !isset( $block['type'] ) || empty( $block['type'] ) )
			continue;
		
		$html .= ConvBlockData( $block, $post ) . PHP_EOL;
	}

	return $html;
}

#####################################################
#
# Convert Single Block Data function
#
#####################################################
function ConvBlockData( $block, $post = null )
{
	$themeValues = ( !empty( ThemeValue( 'theme-image' ) ) ? ThemeValue( 'theme-image' ) : null );
	$themeValues = ( isset( $themeValues['0'] ) ? $themeValues['0'] : $themeValues );
	
	$hasLazy	= ( ( !empty( $themeValues ) && !empty( $themeValues['has_lazy_mode'] ) ) ? true : false );
	$lazy		= Settings::IsTrue( 'enable_lazyloader' );
	
	$amp = Router::GetVariable( 'isAmp' );

	$html = '';
	
	if (  $block['type'] == 'markdown' )
	{
		$text = Parsedown( StripContent( $block['data']['text'] ) );
		$text = preg_replace( '%<p(.*?)>|</p>%s', '' , $text );
		
		$html .= '<p class="tk-block">' . $text . '</p>';
	}
	
	if (  $block['type'] == 'paragraph' )
	{
		$html .= '<p class="tk-block">' . StripContent( $block['data']['text'] ) . '</p>';
	}
		
	if (  $block['type'] == 'header' )
	{
		$num = ( ( !empty( $block['data']['level'] ) && is_numeric( $block['data']['level'] ) ) ? (int) $block['data']['level'] : 1 );
			
		$html .= '<h' . $num . ' class="tk-block">' . StripContent( $block['data']['text'] ) . '</h' . $num . '>';
	}
	
	if (  $block['type'] == 'image' )
	{
		$srcset 	= $_data = null;
		$width 		= ( is_numeric( $block['data']['width'] ) ? $block['data']['width'] : null );
		$height 	= 600;
		$align 		= ( !empty( $block['data']['align'] ) ? $block['data']['align'] : 'none' );
		$imageId 	= ( ( !empty( $block['data']['imageId'] ) && is_numeric( $block['data']['imageId'] ) ) ? $block['data']['imageId'] : null );
		$id 		= $block['id'];
		
		if ( $imageId && !empty( $post ) )
		{
			$imgData = GetFullImage( $imageId, $post['ls'], $post['id_site'] );

			if ( !empty( $imgData ) )
			{
				if ( is_numeric( $width ) && isset( $imgData['sizes'][$width] ) )
				{
					$_data = $imgData['sizes'][$width];
				}
				else
				{
					$_data = $imgData['sizes']['default'];
				}
				
				$height = $_data['imageHeight'];

				if ( count( $imgData['sizes'] ) > 2 )
				{
					$t = $i = 0;
						
					//We need the correct number of sizes
					foreach( $imgData['sizes'] as $_size => $size )
					{
						if ( $_size == 'default' )
							continue;
							
						if ( is_numeric( $width ) && ( $size['imageWidth'] == $width ) )
							continue;
							
						$t++;
					}
						
					if ( $t > 1 )
					{
						foreach( $imgData['sizes'] as $_size => $size )
						{
							if ( $_size == 'default' )
								continue;
								
							if ( is_numeric( $width ) && ( $size['imageWidth'] == $width ) )
								continue;

							$srcset .= $size['imageUrl'] . ' ' . $size['imageWidth'] . 'w';
								
							$i++;
								
							if ( $i < $t )
								$srcset .= ', ';
						}
					}
				}
				
				$width = $_data['imageWidth'];
			}
		}
		
		$width = ( is_numeric( $width ) ? $width : 600 );

		if ( !empty( $block['data']['caption'] ) )
		{
			$html .= '<figure class="tk-block';
			
			if ( !empty( $themeValues ) && isset( $themeValues['figure_class'] ) )
			{
				$html .= ' ' . sprintf( $themeValues['figure_class'], $align );
			}
			
			$html .= '"';
			
			if ( !empty( $themeValues ) && isset( $themeValues['figure_id'] ) )
			{
				$html .= ' id="' . sprintf( $themeValues['figure_id'], $imageId ) . '"';
			}
	
			$html .= '>' . PHP_EOL;
		}
		
		if ( !empty( $_data ) && !empty( $_data['imageUrl'] ) )
		{
			$url = $_data['imageUrl'];
		}
		else
		{
			$url = ( isset( $block['data']['file']['url'] ) ? $block['data']['file']['url'] : $block['data']['url'] );
		}
		
		if ( $amp )
		{
			$html .= '<amp-img width="' . $width . '" height="' . $height . '"' . PHP_EOL;
			$html .= 'src="' . $url . '"' . PHP_EOL;
			$html .= 'layout="responsive"' . PHP_EOL;
			
			if ( $srcset )
			{
				$html .= 'srcset="' . $srcset . '"' . PHP_EOL;
			}
			
			$html .= 'alt="' . ( !empty( $imgData ) ? $imgData['imageAlt'] : '' ) . '"></amp-img>';
		}
		else
		{
			$html .= '<img width="' . $width . '" height="' . $height . '" ';

			$html .= 'src="' . $url . '"';

			$html .= ' alt="' . ( !empty( $imgData ) ? htmlspecialchars( $imgData['imageAlt'], ENT_QUOTES ) : '' ) . '"';

			if ( $lazy || $hasLazy )
			{
				$html .= ' loading="lazy"';
			}

			if ( !empty( $themeValues ) && isset( $themeValues['image_class'] ) )
			{
				$html .= ' class="' . sprintf( $themeValues['image_class'], $align, $imageId ) . '"';
			}

			if ( !empty( $imgData ) && !empty( $imgData['imageTitle'] ) )
			{
				$html .= ' title="' . htmlspecialchars( $imgData['imageTitle'], ENT_QUOTES ) . '"';
			}

			if ( !empty( $srcset ) )
			{
				$html .= ' srcset="' . $srcset . '"';

				//Just a default value here, maybe an auto calculation later
				$html .= ' sizes="(min-width: 60rem) 80vw, (min-width: 40rem) 90vw, 100vw"';
			}

			$html .= '>' . PHP_EOL;
		}

		if ( !empty( $block['data']['caption'] ) )
		{
			$html .= PHP_EOL;
			
			$html .= '<figcaption';
			
			if ( !empty( $themeValues ) && isset( $themeValues['caption_class'] ) )
			{
				$html .= ' class="' . sprintf( $themeValues['caption_class'], $align ) . '"';
			}

			if ( !empty( $themeValues ) && isset( $themeValues['caption_id'] ) )
			{
				$html .= ' id="' . sprintf( $themeValues['caption_id'], $imageId ) . '"';
			}
			
			$html .= '>' . StripContent( $block['data']['caption'] ) . '</figcaption>
			</figure>';
		}
		
		if ( !empty( $themeValues ) && isset( $themeValues['image_wrap'] ) )
		{
			$html = sprintf( $themeValues['image_wrap'], $html );
		}
	}
	
	if ( $block['type'] == 'twoColumns' )
	{
		$html .= '<section class="cards-simple">
			<div class="row tk-block" style="display: flex;">' . PHP_EOL;
		
		if ( !empty( $block['data']['itemContent'] ) )
		{
			foreach ( $block['data']['itemContent'] as $content )
			{
				foreach ( $content['blocks'] as $block )
				{
					$html .= '<div class="column" style="float: left;width: 50%;flex: 50%;">' . ConvBlockData( $block, $post ) . '</div>' . PHP_EOL;
				}
			}
		}
		
		$html .= '</div>
		</section>';
	}

	if (  $block['type'] == 'checklist' )
	{
		$html .= '<ul class="tk-block" style="list-style: none;margin-left: 0;padding-left: 0;">' . PHP_EOL;
		
		if ( !empty( $block['data']['items'] ) )
		{
			foreach( $block['data']['items'] as $row )
			{
				$html .= '<li>' . PHP_EOL;
				
				$html .= '<input type="checkbox" name="' . $row['text'] . '" id="' . $row['text'] . '"' . ( IsTrue( $row['checked'] ) ? ' checked' : '' ) . ' onclick="return false;"><label for="' . $row['text'] . '">' . $row['text'] . '</label><span class="input"><span class="check"></span></span>';

				$html .= '</li>' . PHP_EOL;
			}
		}
		
		$html .= '</ul>' . PHP_EOL;
	}

	if (  $block['type'] == 'delimiter' )
	{
		$html .= '<hr class="tk-block" style="border: 1px solid white;width: 10%;text-align: center;position: relative;margin: 30px auto;border-bottom: 3px dashed black;" />';
	}
		
	if (  $block['type'] == 'raw' )
	{
		$html .= StripContent( $block['data']['html'] );
	}
		
	if (  $block['type'] == 'code' )
	{
		$html .= '<pre class="tk-block"><code>' . htmlentities( StripContent( $block['data']['code'] ) ) . '</code></pre>';
	}
		
	if (  $block['type'] == 'warning' )
	{
		$html .= '<div class="tk-block alert alert-warning" role="alert">' . PHP_EOL;
		$html .= '<h4 class="alert-heading">' . StripContent( $block['data']['title'] ) . '</h4>' . PHP_EOL;
		$html .= '<p>' . StripContent( $block['data']['message'] ) . '</p>' . PHP_EOL;
		$html .= '</div>';
	}
		
	if (  $block['type'] == 'table' )
	{
		$html .= '<table class="tk-block table">' . PHP_EOL;
			
		if ( !empty( $block['data']['content'] ) )
		{
			$first = array_slice( $block['data']['content'], 0, 1 );
			$other = array_slice( $block['data']['content'], 1, count( $block['data']['content'] ) );
				
			$html .= '<thead>
				<tr>' . PHP_EOL;
				
			foreach ( $first['0'] as $row )
			{
				$html .= '<th scope="col">' . $row . '</th>' . PHP_EOL;
			}
				
			$html .= '</tr>
				</thead>' . PHP_EOL;

			if ( !empty( $other ) )
			{
				$html .= '<tbody>' . PHP_EOL;
					
				foreach ( $other as $row )
				{
					$html .= '<tr>' . PHP_EOL;
						
					foreach ( $row as $r )
					{
						$html .= '<td>' . $r . '</td>' . PHP_EOL;
					}
						
					$html .= '</tr>' . PHP_EOL;
				}
					
				$html .= '</tbody>' . PHP_EOL;
			}
		}

		$html .= '</table>';
	}
		
	if (  $block['type'] == 'quote' )
	{
		$align = $block['data']['alignment'];
		
		$id = $block['id'];
		
		$html .= '<figure class="tk-block align' . $block['data']['alignment'] . '">' . PHP_EOL;
		$html .= '<blockquote>' . StripContent( $block['data']['text'] ) . '</blockquote>' . PHP_EOL;
		$html .= '<figcaption>&mdash;' . StripContent( $block['data']['caption'] ) . '</figcaption>' . PHP_EOL;
		$html .= '</figure>';
	}
	
	//Embed
	if (  $block['type'] == 'embed' )
	{
		$align = 'center';
		
		$id = $block['id'];
		
		if ( $amp )
		{
			$html .= '<amp-iframe width="' . $block['data']['width'] . '" height="' . $block['data']['height'] . '"' . PHP_EOL;
			$html .= 'sandbox="allow-scripts allow-same-origin"' . PHP_EOL;
			$html .= 'layout="responsive"' . PHP_EOL;
			$html .= 'frameborder="0"' . PHP_EOL;
			$html .= 'src="' . $block['data']['source'] . '">' . PHP_EOL;
			$html .= '</amp-iframe>';
		}
		else
		{
			if ( !empty( $block['data']['caption'] ) ) 
			{
				$html .= '<figure class="tk-block';
					
				if ( !empty( $themeValues ) && isset( $themeValues['figure_class'] ) )
				{
					$html .= ' ' . sprintf( $themeValues['figure_class'], $align );
				}
					
				$html .= '"';
					
				if ( !empty( $themeValues ) && isset( $themeValues['figure_id'] ) )
				{
					$html .= ' id="' . sprintf( $themeValues['figure_id'], $id ) . '"';
				}

				$html .= '>' . PHP_EOL;
			}

			$html .= '<iframe' . ( empty( $block['data']['caption'] ) ? ' class="tk-block"' : '' ) . ' width="' . $block['data']['width'] . '" height="' . $block['data']['height'] . '" src="' . $block['data']['source'] . '" frameborder="0" allowfullscreen></iframe>' . PHP_EOL;

			if ( !empty( $block['data']['caption'] ) ) 
			{
				$html .= '<figcaption';
				
				if ( !empty( $themeValues ) && isset( $themeValues['caption_class'] ) )
				{
					$html .= ' class="' . sprintf( $themeValues['caption_class'], $align ) . '"';
				}

				if ( !empty( $themeValues ) && isset( $themeValues['caption_id'] ) )
				{
					$html .= ' id="' . sprintf( $themeValues['caption_id'], $id ) . '"';
				}
				
				$html .= '>' . StripContent( $block['data']['caption'] ) . '</figcaption>
				</figure>';
			}
		}
	}

	//List
	if (  $block['type'] == 'list' )
	{
		$type = ( ( $block['data']['style'] == 'unordered' ) ? 'ul' : 'ol' );
			
		$html .= '<' . $type . ' class="tk-block">' . PHP_EOL;
			
		if ( !empty( $block['data']['items'] ) )
		{
			foreach( $block['data']['items'] as $item )
			{
				$html .= '<li>' . $item . '</li>' . PHP_EOL;
			}
		}

		$html .= '</' . $type . '>';
	}

	return $html;
}

#####################################################
#
# Create Embed function
#
# Calls the embed class and returns the content
#
#####################################################
function CreateEmbed( $string, $amp = false, $isComment = false )
{
	require_once ( CLASSES_ROOT . 'Embed.php' );

	//Load the Embed class
	$Embed = new Embed( $amp );
	
	//Return the embed code
	return $Embed->parse( $string );
}

#####################################################
#
# Replace Special Chars form Content function
#
#####################################################
function ReplaceSpecialChars( $p )
{
	$arr = array( '\\' );
	$rep = array( '&bsol;' );
	
	return str_replace( $arr, $rep, $p );
}

#####################################################
#
#  Formats the img tags from Blogger
#
#####################################################
function BloggerClean( $content )
{
	//$settings = Json( Settings::Get()['embedder_data'] );
	
	$ampWidth = 600;

	$ampHeight = 400;

	//IMAGES With Caption
	$pat = '/<table.*>([\s]+)<tbody>([\s]+)<tr>([\s]+)<td style=\"text-align: center;\"><a.+href=[\'"]([^\'"]+)[\'"].*><img.+src=[\'"]([^\'"]+)[\'"].*><\/a><\/td>([\s]+)<\/tr>([\s]+)<tr>([\s]+)<td class=\"tr-caption\" style=\"text-align: center;\">(.*)<\/td>([\s]+)<\/tr>([\s]+)<\/tbody>([\s]+)<\/table>/';
			
	$embed  = '<figure>' . PHP_EOL;
	$embed .= '<amp-img on="tap:lightbox1"' . PHP_EOL;
	$embed .= 'role="button"' . PHP_EOL;
	$embed .= 'tabindex="0"' . PHP_EOL;
	$embed .= 'src="$4"' . PHP_EOL;
	$embed .= 'layout="responsive"' . PHP_EOL;
	$embed .= 'width="' . $ampWidth . '" height="' . $ampHeight . '"></amp-img>' . PHP_EOL;
	$embed .= '<figcaption>$9</figcaption>' . PHP_EOL;
	$embed .= '</figure>';

	$content = preg_replace($pat, $embed, $content );

	//IMAGES With no Caption
	$pat = '/<div class=\"separator\".*>(?:[\s]+)?<a.+href=[\'"]([^\'"]+)[\'"].*><img.*><\/a>(?:[\s]+)?<\/div>/';
			
	$embed = '<amp-img width="' . $ampWidth . '"' . PHP_EOL;
	$embed .= 'height="' . $ampHeight . '"' . PHP_EOL;
	$embed .= 'layout="responsive"' . PHP_EOL;
	$embed .= 'alt="AMP"' . PHP_EOL;
	$embed .= 'src="$1">' . PHP_EOL;
	$embed .= '</amp-img>';

	$content = preg_replace( $pat, $embed, $content );

	//VIDEOS
	$pat = '/(<div class=\"separator\" style=\"clear: both; text-align: center;\">)?<iframe.+data-thumbnail-src=[\'"]([^\'"]+)[\'"].+src=[\'"]([^\'"]+)[\'"].*><\/iframe>(<\/div>)?/';

	$embed = '<amp-iframe width="' . $ampWidth . '" height="' . $ampHeight . '"' . PHP_EOL;
	$embed .= 'sandbox="allow-scripts allow-same-origin"' . PHP_EOL;
	$embed .= 'layout="responsive"' . PHP_EOL;
	$embed .= 'frameborder="0"' . PHP_EOL;
	$embed .= 'src="$3">' . PHP_EOL;
	$embed .= '<amp-img layout="fill" src="$2" placeholder></amp-img>' . PHP_EOL;
	$embed .= '</amp-iframe>';

	$content = preg_replace( $pat, $embed, $content );
			
	return $content;
}

#####################################################
#
# Cleans the content for AMP by removing any script from the content
#
#####################################################
function FormatAmpImages( $content )
{
	$ampWidth = 600;

	$ampHeight = 400;
	
	$pat = '/(?:<p>)<img.+src=[\'"]([^\'"]+)[\'"].*>(?:<\/p>)?/i';

	$embed = '<amp-img width="' . $ampWidth . '"' . PHP_EOL;
	$embed .= 'height="' . $ampHeight . '"' . PHP_EOL;
	$embed .= 'layout="responsive"' . PHP_EOL;
	$embed .= 'alt=""' . PHP_EOL;
	$embed .= 'src="$1">' . PHP_EOL;
	$embed .= '</amp-img>';

	return preg_replace( $pat, $embed, $content );
}

#####################################################
#
# Cleans the content for AMP by removing any script from the content
#
#####################################################
function AmpClean( $content )
{
	$pat = '/<script.*><\/script>/';
	$content = preg_replace( $pat, '', $content );
	
	//Generic iframe(s)
	$content = preg_replace_callback
	(
		'/(?:<p>)<iframe.+src=[\'"]([^\'"]+)[\'"].*><\/iframe>(?:<\/p>)?/i',
		function ( $matches )
		{
			if ( empty( $matches['1'] ) )
				return null;
			
			$ampWidth = 600;

			$ampHeight = 400;
			
			$embed  = '<amp-iframe width="' . $ampWidth . '"' . PHP_EOL;
			$embed .= 'height="' . $ampHeight . '"' . PHP_EOL;
			$embed .= 'layout="responsive"' . PHP_EOL;
			$embed .= 'src="' . $matches['1'] . '">' . PHP_EOL;
			$embed .= '</amp-iframe>';
			
			return $embed;
		},
		$content
	);
	
	return $content;
}

#####################################################
#
# Replace Caption to Html function
#
# Added for WP Support
#
#####################################################
function replaceCaptionImage( $content, $amp = false )
{
	preg_match_all('/\[caption.+width="(.*)".*\]<img.+src=[\'"]([^\'"]+)[\'"].*>(.*)\[\/caption\]/iU', $content, $matches, PREG_SET_ORDER );

	if ( !empty ( $matches ) )
	{
		foreach ( $matches as $match ) 
		{
			if ( empty( $match ) )
				continue;
			
			$alt = ( isset( $match['3'] ) ? $match['3'] : '' );
			
			$width = $match['1'];
			
			$height = ( ( is_numeric( $width ) && ( ( $width / 2 ) > 246 ) ) ? ( $width / 2 ) : 246 );
			
			$align = 'center';

			if ( strpos( $match['2'], '?' ) !== false ) 
			{
				$img = explode('?', $match['2']);
				$img = $img['0'];
			} 

			else
				$img = $match['2'];

			if ( $amp )
			{
				$format  = '<figure>' . PHP_EOL;
				$format .= '<amp-img on="tap:lightbox1"' . PHP_EOL;
				$format .= 'role="button"' . PHP_EOL;
				$format .= 'tabindex="0"' . PHP_EOL;
				$format .= 'src="' . $img . '"' . PHP_EOL;
				$format .= 'layout="responsive"' . PHP_EOL;
				$format .= 'width="' . $width . '" height="' . $height . '"></amp-img>' . PHP_EOL;
				$format .= '<figcaption>' . htmlspecialchars_decode( $alt ) . '</figcaption>' . PHP_EOL;
				$format .= '</figure>';
			}
			else
			{
				$format = '<figure><img src="' . $img . '" alt="" width="' . $width . '" class="' . $align . ' size-full wp-image"><figcaption>' . htmlspecialchars_decode( $alt ) . '</figcaption></figure>';
			}

			$content = str_replace( $match['0'], $format, $content );
		}
	}

	return $content;
}

#####################################################
#
# Formats video duration function
#
#####################################################
function FormatDuration( $duration )
{
	if ( !empty( $duration ) && ( false !== strpos( $duration, 'M' ) ) )
	{
		$time = explode( 'M', $duration );

		$time_seconds = ( isset( $time['1'] ) ? str_replace( array( 'M', 'S' ), '', $time['1'] ) : '0' );

		$time_minutes = ( isset( $time['0'] ) ? str_replace( array( 'PT', 'M' ), '', $time['0'] ) : '00' );
			
		return array( 'min' => $time_minutes, 'sec' => $time_seconds, 'fixed' => $time_minutes . ':' . $time_seconds );
	}
		
	return null;
}

#####################################################
#
# Create Post Content function
#
# Add the class "lazyload" to images/iframes in post(s)
#####################################################
function AddLazyLoader( $p, $lazyClass = 'lazyload' )
{
	preg_match_all( '/<img[\s\r\n]+.*?>/is', $p, $matches );
	
	$search = array();
	$replace = array();
	
	if ( !empty( $matches ) )
	{
		foreach ( $matches['0'] as $imgMatch )
		{
			if ( !preg_match( "/src=['\"]data:image/is", $imgMatch ) )
			{
				// replace the src and add the data-src attribute
				$replaceHTML = preg_replace( "/(<img[^>]*)src=/", "$1data-src=", $imgMatch );
				
				$replaceHTML = str_replace( 'srcset', 'data-srcset', $replaceHTML );
				
				// replace sizes to avoid w3c errors for missing srcset
				$replaceHTML = str_replace( 'sizes', 'data-sizes', $replaceHTML );
				
				// add the lazy class to the img element
				if ( preg_match( '/class=["\']/i', $replaceHTML ) )
				{
					$replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1' . $lazyClass . ' $2$1', $replaceHTML );
				}
				
				else
				{
					$replaceHTML = preg_replace( '/<img/is', '<img class="' . $lazyClass . '"', $replaceHTML );
				}
				
				array_push( $search, $imgMatch );
				array_push( $replace, $replaceHTML );
			}
		}
		
		$p = str_replace( $search, $replace, $p );
	}
	
	//Do the same for the embeds
	preg_match_all( '/<iframe[\s\r\n]+.*?>/is', $p, $matches );
	
	$search = array();
	$replace = array();
	
	if ( !empty( $matches ) )
	{
		foreach ( $matches['0'] as $embedMatch )
		{
			// replace the src and add the data-src attribute
			$replaceHTML = preg_replace( "/(<iframe[^>]*)src=/", "$1data-src=", $embedMatch );
			
			// add the lazy class to the img element
			if ( preg_match( '/class=["\']/i', $replaceHTML ) )
			{
				$replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1' . $lazyClass . ' $2$1', $replaceHTML );
			}
				
			else
			{
				$replaceHTML = preg_replace( '/<iframe/is', '<iframe class="' . $lazyClass . '"', $replaceHTML );
			}
				
			array_push( $search, $embedMatch );
			array_push( $replace, $replaceHTML );
		}
		
		$p = str_replace( $search, $replace, $p );
	}

	return $p;
}

#####################################################
#
# Add Alt to Images from the Content function
#
#####################################################
function AddAltTagToImages ( $post, $postTitle )
{
	//Find only the images that don't have an alt parameter
	$pattern = '#<img(?!.*alt=")(.+src="(([^"]+/)?(.+)\..+)"[^ /]*)( ?\/?)>#i';

	preg_match_all( $pattern, $post, $matches );

	if ( !empty ( $matches ) )
	{
		$post = preg_replace_callback($pattern, function ($matches) use ($postTitle)
		{
			static $incr = 0;
			++$incr;
			$title = htmlspecialchars( $postTitle );
			return "<img$matches[1] alt=\"$title $incr\"$matches[5]>";
		}, $post);
		
	}

	return $post;
}

#####################################################
#
# Update the Posts Views In A File DB Function
#
# This is needed to avoid a DB connection if the posts has been loaded from the cache
#
#####################################################
function UpdateFilePostViews ( $id )
{
	if ( empty( $id ) )
		return;
	
	$data = OpenFileDB( POSTS_VIEWS_FILE );
	
	if ( isset( $data[$id] ) )
	{
		$views = ( $data[$id]['views'] + 1 );
		
		$data[$id]['views'] = $views;
		$data[$id]['time'] = time();
	}
	
	else
	{
		$data[$id] = array(
			'views' => 1,
			'time' => time()
		);
	}
	
	WriteFileDB ( $data, POSTS_VIEWS_FILE );
}

#####################################################
#
# Update the Posts Views In The DB Function
#
#####################################################
function UpdatePostViews ( $id )
{
	if ( empty( $id ) )
		return;
	
	$db = db();
	
	$dbarr = array(
		"last_time_viewed" => time(),
		"views" => "views + 1"
    );

	$db->update( POSTS )->where( 'id_post', $id )->set( $dbarr );
}

#####################################################
#
# Generate a description from a string function
#
#####################################################
function generateDescr ($string, $length = 180 ) 
{
	$string = html_entity_decode( $string, ENT_QUOTES, 'UTF-8' );

	$string = trim( str_replace( array( "\r", "\n", '"', "\t" ), ' ', strip_tags( $string ) ) );
	
	// Cut the string to the requested length, and strip any extraneous spaces from the beginning and end.
	$string = trim( mb_substr( $string, 0, $length ) );
	
	//Sometimes the string returns bigger, so let's check again
	if ( strlen( $string ) > $length )
		$string = mb_strcut( $string, 0, $length, "UTF-8" );

	// Send the new description back.
	return $string;
}

function postDate( $date, $c = false, $format = null, $time = false, $lang = null )
{
	$CurrentLang = ( $lang ? $lang : CurrentLang() );
	
	$format = ( $format ? $format : $CurrentLang['data']['date_format'] );
	
	$format .= ( $time ? ' ' . $CurrentLang['data']['time_format'] : '' );
	
	return date ( ( $c ? 'c' : $format ), $date );
}

function niceTime($time) 
{
	$delta = time() - $time;
	
	if ($delta < 60)
		return __( 'less-than-a-minute-ago' );
	
	elseif ($delta < 120)
		return __( 'about-a-minute-ago' );
	
	elseif ($delta < (45 * 60))
		return floor($delta / 60) . ' ' . __( 'minutes-ago' );
	
	elseif ($delta < (90 * 60))
		return __( 'about-an-hour-ago' );
	
	elseif ($delta < (24 * 60 * 60))
		return sprintf( __( 'about-s-hours-ago' ), floor( $delta / 3600 ) );
	
	elseif ($delta < (48 * 60 * 60))
		return __( 'a-day-ago' );
	
	elseif ($delta < (7 * 24 * 60 * 60))
		return sprintf( __( 's-days-ago' ), floor( $delta / 86400 ) );
	
	elseif ($delta < (31 * 24 * 60 * 60))
		return floor($delta / 604800) . ' ' . __( 'week' ) . ( ( floor($delta / 604800) > 1 ) ? 's' : '' ) . ' ' . __( 'ago' );
	
	elseif( ($delta > (30 * 24 * 60 * 60) ) && ($delta < (13 * 30 * 24 * 60 * 60)) )
		return floor($delta / 2592000) . ' ' . __( 'month' ) . ( ( floor($delta / 2592000) > 1 ) ? 's' : '' ) . ' ' . __( 'ago' );
	
	else
		return floor($delta / 31104000) . ' ' . __( 'year' ) . ( ( floor($delta / 31104000) > 1 ) ? 's' : '' ) . ' ' . __( 'ago' );
}

#####################################################
#
# WordPress functions
#
# All the following functions were taken from WordPress
#
#####################################################
function wpautop( $pee, $br = true )
{
	$pre_tags = array();

		if ( trim($pee) === '' )
			return '';

		// Just to make things a little easier, pad the end.
		$pee = $pee . "\n";

		/*
		 * Pre tags shouldn't be touched by autop.
		 * Replace pre tags with placeholders and bring them back after autop.
		 */
		if ( strpos($pee, '<pre') !== false ) {
			$pee_parts = explode( '</pre>', $pee );
			$last_pee = array_pop($pee_parts);
			$pee = '';
			$i = 0;

			foreach ( $pee_parts as $pee_part ) {
				$start = strpos($pee_part, '<pre');

				// Malformed html?
				if ( $start === false ) {
					$pee .= $pee_part;
					continue;
				}

				$name = "<pre wp-pre-tag-$i></pre>";
				$pre_tags[$name] = substr( $pee_part, $start ) . '</pre>';

				$pee .= substr( $pee_part, 0, $start ) . $name;
				$i++;
			}

			$pee .= $last_pee;
		}
		// Change multiple <br>s into two line breaks, which will turn into paragraphs.
		$pee = preg_replace('|<br\s*/?>\s*<br\s*/?>|', "\n\n", $pee);

		$allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';

		// Add a double line break above block-level opening tags.
		$pee = preg_replace('!(<' . $allblocks . '[\s/>])!', "\n\n$1", $pee);

		// Add a double line break below block-level closing tags.
		$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);

		// Standardize newline characters to "\n".
		$pee = str_replace(array("\r\n", "\r"), "\n", $pee);

		// Find newlines in all elements and add placeholders.
		$pee = wp_replace_in_html_tags( $pee, array( "\n" => " <!-- wpnl --> " ) );

		// Collapse line breaks before and after <option> elements so they don't get autop'd.
		if ( strpos( $pee, '<option' ) !== false ) {
			$pee = preg_replace( '|\s*<option|', '<option', $pee );
			$pee = preg_replace( '|</option>\s*|', '</option>', $pee );
		}

		/*
		 * Collapse line breaks inside <object> elements, before <param> and <embed> elements
		 * so they don't get autop'd.
		 */
		if ( strpos( $pee, '</object>' ) !== false ) {
			$pee = preg_replace( '|(<object[^>]*>)\s*|', '$1', $pee );
			$pee = preg_replace( '|\s*</object>|', '</object>', $pee );
			$pee = preg_replace( '%\s*(</?(?:param|embed)[^>]*>)\s*%', '$1', $pee );
		}

		/*
		 * Collapse line breaks inside <audio> and <video> elements,
		 * before and after <source> and <track> elements.
		 */
		if ( strpos( $pee, '<source' ) !== false || strpos( $pee, '<track' ) !== false ) {
			$pee = preg_replace( '%([<\[](?:audio|video)[^>\]]*[>\]])\s*%', '$1', $pee );
			$pee = preg_replace( '%\s*([<\[]/(?:audio|video)[>\]])%', '$1', $pee );
			$pee = preg_replace( '%\s*(<(?:source|track)[^>]*>)\s*%', '$1', $pee );
		}

		// Collapse line breaks before and after <figcaption> elements.
		if ( strpos( $pee, '<figcaption' ) !== false ) {
			$pee = preg_replace( '|\s*(<figcaption[^>]*>)|', '$1', $pee );
			$pee = preg_replace( '|</figcaption>\s*|', '</figcaption>', $pee );
		}

		// Remove more than two contiguous line breaks.
		$pee = preg_replace("/\n\n+/", "\n\n", $pee);

		// Split up the contents into an array of strings, separated by double line breaks.
		$pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);

		// Reset $pee prior to rebuilding.
		$pee = '';

		// Rebuild the content as a string, wrapping every bit with a <p>.
		foreach ( $pees as $tinkle ) {
			$pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
		}

		// Under certain strange conditions it could create a P of entirely whitespace.
		$pee = preg_replace('|<p>\s*</p>|', '', $pee);

		// Add a closing <p> inside <div>, <address>, or <form> tag if missing.
		$pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);

		// If an opening or closing block element tag is wrapped in a <p>, unwrap it.
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

		// In some cases <li> may get wrapped in <p>, fix them.
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee);

		// If a <blockquote> is wrapped with a <p>, move it inside the <blockquote>.
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);

		// If an opening or closing block element tag is preceded by an opening <p> tag, remove it.
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);

		// If an opening or closing block element tag is followed by a closing <p> tag, remove it.
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

		// Optionally insert line breaks.
		if ( $br ) {
			// Replace newlines that shouldn't be touched with a placeholder.
			//$pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', '_autop_newline_preservation_helper', $pee);

			// Normalize <br>
			$pee = str_replace( array( '<br>', '<br/>' ), '<br />', $pee );

			// Replace any new line characters that aren't preceded by a <br /> with a <br />.
			$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee);

			// Replace newline placeholders with newlines.
			$pee = str_replace('<WPPreserveNewline />', "\n", $pee);
		}

		// If a <br /> tag is after an opening or closing block tag, remove it.
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);

		// If a <br /> tag is before a subset of opening or closing block tags, remove it.
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
		$pee = preg_replace( "|\n</p>$|", '</p>', $pee );

		// Replace placeholder <pre> tags with their original content.
		if ( !empty($pre_tags) )
			$pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);

		// Restore newlines in all elements.
		if ( false !== strpos( $pee, '<!-- wpnl -->' ) )
		{
			$pee = str_replace( array( ' <!-- wpnl --> ', '<!-- wpnl -->' ), "\n", $pee );
		}

		return $pee;
	}
	
	function _autop_newline_preservation_helper( $matches ) {
	return str_replace( "\n", "<WPPreserveNewline />", $matches[0] );
}

function get_html_split_regex() {
	static $regex;
	 
	if ( ! isset( $regex ) ) {
			$comments =
				  '!'           // Start of comment, after the <.
				. '(?:'         // Unroll the loop: Consume everything until --> is found.
				.     '-(?!->)' // Dash not followed by end of comment.
				.     '[^\-]*+' // Consume non-dashes.
				. ')*+'         // Loop possessively.
				. '(?:-->)?';   // End of comment. If not found, match all input.
	 
			$cdata =
				  '!\[CDATA\['  // Start of comment, after the <.
				. '[^\]]*+'     // Consume non-].
				. '(?:'         // Unroll the loop: Consume everything until ]]> is found.
				.     '](?!]>)' // One ] not followed by end of comment.
				.     '[^\]]*+' // Consume non-].
				. ')*+'         // Loop possessively.
				. '(?:]]>)?';   // End of comment. If not found, match all input.
	 
			$escaped =
				  '(?='           // Is the element escaped?
				.    '!--'
				. '|'
				.    '!\[CDATA\['
				. ')'
				. '(?(?=!-)'      // If yes, which type?
				.     $comments
				. '|'
				.     $cdata
				. ')';
	 
			$regex =
				  '/('              // Capture the entire match.
				.     '<'           // Find start of element.
				.     '(?'          // Conditional expression follows.
				.         $escaped  // Find end of escaped element.
				.     '|'           // ... else ...
				.         '[^>]*>?' // Find end of normal element.
				.     ')'
				. ')/';
		}
	 
	return $regex;
}

function wp_html_split( $input ) {
	return preg_split( get_html_split_regex(), $input, -1, PREG_SPLIT_DELIM_CAPTURE );
}
	
function wp_replace_in_html_tags( $haystack, $replace_pairs ) {
		// Find all elements.
		$textarr = wp_html_split( $haystack );
		$changed = false;
	 
		// Optimize when searching for one item.
		if ( 1 === count( $replace_pairs ) ) {
			// Extract $needle and $replace.
			foreach ( $replace_pairs as $needle => $replace );
	 
			// Loop through delimiters (elements) only.
			for ( $i = 1, $c = count( $textarr ); $i < $c; $i += 2 ) {
				if ( false !== strpos( $textarr[$i], $needle ) ) {
					$textarr[$i] = str_replace( $needle, $replace, $textarr[$i] );
					$changed = true;
				}
			}
		} else {
			// Extract all $needles.
			$needles = array_keys( $replace_pairs );
	 
			// Loop through delimiters (elements) only.
			for ( $i = 1, $c = count( $textarr ); $i < $c; $i += 2 ) {
				foreach ( $needles as $needle ) {
					if ( false !== strpos( $textarr[$i], $needle ) ) {
						$textarr[$i] = strtr( $textarr[$i], $replace_pairs );
						$changed = true;
						// After one strtr() break out of the foreach loop and look at next element.
						break;
					}
				}
			}
		}
	 
		if ( $changed ) {
			$haystack = implode( $textarr );
		}
	 
		return $haystack;
}

#####################################################
#
# Cleans the content by removing any caption found in the content
#
#####################################################
/*
function Captions( $content )
{
	//preg_match('/\[caption([^\]]+)align="([^"]+)"\s+width="(\d+)"\](\s*\<img.+src=[\'"]([^\'"]+)[\'"].*>)\s*(.*?)\s*\[\/caption\]/i', $content, $matches);
	return preg_replace
	(
       '/(?:<p>)?\[caption([^\]]+)align="([^"]+)"\s+width="(\d+)"\](?:<a.*>)?(\s*\<img[^>]+>)(?:<\/a>)?\s*(.*?)\s*\[\/caption\](?:<\/p>)?/i', 
		'<figure class="figure text-center">
			\4
			<figcaption class="figure-caption">\5</figcaption>
		</figure>',
		//<figure class="figure">\4<figcaption class="figure-caption text-center">\5</figcaption></figure>',
        //'<div\1style="width: \3px" class="caption \2">\4<p class="caption figure-caption text-center">\5</p></div>', 
        $content
	); 
}
*/