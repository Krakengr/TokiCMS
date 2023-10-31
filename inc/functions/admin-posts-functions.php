<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Remove any special character from string function
#
#####################################################
function RemoveSpecialChars( $string, $replace = '' )
{
	return preg_replace("/[\/_|+:!@#$%^&*()'\"<>\\\`}{;=,?\[\]~. -]+/", $replace, $string);
}


#####################################################
#
# Delete a post/page function
#
#####################################################
function DeletePost( $post )
{
	$db = db();
	
	if ( empty( $post ) )
	{
		return false;
	}
	
	$categoryId 	= ( !empty( $post->Category()->id ) ? $post->Category()->id : 0 );
	$subCategoryId 	= ( !empty( $post->SubCategory()->id ) ? $post->SubCategory()->id : 0 );
	$blogId 		= ( !empty( $post->Blog()->id ) ? $post->Blog()->id : 0 );
	
	//Delete the post
	$q = $db->delete( POSTS )->where( "id_post", $post->PostID() )->run();
	
	if ( !$q )
	{
		return false;
	}
	
	//Update any child post/page relation this post may have
	$db->update( POSTS )->where( 'id_parent', $post->PostID() )->set( "id_parent", '0' );
	
	$db->update( POSTS )->where( 'id_page_parent', $post->PostID() )->set( "id_page_parent", '0' );
	
	//Delete any comments this post may have
	$db->delete( "comments" )->where( "id_post", $post->PostID() )->run();
	
	//Delete any autosaves this post may have
	$db->delete( "posts_autosaves" )->where( "post_id", $post->PostID() )->run();
	
	//Delete any favorites this post may have
	$db->delete( "posts_favorites" )->where( "post_id", $post->PostID() )->run();
	
	//Delete any product data this post may have
	$db->delete( "posts_product_data" )->where( "id_post", $post->PostID() )->run();
	
	//Delete any subs this post may have
	$db->delete( "posts_subscriptions" )->where( "post_id", $post->PostID() )->run();
	
	//Delete any attribute this post may have
	$db->delete( "post_attribute_data" )->where( "id_post", $post->PostID() )->run();
	
	//Delete any variation this post may have
	$db->delete( "post_variations" )->where( "id_post", $post->PostID() )->run();
	
	//Delete any variation item this post may have
	$db->delete( "post_variations_items" )->where( "id_post", $post->PostID() )->run();
	
	//Delete its data
	$db->delete( "posts_data" )->where( "id_post", $post->PostID() )->run();
	
	//Delete any price this post may have
	$prices = $db->from( 
	null, 
	"SELECT id_price
	FROM `" . DB_PREFIX . "prices`
	WHERE (id_post = " . $post->PostID() . ")"
	)->all();
	
	if ( !empty( $prices ) )
	{
		foreach( $prices as $price )
		{
			$db->delete( "price_info" )->where( "id_price", $price['id_price'] )->run();
			$db->delete( "price_update_info" )->where( "id_price", $price['id_price'] )->run();
			$db->delete( "prices" )->where( "id_price", $price['id_price'] )->run();
		}
	}

	//Delete the tags
	$tags = $db->from( 
	null, 
	"SELECT id_relation, taxonomy_id
	FROM `" . DB_PREFIX . "tags_relationships`
	WHERE (object_id = " . $post->PostID() . ")"
	)->all();
		
	if ( !empty( $tags ) )
	{
		foreach ( $tags as $tag )
		{
			//Update the num items
			$db->update( "tags" )->where( 'id', $tag['taxonomy_id'] )->set( "num_items", array( "num_items", "1", "-" ) );

			//Delete the relation
			$db->delete( "tags_relationships" )->where( "id_relation", $tag['id_relation'] )->run();
		}
	}
		
	//Update the image status
	$db->update( "images" )->where( 'id_attach_post', $post->PostID() )->set( "id_attach_post", '0' );

	//Update the categories items
	if ( $categoryId > 0 )
	{
		$db->update( "categories" )->where( 'id', $categoryId )->set( "num_items", array( "num_items", "1", "-" ) );
	}
	
	//Update the subcategories items
	if ( $subCategoryId > 0 )
	{
		$db->update( "categories" )->where( 'id', $subCategoryId )->set( "num_items", array( "num_items", "1", "-" ) );
	}
	
	//Update the blog items
	if ( $blogId > 0 )
	{
		$db->update( "blogs" )->where( 'id_blog', $blogId )->set( "num_posts", array( "num_posts", "1", "-" ) );
	}
	
	return true;
}

