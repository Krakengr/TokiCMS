<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Get the id of a member of the target site
#
#####################################################
function GetMemberRel( $id, $site = null, $targetSite )
{
	$db = db();
	
	$userId = 1;

	//If the target site is the default site, then it's easy to look up for the ID
	if ( $site && ( $site == SITE_ID ) )
	{
		$query = "SELECT id_member
		FROM `" . DB_PREFIX . "members_relationships`
		WHERE (id_cloned_member = " . $id . ")";
	
		//Query: relationship
		$q = $db->from( null, $query )->single();

		if ( $q )
		{
			$userId = $q['id_cloned_member'];
		}
	}
	
	else
	{
		$query = "SELECT id_cloned_member
		FROM `" . DB_PREFIX . "members_relationships`
		WHERE (id_member = " . $id . ")" . ( $targetSite ? " AND (id_site = " . $targetSite . ")" : "" );
	
		//Query: relationship
		$q = $db->from( null, $query )->single();

		if ( $q )
		{
			$userId = $q['id_cloned_member'];
		}
	}

	return $userId;
}

#####################################################
#
# Get the id of a language based on the same code, returns the default otherwise
#
#####################################################
function GetSiteLang( $lang, $site, $copy = true )
{
	$db = db();
	
	//Query: language
	$q = $db->from( null, "
	SELECT *
	FROM `" . DB_PREFIX . "languages`
	WHERE (id = " . $lang . ")"
	)->single();
	
	if ( !$q )
		return null;
	
	//Get the id for the target site
	$query = "SELECT id
	FROM `" . DB_PREFIX . "languages`
	WHERE (code = '" . $q['code'] . "') AND (id_site = " . $site . ")";
		
	//Query: language
	$la = $db->from( null, $query )->single();

	if ( $la )
	{
		return $la['id'];
	}
	
	//This language doesn't exist, copy it
	if ( $copy )
	{
		$dbarr = array(
			"id_site" 		=> $site,
			"code" 			=> $q['code'],
			"title" 		=> $q['title'],
			"locale" 		=> $q['locale'],
			"direction" 	=> $q['direction'],
			"is_default" 	=> $q['is_default'],
			"flagicon" 		=> $q['flagicon'],
			"lang_order" 	=> $q['lang_order']
        );
            
		$put = $db->insert( 'languages' )->set( $dbarr );
			
		if ( $put )
		{
			$langId = $db->lastId();
			
			if ( $langId )
			{
				$query = "SELECT date_format,time_format
				FROM `" . DB_PREFIX . "languages_config`
				WHERE (id_lang = '" . $q['id'] . "')";
					
				//Query: config
				$la = $db->from( null, $query )->single();

				if ( $la )
				{
					$dbarr = array(
						"date_format" 	=> $la['date_format'],
						"time_format" 	=> $la['time_format'],
						"id_lang" 		=> $langId
					);
						
					$q = $db->insert( 'languages_config' )->set( $dbarr, null, true );

					return $q;
				}
			}
		}
	}
	
	//Target site doesn't have this language, return the default instead
	$query = "SELECT id
	FROM `" . DB_PREFIX . "languages`
	WHERE (is_default = 1) AND (id_site = " . $site . ")";

	//Query: language
	$q = $db->from( null, $query )->single();
	
	return ( $q ? $q['id'] : null );
}

#####################################################
#
# Get single email
#
#####################################################
function GetSingleEmail( $id, $siteId = null, $isReply = false )
{
	global $Admin;
	
	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );
	
	$userId = $Admin->UserID();
	
	//Query: mail
	$q = $Admin->db->from( null, "
	SELECT *
	FROM `" . DB_PREFIX . "mails`
	WHERE (id = " . (int) $id . ") AND (id_site = " . $siteId . ")"
	)->single();

	if ( !$q )
	{
		return null;
	}
	
	//Query: replies
	$r = $Admin->db->from( null, "
	SELECT added_time
	FROM `" . DB_PREFIX . "mail_replies`
	WHERE (id_mail = " . $q['id'] . ") AND (id_member = " . $userId . ")"
	)->single();
	
	if ( $isReply )
	{
		//Query: reply
		$rp = $Admin->db->from( null, "
		SELECT post
		FROM `" . DB_PREFIX . "mails`
		WHERE (id_parent = " . $q['id'] . ") AND (id_member = " . $userId . ")"
		)->single();
	}
	
	//Query: mails forward
	$f = $Admin->db->from( null, "
	SELECT email, added_time
	FROM `" . DB_PREFIX . "mail_forward`
	WHERE (id_mail = " . $q['id'] . ") AND (id_member = " . $userId . ")"
	)->single();

	$data = array(
		'id' 				=> $q['id'],
		'name' 				=> StripContent( $q['name'] ),
		'subject' 			=> StripContent( $q['subject'] ),
		'email' 			=> StripContent( $q['email'] ),
		'post' 				=> ( ( $isReply && $rp ) ? StripContent( $rp['post'] ) : StripContent( $q['post'] ) ),
		'status' 			=> $q['status'],
		'default_status' 	=> $q['default_status'],
		'replied' 			=> ( $r ? postDate( $r['added_time'] ) : null ),
		'forwarded' 		=> ( $f ? array( 'time' => $f['added_time'], 'email' => StripContent( $f['email'] ) ) : array() ),
		'timeRaw' 			=> $q['added_time'],
		'time' 				=> postDate( $q['added_time'] ),
		'timeNice' 			=> niceTime( $q['added_time'] )
	);

	return $data;
}

#####################################################
#
# Counts the total unread emails
#
#####################################################
function AdminTotalEmailsCount( $userId = null, $siteId = null )
{
	global $Admin;
	
	$userId = ( $userId ? $userId : $Admin->UserID() );
	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );

	$tmp = $Admin->db->from( null, 
	"SELECT COUNT(id) as total
	FROM `" . DB_PREFIX . "mails`
	WHERE 1=1 AND (id_site = " . $siteId . ") AND (status = 'inbox' OR status = 'junk') AND NOT EXISTS (SELECT * FROM " . DB_PREFIX . "log_emails as lo WHERE lo.id_mail = id AND lo.id_member = " . $userId . " AND lo.id_site = " . $siteId . ")"
	)->total();

	return ( $tmp ? $tmp : 0 );
}

#####################################################
#
# Counts the unread emails for the email page
#
#####################################################
function AdminEmailsCounts( $userId = null, $siteId = null )
{
	global $Admin;
	
	$userId = ( $userId ? $userId : $Admin->UserID() );
	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );
	
	$data = array(
		'total' => 0,
		'inbox' => 0,
		'junk'  => 0,
	);
	
	$tmp = $Admin->db->from( null, 
	"SELECT COUNT(id) as total
	FROM `" . DB_PREFIX . "mails`
	WHERE (id_site = " . $siteId . ") AND (status = 'inbox') AND NOT EXISTS (SELECT * FROM " . DB_PREFIX . "log_emails as lo WHERE lo.id_mail = id AND lo.id_member = " . $userId . " AND lo.id_site = " . $siteId . ")"
	)->total();

	if ( $tmp )
	{
		$data['total'] = $tmp;
		$data['inbox'] = $tmp;
	}
	
	$tmp = $Admin->db->from( null, 
	"SELECT COUNT(id) as total
	FROM `" . DB_PREFIX . "mails`
	WHERE (id_site = " . $siteId . ") AND (status = 'junk') AND NOT EXISTS (SELECT * FROM " . DB_PREFIX . "log_emails as lo WHERE lo.id_mail = id AND lo.id_member = " . $userId . " AND lo.id_site = " . $siteId . ")"
	)->total();
	
	if ( $tmp )
	{
		$data['total'] += $tmp;
		$data['junk']   = $tmp;
	}

	return $data;
}

#####################################################
#
# Counts the admin logs function
#
#####################################################
function AdminLogCounts( $userId = null, $siteId = null, $langId = null, $blogId = null, $showAll = null )
{
	global $Admin;
	
	$userId = ( $userId ? $userId : $Admin->UserID() );
	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );
	$langId = ( $langId ? $langId : $Admin->GetLang() );
	$blogId = ( $blogId ? $blogId : $Admin->GetBlog() );
	
	$showAll = ( ( is_null( $showAll ) && ( $siteId == SITE_ID ) ) ? $Admin->Settings()::IsTrue( 'parent_site_shows_everything' ) : ( !is_null( $showAll ) ? $showAll : false ) );
	
	$data = array(
		'totalNotes' => 0
	);
	
	$query = "
	SELECT co.id, co.name, co.url as cu, co.email, co.comment, co.added_time, co.status, co.id_parent, co.ip,
	p.sef AS sef, p.id_post, p.title AS tl, b.sef as blog_sef, b.name as blog_name, COALESCE(u.real_name, u.user_name, NULL) as user_name, u.image_data, la.code as ls, la.title as lt, la.locale as ll, la.flagicon, s.url, s.title as sna
	FROM `" . DB_PREFIX . "comments` AS co
	LEFT JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = co.id_blog
	LEFT JOIN `" . DB_PREFIX . POSTS . "` as p ON p.id_post = co.id_post
	LEFT JOIN `" . DB_PREFIX . USERS . "` as u ON u.id_member = co.user_id
	INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = co.id_lang
	INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = co.id_site
	WHERE " . ( !$showAll ? "(co.id_lang = " . (int) $langId . ") AND (co.id_site = " . (int) $siteId . ") AND (co.id_blog = " . (int) $blogId . ") AND " : '' ) . "(co.status = 'approved' OR co.status = 'pending') AND NOT EXISTS (SELECT * FROM " . DB_PREFIX . "log_comments as lo WHERE lo.id_comment = co.id AND lo.id_member = " . (int) $userId . ")";

	//Query: comments
	$q = $Admin->db->from( null, $query )->all();
	
	if ( $q )
	{
		$num = count( $q );
		
		$data['totalNotes'] += $num;
		
		$data['comments'] = array(
			'num' => count( $q ),
			'data' => $q
		);
	}
	else
	{
		$data['comments'] = array(
			'num' => 0,
			'data' => null
		);
	}
	
	if ( IsAllowedTo( 'admin-site' ) )
	{
		$query = "
		SELECT lo.id, lo.user_id, lo.title, lo.descr, lo.added_time, lo.ip, lo.type, COALESCE(u.real_name, u.user_name, NULL) as user_name, s.url, s.title as sna
		FROM `" . DB_PREFIX . "logs` AS lo
		LEFT JOIN `" . DB_PREFIX . USERS . "` as u ON u.id_member = lo.user_id
		LEFT JOIN `" . DB_PREFIX . "sites` as s ON s.id = lo.id_site
		WHERE " . ( !$showAll ? "lo.id_site = " . $siteId . " AND " : '' ) . "NOT EXISTS (SELECT * FROM " . DB_PREFIX . "log_log as lg WHERE lg.id_log = lo.id AND lg.id_member = " . $userId . ")
		ORDER BY lo.id DESC";

		//Query: logs
		$q = $Admin->db->from( null, $query )->all();

		if ( $q )
		{
			$num = count( $q );
			
			$data['totalNotes'] += $num;

			$data['logs'] = array(
				'num' => count( $q ),
				'data' => $q
			);
		}
		else
		{
			$data['logs'] = array(
				'num' => 0,
				'data' => null
			);
		}
	
	}

	return $data;
}

#####################################################
#
# Get user images function
#
#####################################################
function AdminGetUserImages( $userID, $siteID = null, $empty = true, $single = false )
{
	global $Admin;
	
	$siteID = ( !$siteID ? $Admin->GetSite() : $siteID );
	
	$siteUrl = null;
	
	$share = $Admin->ImageUpladDir( $siteID );
	
	if ( !empty( $share ) && isset( $share['share'] ) && $share['share'] )
	{
		$siteUrl = $share['html'];
	}

	//Query: images
	$img = $Admin->db->from( null, "
	SELECT *
	FROM `" . DB_PREFIX . "images`
	WHERE (id_member = " . $userID . ") AND (img_type = 'user')"
	)->single();
	
	$data = array();
	
	if ( $empty && empty( $img ) )
	{
		return $data;
	}

	if ( empty( $img ) )
	{
		$data['default'] = array(
					'imageId' => 0,
					'imageFilename' => '',
					'imageTitle' => '',
					'imageWidth' => 0,
					'imageHeight' => 0,
					'imageSize' => 0,
					'imageCaption' => '',
					'mimeType' => '',
					'imageAlt' => '',
					'imageDescr' => '',
					'imageUrl' => 'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII='
		);
	
		return $data;
	}
	
	$data['default'] = array(
					'imageId' => $img['id_image'],
					'imageFilename' => stripslashes( $img['filename'] ),
					'imageTitle' => stripslashes( $img['title'] ),
					'imageWidth' => $img['width'],
					'imageHeight' => $img['height'],
					'imageSize' => $img['size'],
					'imageCaption' => '',
					'mimeType' => $img['mime_type'],
					'imageAlt' => '',
					'imageDescr' => '',
					'imageUrl' => FolderUrlByDate( $img['added_time'], $siteUrl ) . stripslashes( $img['filename'] )
	);
	
	if ( !$single )
	{
		$imgs = $Admin->db->from( null, "
		SELECT id_image, filename, width, height, size
		FROM `" . DB_PREFIX . "images`
		WHERE (id_parent = " . $img['id_image'] . ") AND (img_type = 'user')
		ORDER BY width ASC"
		)->all();
		
		if ( $imgs )
		{
			$addedTime = $img['added_time'];
			
			foreach( $imgs as $_img )
			{
				$data[$_img['width']] = array(
								'imageId' => $_img['id_image'],
								'imageFilename' => stripslashes( $_img['filename'] ),
								'imageTitle' => stripslashes( $img['title'] ),
								'imageWidth' => $_img['width'],
								'imageHeight' => $_img['height'],
								'imageSize' => $_img['size'],
								'imageCaption' => '',
								'mimeType' => $img['mime_type'],
								'imageAlt' => '',
								'imageDescr' => '',
								'imageUrl' => FolderUrlByDate( $addedTime, $siteUrl ) . stripslashes( $_img['filename'] )
					);
			}
		}
	}

	return $data;
}

#####################################################
#
# Get single Stores Attribute function
#
#####################################################
function ΑdminGetSingleStoresAttribute( $id )
{
	global $Admin;
	
	$tmp = $Admin->db->from( null, "
	SELECT p.*, la.title as lt
	FROM `" . DB_PREFIX . "stores_attributes` as p
	LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = p.id_lang
	WHERE (p.id = " . $id . ") AND (p.id_lang = " . $Admin->GetLang() . ")"
	)->single();

	return $tmp;
}

