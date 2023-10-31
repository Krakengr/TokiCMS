<?php defined('TOKICMS') or die('Hacking attempt...');

//
// Tags Functions
//

#####################################################
#
# Get the category's data function
#
#####################################################
function GetTag( $key = null, $id = null, $siteId, $langId = null, $langKey = null, $cache = true )
{
	$cacheFile = CACHE_ROOT . 'content' . PS . 'tag_data-' . ( $key ? 'key_' . $key : 'id_' . $id ) . ( $langId ? '-langid_' . $langId : '' ) . ( $langKey ? '-langkey_' . $langKey : '' ) . '-siteid_' . $siteId;

	$cacheFile .= '-' . sha1( $cacheFile . CACHE_HASH ) . '.php';
	
	//Get data from cache
	if ( $cache && ValidCache( $cacheFile ) )
	{
		$data = ReadCache( $cacheFile );
	}
	else
	{
		$db = db();
		
		//Query: config
		$cnf = $db->from( null, "SELECT value FROM `" . DB_PREFIX . "config` WHERE id_site = " . $siteId . " AND variable = 'share_tags_langs'" )->single();

		$query = "SELECT t.*, la.code as ls, cnf.value as hide_lang, cnf2.value as tags_filter, cnf3.value as trans_data, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, ld.code as dlc
		FROM `" . DB_PREFIX . "tags` AS t
		INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = t.id_lang
		INNER JOIN `" . DB_PREFIX . "languages` as ld ON ld.id_site = la.id_site AND ld.is_default = 1
		INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = la.id_site
		INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site = la.id_site AND cnf.variable = 'hide_default_lang_slug'
		INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = la.id_site AND cnf2.variable = 'tags_filter'
		INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = la.id_site AND cnf3.variable = 'trans_data'
		WHERE (" . ( $key ? "t.sef = :sef" : "t.id = :id" ) . ") AND (la.id_site = " . $siteId . ")" . ( ( !IsTrue( $cnf['value'] ) && $langId ) ? " AND (t.id_lang = " . $langId . ")" : "" );
		
		if ( $key )
		{
			$binds = array( $key => ':sef' );
		}
		else
		{
			$binds = array( $id => ':id' );
		}

		//Query: tag
		$tmp = $db->from( null, $query, $binds )->single();

		if ( !$tmp )
		{
			$log = Settings::LogSettings();
				
			if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
			{
				$errorMessage = 'Tag not found in database (' . ( $key ? 'Key: ' . $key : 'Id: ' . $id ) . ')';
				$errorData = null;
					
				if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
				{
					$errorData = 'Query: ' . PHP_EOL . $query;
				}
				
				Log::Set( $errorMessage, $errorData, $query, 'system' );
			}

			return null;
		}
		
		$data = $tmp;
		
		$data['tagUrl']  	= BuildTagUrl( $data, $data['ls'] );
		$data['image'] 	 	= BuildImageArray( $data['id_image'] );

		WriteCacheFile( $data, $cacheFile );
	}
	
	return $data;
}

#####################################################
#
# Get Post Custom Posts Tags function
#
#####################################################
function GetCusAssocTags( $post_id, $type = 0 )
{
	$db = db();
	
	$query = "
	SELECT r.taxonomy_id, p.id_site, s.url, t.sef
	FROM `" . DB_PREFIX . "tags_relationships` AS r
	INNER JOIN `" . DB_PREFIX . POSTS . "`   as p ON p.id_post = r.object_id
	INNER JOIN `" . DB_PREFIX . "post_types` as t ON t.id = r.id_custom_type
	INNER JOIN `" . DB_PREFIX . "sites` 	 as s ON s.id = p.id_site
	WHERE 1=1 AND (r.object_id = " . (int) $post_id . ") AND (r.id_custom_type = " . (int) $type . ")";
			
	//Query: tags
	$data = $db->from( null, $query )->all();

	if( !$data )
	{
		return null;
	}
	
	$tags = array();
	
	foreach ( $data as $b )
	{
		$tag = $db->from( null, "
		SELECT id, title, sef
		FROM `" . DB_PREFIX . "tags`
		WHERE (id = " . (int) $b['taxonomy_id'] . ")"
		)->single();
		
		if ( $tag )
		{
			$tags[] = array( 
				'name' => StripContent( $tag['title'] ),
				'sef'  => $tag['sef'],
				'url'  => $b['url'] . ( !empty( $b['sef'] ) ? $b['sef'] . PS . $tag['sef'] . PS : '' ),
				'id'   => $tag['id']
			);
			
			unset( $tag );
		}
	}

	unset( $data );

	return $tags;
}