#####################################################
#
# Move orphan content
#
#####################################################
function MoveMediaContent( $postId )
{	
	$tmp = GetSinglePost( $postId, null, false, false, true );
	
	if ( !$tmp )
	{
		return;
	}
	
	$content = $tmp['postRaw'];
	
	preg_match_all( '/\[image.+id="([0-9]+)".*]/i', $content, $p );
	
	if ( empty( $p['1'] ) )
	{
		return;
	}
	
	$db = db();
	
	foreach( $p['1'] as $i )
	{
		//Try to clone this image to the target site
		CheckImageExists( ( !empty( $im['id_parent'] ) ? $im['id_parent'] : $im['id_image'] ), $tmp['site']['id'] );
	}
}

#####################################################
#
# Move orphan content
#
#####################################################
function MoveOrphanContent( $target = null, $targetId = 0, $type = 'post', $siteId = SITE_ID, $limit = 10 )
{	
	if ( ( $targetId == 0 ) || !$target )
		return null;
	
	$db = db();
	
	$all = ( ( $type == 'all' ) ? true : false );
	
	$items = $errors = 0;
	
	$binds = null;
	
	if ( !$all )
	{
		$binds = array( $type => ':type' );
	}
	
	//Query: posts
	$ps = $db->from( 
	null, 
	"SELECT p.id_post, p.post_type, p.id_lang, p.id_member, la.code
	FROM `" . DB_PREFIX . POSTS . "` AS p
	LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = p.id_lang
	WHERE (p.id_site = " . (int) $siteId . ") AND (p.id_blog = 0)" . ( !$all ? " AND (p.post_type = :type)" : "" ) . "
	LIMIT " . (int) $limit,
	$binds
	)->all();

	if ( !$ps )
		return null;
	
	foreach( $ps as $p )
	{
		if ( $items >= $limit )
			break;
		
		if ( $target == 'blog' )
		{
			if ( $p['post_type'] == 'post' )
			{
				$catId = GetBlogDefaultCategory( $targetId, $p['id_lang'] );
				
				if ( !$catId )
				{
					$errors++;
					continue;
				}
			}
			
			else
			{
				$catId = 0;
			}
			
			//Update the post
			$dbarr = array(
				"id_category" 		=> $catId,
				"id_blog" 			=> $targetId,
				"id_sub_category" 	=> 0,
			);

			$db->update( POSTS )->where( 'id_post', $p['id_post'] )->set( $dbarr );
		}
		
		elseif ( $target == 'lang' )
		{
			//Update the post
			$db->update( POSTS )->where( 'id_post', $p['id_post'] )->set( "id_lang", $targetId );
		}
		
		elseif ( $target == 'site' )
		{
			$targetLang = GetSiteLang( $p['code'], $targetId );

			if ( $targetLang == 0 )
			{
				$errors++;
				continue;
			}

			$targetUser = GetMemberRel( $p['id_member'], null, $targetId );
			
			//Get the ID of the target's site category
			$catId = GetSiteDefaultCategory( $targetId, $targetLang );

			//Move any images this post may have
			MovePostCoverImage( $p['id_post'], $targetId );
			MoveInPostImages( $p['id_post'], $targetId );
			
			//Update DB
			$dbarr = array(
				"id_member" 		=> $targetUser,
				"id_lang" 			=> $targetLang,
				"id_site" 			=> $targetId,
				"id_blog" 			=> 0,
				"id_category" 		=> $catId,
				"id_sub_category" 	=> 0
			);

			$db->update( POSTS )->where( 'id_post', $p['id_post'] )->set( $dbarr );
		}
		
		else
		{
			$errors++;
			continue;
		}
	}
	
	return array( 'items' => $items, 'errors' => $errors );
}

#####################################################
#
# Categories from a lang Move function
#
#####################################################
function MoveLangCats( $langId, $targetId )
{
	$db = db();
	
	$cats = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "categories`
	WHERE (id_lang = " . (int) $langId . ") AND (id_blog = 0)"
	)->all();

	if ( !$cats )
		return;
	
	foreach( $cats as $cat )
	{
		if ( $cat['is_default'] )
		{
			// add the category
			$dbarr = array(
				"id_lang" 	=> $targetId,
				"id_site" 	=> $cat['id_site'],
				"name" 		=> $cat['name'],
				"sef" 		=> $cat['sef'],
				"descr" 	=> $cat['descr']
			);
				
			$db->insert( 'categories' )->set( $dbarr );
		}
		
		else
		{
			//Update the category
			$db->update( "categories" )->where( 'id', $cat['id'] )->set( "id_lang", $targetId );
		}
	}
}