#####################################################
#
# Check if the image exists function
#
#####################################################
function CheckImageExists( $imageID, $siteId = null )
{
	global $Admin;
		
	//Check if we have this image
	$imgData = $Admin->db->from( null, "
	SELECT id_lang, id_site, filename, added_time, mime_type
	FROM `" . DB_PREFIX . "images`
	WHERE (id_image = " . $imageID . ")"
	)->single();

	if ( !$imgData )
		return;
	
	$local = $Admin->ImageUpladDir( SITE_ID );

	$root = ( !empty( $local ) ? $local['root'] : null );
	
	$folder = FolderRootByDate( $imgData['added_time'], $root );
	
	//Set the image's url
	$imgUrl = ( !empty( $local ) ? FolderUrlByDate( $imgData['added_time'], $local['html'] ) : FolderUrlByDate( $imgData['added_time'] ) ) . $imgData['filename'];
	
	$imgRoot = $folder . $imgData['filename'];
	
	//There is nothing to do here
	if ( !file_exists( $imgRoot ) )
		return;
	
	$siteId = ( $siteId ? $siteId : $imgData['id_site'] );
	
	if ( $siteId == SITE_ID )
		return;

	$share = $Admin->PingChildSite( 'sync', 'image', null, $siteId, $imgUrl, $imgData['added_time'] );
	
	if ( !empty( $share ) && ( $imgData['mime_type'] == 'image' ) && isset( $share['message'] ) && ( $share['message'] == 'Success' ) )
	{
		$childs = GetChildImages( $imageID );
		
		if ( $childs )
		{
			foreach( $childs as $child )
			{
				$imgUrl = ( !empty( $local ) ? FolderUrlByDate( $imgData['added_time'], $local['html'] ) : FolderUrlByDate( $imgData['added_time'] ) ) . $child['filename'];
				
				$Admin->PingChildSite( 'sync', 'image', null, $siteId, $imgUrl, $imgData['added_time'] );
			}
		}
	}
}

#####################################################
#
# Get All Ads function
#
# Returns every ad based on which language they are enabled
#
#####################################################
function GetFullAds()
{
	global $Admin;
	
	$ads = array();
	
	$_langs = $Admin->db->from( null, "
	SELECT id, code, is_default, locale, title
	FROM `" . DB_PREFIX . "languages`
	WHERE (id_site = " . $Admin->GetSite() . ") AND (status = 'active')
	ORDER BY lang_order ASC"
	)->all();
	
	if ( !$_langs )
		return null;
	
	foreach( $_langs as $_lang )
	{
		$ads[$_lang['code']] = array(
			'name' => stripslashes( $_lang['title'] ),
			'id' => $_lang['id'],
			'type' => 'lang',
			'childs' => array()
		);
		
		//Grab the ads from this lang
		$_ads = $Admin->db->from( null, "
		SELECT id, title
		FROM `" . DB_PREFIX . "ads`
		WHERE (id_site = " . $Admin->GetSite() . ") AND (disabled = 0) AND (id_lang = " . $_lang['id'] . " OR id_lang = 0)
		ORDER BY title ASC"
		)->all();

		if ( $_ads )
		{
			foreach( $_ads as $_ad )
			{
				$ads[$_lang['code']]['childs'][$_ad['id']] = array(
						'name' => stripslashes( $_ad['title'] ),
						'id' => $_ad['id'],
						'type' => 'ad',
						'childs' => null
						
				);
			}
		}
	}

	unset( $_ads, $_ad );
	
	return $ads;	
}

#####################################################
#
# Get All Blogs function
#
# Returns every blog based on which language they are enabled
#
#####################################################
function GetFullBlogs()
{
	global $Admin;
	
	$blogs = array();
	
	$_langs = $Admin->db->from( null, "
	SELECT id, code, is_default, locale, title
	FROM `" . DB_PREFIX . "languages`
	WHERE (id_site = " . $Admin->GetSite() . ") AND (status = 'active')
	ORDER BY lang_order ASC"
	)->all();
	
	if ( !$_langs )
		return null;
	
	foreach( $_langs as $_lang )
	{
		$blogs[$_lang['code']] = array(
					'name' => stripslashes( $_lang['title'] ),
					'id' => $_lang['id'],
					'type' => 'lang',
					'childs' => array()
		);
		
		//Grab the blogs from this lang
		$_blogs = $Admin->db->from( null, "
		SELECT id_blog, name
		FROM `" . DB_PREFIX . "blogs`
		WHERE (id_site = " . $Admin->GetSite() . ") AND (disabled = 0) AND (id_lang = " . $_lang['id'] . " OR id_lang = 0)
		ORDER BY name ASC"
		)->all();

		if ( $_blogs )
		{
			foreach( $_blogs as $_blog )
			{
				$blogs[$_lang['code']]['childs'][$_blog['id_blog']] = array(
							'name' => stripslashes( $_blog['name'] ),
							'id' => $_blog['id_blog'],
							'type' => 'blog',
							'childs' => null
						
				);
			}
		}
	}

	unset( $_blogs, $_blog );
	
	return $blogs;	
}

#####################################################
#
# Get Full Categories List function
#
# Returns every category based on its lang and blog
#
#####################################################
function GetFullCats()
{
	global $Admin;
	
	$_categories = array();
		
	$_langs = $Admin->db->from( null, "
	SELECT id, code, is_default, locale, title
	FROM `" . DB_PREFIX . "languages`
	WHERE (id_site = " . $Admin->GetSite() . ") AND (status = 'active')
	ORDER BY lang_order ASC"
	)->all();
	
	if ( $_langs )
	{
		foreach( $_langs as $_lang )
		{
			$_categories[$_lang['code']] = array(
					'name' => stripslashes( $_lang['title'] ),
					'id' => $_lang['id'],
					'type' => 'lang',
					'childs' => array()
				
				
			);
		}
	
		//If the site has multiblog enabled, we need a bit more work
		if ( IsTrue( $Admin->settings::Site()['enable_multiblog'] ) )
		{
			foreach( $_langs as $_lang )
			{
				//We need the blogs
				$_blogs = $Admin->db->from( null, "
				SELECT id_blog, name
				FROM `" . DB_PREFIX . "blogs`
				WHERE (id_lang = " . $_lang['id'] . ") AND (id_site = " . $Admin->GetSite() . ")
				ORDER BY name ASC"
				)->all();
		
				if ( $_blogs )
				{
					foreach( $_blogs as $_blog )
					{
						$_categories[$_lang['code']]['childs'][$_blog['id_blog']] = array(
													'name' => stripslashes( $_blog['name'] ),
													'id' => $_blog['id_blog'],
													'type' => 'blog',
													'childs' => array()
						
						);
						
						$cats = $Admin->db->from( null, "
						SELECT id, name
						FROM `" . DB_PREFIX . "categories`
						WHERE (id_parent = 0) AND (id_lang = " . $_lang['id'] . ") AND (id_blog = " . $_blog['id_blog'] . ")
						ORDER BY name ASC"
						)->all();
								
						if ( $cats )
						{
							foreach ( $cats as $cat )
							{
								$_categories[$_lang['code']]['childs'][$_blog['id_blog']]['childs'][$cat['id']] = array(
												'name' => stripslashes( $cat['name'] ),
												'id' => $cat['id'],
												'type' => 'cat',
												'childs' => array()
						
								);
								
								$subCats = $Admin->db->from( null, "
								SELECT id, name
								FROM `" . DB_PREFIX . "categories`
								WHERE (id_parent = " . $cat['id'] . ")
								ORDER BY name ASC"
								)->all();
						
								if ( $subCats )
								{
									foreach ( $subCats as $sub )
									{
										$_categories[$_lang['code']]['childs'][$_blog['id_blog']]['childs'][$cat['id']]['childs'][$sub['id']] = array(
												'name' => stripslashes( $sub['name'] ),
												'type' => 'sub',
												'id' => $sub['id'],
										);
									}
								}
							}
						}
					}
				}
				
				$_cats = $Admin->db->from( null, "
				SELECT id, name
				FROM `" . DB_PREFIX . "categories`
				WHERE (id_parent = 0) AND (id_lang = " . $_lang['id'] . ") AND (id_blog = 0)
				ORDER BY name ASC"
				)->all();
				
				$_categories[$_lang['code']]['childs']['orphanCats'] = array(
																		'name' => $L['orphan-categories'],
																		'type' => 'blog',
																		'id' => '0',
																		'childs' => array()
						
				);
				
				if ( $_cats )
				{
					foreach ( $_cats as $_cat )
					{
						$_categories[$_lang['code']]['childs']['orphanCats']['childs'][$_cat['id']] = array(
										'name' => stripslashes( $_cat['name'] ),
										'type' => 'cat',
										'id' => $_cat['id'],
										'childs' => array()
						
						);
						
						$subCats = $Admin->db->from( null, "
						SELECT id, name
						FROM `" . DB_PREFIX . "categories`
						WHERE (id_parent = " . $_cat['id'] . ")
						ORDER BY name ASC"
						)->all();
						
						if ( $subCats )
						{
							foreach ( $subCats as $sub )
							{
								$_categories[$_lang['code']]['childs']['orphanCats']['childs']['0']['childs'][$sub['id']] = array(
											'name' => stripslashes( $sub['name'] ),
											'type' => 'sub',
											'id' => $sub['id']
								);
							}
						}
					}
				}
			}
			
			unset( $_blogs );
		}
		
		else
		{
			foreach( $_langs as $_lang )
			{
				$_cats = $Admin->db->from( null, "
				SELECT id, name
				FROM `" . DB_PREFIX . "categories`
				WHERE (id_parent = 0) AND (id_lang = " . $_lang['id'] . ") AND (id_blog = 0)
				ORDER BY name ASC"
				)->all();
	
				$_categories[$_lang['code']]['childs']['orphanCats'] = array(
									'name' => $L['orphan-categories'],
									'type' => 'blog',
									'id' => '0',
									'childs' => array()
						
				);
				
				if ( $_cats )
				{
					foreach ( $_cats as $_cat )
					{
						$_categories[$_lang['code']]['childs']['orphanCats']['childs'][$_cat['id']] = array(
											'name' => stripslashes( $_cat['name'] ),
											'type' => 'cat',
											'id' => $_cat['id'],
											'childs' => array()
						
						);
						
						$subCats = $Admin->db->from( null, "
						SELECT id, name
						FROM `" . DB_PREFIX . "categories`
						WHERE (id_parent = " . $_cat['id'] . ")
						ORDER BY name ASC"
						)->all();

						if ( $subCats )
						{
							foreach ( $subCats as $sub )
							{
								$_categories[$_lang['code']]['childs']['orphanCats']['childs']['0']['childs'][$sub['id']] = array(
												'name' => stripslashes( $sub['name'] ),
												'type' => 'sub',
												'id' => $sub['id']
								);
							}
						}
					}
				}
			}
		}
	}

	unset( $_langs, $subCats, $_cats );
	
	return $_categories;
}



#####################################################
#
# Notify Search Engines function
#
#####################################################
function AdminPingSearchEngines( $sitemapUrl )
{
	global $Admin;
	
	if ( !$Admin->Settings()::IsTrue( 'notify_search_engines' ) )
		return;
	
	if ( $Admin->IsDefaultSite() )
	{
		$siteMapUrl = SITE_URL . 'sitemap_index.xml';
	}
	
	else
	{
		$site = $Admin->db->from( null, "
		SELECT url
		FROM `" . DB_PREFIX . "sites`
		WHERE (id = " . $Admin->GetSite() . ")"
		)->single();
		
		if ( !$site )
			return;
		
		$siteMapUrl = $site['url'] . 'sitemap_index.xml';
	}

	return SitemapSubmit( $siteMapUrl );
}

#####################################################
#
# Get Site's URL Function
#
#####################################################
function GetDefaultSiteUrl()
{
	$db = db();
	
	$site = $db->from( null, "
	SELECT url
	FROM `" . DB_PREFIX . "sites`
	WHERE (is_primary = 1)"
	)->single();

	return ( $site ? $site['url'] : null );
}

#####################################################
#
# Edit Single Post Slug function
#
#####################################################
function AdminEditPostSlug( $id, $slug )
{
	global $Admin;
	
	if ( empty( $slug ) || !is_numeric( $id ) )
		return $slug;
	
	//Get the site id of the post	
	//Query: post
	$p = $Admin->db->from( null, "
	SELECT id_site, id_lang
	FROM `" . DB_PREFIX . POSTS . "`
	WHERE (id_post = " . $id . ")"
	)->single();
	
	$siteId  = ( $p ? $p['id_site'] : null );
	$langKey = ( $p ? GetLangKey( $p['id_lang'] ) : $Admin->LangKey() );

	$newSlug = SetShortSef( POSTS, 'id_post', 'sef', CreateSlug( $slug, true ), $id, $siteId );

	$q = $Admin->db->update( POSTS )->where( 'id_post', $id )->set( "sef", $newSlug );
	
	if ( $q )
	{
		$cacheFile = PostCacheFile( $slug, null, $langKey );
		
		if ( file_exists( $cacheFile ) )
			@unlink( $cacheFile );
		
		return $newSlug;
	}
	
	//We have to return something
	return $slug;
}

#####################################################
#
# Get Categories function (Single Post)
#
#####################################################
function AdminCategoriesPost( $langId = null, $blogId = null, $siteId = null )
{
	global $Admin;
	
	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );
	$langId = ( $langId ? $langId : $Admin->GetLang() );
	$blogId = ( $blogId ? $blogId : $Admin->GetBlog() );
	
	//Query: categories
	$cats = $Admin->db->from( null, "
	SELECT *
	FROM `" . DB_PREFIX . "categories`
	WHERE (id_parent = 0) AND (id_site = " . $siteId . ") AND (id_lang = " . $langId . ") AND (id_blog = " . $blogId . ")
	ORDER BY name ASC"
	)->all();
	
	$data = array();
	
	if ( !empty( $cats ) )
	{
		foreach( $cats as $cat )
		{
			$data[$cat['id']] = array(
						'id' => $cat['id'],
						'name' => stripslashes( $cat['name'] ),
						'default' => $cat['is_default'],
						'description' => stripslashes( $cat['descr'] ),
						'childs' => array()
			);
					
			//Get the subcategories, if any
			//Query: categories
			$subs = $Admin->db->from( null, "
			SELECT *
			FROM `" . DB_PREFIX . "categories`
			WHERE (id_parent = " . $cat['id'] . ")
			ORDER BY name ASC"
			)->all();
			
			if ( $subs )
			{
				foreach ( $subs as $sub )
				{
					$data[$cat['id']]['childs'][$sub['id']] = array(
								'id' => $sub['id'],
								'name' => stripslashes( $sub['name'] ),
								'description' => stripslashes( $sub['descr'] )
					);
				}
			}
		}
	}

	return $data;
}

#####################################################
#
# Build a post's URL Function
#
#####################################################
function AdminBuildPostUrl( $data, $skipSlug = false )
{
	if ( empty( $data ) )
		return false;
	
	global $Admin;
	
	$langKey = $Admin->LangKey();
	
	$url = $data['site']['url'];
	
	//Add the lang slug
	if ( 
		$Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) && !empty( $data['languageKey'] )
		&& 
		( !$Admin->Settings()::IsTrue( 'hide_default_lang_slug' ) || ( $Admin->Settings()::IsTrue( 'hide_default_lang_slug' ) && ( $data['language']['key'] != $langKey ) ) )
	)
		$url .= $data['language']['key'] . PS;

	//Add the blog slug
	$url .= ( $Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) && !empty( $data['blog']['sef'] ) ? $data['blog']['sef'] . PS : '' );
	
	//Add the posts filter
	if ( $data['postType'] != 'page' )
		$url .= ltrim( $Admin->Settings()::Get()['posts_filter'], '/' );
	
	if ( !$skipSlug )
	{
		//Add the post slug
		$url .= $data['sef'] . PS;
	}

	return $url;
}