#####################################################
#
# Get Post Tags function
#
#####################################################
function GetAssocTags ( $postId, $type = 0, $array_only = false )
{
	$db = db();
	
	//Query: tags
	$data = $db->from( null, "
	SELECT taxonomy_id
	FROM `" . DB_PREFIX . "tags_relationships`
	WHERE (object_id = " . (int) $postId . ") AND (id_custom_type = " . (int) $type . ")"
	)->all();

	if( !$data ) 
		return null;
	
	$tags = array();
	
	foreach ( $data as $b )
	{
		$tag = $db->from( null, "
		SELECT id, title, sef
		FROM `" . DB_PREFIX . "tags`
		WHERE (id = " . $b['taxonomy_id'] . ")"
		)->single();

		if ( $tag )
		{
			$tags[] = array( 'name' => $tag['title'], 'sef' => $tag['sef'], 'id' => $tag['id'] );
			unset( $tag );
		}
	}
		
	unset( $data );

	if ( $array_only )
	{
		$ts = $tags;
			
		$tags = array();
			
		foreach( $ts as $a => $t )
			$tags[$t['id']] = $t['name'];
	}

	return $tags;
}

#####################################################
#
# Add Tags To Post function
#
#####################################################
function AddTags( $array, $postId, $langId, $siteId, $type = 0 )
{
	$siteId = (int) $siteId;
	$langId = (int) $langId;
	$db 	= db();
	
	//Empty array means we removed all the tags from this post OR we don't have tags. So we have to remove them aswell
	if ( empty( $array ) )
	{
		$data = $db->from( null, "
		SELECT id_relation, taxonomy_id
		FROM `" . DB_PREFIX . "tags_relationships`
		WHERE (object_id = " . $postId . ") AND (id_site = " . $siteId . ") AND (id_custom_type = " . (int) $type . ")"
		)->all();
	
		if ( $data )
		{
			foreach( $data as $t )
			{
				//Delete this tag
				$q = $db->delete( "tags_relationships" )->where( "id_relation", $t['id_relation'] )->run();
		
				if ( $q )
				{
					$db->update( "tags" )->where( "id", $t['taxonomy_id'] )->decrease( "num_items" );
				}
			}
		}
		
		return;
	}
	
	//Create an array, we need to search it later
	$tagsArray = array();
	
	//We have some tags, so we have to add them to this post, if needed
	foreach( $array as $tag )
	{
		$tagKey = URLify( $tag['value'] );
	
		$tagId = GetTag( $tagKey, null, $siteId, $langId, null, false );

		if ( $tagId )
		{
			$data = $db->from( null, "
			SELECT id_relation, taxonomy_id
			FROM `" . DB_PREFIX . "tags_relationships`
			WHERE (object_id = " . $postId . ") AND (taxonomy_id = " . $tagId['id'] . ") AND (id_custom_type = " . (int) $type . ")"
			)->single();

			//No data means that this tag is not related to the post
			if ( !$data )
			{
				$dbarr = array(
					"object_id" 		=> $postId,
					"taxonomy_id" 		=> $tagId['id'],
					"id_site" 			=> $siteId,
					"id_custom_type" 	=> $type
				);

				$q = $db->insert( 'tags_relationships' )->set( $dbarr );
					
				if ( $q )
				{
					//Update the tag's num items
					//$db->update( "tags" )->where( "id", $tagId['id'] )->set( "num_items", "num_items + 1" );
					$db->update( "tags" )->where( "id", $tagId['id'] )->increase( "num_items" );
				}
			}
			
			$tagsArray[] = $tagId['id'];
		}
		
		//Tag is not found, so we have to add it first
		else
		{
			$dbarr = array(
				"title" 	=> $tag['value'],
				"sef" 		=> $tagKey,
				"id_lang" 	=> $langId
			);

			$tagId = $db->insert( 'tags' )->set( $dbarr, null, true );

			if ( $tagId )
			{
				$dbarr = array(
					"object_id" 		=> $postId,
					"taxonomy_id" 		=> $tagId,
					"id_site" 			=> $siteId,
					"id_custom_type" 	=> $type
				);

				$db->insert( 'tags_relationships' )->set( $dbarr, null, true );

				//Update the tag's num items
				//$db->update( "tags" )->where( "id", $tagId )->set( "num_items", "num_items + 1" );
				$db->update( "tags" )->where( "id", $tagId['id'] )->increase( "num_items" );

				$tagsArray[] = $tagId;
			}
		}
	}

	//Get the tags of this post
	$arrayToCheck = GetAssocTags( $postId, $type );
	
	if ( !empty( $arrayToCheck ) )
	{
		foreach( $arrayToCheck as $a => $arr )
		{
			if ( in_array( $arr['id'], $tagsArray ) )
				continue;
			
			//Get the ID for this tag
			$tagID = $db->from( 
			null, 
			"SELECT id
			FROM `" . DB_PREFIX . "tags`
			WHERE (id_lang = " . $langId . ") AND (sef = :sef)",
			array( $arr['sef'] => ':sef' )
			)->single();
			
			if ( $tagId )
			{
				//Delete this tag
				$q = $db->delete( "tags_relationships" )->where( "object_id", $postId )->where( "taxonomy_id", $tagId['id'] )->where( "id_custom_type", $type )->run();
		
				if ( $q )
				{
					//$db->update( "tags" )->where( "id", $tagID['id'] )->set( "num_items", "num_items - 1" );
					$db->update( "tags" )->where( "id", $tagId['id'] )->decrease( "num_items" );
				}
			}
		}
	}
}