#####################################################
#
# Move Blogs and categories from a lang
#
#####################################################
function MoveLangBlogs( $langId, $targetId, $siteId )
{
	$db = db();
	
	//Check if there are any blogs
	$bs = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "blogs`
	WHERE (id_lang = " . (int) $langId . " OR id_lang = 0) AND (id_site = " . (int) $siteId . ")"
	)->all();
	
	if ( !$bs )
	{
		return;
	}
	
	foreach( $bs as $b )
	{
		//If this blog is enabled only for this language, then move everything
		if ( $b['id_lang'] == $langId )
		{
			$db->update( "categories" )->where( 'id_lang', $langId )->where( 'id_blog', $b['id_blog'] )->set( "id_lang", $targetId );
		
			//Update the blog
			$db->update( "blogs" )->where( 'id_blog', $b['id_blog'] )->set( "id_lang", $targetId );

			//Update the Posts
			$db->update( POSTS )->where( 'id_lang', $langId )->where( 'id_blog', $b['id_blog'] )->set( "id_lang", $targetId );
		}
		
		//This blog is still enabled to other languages, so we have to copy it
		else
		{
			$dbarr = array(
				"id_lang" 		=> $targetId,
				"id_site" 		=> $b['id_site'],
				"name" 			=> $b['name'],
				"sef" 			=> $b['sef'],
				"description" 	=> $b['description'],
				"slogan" 		=> $b['slogan']
			);
				
			$q = $db->insert( 'blogs' )->set( $dbarr );
			
			$blogId = null;
			
			if ( $q )
			{
				$blogId = $db->lastId();
			}
			
			//We have the blog, continue with updating
			if ( $blogId )
			{
				$dbarr = array(
					"id_lang" 	=> $targetId,
					"id_blog" 	=> $blogId
				);
			
				$db->update( "categories" )->where( 'id_lang', $langId )->where( 'id_blog', $b['id_blog'] )->set( $dbarr );

				//Update the Posts
				$db->update( POSTS )->where( 'id_lang', $langId )->where( 'id_blog', $b['id_blog'] )->set( $dbarr );
			}
		}
	}
}

#####################################################
#
# Move a language's content to another
#
#####################################################
function MoveLangContent( $langId, $targetId, $limit = 10 )
{
	global $Admin;
	
	//Get the site id of this language
	$la = $Admin->db->from( 
	null, 
	"SELECT id_site
	FROM `" . DB_PREFIX . "languages`
	WHERE (id = " . (int) $langId . ")"
	)->single();
	
	if ( !$la )
	{
		return;
	}
	
	$siteId = $la['id_site'];
	
	//Move Categories
	MoveLangCats( $langId, $targetId );
	
	//Move Blogs
	MoveLangBlogs( $langId, $targetId, $siteId );
	
	//Move the posts
	$Admin->db->update( POSTS )->where( 'id_lang', $targetId )->set( "id_lang", $langId );

	//Clean the cache files
	$Admin->EmptyCaches( $siteId );
}
#####################################################
#
# Move a sites's content to another
#
#####################################################
function MoveSiteContent( $siteId, $targetId, $limit = 10 )
{
	global $Admin;
	
	//Query: posts
	$ps = $Admin->db->from( 
	null, 
	"SELECT p.id_post, p.id_member, p.post_type, p.id_blog, p.id_lang, la.code
	FROM `" . DB_PREFIX . POSTS . "` AS p
	INNER JOIN `" . DB_PREFIX . "languages` AS la ON la.id = p.id_lang
	WHERE (p.id_site = " . $siteId . ")
	LIMIT " . (int) $limit
	)->all();

	if ( !$ps )
	{
		$arr = array(
			'status' => 'nothing-found',
			'message' => __( 'no-posts-found' )
		);
		
		return $arr;
	}
	
	//Move Categories
	MoveSiteCats( $siteId, $targetId );
	
	//Move the blogs, if any
	MoveSiteBlogs( $siteId, $targetId );
	
	foreach( $ps as $p )
	{
		$targetLang = GetSiteLang( $p['id_lang'], $targetId );

		if ( !$targetLang || ( $targetLang == 0 ) )
		{
			continue;
		}

		$targetUser = GetMemberRel( $p['id_member'], $siteId, $targetId );

		//Move any images this post may have
		MovePostCoverImage( $p['id_post'], $targetId );
		MoveInPostImages( $p['id_post'], $targetId );
		
		$dbarr = array(
			"id_member" 		=> $targetUser,
			"id_lang" 			=> $targetLang,
			"id_site" 			=> $targetId,
			"id_sub_category" 	=> 0
		);
		
		//Update Posts
		$Admin->db->update( POSTS )->where( 'id_post', $p['id_post'] )->set( $dbarr );
	}
	
	//Clean the cache of the source
	$Admin->EmptyCaches( $siteId );
	
	//Also clean the cache of the target
	$Admin->EmptyCaches( $targetId );
}