#####################################################
#
# Get the post schemas function
#
#####################################################
function AdminGetPostSchemas( $Post )
{
	global $Admin;
	
	$where = "(";
	
	$where .= " enable_on = '" . ( $Post->PostType() == 'post'? 'all-posts' : 'all-pages' ) . "'";
	
	if ( $Admin->MultiBlog() && !empty( $Post->Blog()->id ) && ( $Post->Blog()->id > 0 ) )
		$where .= " OR ( enable_on = 'blog' AND enable_on_id = '" . $Post->Blog()->id . "' )";
	
	else
		$where .= " OR enable_on = '" . ( $Post->PostType() == 'post' ? 'orphan-posts' : 'orphan-pages' ) . "'";
	
	if ( $Admin->MultiLang() )
		$where .= " OR ( enable_on = 'lang' AND enable_on_id = '" . $Post->Language()->id . "' )";
	
	$where .= ")";
	
	$where .= " AND NOT (";
	
	$where .= " exclude_from = '" . ( $Post->PostType() == 'post'? 'all-posts' : 'all-pages' ) . "'";
	
	if ( $Admin->MultiBlog() && !empty( $Post->Blog()->id ) && ( $Post->Blog()->id > 0 ) )
		$where .= " OR ( exclude_from = 'blog' AND exclude_from_id = '" . $Post->Blog()->id . "' )";
	
	else
		$where .= " OR exclude_from = '" . ( $Post->PostType() == 'post'? 'orphan-posts' : 'orphan-pages' ) . "'";
	
	if ( $Admin->MultiLang() )
		$where .= " OR ( exclude_from = 'lang' AND exclude_from_id = '" . $Post->Language()->id . "' )";
	
	$where .= ")";
	
	//Query: schemas
	$tmp = $Admin->db->from( null, "
	SELECT id, title, type, data
	FROM `" . DB_PREFIX . "schemas`
	WHERE (id_site = " . $Admin->GetSite() . ") AND " . $where
	)->all();
	
	return $tmp;
}

#####################################################
#
# Get schemas function
#
#####################################################
function Schemas()
{
	global $Admin;
	
	$s = array();

	//Query: schemas
	$data = $Admin->db->from( null, "
	SELECT *
	FROM `" . DB_PREFIX . "schemas`
	WHERE (id_site = " . $Admin->GetSite() . ")
	ORDER BY title ASC"
	)->all();

	if ( $data )
	{
		foreach( $data as $row )
			$s[] = $row;
	}
	
	return $s;
}

#####################################################
#
# Get Admin Single Filter function
#
#####################################################
function AdminFilter( $id )
{
	$db = db();
	
	$g = $db->from( null, "
	SELECT id, name, group_data, group_order
	FROM `" . DB_PREFIX . "filter_group`
	WHERE (id = " . $id . ")"
	)->single();
		
	if ( !$g )
		return null;
		
	$s = array();
		
	$d = $db->from( null, "
	SELECT id, filter_name, trans_data, filter_order
	FROM `" . DB_PREFIX . "filters_data`
	WHERE (id_group = " . $g['id'] . ")
	ORDER BY filter_order ASC"
	)->all();

	$s = array(
			'id' => $g['id'],
			'name' => $g['name'],
			'order' => $g['group_order'],
			'groupData' => Json( $g['group_data'] ),
			'trans' => ( !empty( $g['trans_data'] ) ? Json( $g['trans_data'] ) : null ),
			'data' => array()
	);

	if ( $d )
	{
		foreach ( $d as $_d )
		{
			$s['data'][] = array(
					'id' => $_d['id'],
					'name' => $_d['filter_name'],
					'order' => $_d['filter_order'],
					'trans' => ( !empty( $_d['trans_data'] ) ? Json( $_d['trans_data'] ) : null )
			);
		}
	}
	
	return $s;
}

#####################################################
#
# Get Admin Filters function
#
#####################################################
function AdminFilters( $siteId, $keysOnly = false, $cache = true )
{
	$cacheFile = CacheFileName( 'admin-filters', null, null, null, null, null, null, $siteId );
		
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$s = ReadCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		//Query: filter group
		$g = $db->from( 
		null, 
		"SELECT id, name, group_data, group_order
		FROM `" . DB_PREFIX . "filter_group`
		WHERE (id_site = " . (int) $siteId . ")
		ORDER BY group_order ASC"
		)->all();
		
		if ( !$g )
			return null;

		$s = array();
		
		foreach( $g as $_g )
		{
			$d = $db->from( 
			null, 
			"SELECT filter_name, filter_order, trans_data
			FROM `" . DB_PREFIX . "filters_data`
			WHERE (id_group = " . $_g['id'] . ")
			ORDER BY filter_order ASC"
			)->all();

			$s[$_g['id']] = array(
						'name' => $_g['name'],
						'order' => $_g['group_order'],
						'groupData' => Json( $_g['group_data'] ),
						'trans' => ( !empty( $_g['trans_data'] ) ? Json( $_g['trans_data'] ) : null ),
						'data' => array()
			);
			
			if ( $d )
			{
				foreach ( $d as $_d )
				{
					$s[$_g['id']]['data'] = array(
							'name' => $_d['filter_name'],
							'order' => $_d['filter_order'],
							'trans' => ( !empty( $_d['trans_data'] ) ? Json( $_d['trans_data'] ) : null )
					);
				}
			}
		}
		
		if ( $cache )
			WriteOtherCacheFile( $s, $cacheFile );
	}
	
	return ( $keysOnly ? array_keys( $s ) : $s );
}

#####################################################
#
# Get Admin Custom Types function
#
#####################################################
function AdminCustomTypes( $type = null, $site = null, $orderBy = 'title', $order = 'ASC' )
{
	global $Admin;
	
	$siteId = $Admin->GetSite();
	
	$binds = null;
	
	if ( $type )
	{
		$binds = array( $type => ':type' );
	}
	
	//Query: post types
	$g = $Admin->db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "post_types`
	WHERE (id_site = " . (int) $siteId . ")" . ( $type ? " AND category = :type" : "" ) . "
	ORDER BY " . $orderBy . " " . $order,
	$binds
	)->all();

	return $g;
}

#####################################################
#
# Sites function (Generic)
#
# This function returns the enabled sites from the DB
#
#####################################################
function Sites( $cache = true, $keysOnly = false )
{	
	$cacheFile = CacheFileName( 'admin-sites', null, null, null, null, null, null, SITE_ID );

	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$s = ReadCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		//Query: sites
		$q = $db->from( 
		null, 
		"SELECT *
		FROM `" . DB_PREFIX . "sites`
		ORDER BY title ASC"
		)->all();
		
		if ( !$q )
			return null;

		$s = array();
		
		foreach( $q as $_q )
		{
			$s[] = $_q;
		}
		
		if ( $cache )
			WriteOtherCacheFile( $s, $cacheFile );
	}

	return ( $keysOnly ? array_keys( $s ) : $s );
}

#####################################################
#
# Get Single Video Playlist function
#
#####################################################
function AdminGetVideoPlaylist( $id )
{
	$db = db();

	//Query: playlist
	$q = $db->from( 
	null, 
	"SELECT title, descr, added_time
	FROM `" . DB_PREFIX . "playlists`
	WHERE (id = " . (int) $id . ")"
	)->single();

	return $q;
}

#####################################################
#
# Get Playlists function
#
#####################################################
function Playlists()
{
	global $Admin;

	//Query: playlists
	$q = $Admin->db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "playlists`
	WHERE (id_site = " . (int) $Admin->GetSite() . ")"
	)->all();

	return $q;
}

#####################################################
#
# Get Stores's Attributes Data function
#
#####################################################
function AdminGetStoresAttributesData( $idAttr, $idStore = null )
{
	$db = db();

	//Query: store attribute data
	$q = $db->from( 
	null, 
	"SELECT id_attr, value
	FROM `" . DB_PREFIX . "store_attribute_data`
	WHERE (" . ( $idStore ? "id_store" : "id_attr" ) . " = " . (int) ( $idStore ? $idStore : $idAttr ) . ")"
	)->single();
	
	if ( !$q )
	{
		return null;
	}
	
	if ( $idStore )
	{
		$data = array();
		
		foreach ( $q as $p )
		{
			$data[$p['id_attr']] = array( 'value' => $p['value'] );
		}
		
		return $data;
	}
	
	return $q['value'];
}

#####################################################
#
# Get Post's Attributes Data function
#
#####################################################
function AdminGetPostAttributesData( $idAttr, $idPost )
{
	$db = db();

	//Query: post attribute data
	$q = $db->from( 
	null, 
	"SELECT id, value
	FROM `" . DB_PREFIX . "post_attribute_data`
	WHERE (id_post = " . (int) $idPost . ") AND (id_attr = " . (int) $idAttr . ")"
	)->all();

	return $q;
}

#####################################################
#
# Get Stores Attributes function
#
#####################################################
function AdminGetStoreAttributes( $siteId = null, $langId = null )
{
	global $Admin;
	
	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );
	$langId = ( $langId ? $langId : $Admin->GetLang() );
	
	//Query: store attributes
	$g = $Admin->db->from( 
	null, 
	"SELECT p.*, la.title AS lt
	FROM `" . DB_PREFIX . "stores_attributes` AS p
	LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = p.id_lang
	WHERE (p.id_site = " . $siteId . ") AND (p.id_lang = " . $langId . ")"
	)->all();

	return $g;
}

#####################################################
#
# Get All the Attributes function
#
#####################################################
function AdminGetAllAttributes( $siteId = null )
{
	global $Admin;
	
	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );
	
	//Query: post attributes
	$g = $Admin->db->from( 
	null, 
	"SELECT p.*, g.name AS gn, g.id_lang AS lan, b.name AS bname, la.title AS lt, ty.title AS t, c.name as cat
	FROM `" . DB_PREFIX . "post_attributes` AS p
	LEFT JOIN `" . DB_PREFIX . "post_attr_group` AS g ON g.id = p.id_group
	LEFT JOIN `" . DB_PREFIX . "blogs` AS b ON b.id_blog = g.id_blog
	LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = g.id_lang
	LEFT JOIN `" . DB_PREFIX . "post_types` AS ty ON ty.id = g.id_custom_type
	LEFT JOIN `" . DB_PREFIX . "categories` AS c ON c.id = g.id_category
	WHERE (g.id_site = " . $siteId . ")
	ORDER BY p.attr_order ASC"
	)->all();

	return $g;
}

#####################################################
#
# Get Attributes function
#
#####################################################
function AdminGetAttributes( $siteId = null, $langId = null, $blogId = null, $catId = null )
{
	global $Admin;

	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );
	$langId = ( $langId ? $langId : $Admin->GetLang() );
	$blogId = ( $blogId ? $blogId : $Admin->GetBlog() );
	
	//Query: attributes
	$tmp = $Admin->db->from( null, "
	SELECT p.*, g.name AS gn, b.name AS bname, la.title AS lt, ty.title AS t, c.name as cat
	FROM `" . DB_PREFIX . "post_attributes` AS p
	INNER JOIN `" . DB_PREFIX . "post_attr_group` AS g ON g.id = p.id_group
	INNER JOIN `" . DB_PREFIX . "languages` AS la ON la.id = g.id_lang
	LEFT JOIN `" . DB_PREFIX . "blogs` AS b ON b.id_blog = g.id_blog
	LEFT JOIN `" . DB_PREFIX . "post_types` AS ty ON ty.id = g.id_custom_type
	LEFT JOIN `" . DB_PREFIX . "categories` AS c ON c.id = g.id_category
	WHERE (g.id_site = " . $siteId . ") AND (g.id_lang = " . $langId . " OR g.every_lang = 1) AND (" . ( ( $blogId > 0 ) ? "g.id_blog = " . $blogId . " OR " : "" ) . "g.id_blog = 0) AND (" . ( !empty( $catId ) ? "g.id_category = " . (int) $catId . " OR " : "" ) . "g.id_category = 0)"
	)->all();

	return $tmp;
}

#####################################################
#
# Get All Auto Content Sources function
#
#####################################################
function Sources( $siteId = SITE_ID, $cache = false, $keysOnly = false )
{
	$cacheFile = CacheFileName( 'admin-auto-content-sources', null, null, null, null, null, null, $siteId );
		
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$b = ReadCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		//Query: auto sources
		$data = $db->from( 
		null, 
		"SELECT a.id, a.title, a.url, a.added_time, a.post_type, a.source_type, a.xml_data, u.user_name, u.real_name, (SELECT COUNT(*) FROM " . DB_PREFIX . POSTS . " AS p WHERE p.id_source = a.id) as posts_num
		FROM `" . DB_PREFIX . "auto_sources` AS a
		LEFT JOIN `" . DB_PREFIX . USERS . "` AS u ON u.id_member = a.user_id
		WHERE (a.id_site = " . $siteId . ")"
		)->all();

		if ( !$data )
			return null;
		
		$b = array();
		
		foreach( $data as $row )
			$b[] = $row;
		
		if ( $cache )
			WriteOtherCacheFile( $b, $cacheFile );
	}
	
	return ( $keysOnly ? array_keys( $b ) : $b );
}

#####################################################
#
# Get Single Auto Content function
#
#####################################################
function AdminGetAutoSource( $id )
{
	$db = db();
		
	//Query: auto sources
	$data = $db->from( 
	null, 
	"SELECT a.*, u.user_name, u.real_name, (SELECT COUNT(*) FROM " . DB_PREFIX . POSTS . " AS p WHERE p.id_source = a.id) as posts_num
	FROM `" . DB_PREFIX . "auto_sources` AS a
	LEFT JOIN `" . DB_PREFIX . USERS . "` AS u ON u.id_member = a.user_id
	WHERE (a.id = " . $id . ")"
	)->single();
	
	return $data;
}

#####################################################
#
# Admin Count Comments For Browsing Page function
#
#####################################################
function AdminCommentsCount()
{
	global $Admin;
	
	$arr = array();
	
	$showAll = $Admin->Settings()::IsTrue( 'parent_site_shows_everything' );
	
	$showAllSites = ( ( $showAll && MULTISITE && $Admin->IsDefaultSite() ) ? true : false );
	
	$qr = "(id_lang = " . $Admin->GetLang() . ")" . ( ( $showAll && IsAllowedTo( 'admin-site' ) ) ? "" : " AND (id_blog = " . $Admin->GetBlog() . ") AND (id_site = " . $Admin->GetSite() . ")" ) . 
	( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) ) ? " AND (user_id = " . $Admin->UserID() . ")" : "" );
		
	//Count approved comments
	$q = $Admin->db->from( null, 
	"SELECT count(id) AS total
	FROM `" . DB_PREFIX . "comments`
	WHERE (status = 'approved') AND " . $qr
	)->total();

	//Add the number into the array
	$arr['comApproved'] = ( $q ? $q : 0 );

	//Count pending comments
	$q = $Admin->db->from( null, 
	"SELECT count(id) AS total
	FROM `" . DB_PREFIX . "comments`
	WHERE (status = 'pending') AND " . $qr
	)->total();
		
	//Add the number into the array
	$arr['comPending'] = ( $q ? $q : 0 );
	
	//Count spam comments
	$q = $Admin->db->from( null, 
	"SELECT count(id) AS total
	FROM `" . DB_PREFIX . "comments`
	WHERE (status = 'spam') AND " . $qr
	)->total();
	
	//Add the number into the array
	$arr['comSpam'] = ( $q ? $q : 0 );
	
	//Count deleted comments
	$q = $Admin->db->from( null, 
	"SELECT count(id) AS total
	FROM `" . DB_PREFIX . "comments`
	WHERE (status = 'deleted') AND " . $qr
	)->total();
	
	//Add the number into the array
	$arr['comDeleted'] = ( $q ? $q : 0 );

	return $arr;
}