#####################################################
#
# Build Tag URL function
#
# Builds the url based on tag's data and current settings
#
#####################################################
function BuildTagUrl( $data, $langCode = null, $skipSlug = false )
{
	if ( empty( $data ) )
		return false;

	if ( !empty( $data['dlc'] ) )
	{
		$url = LangSlugUrl( $data );
	}
	
	else
	{
		$CurrentLang = CurrentLang();

		$url = SITE_URL;

		$slugTrans = Settings::Trans();

		$langCode = ( $langCode ? $langCode : $CurrentLang['lang']['code'] );

		//Add the lang slug
		if ( MULTILANG )
		{
			if ( !empty( $data['ls'] ) && ( !Settings::IsTrue( 'hide_default_lang_slug' ) || ( Settings::IsTrue( 'hide_default_lang_slug' ) && ( $data['ls'] != $langCode ) ) ) )
				$url .= $data['ls'] . PS;
		
			elseif 
			(
				!empty( $langCode ) && ( !Settings::IsTrue( 'hide_default_lang_slug' ) || ( Settings::IsTrue( 'hide_default_lang_slug' )
				&& ( $langCode != Settings::LangData()['lang']['code'] ) ) ) 
			)
				$url .= $langCode . PS;
		}
	}

	//Add the tags filter
	$url .= ltrim( TagFilter( $langCode ), '/' );

	//Add the slug
	if ( !$skipSlug )
	{
		$url .= $data['sef'] . PS;
	}

	return rawurldecode( $url );
}

#####################################################
#
# Return Tag Filter function
#
#####################################################
function TagFilter( $langCode = null )
{
	if ( !empty( $data ) && !empty( $data['dlc'] ) )
	{
		//We need this value
		$filter = ( !empty( $data['tags_filter'] ) ? $data['tags_filter'] : Settings::Get()['tags_filter'] );
		
		$defaultLang = $data['dlc'];
		
		$langCode = ( $langCode ? $langCode : $data['ls'] );

		if ( IsTrue( $data['multilang'] ) )
		{
			$trans = Json( $data['trans_data'] );
			
			if ( !empty( $trans ) && isset( $trans[$langCode] ) && !empty( $trans[$langCode]['tags_filter_trans'] ) )
			{
				$filter = '/' . $trans[$langCode]['tags_filter_trans'] . '/';
			}
		}
	}
	
	else
	{
		$CurrentLang = CurrentLang();
		
		$filter = Settings::Get()['tags_filter'];
		$defaultLang = Settings::LangData()['lang']['code'];
		$langCode = ( $langCode ? $langCode : $CurrentLang['lang']['code'] );

		if ( MULTILANG )
		{
			$trans = Settings::Trans();
			
			if ( !empty( $trans ) && isset( $trans[$langCode] ) && !empty( $trans[$langCode]['tags_filter_trans'] ) )
				$filter = PS . $trans[$langCode]['tags_filter_trans'] . PS;
		}
	}

	return rawurldecode( $filter );
}