#####################################################
#
# Categories Move function
#
#####################################################
function MoveSiteCats( $siteId, $targetId )
{
	$db = db();
	
	//Check if there are categories
	$cats = $db->from( 
	null, 
	"SELECT id, id_lang
	FROM `" . DB_PREFIX . "categories`
	WHERE (id_site = " . (int) $siteId . ") AND (id_blog = 0)"
	)->all();
		
	if ( !$cats )
		return;
	
	foreach( $cats as $cat )
	{
		$catTargetLang = GetSiteLang( $cat['id_lang'], $targetId );
		
		$dbarr = array(
			"id_lang" => $catTargetLang,
			"id_site" => $targetId
		);

		//Update the category
		$db->update( "categories" )->where( 'id', $cat['id'] )->set( $dbarr );
	}
}

#####################################################
#
# Blog Move function
#
#####################################################
function MoveSiteBlogs( $siteId, $targetId )
{
	$db = db();
	
	//Check if there are any blogs
	$bs = $db->from( 
	null, 
	"SELECT id_blog,id_lang
	FROM `" . DB_PREFIX . "blogs`
	WHERE (id_site = " . (int) $siteId . ")"
	)->all();
	
	if ( !$bs )
	{
		return;
	}
	
	foreach( $bs as $b )
	{
		if ( $b['id_lang'] > 0 )
		{
			$targetLang = $catTargetLang = GetSiteLang( $b['id_lang'], $targetId );
		}
		
		else
		{
			$targetLang = $catTargetLang = 0;
		}
		
		//We don't have a lang id, so we have some work to do
		if ( $catTargetLang == 0 )
		{
			$cats = $db->from( 
			null, 
			"SELECT id, id_lang
			FROM `" . DB_PREFIX . "categories`
			WHERE (id_blog = " . $b['id_blog'] . ")"
			)->all();
	
			if ( $cats )
			{
				foreach( $cats as $cat )
				{
					$catTargetLang = GetSiteLang( $cat['id_lang'], $targetId );
					
					//Update the category
					$dbarr = array(
						"id_lang" => $catTargetLang,
						"id_site" => $targetId
					);

					$db->update( "categories" )->where( 'id', $cat['id'] )->set( $dbarr );
				}
			}
		}
		
		else
		{
			//Update the categories
			$dbarr = array(
				"id_lang" => $catTargetLang,
				"id_site" => $targetId
			);

			$db->update( "categories" )->where( 'id_blog', $b['id_blog'] )->set( $dbarr );
		}
		
		$dbarr = array(
			"id_lang" => $targetLang,
			"id_site" => $targetId
		);
		
		//Update the blog
		$db->update( "blogs" )->where( 'id_blog', $b['id_blog'] )->set( $dbarr );
	}
	
	//Update the target site and set it as multi-blog and multi-lang
	$dbarr = array(
		"enable_multilang" => 'true',
		"enable_multiblog" => 'true'
	);
		
	$db->update( "sites" )->where( 'id', $targetId )->set( $dbarr );
}