#####################################################
#
# Get Admin Post Attribute Group function
#
#####################################################
function GetAdminAttributeGroups( $orderBy = 'name', $order = 'ASC' )
{
	global $Admin;
	
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
		return;
	
	$data = $Admin->db->from( 
	null, 
	"SELECT id, name, group_order
	FROM `" . DB_PREFIX . "post_attr_group`
	WHERE (id_site = " . $Admin->GetSite() . ") AND (id_lang = " . $Admin->GetLang() . " OR id_lang = 0)
	AND (id_blog = " . $Admin->GetBlog() . " OR id_blog = 0)
	ORDER BY " . $orderBy . " " . $order
	)->all();

	return $data;
}

#####################################################
#
# Update Posts Category and Subcategory function
#
# $id => The category ID that is being deleted, $cat => The default category ID
#
# Used when a category is being deleted
#####################################################
function UpdatePostsCatSubCat( $id, $cat )
{
	if ( empty( $id ) )
		return;
	
	$db = db();
	
	$db->update( POSTS )->where( 'id_category', $cat )->set( 'id_category', $id );
	
	$db->update( POSTS )->where( 'id_sub_category', $id )->set( 'id_sub_category', 0 );
}

#####################################################
#
# Get Category Translation function
#
#####################################################
function GetCatTrans( $id )
{
	$db = db();

	$q = $db->from( 
	null, 
	"SELECT id, name
	FROM `" . DB_PREFIX . "categories`
	WHERE (id = " . (int) $id . ")"
	)->single();

	return $q;	
}

#####################################################
#
# Create Category Url function
#
#####################################################
function CatEditUri( $id, $blog = null, $site = null, $lang = null, $defLang = null, $slug = 'edit-category' )
{
	$uri = ADMIN_URI . $slug . PS . 'id' . PS . $id . PS;
	
	$hasSite = false;
	$hasLang = false;

	if ( $site && ( $site != SITE_ID ) )
	{
		$uri .= '?site=' . $site;
		$hasSite = true;
	}
	
	if ( $lang && $defLang && ( $lang != $defLang ) )
	{
		$uri .= ( $hasSite ? ';' : '?' ) . 'lang=' . $lang;
		$hasLang = true;
	}
	
	$uri .= ( $blog && ( $blog > 0 ) ? ( ( $hasSite || $hasLang ) ? ';' : '?' ) . 'blog=' . $blog : '' );
	
	return $uri;
}

#####################################################
#
# Get single Attribute Group function
#
#####################################################
function GetSingleAttributeGroup( $id )
{
	$db = db();

	$q = $db->from( 
	null, 
	"SELECT p.*, b.name as bname, la.title as lt
	FROM `" . DB_PREFIX . "post_attr_group` AS p
	LEFT JOIN `" . DB_PREFIX . "blogs` AS b ON b.id_blog = p.id_blog
	LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = p.id_lang
	WHERE (p.id = " . (int) $id . ")"
	)->single();

	return $q;
}

#####################################################
#
# Multi Categories function
#
#####################################################
function AdminGetCategoriesFull()
{
	global $Admin;
	
	$_categories = array();
	
	$_langs = $Admin->db->from( 
	null, 
	"SELECT id, code, is_default, locale, title
	FROM `" . DB_PREFIX . "languages`
	WHERE (id_site = " . $Admin->GetSite() . ") AND (status = 'active')
	ORDER BY lang_order ASC"
	)->all();
	
	if ( $_langs )
	{
		foreach( $_langs as $_lang )
		{
			$_categories[$_lang['code']] = array(
						'name' => stripslashes( $_lang['title'] ),
						'id' => $_lang['id'],
						'type' => 'lang',
						'childs' => array()
				
				
			);	
		}
		//If the site has multiblog enabled, we need a bit more work
		if ( $Admin->Settings()::IsTrue('enable_multiblog', 'site' ) )
		{
			foreach( $_langs as $_lang )
			{
				//We need the blogs now
				$_blogs = $Admin->db->from( 
				null, 
				"SELECT id_blog, name
				FROM `" . DB_PREFIX . "blogs`
				WHERE (id_site = " . $Admin->GetSite() . ") AND (id_lang = " . $_lang['id'] . " OR id_lang = 0)
				ORDER BY name ASC"
				)->all();

				if ( $_blogs )
				{
					foreach( $_blogs as $_blog )
					{
						$_categories[$_lang['code']]['childs'][$_blog['id_blog']] = array(
												'name' => stripslashes( $_blog['name'] ),
												'id' => $_blog['id_blog'],
												'type' => 'blog',
												'childs' => array()
						
						);
		
						$cats = $Admin->db->from( 
						null, 
						"SELECT id, name
						FROM `" . DB_PREFIX . "categories`
						WHERE (id_blog = " . $_blog['id_blog'] . ") AND (id_lang = " . $_lang['id'] . ") AND (id_parent = 0)
						ORDER BY name ASC"
						)->all();

						if ( $cats )
						{
							foreach ( $cats as $cat )
							{
								$_categories[$_lang['code']]['childs'][$_blog['id_blog']]['childs'][$cat['id']] = array(
														'name' => stripslashes( $cat['name'] ),
														'id' => $cat['id'],
														'type' => 'cat',
														'childs' => array()
						
								);
								
								$subCats = $Admin->db->from( 
								null, 
								"SELECT id, name
								FROM `" . DB_PREFIX . "categories`
								WHERE (id_parent = " . $cat['id'] . ")
								ORDER BY name ASC"
								)->all();
														
								if ( $subCats )
								{
									foreach ( $subCats as $sub )
									{
										$_categories[$_lang['code']]['childs'][$_blog['id_blog']]['childs'][$cat['id']]['childs'][$sub['id']] = array(
														'name' => stripslashes( $sub['name'] ),
														'type' => 'sub',
														'id' => $sub['id'],
										);
									}
								}
							}
						}
					}
				}
				
				$_cats = $Admin->db->from( 
				null, 
				"SELECT id, name
				FROM `" . DB_PREFIX . "categories`
				WHERE (id_blog = 0) AND (id_lang = " . $_lang['id'] . ") AND (id_parent = 0)
				ORDER BY name ASC"
				)->all();

				$_categories[$_lang['code']]['childs']['orphanCats'] = array(
													'name' => __( 'orphan-categories' ),
													'type' => 'blog',
													'id' => '0',
													'childs' => array()
						
				);
				
				if ( $_cats )
				{
					foreach ( $_cats as $_cat )
					{
						$_categories[$_lang['code']]['childs']['orphanCats']['childs'][$_cat['id']] = array(
													'name' => stripslashes( $_cat['name'] ),
													'type' => 'cat',
													'id' => $_cat['id'],
													'childs' => array()
						
						);
						
						$subCats = $Admin->db->from( 
						null, 
						"SELECT id, name
						FROM `" . DB_PREFIX . "categories`
						WHERE (id_parent = " . $_cat['id'] . ")
						ORDER BY name ASC"
						)->all();
						
						if ( $subCats )
						{
							foreach ( $subCats as $sub )
							{
								$_categories[$_lang['code']]['childs']['orphanCats']['childs'][0]['childs'][$sub['id']] = array(
														'name' => stripslashes( $sub['name'] ),
														'type' => 'sub',
														'id' => $sub['id']
								);
							}
						}
					}
				}
			}
			
			unset( $_blogs );
		}
		
		else
		{
			foreach( $_langs as $_lang )
			{
				$_cats = $Admin->db->from( 
				null, 
				"SELECT id, name
				FROM `" . DB_PREFIX . "categories`
				WHERE (id_blog = 0) AND (id_lang = " . $_lang['id'] . ") AND (id_parent = 0)
				ORDER BY name ASC"
				)->all();
				
				$_categories[$_lang['code']]['childs']['orphanCats'] = array(
														'name' => $L['orphan-categories'],
														'type' => 'blog',
														'id' => '0',
														'childs' => array()
						
				);
				
				if ( $_cats )
				{
					foreach ( $_cats as $_cat )
					{
						$_categories[$_lang['code']]['childs']['orphanCats']['childs'][$_cat['id']] = array(
																		'name' => stripslashes( $_cat['name'] ),
																		'type' => 'cat',
																		'id' => $_cat['id'],
																		'childs' => array()
						
						);
						
						$subCats = $Admin->db->from( 
						null, 
						"SELECT id, name
						FROM `" . DB_PREFIX . "categories`
						WHERE (id_parent = " . $_cat['id'] . ")
						ORDER BY name ASC"
						)->all();

						if ( $subCats )
						{
							foreach ( $subCats as $sub )
							{
								$_categories[$_lang['code']]['childs']['orphanCats']['childs'][0]['childs'][$sub['id']] = array(
																			'name' => stripslashes( $sub['name'] ),
																			'type' => 'sub',
																			'id' => $sub['id']
								);
							}
						}
					}
				}
			}
		}
	}
	
	return $_categories;
}

#####################################################
#
# Get Ads function
#
#####################################################
function GetAdminAds( $pos = null, $type = null, $all = false, $orderBy = 'ad_order', $order = 'ASC' )
{
	global $Admin;
	
	$binds = array();
	
	if ( $pos )
	{
		$binds[$pos] = ':pos';
	}
	
	if ( $type )
	{
		$binds[$type] = ':type';
	}
	
	$q = $Admin->db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "ads`
	WHERE (id_site = " . $Admin->GetSite() . ") AND (id_lang = " . $Admin->GetLang() . ")" . ( !$all ? " AND (disabled = 0)" : "" ) . ( $pos ? " AND (ad_pos = :pos)" : "" ) . ( $type ? " AND (type = :type)" : "" ) . "
	ORDER BY " . $orderBy . " " . $order,
	$binds
	)->all();
	
	return $q;
}

#####################################################
#
# Get Single Ad function
#
#####################################################
function GetAd ( $id )
{
	$db = db();
	
	$q = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "ads`
	WHERE (id = " . (int) $id . ")"
	)->single();
	
	return $q;
}

#####################################################
#
# Get Single Price function
#
#####################################################
function adminSinglePrice( $id )
{
	$db = db();
	
	$q = $db->from( 
	null, 
	"SELECT p.*, s.name as st, c.name as cu, c.code as cc, c.symbol as cs, pi.last_time_updated as lu
	FROM `" . DB_PREFIX . "prices` AS p
	LEFT JOIN `" . DB_PREFIX . "stores` AS s ON s.id_store = p.id_store
	LEFT JOIN `" . DB_PREFIX . "currencies` AS c ON c.id = p.id_currency
	LEFT JOIN `" . DB_PREFIX . "price_info` AS pi ON pi.id_price = p.id_price
	WHERE (p.id_price = " . $id . ")"
	)->single();
	
	return $q;
}

#####################################################
#
# Get Admin Single Post Deal function
#
#####################################################
function GetAdminSingleDeal( $postId )
{
	$db = db();
	
	$q = $db->from( 
	null, 
	"SELECT p.*, s.name as st, c.name as cu, c.code as cc, c.symbol as cs, pi.last_time_updated as lu
	FROM `" . DB_PREFIX . "prices` AS p
	LEFT JOIN `" . DB_PREFIX . "stores` AS s ON s.id_store = p.id_store
	LEFT JOIN `" . DB_PREFIX . "currencies` AS c ON c.id = p.id_currency
	LEFT JOIN `" . DB_PREFIX . "price_info` AS pi ON pi.id_price = p.id_price
	WHERE (p.id_post = " . $postId . ") AND (p.type = 'coupon')"
	)->single();
	
	return $q;
}

#####################################################
#
# Get Admin Post Deals/Coupons function
#
#####################################################
function GetAdminDeals( $postId )
{
	$db = db();
	
	$q = $db->from( 
	null, 
	"SELECT p.*, s.name as st, c.name as cu, c.code as cc, c.symbol as cs, c.format as cf, c.exchange_rate as cr, pi.last_time_updated as lu
	FROM `" . DB_PREFIX . "prices` AS p
	LEFT JOIN `" . DB_PREFIX . "stores` AS s ON s.id_store = p.id_store
	LEFT JOIN `" . DB_PREFIX . "currencies` AS c ON c.id = p.id_currency
	LEFT JOIN `" . DB_PREFIX . "price_info` AS pi ON pi.id_price = p.id_price
	WHERE (p.id_post = " . $postId . ") AND (p.type = 'coupon')
	ORDER BY p.id_price ASC"
	)->all();
	
	return $q;
}

#####################################################
#
# Get Admin Post Prices function
#
#####################################################
function GetAdminPrices( $postId )
{
	global $Admin;
	
	//Query: prices
	$tmp = $Admin->db->from( null, "
	SELECT p.*, s.name as st, c.name as cu, c.code as cc, c.symbol as cs, pi.last_time_updated as lu
	FROM `" . DB_PREFIX . "prices` AS p
	LEFT JOIN `" . DB_PREFIX . "stores` as s ON s.id_store = p.id_store
	LEFT JOIN `" . DB_PREFIX . "currencies` as c ON c.id = p.id_currency
	LEFT JOIN `" . DB_PREFIX . "price_info` as pi ON pi.id_price = p.id_price
	WHERE (p.id_post = " . $postId . ") AND (p.type = 'normal')
	ORDER BY p.id_price ASC"
	)->all();
	
	return $tmp;
}

#####################################################
#
# Delete Custom Types function
#
#####################################################
function DeleteCustomType( $id )
{
	$db = db();
	
	$q = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "post_types`
	WHERE (id = " . (int) $id . ")"
	)->single();
	
	if ( !$q )
	{
		return;
	}
	
	//Delete this custom post type
	$q = $db->delete( 'post_types' )->where( "id", $id )->run();
	
	if ( !$q )
		return;
	
	//Delete any relations
	$db->delete( 'types_relationships' )->where( "id_post_type", $id )->run();

	//Delete any tag relations
	$db->delete( 'tags_relationships' )->where( "id_custom_type", $id )->run();
}

#####################################################
#
# Get Admin Custom Types function
#
#####################################################
function GetAdminCustomTypes()
{
	global $Admin;
	
	//Query: post types
	$q = $Admin->db->from( null, "
	SELECT t.*, (SELECT COUNT(*) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_custom_type = t.id AND p.id_site = t.id_site) as num
	FROM `" . DB_PREFIX . "post_types` AS t
	WHERE (t.id_site = " . $Admin->GetSite() . ") AND (t.id_parent = 0)
	ORDER BY t.type_order ASC"
	)->all();
	
	if ( !$q )
		return null;
	
	$data = array();
	
	foreach ( $q as $p )
	{
		$data[$p['id']] = array(
			'id' 			=> $p['id'],
			'order' 		=> $p['type_order'],
			'num' 			=> $p['num'],
			'title' 		=> StripContent( $p['title'] ),
			'sef' 			=> StripContent( $p['sef'] ),
			'description' 	=> StripContent( $p['description'] ),
			'trans_data' 	=> Json( $p['trans_data'] ),
			'image' 		=> ( !empty( $p['id_image'] ) ? PostImageDetails( $p['id_image'], null, true ) : null ),
			'childs'		=> array()
		);
		
		//Query: post types
		$pr = $Admin->db->from( null, "
		SELECT t.*, (SELECT COUNT(*) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_custom_type = t.id AND p.id_site = t.id_site) as num
		FROM `" . DB_PREFIX . "post_types` AS t
		WHERE (t.id_parent = " . $p['id'] . ")
		ORDER BY t.type_order ASC"
		)->all();
	
		if ( $pr )
		{
			foreach ( $q as $pc )
			{
				$data[$p['id']]['childs'][$pc['id']] = array(
					'id' 			=> $pc['id'],
					'is_default' 	=> $pc['is_default'],
					'num' 			=> $pc['num'],
					'title' 		=> StripContent( $pc['title'] ),
					'sef' 			=> StripContent( $pc['sef'] ),
					'description' 	=> StripContent( $pc['description'] ),
					'trans_data' 	=> Json( $pc['trans_data'] ),
					'image' 		=> ( !empty( $pc['id_image'] ) ? PostImageDetails( $pc['id_image'], null, true ) : null )
				);
			}
		}
	}
	
	return $data;
}