#####################################################
#
# Move blog's content to another
#
#####################################################
function MoveBlogContent( $blogId, $siteId = null, $targetId = 0, $options = 'move', $limit = 10 )
{
	if ( ( ( $blogId == 0 ) && !$siteId ) || ( ( $blogId  == 0 ) && ( $targetId == 0 ) ) )
		return null;
	
	$orphan = ( ( $blogId == 0 ) && !empty( $siteId ) );
	
	$db = db();
	
	$cats = $db->from( 
	null, 
	"SELECT id, sef, id_lang, id_site, name, sef, descr, is_default
	FROM `" . DB_PREFIX . "categories`
	WHERE (id_blog = " . (int) $blogId . ")" . ( $orphan ? " AND (id_site = " . (int) $siteId . ")" : "" )
	)->all();
	
	$items = 0;
	
	if ( !$cats )
	{
		$ps = $db->from( 
		null, 
		"SELECT id_post, id_lang, id_site
		FROM `" . DB_PREFIX . POSTS . "`
		WHERE (id_blog = " . (int) $blogId . ")
		LIMIT " . (int) $limit
		)->all();
		
		if ( !$ps )
		{
			return null;
		}
		
		foreach( $ps as $p )
		{
			//Check for the category
			$bCat = $db->from( 
			null, 
			"SELECT id
			FROM `" . DB_PREFIX . "categories`
			WHERE (id_blog = " . (int) $targetId . ") AND (id_lang = " . $p['id_lang'] . ")
			LIMIT " . (int) $limit
			)->single();

			if ( $bCat )
			{
				$catId = $bCat['id'];
			}
			
			else
			{
				$title = 'Uncategorized';
				
				$slug = SetShortSef( 'categories', 'id', 'sef', CreateSlug( $title, true ) );
				
				// put the category
				$dbarr = array(
					"id_lang" 	=> $p['id_lang'],
					"id_site" 	=> $p['id_site'],
					"id_blog" 	=> $targetId,
					"name" 		=> $title,
					"sef" 		=> $slug,
					"descr" 	=> $title
				);
					
				$q = $db->insert( 'categories' )->set( $dbarr );

				$catId = ( $q ? $db->lastId() : 0 );
			}
		
			//Update the post
			$dbarr = array(
				"id_sub_category" 	=> 0,
				"id_category" 	 	=> $catId,
				"id_blog" 			=> $targetId
			);
				
			$db->update( POSTS )->where( 'id_post', $p['id_post'] )->set( $dbarr );
		}
	}
	
	foreach( $cats as $cat )
	{
		if ( $items >= $limit )
			break;
		
		//Make sure this category has at least one post in it.
		$ps = $db->from( null, 
		"SELECT count(id_post) AS total FROM `" . DB_PREFIX . POSTS . "` WHERE (id_category = " . $cat['id'] . ")"
		)->total();		

		if ( empty( $ps ) )
		{
			//If there is no posts, we can delete this category
			if ( $options == 'move' && !$cat['is_default'] )
			{
				$db->delete( "categories" )->where( "id", $cat['id'] )->run();
			}
			
			continue;
		}
		
		//Set a default id for any case
		$catId = 0;

		//Check if this category already exists
		$bCat = $db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "categories`
		WHERE (sef = :sef) AND (id_blog = " . (int) $targetId . ") AND (id_lang = " . $cat['id_lang'] . ")",
		array( $cat['sef'] => ':sef' )
		)->single();
		
		if ( $bCat )
		{
			$catId = $bCat['id'];
		}
			
		else
		{
			// put the category
			$dbarr = array(
				"id_lang" 	=> $cat['id_lang'],
				"id_site" 	=> $cat['id_site'],
				"id_blog" 	=> $targetId,
				"name" 		=> $cat['name'],
				"sef" 		=> $cat['sef'],
				"descr" 	=> $cat['descr']
			);

			$q = $db->insert( 'categories' )->set( $dbarr );
			
			$catId = ( $q ? $db->lastId() : 0 );
		}
		
		$ps = $db->from( 
		null, 
		"SELECT id_post, post_type
		FROM `" . DB_PREFIX . POSTS . "`
		WHERE (id_blog = " . $blogId . ") AND (id_lang = " . $cat['id_lang'] . ")"
		)->all();
		
		if ( !$ps )
		{
			continue;
		}
		
		foreach( $ps as $p )
		{
			$items++;
			
			if ( $p['post_type'] == 'page' )
			{
				$catId = 0;
			}
			
			//Update the post
			$dbarr = array(
				"id_sub_category" 	=> 0,
				"id_category" 	 	=> $catId,
				"id_blog" 			=> $targetId
			);
				
			$db->update( POSTS )->where( 'id_post', $p['id_post'] )->set( $dbarr );
		}
	}
	
	return $items;
}

#####################################################
#
# Move a cat's content to another
#
#####################################################
function MoveCatContent( $catId, $targetId, $limit = 10 )
{
	$db = db();
	
	$ps = $db->from( 
	null, 
	"SELECT id_post, post_type
	FROM `" . DB_PREFIX . POSTS . "`
	WHERE (id_category = " . (int) $catId . ")
	LIMIT " . (int) $limit
	)->all();

	if ( !$ps )
	{
		$arr = array(
			'status' => 'nothing-found',
			'message' => __( 'no-posts-found' )
		);
		
		return $arr;
	}
	
	foreach( $ps as $p )
	{
		$type = $p['post_type'];
		
		if ( ( $targetId == 0 ) && ( $type == 'post' ) )
		{
			$type = 'page';
		}
		
		//Update the post
		$dbarr = array(
			"id_sub_category" 	=> 0,
			"id_category" 	 	=> $targetId,
			"post_type" 		=> $type
		);

		$db->update( POSTS )->where( 'id_post', $p['id_post'] )->set( $dbarr );
	}
	
	return array( 'status' => 'ok' );
}