#####################################################
#
# Get Single Comment function
#
#####################################################
function AdminGetCustomType( $id )
{
	$db = db();
	
	//Query: post type
	$data = $db->from( null, "
	SELECT *
	FROM `" . DB_PREFIX . "post_types`
	WHERE (id = " . (int) $id . ")"
	)->single();

	return $data;
}
#####################################################
#
# Get Single Comment function
#
#####################################################
function AdminSingleComment( $id )
{
	$db = db();
	
	//Query: comment
	$data = $db->from( null, "
	SELECT co.*, p.id_post AS pid, p.title AS tl, p.sef AS ts
	FROM `" . DB_PREFIX . "comments` AS co
	LEFT JOIN `" . DB_PREFIX . POSTS . "` AS p ON p.id_post = co.id_post
	WHERE (co.id = " . (int) $id . ")"
	)->single();
	
	return $data;
}

#####################################################
#
# Get Single Manufacturer function
#
#####################################################
function GetManufacturer( $id )
{
	$db = db();
	
	//Query: manufacturer
	$data = $db->from( null, "
	SELECT *
	FROM `" . DB_PREFIX . "manufacturers`
	WHERE (id = " . (int) $id . ")"
	)->single();
	
	return $data;
}

#####################################################
#
# Get Single Store function
#
#####################################################
function GetStore( $id )
{
	$db = db();
	
	//Query: store
	$q = $db->from( null, "
	SELECT s.*, p.name AS pr
	FROM `" . DB_PREFIX . "stores` AS s
	LEFT JOIN `" . DB_PREFIX . "stores` AS p ON p.id_store = s.id_parent
	WHERE (s.id_store = " . (int) $id . ")"
	)->single();
		
	if ( !$q )
		return null;

	$data = array();
	$data['pregs'] = array();
	$data['data'] = $q;
	
	//Query: store data
	$q = $db->from( null, "
	SELECT id, reg_data, name, key_value
	FROM `" . DB_PREFIX . "stores_data`
	WHERE (id_store = " . (int) $id . ")"
	)->all();
	
	if ( $q )
	{
		foreach( $q as $r )
		{
			$data['pregs'][$r['id']] = $r;
		}
	}
	
	return $data;
}

#####################################################
#
# Get Single Cat function
#
#####################################################
function AdminGetCategory( $id )
{
	$db = db();
	
	//Query: category
	$data = $db->from( null, "
	SELECT *
	FROM `" . DB_PREFIX . "categories`
	WHERE (id = " . (int) $id . ")"
	)->single();
	
	return $data;
}

#####################################################
#
# Get Admin Forms function
#
#####################################################
function GetAdminForms( $siteId = null, $type = 'form', $orderBy = 'title', $order = 'ASC' )
{
	global $Admin;
	
	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );
	
	$all = ( ( $type == 'all' ) ? true : false );
	
	$binds = null;
	
	if ( !$all )
	{
		$binds = array( $type => ':type' );
	}
	
	//Query: forms
	$data = $Admin->db->from( null, "
	SELECT *
	FROM `" . DB_PREFIX . "forms`
	WHERE (id_site = " . $siteId . ")" . ( $all ? "" : " AND (form_type = :type)" ) . "
	ORDER BY " . $orderBy . " " . strtoupper( $order ),
	$binds
	)->all();
	
	return $data;
}

#####################################################
#
# Get Admin Form function
#
#####################################################
function AdminGetSingleForm( $formId )
{
	$db = db();
	
	$data = $db->from( null, "
	SELECT *
	FROM `" . DB_PREFIX . "forms`
	WHERE (id = " . (int) $formId . ")"
	)->single();
	
	return $data;
}

#####################################################
#
# Get Site's Form Templates Function
#
#####################################################
function GetFormTemplates( $siteId = null, $type = 'form' )
{
	global $Admin;
	
	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );
	
	$binds = array( $type => ':type' );
	
	//Query: form templates
	$data = $Admin->db->from( null, "
	SELECT id, title, data
	FROM `" . DB_PREFIX . "form_templates`
	WHERE (id_site = " . $siteId . ") AND (template_type = :type)",
	$binds
	)->all();
	
	return $data;
}

#####################################################
#
# Get Admin Categories function
#
#####################################################
function GetAdminCategories( $orderBy = 'name', $order = 'ASC', $showAll = false, $getChilds = true, $cache = true )
{
	global $Admin;
	
	$langId = $Admin->GetLang();//( $showAll ? null : $Admin->GetLang() );
	$blogId = ( $showAll ? null : $Admin->GetBlog() );
	
	$cacheFile = CacheFileName( 'admin-categories' . ( $getChilds ? '-has_childs' : '' ), null, $langId, $blogId, null, null, null, $Admin->GetSite() );
		
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$data = ReadCache( $cacheFile );
	}
	
	else
	{
		$query = "SELECT c.*, la.code as ls, la.title as lt, b.sef as blog_sef, b.name as blog_name, cnf.value as hide_lang, cnf2.value as categories_filter, cnf3.value as trans_data, s.url, s.title as site_name, s.enable_multilang as multilang, s.enable_multiblog as multiblog, ld.code as dlc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_category = c.id AND p.id_lang = c.id_lang AND p.post_status = 'published') as numposts
		FROM `" . DB_PREFIX . "categories` AS c
		INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
		INNER JOIN `" . DB_PREFIX . "languages` as ld ON ld.id_site = c.id_site AND ld.is_default = 1
		INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = c.id_site
		INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site = c.id_site AND cnf.variable = 'hide_default_lang_slug'
		INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = c.id_site AND cnf2.variable = 'categories_filter'
		INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = c.id_site AND cnf3.variable = 'trans_data'
		LEFT  JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
		WHERE (c.id_parent = 0) AND (c.id_site = " . $Admin->GetSite() . ")" . ( $langId ? " AND (c.id_lang = " . $langId . ")" : "" ) . ( $blogId ? " AND (c.id_blog = " . $blogId . ")" : "" ) . "
		ORDER BY " . $orderBy . " " . $order;

		//Query: categories
		$tmp = $Admin->db->from( null, $query )->all();
		
		$data = array();
		
		$i = 0;
		
		foreach ( $tmp as $cat )
		{
			$data[$i] = array(
					'id' => $cat['id'],
					'name' => StripContent( $cat['name'] ) . ( ( $showAll && !empty( $cat['blog_name'] ) ) ? ' (' . StripContent( $cat['blog_name'] ) . ')' : '' ),
					'descr' => StripContent( $cat['descr'] ),
					'slug' => $cat['sef'],
					'siteId' => $cat['id_site'],
					'blogName' => StripContent( $cat['blog_name'] ),
					'siteName' => StripContent( $cat['site_name'] ),
					'blogSef' => $cat['blog_sef'],
					'transParent' => $cat['id_trans_parent'],
					'items' => $cat['numposts'],
					'color' => $cat['cat_color'],
					'lang' => ( $cat['lt'] ),
					'langId' => $cat['id_lang'],
					'langCode' => $cat['ls'],
					'postLimit' => $cat['article_limit'],
					'image' => BuildImageArray( $cat['id_image'] ),
					'groups' => Json( $cat['groups_data'] ),
					'hiddenFrontPage' => $cat['hide_front'],
					'isDefault' => ( $cat['is_default'] ? true : false ),
					'url' => BuildCategoryUrl( $cat, $cat['ls'] ),
					'filters' => GetFilters( $cat['id'], $cat['id_lang'] ),
					'trans' => CategoryTrans( $cat, $cat['ls'], $cat['url'], $cat['ls'] ),
					'childs' => array()
			);
	
			//Get the childs, if any
			if ( $getChilds )
			{
				$query = "SELECT c.*, la.code as ls, b.sef as blog_sef, cnf.value as hide_lang, cnf2.value as categories_filter, cnf3.value as trans_data, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, ld.code as dlc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_category = c.id AND p.id_lang = c.id_lang AND p.post_status = 'published') as numposts
				FROM `" . DB_PREFIX . "categories` AS c
				INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
				INNER JOIN `" . DB_PREFIX . "languages` as ld ON ld.id_site = c.id_site AND ld.is_default = 1
				INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = c.id_site
				INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site = c.id_site AND cnf.variable = 'hide_default_lang_slug'
				INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = c.id_site AND cnf2.variable = 'categories_filter'
				INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = c.id_site AND cnf3.variable = 'trans_data'
				LEFT  JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
				WHERE (c.id_parent = " . $cat['id'] . ") AND (c.id_site = " . $Admin->GetSite() . ")" . ( $langId ? " AND (c.id_lang = " . $langId . ")" : "" ) . ( $blogId ? " AND (c.id_blog = " . $blogId . ")" : "" ) . "
				ORDER BY " . $orderBy . " " . $order;

				//Query: categories
				$subs = $Admin->db->from( null, $query )->all();
				
				if ( $subs )
				{
					foreach( $subs as $sub )
					{						
						$data[$i]['childs'][] = array(
							'id' => $sub['id'],
							'name' => stripslashes( $sub['name'] ),
							'descr' => stripslashes( $sub['descr'] ),
							'slug' => $sub['sef'],
							'blogName' => stripslashes( $sub['bn'] ),
							'blogSef' => $sub['bs'],
							'transParent' => $cat['id_trans_parent'],
							'items' => $sub['numItems'],
							'color' => $sub['cat_color'],
							'lang' => ( $sub['lt'] ),
							'langId' => $sub['id_lang'],
							'langCode' => $sub['ls'],
							'hiddenFrontPage' => $sub['hide_front'],
							'isDefault' => false,
							'url' => BuildCategoryUrl( $sub, $sub['ls'] ),
							'trans' => CategoryTrans( $sub, $sub['ls'], $sub['url'], $sub['ls'] )
						);
					}
				}
			}
			
			$i++;
		}
		
		if ( $cache )
		{
			WriteOtherCacheFile( $data, $cacheFile );
		}
	}
	
	return $data;
}

#####################################################
#
# Links function
#
#####################################################
function GetAdminLinks( $siteId = SITE_ID, $cache = true, $keysOnly = false )
{
	$cacheFile = CacheFileName( 'admin-links', null, null, null, null, null, null, $siteId );
		
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$data = ReadCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		$q = $db->from( 
		null, 
		"SELECT *
		FROM `" . DB_PREFIX . "links`
		WHERE (id_site = " . (int) $siteId . ")
		ORDER BY id ASC"
		)->all();
		
		if ( !$q )
		{
			return null;
		}
		
		$data = array();

		foreach( $q as $row )
		{
			$tmp = ( !empty( $row['link_data'] ) ? Json( $row['link_data'] ) : array() );
			$trans = ( !empty( $row['trans_data'] ) ? Json( $row['trans_data'] ) : array() );

			$data[$row['id']] = $row;
			$data[$row['id']]['link_data'] = $tmp;
			$data[$row['id']]['trans_data'] = $trans;
		}
		
		if ( $cache )
		{
			WriteOtherCacheFile( $data, $cacheFile );
		}
	}

	return ( $keysOnly ? array_keys( $data ) : $data );
}

#####################################################
#
# Langs function (Generic)
#
# This function returns the enabled langs from the DB
#
#####################################################
function Langs( $siteId = SITE_ID, $cache = true, $keysOnly = false )
{
	$cacheFile = CacheFileName( 'admin-langs', null, null, null, null, null, null, $siteId );
		
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$l = ReadCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		//Query: languages
		$data = $db->from( 
		null, 
		"SELECT la.*, (SELECT COUNT(*) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_lang = la.id AND p.id_site = la.id_site AND p.post_status = 'published') as num
		FROM `" . DB_PREFIX . "languages` AS la
		WHERE (la.id_site = " . (int) $siteId . ") AND (la.status = 'active')
		ORDER BY la.lang_order ASC"
		)->all();
	
		$l = array();

		if ( $data )
		{
			foreach( $data as $row )
			{
				$l[$row['code']] = $row;
				$l[$row['code']]['flagurl'] = SITE_URL . 'languages' . PS . 'flags' . PS . $row['flagicon'];
			}
		
			if ( $cache )
				WriteOtherCacheFile( $l, $cacheFile );
		}
	}
	
	return ( $keysOnly ? array_keys( $l ) : $l );
}

#####################################################
#
# Get the Main Image URL By Id function
#
#####################################################
function GetMainImageUrl( $id, $share = null )
{
	global $Admin;
	
	$imgDt = $Admin->db->from( 
	null, 
	"SELECT filename, external_url, added_time
	FROM `" . DB_PREFIX . "images`
	WHERE (id_image = " . $id . ")"
	)->single();
	
	if ( !$imgDt )
		return null;
		
	if ( !empty( $imgDt['external_url'] ) )
	{
		return $imgDt['external_url'];
	}
	
	//For multiple files, we can get this data to avoid multiple DB calls
	if ( !$share )
	{
		$share = $Admin->ImageUpladDir( $Admin->GetSite() );
	}

	$uri = ( ( !empty( $share ) && isset( $share['share'] ) && $share['share'] ) ? $share['html'] : null );

	return FolderUrlByDate( $imgDt['added_time'], $uri ) . $imgDt['filename'];
}

#####################################################
#
# Get Post Drafts function
#
#####################################################
function Drafts( $postId, $userId )
{	
	$db = db();
		
	//Query: autosaves
	$data = $db->from( 
	null, 
	"SELECT id, title, added_time, edited_time, draft_type
	FROM `" . DB_PREFIX . "posts_autosaves`
	WHERE (post_id = " . (int) $postId . ") AND (user_id = " . (int) $userId . ")
	ORDER BY edited_time > added_time DESC, added_time DESC
	LIMIT 10"
	)->all();
	
	return $data;
}

#####################################################
#
# Get All Currencies function
#
#####################################################
function Currencies( $siteId = SITE_ID, $cache = true, $keysOnly = false )
{
	$cacheFile = CacheFileName( 'admin-currencies', null, null, null, null, null, null, $siteId );
		
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$b = ReadCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		//Query: currencies
		$data = $db->from( 
		null, 
		"SELECT *
		FROM `" . DB_PREFIX . "currencies`
		ORDER BY name ASC"
		)->all();
		
		$b = array();
		
		if ( $data )
		{
			foreach( $data as $d )
			{
				$b[$d['id']] = $d;
			}
		
			if ( $cache )
			{
				WriteOtherCacheFile( $b, $cacheFile );
			}
		}
	}

	return ( $keysOnly ? array_keys( $b ) : $b );
}