#####################################################
#
# Counts The Posts/Pages
#
#####################################################
function CountPosts( $type = 'post', $lang = null, $category = null, $blog = null, $site = SITE_ID )
{
	$db = db();
	
	$q  = "(id_site = " . (int) $site . ") AND " . ( ( $type == 'all' ) ? "( post_type = 'post' OR post_type = 'page' )" : "(post_type = :type)" );
	$q .= ( !empty( $lang ) ? " AND (id_lang = " . (int) $lang . ")" : "" );
	$q .= ( !empty( $category ) ? " AND (id_category = " . (int) $category . ")" : "" );
	$q .= ( !empty( $blog ) ? " AND (id_blog = " . (int) $blog . ")" : "" );
	
	$binds = null;
	
	if ( $type != 'all' )
	{
		$binds = array( $type => ':type' );
	}
	
	$t = $db->from( null, 
	"SELECT count(id_post) AS total FROM `" . DB_PREFIX . POSTS . "` WHERE " . $q,
	$binds
	)->total();	
	
	return $t;
}

#####################################################
#
# Get the default category from this blog
#
#####################################################
function GetBlogDefaultCategory( $blogId, $langId )
{
	$db = db();
	
	$q = $db->from( 
	null, 
	"SELECT id
	FROM `" . DB_PREFIX . "categories`
	WHERE (id_lang = " . (int) $langId . ") AND (id_blog = " . (int) $blogId . ") AND (is_default = 1)"
	)->single();
	
	return ( $q ? $q['id'] : null );
}

#####################################################
#
# Image(s) Move function
#
#####################################################
function MoveInPostImages( $postId, $siteId )
{
	//We have nothing to do here, because the images are already at the parent site
	if ( $siteId == SITE_ID )
		return;
	
	$db = db();
	
	//Let's search for any images first
	$data = $db->from( 
	null, 
	"SELECT id_image, id_site
	FROM `" . DB_PREFIX . "images`
	WHERE (id_post = " . (int) $postId . ")"
	)->all();
	
	if ( empty( $data ) )
		return;

	//Check if these images exist in the target site
	foreach( $data as $im )
	{
		CheckImageExists( $im['id_image'], $siteId );
	}
}

#####################################################
#
# Image(s) Move function
#
#####################################################
function MovePostCoverImage( $postId, $siteId )
{
	$db = db();
	
	//Let's search for the image first
	$data = $db->from( 
	null, 
	"SELECT id_attach, image_id, user_id
	FROM `" . DB_PREFIX . "image_attachments`
	WHERE (post_id = " . (int) $postId . ")"
	)->single();

	if ( empty( $data ) )
		return;

	$userId = GetMemberRel( $data['user_id'], null, $siteId );

	//Update the relation for this image
	$db->update( 'image_attachments' )->where( 'id_attach', $data['id_attach'] )->set( 'user_id', $userId );

	//We have nothing to do here, because the image is already in the parent site
	if ( $siteId == SITE_ID )
		return;

	//Check if these images exist in the target site
	CheckImageExists( $data['image_id'], $siteId );
}

#####################################################
#
# Move Posts Tags
#
#####################################################
function MoveTags( $postId, $siteId )
{
	$db = db();

	$_tags = $db->from( 
	null, 
	"SELECT taxonomy_id, id_relation
	FROM `" . DB_PREFIX . "tags_relationships`
	WHERE (object_id = " . (int) $postId . ")"
	)->all();

	if ( $_tags )
	{
		foreach( $_tags as $_tag )
		{
			//First get the details from the DB
			$orTag = $db->from( 
			null, 
			"SELECT title, sef
			FROM `" . DB_PREFIX . "tags`
			WHERE (id = " . (int) $_tag['taxonomy_id'] . ")"
			)->single();
				
			//If the tag doesn't exist, delete its relation
			if ( !$orTag )
			{
				$db->delete( 'tags_relationships' )->where( "id_relation", $_tag['id_relation'] )->run();

				continue;
			}

			//Update the relation for this tag
			$db->update( 'tags_relationships' )->where( 'id_relation', $_tag['id_relation'] )->set( 'id_site', $siteId );
		}
	}
}

#####################################################
#
# Get The Remote Data function
#
#####################################################
function GetRemoteDB()
{
	if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'manage-own-posts' ) )
		return null;
	
	global $Admin;
	
	if ( $Admin->SiteIsSelfHosted() )
		return;
	
	$host = $Admin->SiteHost();
	
	$data = $Admin->Settings()::Get()['api_keys'];
	
	$data = Json( $data );
	
	if ( empty( $data ) )
		return;
	
	$siteId = $Admin->GetSite();

	$postsData = OpenFileDB( EXTERNAL_POSTS_FILE );
	$pagesData = OpenFileDB( EXTERNAL_PAGES_FILE );
	
	if ( $host == 'blogger' )
	{
		if ( !isset( $data['blogger'] ) || empty( $data['blogger']['api'] ) || empty( $data['blogger']['blog-id'] ) )
			return;
		
		$data = $data['blogger'];
		
		$url = 'https://www.googleapis.com/blogger/v3/blogs/' . $data['blog-id'] . '?key=' . $data['api'];
		
		$response = PingSite( $url );

		if ( empty( $response ) )
			return;
		
		$posts = ( ( !empty( $response['posts'] ) && isset( $response['posts']['totalItems'] ) ) ? $response['posts']['totalItems'] : null );
		$pages = ( ( !empty( $response['pages'] ) && isset( $response['pages']['totalItems'] ) ) ? $response['pages']['totalItems'] : null );
		
		$postsUrl = 'https://www.googleapis.com/blogger/v3/blogs/' . $data['blog-id'] . '/posts?key=' . $data['api'];
		$pagesUrl = 'https://www.googleapis.com/blogger/v3/blogs/' . $data['blog-id'] . '/pages?key=' . $data['api'];

		if ( $posts )
		{
			if ( $posts > 10 )
			{
				$postsNum = (int) ceil( $posts / 10 ); //Blogger gives 10 items per page
				
				for ( $i = 0; $i < $postsNum; $i++ )
				{
					$response = PingSite( $postsUrl );
			
					if ( empty( $response ) || empty( $response['items'] ) )
						break;
			
					$nextPage = ( ( isset( $response['nextPageToken'] ) && !empty( $response['nextPageToken'] ) ) ? $response['nextPageToken'] : null );
					
					foreach( $response['items'] as $item )
					{
						// Look if we already have this item
						if ( SearchPostByExtId( $item['id'] ) )
							continue;
						
						$postsData[$siteId][$item['id']] = $item;
					}

					if ( $nextPage )
					{
						$postsUrl = 'https://www.googleapis.com/blogger/v3/blogs/' . $data['blog-id'] . '/posts?key=' . $data['api'] . '&pageToken=' . $nextPage;
					}
				}
			}
			else
			{
				$response = PingSite( $postsUrl );
			
				if ( !empty( $response ) && !empty( $response['items'] ) )
				{
					foreach( $response['items'] as $item )
					{
						// Look if we already have this item
						if ( SearchPostByExtId( $item['id'] ) )
							continue;
						
						$postsData[$siteId][$item['id']] = $item;
					}
				}
			}
		}
		
		//Do the same for pages
		if ( $pages )
		{
			if ( $pages > 10 )
			{
				$pagesNum = (int) ceil( $pages / 10 );
				
				for ( $i = 0; $i < $pagesNum; $i++ )
				{
					$response = PingSite( $pagesUrl );
			
					if ( empty( $response ) || empty( $response['items'] ) )
						break;
			
					$nextPage = ( ( isset( $response['nextPageToken'] ) && !empty( $response['nextPageToken'] ) ) ? $response['nextPageToken'] : null );
			
					foreach( $response['items'] as $item )
					{
						// Look if we already have this item
						if ( SearchPostByExtId( $item['id'] ) )
							continue;
						
						$pagesData[$siteId][$item['id']] = $item;
					}
					
					if ( $nextPage )
					{
						$pagesUrl = 'https://www.googleapis.com/blogger/v3/blogs/' . $data['blog-id'] . '/posts?key=' . $data['api'] . '&pageToken=' . $nextPage;
					}
				}
			}
			else
			{
				$response = PingSite( $pagesUrl );
			
				if ( !empty( $response ) && !empty( $response['items'] ) )
				{
					foreach( $response['items'] as $item )
					{
						// Look if we already have this item
						if ( SearchPostByExtId( $item['id'] ) )
							continue;
						
						$pagesData[$siteId][$item['id']] = $item;
					}
				}
			}
		}
	}
	
	//Write the data
	WriteFileDB( $postsData, EXTERNAL_POSTS_FILE );
	WriteFileDB( $pagesData, EXTERNAL_PAGES_FILE );
}

#####################################################
#
# Get the Ext Id of a post function
#
#####################################################
function GetExtIdOfPost( $id )
{
	$db = db();

	$q = $db->from( 
	null, 
	"SELECT ext_id
	FROM `" . DB_PREFIX . "posts_data`
	WHERE (id_post = " . ( is_numeric( $id ) ? (int) $id : "'" . $id . "'" ) . ")"
	)->single();
	
	return $q;
}

#####################################################
#
# Search if the post exists in the DB function
#
#####################################################
function SearchPostByExtId( $id )
{
	$db = db();

	$q = $db->from( 
	null, 
	"SELECT id
	FROM `" . DB_PREFIX . "posts_data`
	WHERE (ext_id = " . ( is_numeric( $id ) ? (int) $id : "'" . $id . "'" ) . ")"
	)->single();
	
	return $q;
}