#####################################################
#
# Get All Stores function
#
#####################################################
function Stores( $siteId = SITE_ID, $cache = true, $keysOnly = false )
{
	$cacheFile = CacheFileName( 'admin-stores', null, null, null, null, null, null, $siteId );
		
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$b = ReadCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		//Query: stores
		$data = $db->from( 
		null, 
		"SELECT v.id_store, v.id_image, v.name, v.url, v.description, t.name as typ
		FROM `" . DB_PREFIX . "stores` AS v
		LEFT JOIN `" . DB_PREFIX . "store_types` AS t ON t.id = v.id_type
		WHERE (v.id_site = " . $siteId . ") AND (v.id_parent = 0)
		ORDER BY v.name ASC"
		)->all();
		
		$b = array();
		
		if ( $data )
		{
			foreach( $data as $d )
			{
				$b[$d['id_store']] = $d;
				
				$ch = $db->from( 
				null, 
				"SELECT v.id_store, v.id_image, v.name, v.url, v.description, t.name as typ
				FROM `" . DB_PREFIX . "stores` AS v
				LEFT JOIN `" . DB_PREFIX . "store_types` AS t ON t.id = v.id_type
				WHERE (v.id_parent = " . $d['id_store'] . ")
				ORDER BY v.name ASC"
				)->all();
				
				if ( $ch )
				{
					foreach( $ch as $c )
					{
						$b[$d['id_store']]['childs'][$c['id_store']] = $c;
					}
				}
				
				else
				{
					$b[$d['id_store']]['childs'] = array();
				}
			}
		
			if ( $cache )
			{
				WriteOtherCacheFile( $b, $cacheFile );
			}
		}
	}

	return ( $keysOnly ? array_keys( $b ) : $b );
}

#####################################################
#
# Get All Manufacturers function
#
#####################################################
function Manufacturers( $siteId = SITE_ID, $cache = true, $keysOnly = false )
{
	$cacheFile = CacheFileName( 'admin-manufacturers', null, null, null, null, null, null, $siteId );
		
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$b = ReadCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		//Query: manufacturers
		$data = $db->from( 
		null, 
		"SELECT *
		FROM `" . DB_PREFIX . "manufacturers`
		WHERE (id_site = " . $siteId . ")
		ORDER BY title ASC"
		)->all();
		
		$b = array();
		
		if ( $data )
		{
			foreach( $data as $d )
			{
				$b[$d['id']] = $d;
			}
		
			if ( $cache )
			{
				WriteOtherCacheFile( $b, $cacheFile );
			}
		}
	}

	return ( $keysOnly ? array_keys( $b ) : $b );
}

#####################################################
#
# Get Single Lang function
#
#####################################################
function GetLang ( $id, $siteId = SITE_ID )
{
	$db = db();
	
	//Query: language
	$tmp = $db->from( 
	null, 
	"SELECT la.*, lc.*
	FROM `" . DB_PREFIX . "languages` AS la
	INNER JOIN `" . DB_PREFIX . "languages_config` AS lc ON lc.id_lang = la.id
	WHERE (la.id = " . (int) $id . ") AND (la.id_site = " . $siteId . ")"
	)->single();
	
	return $tmp;
}

#####################################################
#
# Get Single Menu function
#
#####################################################
function GetAdminMenu ( $id, $langId, $siteId = SITE_ID )
{
	$db = db();
	
	//Query: menu
	$m = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "menus`
	WHERE (id_menu = " . $id . ") AND (id_lang = " . $langId . ") AND (id_site = " . $siteId . ")"
	)->single();
		
	if ( !$m )
		return null;
	
	$m['items'] = array();
	
	$q = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "menu_items`
	WHERE (id_menu = " . $m['id_menu'] . ") AND (id_parent = 0)
	ORDER BY item_order ASC"
	)->all();
	
	if ( $q )
	{
		foreach( $q as $item )
		{
			$itemId = $item['id'];
			
			$m['items'][$itemId] = array(
				'id' => $itemId,
				'itemId' => $item['id_item'],
				'type' => $item['type'],
				'title' => stripslashes( $item['name'] ),
				'label' => stripslashes( $item['label'] ),
				'url' => stripslashes( $item['url'] ),
				'newTab' => $item['new_tab'],
				'order' => $item['item_order'],
				'childs' => array()
			);
			
			$c = $db->from( 
			null, 
			"SELECT *
			FROM `" . DB_PREFIX . "menu_items`
			WHERE (id_parent = " . $item['id_item'] . ")
			ORDER BY item_order ASC"
			)->all();
			
			if ( $c )
			{
				foreach( $c as $child )
				{
					$childId = $child['id'];
				
					$m['items'][$itemId]['childs'][$childId] = array(
						'id' => $childId,
						'itemId' => $child['id_item'],
						'type' => $child['type'],
						'title' => stripslashes( $child['name'] ),
						'label' => stripslashes( $child['label'] ),
						'url' => stripslashes( $child['url'] ),
						'newTab' => $child['new_tab'],
						'order' => $child['item_order'],
						'childs' => array()
					);
					
					$cc = $db->from( 
					null, 
					"SELECT *
					FROM `" . DB_PREFIX . "menu_items`
					WHERE (id_parent = " . $child['id_item'] . ")
					ORDER BY item_order ASC"
					)->all();
					
					if ( $cc )
					{
						foreach( $cc as $_child )
						{
							$_childId = $_child['id'];
						
							$m['items'][$itemId]['childs'][$childId]['childs'][$_childId] = array(
									'id' => $_childId,
									'itemId' => $_child['id_item'],
									'type' => $_child['type'],
									'title' => stripslashes( $_child['title_attr'] ),
									'label' => stripslashes( $_child['label'] ),
									'url' => stripslashes( $_child['url'] ),
									'newTab' => $_child['new_tab'],
									'order' => $_child['item_order']
							);
						}
					}
				}
			}
		}
		
	}
	
	return $m;
}

#####################################################
#
# Get Single Redirection function
#
#####################################################
function GetRedir ( $id, $siteId = SITE_ID )
{
	$db = db();
	
	//Query: redirs
	$tmp = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "redirs`
	WHERE (id = " . $id . ") AND (id_site = " . $siteId . ")"
	)->single();
	
	return $tmp;
}

#####################################################
#
# Get All Widgets function
#
#####################################################
function GetWidgets( $siteId = null, $langId = null, $cache = true )
{
	global $Admin;
	
	$theme 		= $Admin->ActiveTheme();
	
	$siteId 	= ( $siteId ? $siteId : $Admin->GetSite() );
	$langId 	= ( $langId ? $langId : $Admin->GetLang() );
	
	$cacheFile 	= CacheFileName( 'admin-widgets', null, $langId, null, null, null, null, $siteId );

	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$r = ReadCache( $cacheFile );
	}

	else
	{	
		//Query: widgets
		$data = $Admin->db->from( 
		null, 
		"SELECT *
		FROM `" . DB_PREFIX . "widgets`
		WHERE (id_site = " . $siteId . ") AND (id_lang = " . $langId . ")
		ORDER BY theme_pos ASC, widget_order ASC"
		)->all();

		$r = array();

		if ( $data )
		{
			foreach( $data as $row )
			{
				$pos = ( ( $theme == $row['theme'] ) ? $row['theme_pos'] : 'inactive' );
				$r[$pos][] = $row;
			}
		
			if ( $cache )
				WriteOtherCacheFile( $r, $cacheFile );
		}
	}

	return $r;
}

#####################################################
#
# Get Top Tags function
#
#####################################################
function GetTopTags( $siteId, $langId, $typeId = 0, $items = 20, $arr = array() )
{
	$array = array();
	
	$checkForArr = false;
	
	$db = db();
	
	if ( !empty( $arr ) )
	{
		foreach( $arr as $i )
		{
			$array[] = (int) $i;
		}
		
		if ( !empty( $array ) )
		{
			$checkForArr = true;
		}
	}
	
	//Query: tags
	$data = $db->from(
	null, 
	"SELECT t.id, t.title, t.sef, r.id_relation, (SELECT COUNT(id_relation) FROM " . DB_PREFIX . "tags_relationships WHERE taxonomy_id = t.id AND id_site = " . $siteId . " AND id_custom_type = " . (int) $typeId . ") as num_items
	FROM `" . DB_PREFIX . "tags` AS t
	LEFT JOIN `" . DB_PREFIX . "tags_relationships` AS r ON r.taxonomy_id = t.id
	WHERE (r.id_site = " . $siteId . ") AND (t.id_lang = " . $langId . ") AND (r.id_custom_type " . ( $checkForArr ? "IN ('" . join("','", $array ) . "')" : "= " . (int) $typeId ) . ")
	GROUP BY r.taxonomy_id
	ORDER BY num_items DESC
	LIMIT " . $items
	)->all();

	return $data;
}

#####################################################
#
# Get All Checked Links function
#
#####################################################
function GetCheckedLinks( $siteId )
{
	$links = array();
	
	$db = db();
	
	//Query: link checks
	$data = $db->from( 
	null, 
	"SELECT l.*, p.title
	FROM `" . DB_PREFIX . "link_checks` AS l
	LEFT JOIN `" . DB_PREFIX . POSTS . "` AS p ON p.id_post = l.id_post
	WHERE (l.id_site = " . $siteId . ")
	ORDER BY title ASC"
	)->all();
	
	return $data;
}

#####################################################
#
# Get All Tags function
#
#####################################################
function AdminGetTags( $langId, $type = 0 )
{
	$tags = array();
	
	$db = db();
	
	//Query: tags
	$data = $db->from( 
	null, 
	"SELECT id, title, sef
	FROM `" . DB_PREFIX . "tags`
	WHERE (id_lang = " . $langId . ")
	ORDER BY title ASC"
	)->all();

	if( $data ) 
	{
		foreach ( $data as $t )
		{
			$tags[] = array(
				'id' 	=> $t['id'],
				'name' => $t['title'],
				'sef' 	=> $t['sef']
			);
		}
		
		unset( $data );
	}
	
	return $tags;
}

#####################################################
#
# Get All Tags For Ajax function
#
#####################################################
function GetTagsAjax ( $langId, $type = 0 )
{
	$tags = array();
	
	$db = db();
	
	//Query: tags
	$data = $db->from( 
	null, 
	"SELECT id, title, sef
	FROM `" . DB_PREFIX . "tags`
	WHERE (id_lang = " . $langId . ") AND (id_custom_type = " . $type . ")"
	)->all();
	
	if( $data ) 
	{
		foreach ( $data as $t )
		{
			$tags[] = $t['title'];
		}
		
		unset( $data );
	}
	
	return $tags;
}

#####################################################
#
# Get Single Widget function
#
#####################################################
function GetWidget ( $id, $siteId )
{
	$db = db();
	
	//Query: widget
	$data = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "widgets`
	WHERE (id = " . $id . ") AND (id_site = " . $siteId . ")"
	)->single();
	
	return $data;
}

#####################################################
#
# Get Single Blog ID function
#
#####################################################
function AdminGetBlog( $id )
{
	$db = db();
	
	//Query: blog
	$data = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "blogs`
	WHERE (id_blog = " . $id . ")"
	)->single();
	
	return $data;
}

#####################################################
#
# Get Single Tag ID function
#
#####################################################
function AdminGetTag( $id )
{
	$db = db();
	
	//Query: tag
	$data = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "tags`
	WHERE (id = " . $id . ")"
	)->single();
	
	return $data;
}

#####################################################
#
# Empty Caches function
#
#####################################################
function EmptyCaches()
{
	global $Admin;
	
	if ( $Admin->IsDefaultSite() )
		DelFullCache();
	
	else
		DelChildCacheFiles();
}

#####################################################
#
# Recount Site Statistics function
#
#####################################################
function RecountStatistics()
{
	global $Admin;
	
	//If we have multiblogs, count each blog
	if ( $Admin->MultiBlog() )
	{
		RecountBlogsStatistics( $Admin->GetSite() );
	}
	
	//Count the categories
	RecountCategoriesStatistics( $Admin->GetSite() );
	
	//Count the tags
	RecountTagsStatistics( $Admin->GetSite() );
	
	//Count the post comments
	RecountPostsComments( $Admin->GetSite() );
}

#####################################################
#
# Get Scheduled Tasks function
#
#####################################################
function GetScheduledTasks( $siteId = null )
{
	global $Admin;

	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );
	
	//Query: tasks
	$data = $Admin->db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "scheduled_tasks`
	WHERE (id_site = " . $siteId . ")
	ORDER BY task ASC"
	)->all();
	
	return $data;
}

#####################################################
#
# Empty Child Caches function
#
#####################################################
function DelChildCacheFiles( $siteId = null, $type = 'home', $key = null )
{
	global $Admin;
	
	$siteId = ( $siteId ? $siteId : $Admin->GetSite() );
	
	//Query: site
	$site = $Admin->db->from( 
	null, 
	"SELECT url, site_secret, site_ping_url
	FROM `" . DB_PREFIX . "sites`
	WHERE (id = " . $siteId . ")"
	)->single();
	
	if ( !$site )
	{
		$Admin->SetAdminMessage( __( 'an-error-happened' ) );
		return;
	}

	$pingUrl = $site['site_ping_url'] . '?token=' . $site['site_secret'] . '&do=clean-caches&type=' . $type . ( $key ? '&key=' . $key : '' );

	return PingSite( $pingUrl );
}

#####################################################
#
# Menus function (Generic)
#
# This function returns the menus from the DB
#
#####################################################
function Menus( $siteId = null, $langId = null, $cache = true, $keysOnly = false )
{
	global $Admin;
	
	$db 		= $Admin->db;
	
	$siteId 	= ( $siteId ? $siteId : $Admin->GetSite() );
	$langId 	= ( $langId ? $langId : $Admin->GetLang() );
	
	//Query: menus
	$data = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "menus`
	WHERE (id_site = " . $siteId . ") AND (id_lang = " . $langId . ")"
	)->all();
	
	return $data;
}

#####################################################
#
# Load Theme's details function
#
#####################################################
function LoadTheme( $id )
{
	if ( empty( $id ) )
		return;

	global $Admin;

	if ( isset( $_POST['site'] ) && is_numeric( $_POST['site'] ) )
	{
		$Admin->SetSite( $_POST['site'] );
		
		//We need to set this site as "child" if needed
		if ( $_POST['site'] != SITE_ID )
			$Admin->SetChildSite();
	}
	
	$themes = LoadThemes( 'all', ( $Admin->IsDefaultSite() ? false : true ) );

	if ( empty( $themes ) || !isset( $themes[$id] ) )
		return null;
	
	return $themes[$id];
}

#####################################################
#
# Get All Redirections function
#
#####################################################
function GetRedirections( $siteId = null, $cache = true )
{
	global $Admin;

	$siteId 	= ( $siteId ? $siteId : $Admin->GetSite() );
	
	$cacheFile 	= CacheFileName( 'admin-redirs', null, null, null, null, null, null, $siteId );
		
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$r = ReadCache( $cacheFile );
	}

	else
	{
		//Query: redirs
		$data = $Admin->db->from( 
		null, 
		"SELECT *
		FROM `" . DB_PREFIX . "redirs`
		WHERE (id_site = " . $siteId . ")
		ORDER BY uri ASC"
		)->all();
	
		$r = array();

		if ( $data )
		{
			foreach( $data as $row )
			{
				$r[] = $row;
			}
			
			if ( $cache )
			{
				WriteOtherCacheFile( $r, $cacheFile );
			}
		
		}
	}
	
	return $r;
}

#####################################################
#
# Load Themes function
#
# Scans themes dir and loads the themes. Results can be cached
# 'normal' means the classic themes. 'amp' means themes for AMP
#
#####################################################
function LoadThemes( $type = 'normal', $cache = true )
{
	global $Admin;
	
	$cacheFile 	= CacheFileName( 'admin-themes-type_' . $type, null, null, null, null, null, null, $Admin->GetSite() );

	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$themes = ReadCache( $cacheFile );
	}
	
	else
	{
		if ( $Admin->IsDefaultSite() )
		{
			$themes = GetThemes ( $type );
		}
		
		else
		{
			$data = $Admin->PingChildSite ( 'get-themes', $type );
			$themes = ( ( !empty( $data ) && isset( $data['data'] ) ) ? $data['data'] : null );
		}

		if ( $cache && !empty( $themes ) )
			WriteOtherCacheFile( $themes, $cacheFile );
	}

	return $themes;	
}

#####################################################
#
# Load Plugins function
#
# Scans plugins dir and loads the available plugins. Results can be cached
#
#####################################################
function LoadPlugins( $cache = true )
{
	global $Admin;
	
	$cacheFile 	= CacheFileName( 'admin-plugins', null, null, null, null, null, null, $Admin->GetSite() );
	
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$plugins = ReadCache( $cacheFile );
	}
	
	else
	{
		$plugins = array();
		
		//Load the folders first
		$files = ScanDirs( PLUGINS_ROOT );
		
		if ( empty( $files ) )
			return null;

		foreach( $files as $file )
		{
			if ( !is_dir( PLUGINS_ROOT . $file . DS ) )
				continue;
			
			$pluginDir = PLUGINS_ROOT . $file . DS;
			
			$dirFiles = ScanDirs( PLUGINS_ROOT . $file . DS);

			foreach( $dirFiles as $dirFile )
			{
				$subFile = PLUGINS_ROOT . $file . DS . $dirFile;
				
				if ( strpos( $dirFile, 'metadata.json' ) === false )
					continue;
					
				if ( strpos( $dirFile, 'metadata.json' ) !== false )
				{
					//Open the metadata file
					$meta = json_decode( file_get_contents( $subFile ), TRUE );
					
					if ( empty( $meta ) )
						continue;

					//Get the plugin's data
					$compatible 	= $meta['compatible'];
					$version 		= $meta['version'];
					$author 		= $meta['author'];
					$releaseDate 	= $meta['releaseDate'];
					$link 			= $meta['website'];
					$license 		= $meta['license'];
					$notes 			= ( isset( $meta['notes'] ) ? $meta['notes'] : null );
					$options 		= ( !empty( $meta['options'] ) ? $meta['options'] : null );
					$isCompatible 	= IsCompatible( $compatible );
					$hooks = array();
					
					if ( !empty( $meta['hooks'] ) )
					{
						foreach( $meta['hooks'] as $hood_id => $hook )
						{
							$plugin_file = $function = null;
							
							if ( !empty( $hook['function'] ) )
							{
								$function = $hook['function'];
							}
							
							if ( !empty( $hook['file'] ) )
							{
								$plugin_file = $hook['file'];
							}
							
							$hooks[] = array(
								'hood_id' 	=> $hood_id,
								'function' 	=> $function,
								'file' 		=> $plugin_file
							);
						}
					}
					
					$langFile = $pluginDir . 'languages' . DS . 'en.json';

					$pluginData = null;

					if ( file_exists( $langFile ) )
						$pluginData = json_decode( file_get_contents( $langFile ), TRUE );
					
					$plugins[$file] = array(
						'title' 		=> ( $pluginData ? $pluginData['plugin-data']['name'] : $file ),
						'description' 	=> ( $pluginData ? $pluginData['plugin-data']['description'] : '' ),
						'isCompatible' 	=> $isCompatible,
						'version' 		=> $version,
						'author' 		=> $author,
						'link' 			=> $link,
						'license' 		=> $license,
						'notes' 		=> $notes,
						'hooks' 		=> $hooks
					);
				}
			}
		}
		
		if ( $cache )
			WriteOtherCacheFile( $plugins, $cacheFile );
	}

	return $plugins;
}

#####################################################
#
# Get Single Api function
#
#####################################################
function AdminGetSingleApi( $id )
{
	$db = db();
	
	//Query: api
	$data = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "api_obj`
	WHERE (id = " . $id . ")"
	)->single();
	
	return $data;
}

#####################################################
#
# Get All Apis function
#
#####################################################
function GetApis( $siteId = null, $cache = true )
{
	global $Admin;
	
	$theme 		= $Admin->Settings()::Get()['theme'];
	
	$siteId 	= ( $siteId ? $siteId : $Admin->GetSite() );
	
	$cacheFile 	= CacheFileName( 'admin-apis', null, null, null, null, null, null, $siteId );
	
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$r = ReadCache( $cacheFile );
	}
	
	else
	{
		//Query: apis
		$data = $Admin->db->from( 
		null, 
		"SELECT id, token, is_primary, disabled, name, descr, allow_data
		FROM `" . DB_PREFIX . "api_obj`
		WHERE (id_site = " . $siteId . ")
		ORDER BY id ASC"
		)->all();
	
		if ( !$data )
			return null;

		if ( $cache )
			WriteOtherCacheFile( $data, $cacheFile );
	}

	return $data;
}

#####################################################
#
# Get Single MemberGroup function
#
#####################################################
function AdminGetSingleGroup( $id )
{
	global $Admin;
	
	//Query: membergroup
	$data = $Admin->db->from( 
	null, 
	"SELECT g.*, m.group_permissions
	FROM `" . DB_PREFIX . "membergroups` AS g
	LEFT JOIN `" . DB_PREFIX . "membergroup_relation` AS m ON m.id_group = g.id_group AND m.id_site = (" . $Admin->GetSite() . ")
	WHERE (g.id_group = " . $id . ")
	ORDER BY g.id_group ASC"
	)->single();
	
	return $data;
}

#####################################################
#
# Get Single User function
#
#####################################################
function AdminGetSingleUser( $id )
{
	$db = db();
	
	//Get the User
	$data = $db->from( 
	null, 
	"SELECT u.*, m.group_name as gname
	FROM `" . DB_PREFIX . USERS . "` AS u
	LEFT JOIN `" . DB_PREFIX . "membergroups` AS m ON m.id_group = u.id_group
	WHERE (u.id_member = " . (int) $id . ")"
	)->single();
	
	return $data;
}

#####################################################
#
# Get All Groups function
#
#####################################################
function AdminGroups( $siteId = SITE_ID, $cache = true, $keysOnly = false )
{
	$cacheFile 	= CacheFileName( 'admin-groups', null, null, null, null, null, null, $siteId );
	
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$b = ReadCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		$data = $db->from( 
		null, 
		"SELECT g.*, m.group_permissions
		FROM `" . DB_PREFIX . "membergroups` AS g
		INNER JOIN `" . DB_PREFIX . "membergroup_relation` AS m ON m.id_group = g.id_group AND m.id_site = " . $siteId
		)->all();
		
		$b = array();

		if ( $data )
		{
			foreach( $data as $row )
				$b[] = $row;
		}

		if ( $cache )
			WriteOtherCacheFile( $b, $cacheFile );
	}
	
	return ( $keysOnly ? array_keys( $b ) : $b );
}

#####################################################
#
# Get All Users function
#
#####################################################
function AdminUsers( $siteId = SITE_ID, $cache = true, $keysOnly = false )
{
	$cacheFile 	= CacheFileName( 'admin-users', null, null, null, null, null, null, $siteId );
	
	if ( $cache && ValidOtherCache( $cacheFile, 1800 ) )
	{
		$b = ReadCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		$data = $db->from( 
		null, 
		"SELECT u.id_member, u.date_registered, u.real_name, u.user_name, u.num_posts, u.email_address, u.is_activated, m.group_permissions, g.group_name as gname
		FROM `" . DB_PREFIX . USERS . "` AS u
		LEFT JOIN `" . DB_PREFIX . "membergroups` AS g ON g.id_group = u.id_group
		LEFT JOIN `" . DB_PREFIX . "membergroup_relation` AS m ON m.id_group = u.id_group AND m.id_site = u.id_site
		WHERE (u.id_site = " . $siteId . ")"
		)->all();
		
		$b = array();

		if ( $data )
		{
			foreach( $data as $row )
				$b[] = $row;
		}

		if ( $cache )
			WriteOtherCacheFile( $b, $cacheFile );
	}
	
	return ( $keysOnly ? array_keys( $b ) : $b );
}

#####################################################
#
# Redirect function
#
#####################################################
function Redirect( $url = null )
{
	$url = ( !empty( $url ) ? $url : ADMIN_URI );
	@header('Location: ' . $url );
	exit;
}

#####################################################
#
# Return actual maximum upload size Function
# TODO: Remove
# Source: https://www.kavoir.com/2010/02/php-get-the-file-uploading-limit-max-file-size-allowed-to-upload.html
#####################################################
function MaxFileSizeAllowed()
{
	$max_upload = (int)(ini_get('upload_max_filesize'));
	$max_post = (int)(ini_get('post_max_size'));
	$memory_limit = (int)(ini_get('memory_limit'));

	return min($max_upload, $max_post, $memory_limit);
}

#####################################################
#
# Return actual maximum upload size Function
#
# Source: https://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
#####################################################
function ParseFiseSize( $size )
{
	$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	
	if ( $unit )
	{
		// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
		return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
	}
  
	else
		return round( $size );
}

#####################################################
#
# Create Image Dir Based on Date function
#
#####################################################
function imgFolderRoot( $date, $upload_path = null ) 
{
	$path = ( $upload_path ? $upload_path : IMAGES_ROOT );
	
	if ( !is_numeric( $date ) )
		$date = time();
	
	$y_letter = date( 'Y', $date );
	$m_letter = date( 'm', $date);
		
	if ( !is_dir( $path . $y_letter ) )
		mkdir( $path . $y_letter );

	if ( !is_dir( $path . $y_letter . DS . $m_letter ) )
		mkdir( $path . $y_letter . DS . $m_letter );
		
	return $path . $y_letter . DS . $m_letter . DS;
}

#####################################################
#
# Get Site's default Language Function
#
#####################################################
function GetSiteDefaultLanguage( $siteId )
{
	$db = db();
	
	//Query: language
	$q = $db->from( 
	null, 
	"SELECT id
	FROM `" . DB_PREFIX . "languages`
	WHERE (id_site = " . (int) $siteId . ") AND (is_default = 1)"
	)->single();
	
	return ( $q ? $q['id'] : null );
}

#####################################################
#
# Get Site's default category Function
#
#####################################################
function GetSiteDefaultCategory( $siteId, $langId )
{
	$db = db();
	
	//Query: category
	$q = $db->from( 
	null, 
	"SELECT id
	FROM `" . DB_PREFIX . "categories`
	WHERE (id_site = " . (int) $siteId . ") AND (id_lang = " . (int) $langId . ") AND (is_default = 1) AND (id_blog = 0)"
	)->single();

	return $q;
}

#####################################################
#
# Create Post Url function
#
#####################################################
function AdminCommentEditUri( $id, $blog = null, $site = null, $lang = null, $defLang = null, $slug = 'edit-post' )
{
	$uri = ADMIN_URI . $slug . PS . 'id' . PS . $id . PS;
	
	$hasSite = false;
	$hasLang = false;
	$hasBlog = false;

	if ( $site && ( $site != SITE_ID ) )
	{
		$uri .= '?site=' . $site;
		$hasSite = true;
	}
	
	if ( $blog && ( $blog > 0 ) )
	{
		$uri .= ( $hasSite ? ';' : '?' ) . 'blog=' . $blog;
		$hasBlog = true;
	}
	
	if ( $lang && $defLang && ( $lang != $defLang ) )
	{
		$uri .= ( ( $hasSite || $hasBlog ) ? ';' : '?' ) . 'lang=' . $lang;
		$hasLang = true;
	}

	return $uri;
}

#####################################################
#
# Create Post Edit Url function
#
#####################################################
function AdminPostPreviewUri( $id, $lang = null )
{
	global $Admin;
	
	$url = $Admin->Settings()::Site()['url'];

	//Add the lang slug
	if ( !$Admin->Settings()::IsTrue( 'hide_default_lang_slug' ) || ( $Admin->Settings()::IsTrue( 'hide_default_lang_slug' ) 
		&& $lang && ( $lang != $Admin->Settings()::Lang()['code'] ) )
	)
		$url .= $lang . PS;
	
	$url .= 'preview' . PS . $id . PS;
	
	//If this is an external site, we have to add a security hash to the url. If the site allows logins, then the user should be logged in before been able to preview the post
	if ( !$Admin->IsDefaultSite() && $Admin->Settings()::IsTrue( 'disable_user_login' ) )
	{
		$url .= 'hash' . PS . $Admin->Settings()::Site()['preview_hash'] . PS;
	}

	return $url;
}

#####################################################
#
# Create Post Edit Url function
#
#####################################################
function AdminPostEditUri( $id, $blog = null, $site = null, $lang = null, $defLang = null, $slug = 'edit-post' )
{
	$uri = ADMIN_URI . $slug . PS . 'id' . PS . $id . PS;
	
	$hasSite = false;
	$hasLang = false;
	$hasBlog = false;

	if ( $site && ( $site != SITE_ID ) )
	{
		$uri .= '?site=' . $site;
		$hasSite = true;
	}
	
	if ( $blog && ( $blog > 0 ) )
	{
		$uri .= ( $hasSite ? ';' : '?' ) . 'blog=' . $blog;
		$hasBlog = true;
	}
	
	if ( $lang && $defLang && ( $lang != $defLang ) )
	{
		$uri .= ( ( $hasSite || $hasBlog ) ? ';' : '?' ) . 'lang=' . $lang;
		$hasLang = true;
	}

	return $uri;
}

#####################################################
#
# Create (and share) an image and its childs function
#
#####################################################
function CreateTheImgs( $img, $site, $member = null, $ptype = 'post', $idFolder = 0, $time = null, $lang = null, $postID = 0, $ext = false, $asCover = false, $returnFullArray = true )
{
	global $Admin;
	
	$time = ( $time ? $time : time() );
	
	$site = ( $site ? $site : $Admin->GetSite() );
	
	$lang = ( $lang ? $lang : $Admin->GetLang() );
	
	$member = ( $member ? $member : $Admin->UserID() );
	
	$local = $Admin->ImageUpladDir( SITE_ID );
	
	$root = ( !empty( $local ) ? $local['root'] : null );
	
	$folder = FolderRootByDate( $time, $root );
	
	$mime = GetMimeType( $img );
	
	$fileName = CopyImage( $img, $folder, $ext, $mime );

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
	
	$imageID = addDbImage( $fileName, $folder, $site, $member, $ptype, 'full', $idFolder, $time, null, $lang, $postID, $asCover );
	
	if ( $imageID )
	{
		$share = $Admin->ImageUpladDir( $site );
			
		//If we have child site(s), ask them to copy the image
		if ( !empty( $share ) && isset( $share['share'] ) && $share['share'] )
		{
			$Admin->PingChildSite( 'sync', 'image', null, $site, $imgUrl, $time );
		}
		
		if ( $mime == 'image' )
		{
			//Create the smaller images
			CreateChildImgs( $imgRoot, $imageID, $time, $folder, $site, 0, 0, $ptype );
		}
		
		if ( $returnFullArray )
		{
			return array(
					'id' => $imageID,
					'url' => $imgUrl,
					'name' => $fileName,
					'mime' => $mime,
					'height' => $height,
					'width' => $width
			);
		}
		
		return $imageID;
	}

	return null;
}