#####################################################
#
# Get The Posts for Admin Panel function
#
#####################################################
function GetAdminCachedPosts( $file, $siteId = SITE_ID )
{
	if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) )
		return null;
	
	global $Admin;
	
	if ( $Admin->SiteIsSelfHosted() || !file_exists( $file ) )
		return null;
	
	$db = OpenFileDB( $file );
	
	if ( empty( $db ) || !isset( $db[$siteId] ) )
		return array( 'posts' => null, 'totalItems' => 0 );
	
	$db = $db[$siteId];
	
	$numDbItems = count( $db );
	
	$data = array(
		'posts' 		=> $db,
		'totalItems' 	=> $numDbItems
	);
	
	unset( $db );
	
	return $data;
}

#####################################################
#
# Get single Attribute function
#
#####################################################
function ΑdminGetSinglePostAttribute( $id )
{
	global $Admin;
	
	$q = $Admin->db->from( 
	null, 
	"SELECT p.*, g.name as gn, b.name as bname, la.title as lt, tp.title as t, c.name as cat
	FROM `" . DB_PREFIX . "post_attributes` AS p
	LEFT JOIN `" . DB_PREFIX . "post_attr_group` AS g ON g.id = p.id_group
	LEFT JOIN `" . DB_PREFIX . "blogs` AS b ON b.id_blog = g.id_blog
	LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = g.id_lang
	LEFT JOIN `" . DB_PREFIX . "post_types` AS tp ON tp.id = g.id_custom_type
	LEFT JOIN `" . DB_PREFIX . "categories` AS c ON c.id = g.id_category
	WHERE (p.id = " . (int) $id . ") AND (g.id_lang = " . $Admin->GetLang() . ")"
	)->single();
	
	return $q;
}

#####################################################
#
# Edit Schema from the post editor function
#
#####################################################
function EditSchemaFromForm( $schemaData, $postId )
{
	if ( empty( $schemaData ) )
		return;
	
	$post = GetSinglePost( $postId, null, false );
	
	if ( empty( $post ) )
		return;	
	
	require_once ( FUNCTIONS_ROOT . 'schema-functions.php' );
	
	$db = db();
	
	foreach( $schemaData as $schemaId => $schemaFormData )
	{
		//Query: schema
		$schemaData = $db->from( 
		null, 
		"SELECT data
		FROM `" . DB_PREFIX . "schemas`
		WHERE (id = " . (int) $schemaId . ")"
		)->single();
			
		if ( !$schemaData )
			continue;
		
		$s = Json( $schemaData['data'] );
		
		$ready = SchemaDataToArray( $schemaFormData, $post );
		
		foreach( $schemaFormData as $id => $row )
		{			
			if ( $id == 'custom' )
			{
				if ( !empty( $row ) )
				{
					foreach( $row as $a => $r )
					{
						$s['custom-data'][$a] = $r;
					}
				}
			}
			
			else
			{
				$s['data'][$id] = $row;
			}
		}

		//Update the schema
		$dbarr = array(
			"data" 			=> json_encode( $s ),
			"fixed_data" 	=> json_encode( $ready, JSON_UNESCAPED_UNICODE )
		);
	
		$db->update( 'schemas' )->where( 'id', $schemaId )->set( $dbarr );
	}
}

#####################################################
#
# Get All Pages function
#
# Returns every page
#
#####################################################
function GetFullPages()
{
	global $Admin;
	
	$pages = array();

	$_langs = $Admin->db->from( 
	null, 
	"SELECT id, code, is_default, locale, title
	FROM `" . DB_PREFIX . "languages`
	WHERE (id_site = " . $Admin->GetSite() . ") AND (status = 'active')
	ORDER BY lang_order ASC"
	)->all();
	
	if ( !$_langs )
		return null;
	
	foreach( $_langs as $_lang )
	{
		$pages[$_lang['code']] = array(
				'name' => stripslashes( $_lang['title'] ),
				'id' => $_lang['id'],
				'type' => 'lang',
				'childs' => array()
		);
		
		//Grab the pages from this lang
		$_pages = $Admin->db->from( 
		null, 
		"SELECT id_post, title
		FROM `" . DB_PREFIX . POSTS . "`
		WHERE (id_site = " . $Admin->GetSite() . ") AND (id_lang = " . $_lang['id'] . " OR id_lang = 0)
		AND (post_type = 'page') AND (post_status = 'published')
		ORDER BY title ASC"
		)->all();

		if ( $_pages )
		{
			foreach( $_pages as $_page )
			{
				$pages[$_lang['code']]['childs'][$_page['id_post']] = array(
									'name' => stripslashes( $_page['title'] ),
									'id' => $_page['id_post'],
									'type' => 'page',
									'childs' => null
						
				);
			}
		}
	}

	unset( $_langs, $_pages );
	
	return $pages;	
}