#####################################################
#
# Recount post comments function
#
# TODO
#####################################################
function RecountPostsComments( $siteId )
{
	$db = db();
	
	return;
}

#####################################################
#
# Recount Blog Statistics function
#
#####################################################
function RecountBlogsStatistics( $siteId )
{
	$db = db();
	
	$blogs = $db->from( 
	null, 
	"SELECT id_blog
	FROM `" . DB_PREFIX . "blogs`
	WHERE (id_site = " . (int) $siteId . ")"
	)->all();
		
	if ( !empty( $blogs ) )
	{
		//Count the posts first
		foreach( $blogs as $blog )
		{
			$ps = $db->from( null, 
			"SELECT count(id_post) AS total FROM `" . DB_PREFIX . POSTS . "` WHERE (id_blog = " . $blog['id_blog'] . ")"
			)->total();	
				
			$db->update( "blogs" )->where( 'id_blog', $blog['id_blog'] )->set( "num_posts", (int) $ps );
		}
			
		//Count the comments
		foreach( $blogs as $blog )
		{
			$ps = $db->from( null, 
			"SELECT count(id) AS total FROM `" . DB_PREFIX . "comments` WHERE (id_blog = " . $blog['id_blog'] . ")"
			)->total();	
			
			$db->update( "blogs" )->where( 'id_blog', $blog['id_blog'] )->set( "num_comments", (int) $ps );
		}
	}
}

#####################################################
#
# Recount Blog Statistics function
#
#####################################################
function RecountCategoriesStatistics( $siteId )
{
	$db = db();
	
	$cats = $db->from( 
	null, 
	"SELECT id, id_parent
	FROM `" . DB_PREFIX . "categories`
	WHERE (id_site = " . (int) $siteId . ")"
	)->all();

	//We should have at least one category, so we don't need this check, but better safe than sorry
	if ( !empty( $cats ) )
	{
		foreach( $cats as $cat )
		{
			$childs = 0;
			$posts  = 0;
			
			if ( $cat['id_parent'] > 0 )
			{
				$ps = $db->from( null, 
				"SELECT count(id_post) AS total FROM `" . DB_PREFIX . POSTS . "` WHERE (id_sub_category = " . $cat['id'] . ")"
				)->total();
				
				
				$childs = $ps;
			}
			
			$ps = $db->from( null, 
			"SELECT count(id_post) AS total FROM `" . DB_PREFIX . POSTS . "` WHERE (id_category = " . $cat['id'] . ")"
			)->total();
			
			//Parent category must have as total and the child categories counted
			$posts = ( $ps + $childs );
			
			$put = ( ( $cat['id_parent'] > 0 ) ? (int) $childs : (int) $posts );
			
			$db->update( "categories" )->where( 'id', $cat['id'] )->set( "num_items", $put );
		}
	}
}

#####################################################
#
# Recount Blog Statistics function
#
#####################################################
function RecountTagsStatistics( $siteId )
{
	$db = db();
	
	$tags = $db->from( 
	null, 
	"SELECT id
	FROM `" . DB_PREFIX . "tags`"
	)->all();
	
	if ( empty( $tags ) )
	{
		return;
	}
	
	foreach( $tags as $tag )
	{
		$q = $db->from( null, 
		"SELECT count(object_id) AS total FROM `" . DB_PREFIX . "tags_relationships` WHERE (taxonomy_id = " . $tag['id'] . ")"
		)->total();
		
		$db->update( "tags" )->where( 'id', $tag['id'] )->set( "num_items", $q );
	}
}

#####################################################
#
# Optimize Main Site DB function
#
#####################################################
function OptimizeDB()
{
	global $Admin;
	
	$db = dbLoad();
	
	$msg = '';
	$optimized = array();
	
	try 
	{
		$get = $db->prepare( "SHOW TABLE STATUS" );

		$get->execute();
		
		$tables = $get->fetchAll(PDO::FETCH_ASSOC);
	}
	
	catch(PDOException $e) 
	{
		$Admin->SetAdminMessage( __( 'an-error-happened' ) );
		return;
	}
	
	if ( empty( $tables ) )
	{
		$Admin->SetAdminMessage( __( 'an-error-happened' ) );
		return;
	}
	
	$msg .= sprintf( __( 'your-database-contains-tables' ) , count ($tables) ) . '<br />';
	
	$msg .= __( 'attempting-to-optimize-your-database' ) . '...' . '<br />';

	foreach( $tables as $table )
	{
		if ( $table['Data_free'] > 0 )
		{
			try 
			{
				$opt = $db->prepare( sprintf( "OPTIMIZE TABLE %s", $table['Name'] ) );

				if ( $opt->execute() )
				{
					$optimized[] = $table['Name'];
				}
			}
			catch(PDOException $e) 
			{
				//
			}	
		}
	}

	if ( empty( $optimized ) )
		$msg .= '<br /><strong>' . __( 'all-the-tables-were-already-optimized' ) . '</strong><br />';
	
	else
	{
		$msg .= '<br />' . __( 'the-following-database-tables-are-optimized' ) . ':' . '<br />';
		$msg .= implode( '<br />', $optimized );
		$msg .= '<br />';
	}
	
	$Admin->SetAdminMessage( $msg );
}

#####################################################
#
# Get Full Categories List function
#
# Returns every category based on its lang and blog and site
#
#####################################################
function GetFullSiteCats( $lang = null )
{
	global $Admin;
	
	$_categories = array();
	
	$lang = ( $lang ? $lang : $Admin->GetLang() );
	
	//Get the sites
	$_sites = $db->from( null, "
	SELECT id, title, url, is_primary
	FROM `" . DB_PREFIX . "sites`
	ORDER BY id ASC"
	)->all();
	
	if ( !$_sites )
		return null;
	
	foreach ( $_sites as $_site )
	{
		$_categories[$_site['id']] = array(
							'name' => stripslashes( $_site['title'] ),
							'url' => stripslashes( $_site['url'] ),
							'primary' => $_site['is_primary'],
							'id' => $_site['id'],
							'type' => 'site',
							'langs' => array()
				
				
		);
		
		$_langs = $db->from( null, "
		SELECT id, code, is_default, locale, title
		FROM `" . DB_PREFIX . "languages`
		WHERE (status = 'active') AND (id_site = " . $_site['id'] . ")
		ORDER BY lang_order ASC"
		)->all();
		
		if ( !$_langs )
			continue;

		//If the site has multiblog enabled, we need a bit more work
		if ( $Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) )
		{
			foreach( $_langs as $_lang )
			{
				//We need the blogs now
				$_blogs = $db->from( null, "
				SELECT id_blog, name
				FROM `" . DB_PREFIX . "blogs`
				WHERE (id_lang = " . $_lang['id'] . " OR id_lang = 0) AND (id_site = " . $_site['id'] . ")
				ORDER BY name ASC"
				)->all();
			
				$_categories[$_site['id']]['langs'][$_lang['code']] = array(
									'name' => stripslashes( $_lang['title'] ),
									'id' => $_lang['id'],
									'type' => 'lang',
									'blogs' => array()
				
				
				);
				
				if ( $_blogs )
				{
					foreach( $_blogs as $_blog )
					{
						$_categories[$_site['id']]['langs'][$_lang['code']]['blogs'][$_blog['id_blog']] = array(
												'name' => stripslashes( $_blog['name'] ),
												'id' => $_blog['id_blog'],
												'type' => 'blog',
												'cats' => array()
					
						);
						
						$cats = $db->from( null, "
						SELECT id, name, is_default
						FROM `" . DB_PREFIX . "categories`
						WHERE (id_parent = 0) AND (id_blog = " . $_blog['id_blog'] . ") AND (id_lang = " . $_lang['id'] . ")
						ORDER BY name ASC"
						)->all();

						if ( $cats )
						{
							foreach ( $cats as $cat )
							{
								$_categories[$_site['id']]['langs'][$_lang['code']]['blogs'][$_blog['id_blog']]['cats'][$cat['id']] = array(
										'name' => stripslashes( $cat['name'] ),
										'id' => $cat['id'],
										'default' => $cat['is_default'],
										'type' => 'cat',
										'childs' => array()
						
								);
								
								$subCats = $db->from( null, "
								SELECT id, name
								FROM `" . DB_PREFIX . "categories`
								WHERE (id_parent = " . $cat['id'] . ")
								ORDER BY name ASC"
								)->all();
					
								if ( $subCats )
								{
									foreach ( $subCats as $sub )
									{
										$_categories[$_site['id']]['langs'][$_lang['code']]['blogs'][$_blog['id_blog']]['cats'][$cat['id']]['childs'][$sub['id']] = array(
														'name' => stripslashes( $sub['name'] ),
														'type' => 'sub',
														'id' => $sub['id'],
										);
									}
								}
							}
						}
					}
				}
				
				$_cats = $db->from( null, "
				SELECT id, name, is_default
				FROM `" . DB_PREFIX . "categories`
				WHERE (id_parent = 0) AND (id_blog = 0) AND (id_lang = " . $_lang['id'] . ")
				ORDER BY name ASC"
				)->all();
				
				$_categories[$_site['id']]['langs'][$_lang['code']]['blogs'][0] = array(
													'name' => __( 'orphan-categories' ),
													'type' => 'blog',
													'id' => '0',
													'cats' => array()
						
				);
			
				if ( $_cats )
				{
					foreach ( $_cats as $_cat )
					{
						$_categories[$_site['id']]['langs'][$_lang['code']]['blogs'][0]['cats'][$_cat['id']] = array(
														'name' => stripslashes( $_cat['name'] ),
														'type' => 'cat',
														'id' => $_cat['id'],
														'default' => $_cat['is_default'],
														'childs' => array()
						
						);
						
						$subCats = $db->from( null, "
						SELECT id, name
						FROM `" . DB_PREFIX . "categories`
						WHERE (id_parent = " . $_cat['id'] . ")
						ORDER BY name ASC"
						)->all();
						
						if ( $subCats )
						{
							foreach ( $subCats as $sub )
							{
								$_categories[$_site['id']]['langs'][$_lang['code']]['blogs'][0]['cats'][$_cat['id']]['childs'][$sub['id']] = array(
											'name' => stripslashes( $sub['name'] ),
											'type' => 'sub',
											'id' => $sub['id']
								);
							}
						}
					}
				}
			}

			unset( $_blogs );
		}
	
		else
		{
			foreach( $_langs as $_lang )
			{
				$_cats = $db->from( null, "
				SELECT id, name
				FROM `" . DB_PREFIX . "categories`
				WHERE (id_parent = 0) AND (id_blog = 0) AND (id_lang = " . $_lang['id'] . ")
				ORDER BY name ASC"
				)->all();
				
				$_categories[$_site['id']]['langs'][$_lang['code']]['blogs'][0] = array(
											'name' => __( 'orphan-categories' ),
											'type' => 'blog',
											'id' => '0',
											'cats' => array()
						
				);
				
				if ( $_cats )
				{
					foreach ( $_cats as $_cat )
					{
						$_categories[$_site['id']]['langs'][$_lang['code']]['blogs'][0]['cats'][$_cat['id']] = array(
																	'name' => stripslashes( $_cat['name'] ),
																	'type' => 'cat',
																	'id' => $_cat['id'],
																	'childs' => array()
						
						);
						
						$subCats = $db->from( null, "
						SELECT id, name
						FROM `" . DB_PREFIX . "categories`
						WHERE (id_parent = " . $_cat['id'] . ")
						ORDER BY name ASC"
						)->all();

						if ( $subCats )
						{
							foreach ( $subCats as $sub )
							{
								$_categories[$_site['id']]['langs'][$_lang['code']]['blogs'][0]['cats'][$_cat['id']]['childs'][$sub['id']] = array(
															'name' => stripslashes( $sub['name'] ),
															'type' => 'sub',
															'id' => $sub['id']
								);
							}
						}
					}
				}
			}
		}
	}
	
	unset( $_langs, $subCats, $_cats );

	return $_categories;
}

#####################################################
#
# Get Lamg Info function
#
#####################################################
function GetLangInfo( $t = 'id', $id = 1 )
{
	global $Admin;
	
	$langs = $Admin->Settings()::AllLangsById();
	
	if ( !isset( $langs[$id] ) )
		return null;

	$data = null;

	switch( $t )
	{
		case 'key':
				$data = $langs[$id]['lang']['code'];
			break;
		
		case 'flag':
				$data = $langs[$id]['lang']['flagicon'];
			break;
			
		case 'timeFormat':
				$data = $langs[$id]['settings']['time_format'];
			break;
			
		case 'dateFormat':
				$data = $langs[$id]['settings']['date_format'];
			break;
			
		case 'locale':
				$data = $langs[$id]['lang']['locale'];
			break;
			
		case 'code':
				$data = $langs[$id]['lang']['code'];
			break;
			
		case 'id':
				$data = $langs[$id]['lang']['id'];
			break;
	}
	
	return $data;
}

#####################################################
#
# Escape REGEX function
#
#####################################################
function EscapeRegex( $string, $read = false, $preg = false, $entities = false ) 
{
	$string = trim( $string );
	
	if ( $preg )
		return preg_quote( $string );
	
	if ( $read )
	{
		$string = str_replace( 
				array( '\"', "\'", '\/' ),
				array( '"', "'", '/' ),
				$string
		);

		return htmlentities( stripslashes( $string ) );
	}
	
	if ( $entities )
		$string = html_entity_decode ( $string );
		
	$string = htmlspecialchars_decode ($string);

	$string = str_replace('\\\/', "{{3}}", $string);
	$string = str_replace('\\/', "{{2}}", $string);

	$string = str_replace(array('\"', '\/', "\'"), array('"', '/', "'"), $string);
		
	$string = str_replace(array('"', '/', "'"), array('\"', '\/', "\'"), $string);

	$string = str_replace("{{2}}", "\\/", $string);

	$string = str_replace("{{3}}", "\\\/", $string);
		
	if ( $entities )
		$string = htmlentities($string, ENT_QUOTES, "UTF-8");;
		
	return $string;
}

#####################################################
#
# Array Search function
#
#####################################################
function SearchArray( $value, $key, $array )
{
   foreach ($array as $k => $val)
   {
	   if ($val[$key] == $value) 
	   {
		   return $k;
       }
   }

   return null;
}

#####################################################
#
# Is Compatible function
#
# This function checks it the given version is compatible with the current version of the TOKICMS
#
#####################################################
function IsCompatible( $metadata )
{
	//$tokiVersion = str_replace( '.', '', TOKI_VERSION );
	//$compatible = str_replace( '.', '', $metadata );
	
	//if ( $compatible >= $tokiVersion )
		//return true;
	
	//return false;

	if ( version_compare( TOKI_VERSION, $metadata, '<' ) == true )
		return false;
	
	return true;
}