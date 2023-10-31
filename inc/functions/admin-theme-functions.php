<?php defined('TOKICMS') or die('Hacking attempt...');

//V2 Theme Functions

include_once ( FUNCTIONS_ROOT .  'stats-functions.php' );

#####################################################
#
# Categories array for forms
#
#####################################################
function GetCategoriesList( $siteId = SITE_ID )
{
	global $Admin;
	
	$_categories = array();
	
	$_langs = $Admin->db->from( 
	null, 
	"SELECT id, code, is_default, locale, title
	FROM `" . DB_PREFIX . "languages`
	WHERE (id_site = " . (int) $siteId . ") AND (status = 'active')
	ORDER BY lang_order ASC"
	)->all();
	
	if ( $_langs )
	{
		//If the site has multiblog enabled, we need a bit more work
		if ( IsTrue( $Admin->Settings()::Site()['enable_multiblog'] ) )
		{
			foreach( $_langs as $_lang )
			{
				//We need the blogs now
				$_blogs = $Admin->db->from( 
				null, 
				"SELECT id_blog, name
				FROM `" . DB_PREFIX . "blogs`
				WHERE (id_site = " . (int) $siteId . ") AND (id_lang = " . $_lang['id'] . " OR id_lang = 0)
				ORDER BY name ASC"
				)->all();
					
				$_categories[$_lang['code']] = array(
					'name' => stripslashes( $_lang['title'] ),
					'id' => $_lang['id'],
					'type' => 'lang',
					'childs' => array()
				);

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
						WHERE (id_parent = 0) AND (id_blog = " . $_blog['id_blog'] . ") AND (id_lang = " . $_lang['id'] . ")
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
											'id' => $sub['id']
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
				WHERE (id_parent = 0) AND (id_blog = 0) AND (id_lang = " . $_lang['id'] . ")
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
				WHERE (id_parent = 0) AND (id_blog = 0) AND (id_lang = " . $_lang['id'] . ")
				ORDER BY name ASC"
				)->all();

				$_cats = Query( $query, true );
				
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
		}
	}
	
	return $_categories;
}

#####################################################
#
# Get Admin Categories based on language function
#
# For edit theme page
#
#####################################################
function GetAllAdminCategories( $orderBy = 'name', $order = 'ASC', $getChilds = true )
{
	global $Admin;
	
	$query = "
	SELECT c.id, c.name, c.is_default, b.name as blogName
	FROM `" . DB_PREFIX . "categories` AS c
	LEFT JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
	WHERE 1=1 AND (c.id_parent = 0) AND (c.id_lang = " . $Admin->GetLang() . ")
	ORDER BY c." . $orderBy . " " . $order;
	
	//Query: categories
	$tmp = $Admin->db->from( null, $query )->all();

	$cats = array();
	
	if ( !empty( $tmp ) )
	{
		foreach( $tmp as $c )
		{
			$name = $c['name'] . ( $c['is_default'] ? ' [Default]' : '' );
			$name .= ( !empty( $c['blogName'] ) ? ' [' . __( 'blog' ) . ': ' . $c['blogName'] : '' );
			
			$cats[$c['id']] = array(
				'name' => StripContent( $name ),
				'id' => $c['id'],
			);
		}
	}
	
	return $cats;
}

#####################################################
#
# Get Pages Based on Language function
#
# For edit theme page
#
#####################################################
function GetAdminPages( $items = null, $siteId = null, $langId = null, $blogId = null, $cache = true )
{
	global $Admin;
	
	$siteId 	= ( $siteId ? $siteId : $Admin->GetSite() );
	$langId 	= ( $langId ? $langId : $Admin->GetLang() );
	
	$cacheFile 	= CacheFileName( 'admin-dash-pages-userid_' . $Admin->UserID(), null, $langId, $blogId, null, $items, null, $siteId );
	
	//Get the data from the cache, if is valid
	if ( ValidOtherCache( $cacheFile, 1800 ) )
	{
		$pages = ReadCache( $cacheFile );
	}
	
	//Get the data and save it to the cache, if needed...
	else
	{
		$db = db();
		
		$q = "(p.id_site = " . $siteId . ") AND (p.id_lang = " . $langId . ")" . ( $blogId ? " AND (p.id_blog = " . $Admin->GetBlog() . ")" : "" ) . ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) ? " AND (p.id_member = " . $Admin->UserID() . ")" : "" ) . " AND (p.post_type = 'page') AND (p.post_status = 'published')";

		$query = PostsDefaultQuery( $q, $items, 'p.added_time DESC', 'p.id_post', null, false );

		//Query: posts
		$tmp = $db->from( null, $query )->all();
		
		$pages = array();
		
		if ( !empty( $tmp ) )
		{
			$s = GetSettingsData( $siteId );
			
			if ( empty( $s ) )
			{
				return null;
			}

			foreach ( $tmp as $p )
			{			
				$p = array_merge( $p, $s );
				
				$pages[$p['id_post']] 			= BuildPostVars( $p );
				$pages[$p['id_post']]['name'] 	= $p['title'];
			}

			WriteOtherCacheFile( $pages, $cacheFile );
		}
	}

	return $pages;
}

#####################################################
#
# Get Ads based on language function
#
# For edit theme page
#
#####################################################
function GetAllAdminAds( $siteId = SITE_ID, $langId = null )
{
	global $Admin;
	
	$ads = array();
	
	//Query: ads
	$tmp = $Admin->db->from( null, "
	SELECT id, title
	FROM `" . DB_PREFIX . "ads`
	WHERE (id_site = " . $siteId . ") AND (disabled = 0)" . ( $langId ? " AND (id_lang = " . $langId . " OR id_lang = 0)" : "" ) . "
	ORDER BY title ASC"
	)->all();
	
	return $tmp;
}

#####################################################
#
# Get Blogs Based on Language function
#
# For edit theme page
#
#####################################################
function GetAdminBlogs( $siteId = SITE_ID, $langId = null )
{
	$db = db();
	
	$query = "
	SELECT b.name, b.trans_data, b.id_blog, la.title as lt
	FROM `" . DB_PREFIX . "blogs` AS b
	LEFT JOIN `" . DB_PREFIX . "languages` as la ON la.id = b.id_lang
	WHERE (b.id_site = " . (int) $siteId . ") AND (b.disabled = 0) AND (b.id_lang = 0 " . ( $langId ? "OR b.id_lang = " . (int) $langId : "" ) . ")
	ORDER BY b.name ASC";

	//Query: blogs
	$data = $db->from( null, $query )->all();
	
	$code = GetLandCodeById( $langId );

	$b = array();

	if ( $data )
	{
		foreach( $data as $d )
		{
			$name = StripContent( $d['name'] );
			
			if ( !empty( $d['trans_data'] ) )
			{
				$trans = Json( $d['trans_data'] );
				
				if ( !empty( $trans ) && isset( $trans[$code]['name'] ) )
				{
					$name = $trans[$code]['name'] . ' (' . $name . ')';
				}
			}
			
			if ( !empty( $d['lt'] ) )
			{
				$name .= ' [' . __( 'enabled-in' ) . ' ' . StripContent( $d['lt'] ) . ']';
			}
			else
			{
				$name .= ' [' . __( 'enabled-in' ) . ' ' . __( 'everywhere' ) . ']';
			}
			
			$b[$d['id_blog']] = array(
				'name' => $name,
				'id' => $d['id_blog'],
			);
		}
	}
	
	return $b;
}

function AdminCustomTagsInPost( $id, $name, $tags = array() )
{
	$html = '<div class="col-sm-12">';
	
	$html .= '<div id="customTagsDiv' . $id . '" class="d-none">';
	
	$html .= '<input class="tags tagify--outside" id="customtags' . $id . '" placeholder="' . __( 'enter-something' ) . '" name="customTags[' . $id . ']" type="text" value="' . ( !empty( $tags ) ? implode( ', ', $tags ) : '' ) . '">';
	
	$html .= '<p class="mb-3">';
	
	$html .= '<a class="ms-2 text-sm" data-toggle="collapse" href="#collapseCustomAddTags' . $id . '" role="button" aria-expanded="false" aria-controls="collapseStatus">' . __( 'add-tags' ) . '</a>';
	
	$html .= '
		<div class="collapse" id="collapseCustomAddTags' . $id . '">
			<a class="ms-2 text-sm" data-toggle="collapse" href="#collapseCustomTopTags' . $id . '" role="button" aria-expanded="false" aria-controls="collapseStatus">' . __( 'choose-from-the-most-used-tags' ) . '</a>
			<div class="collapse" id="collapseCustomTopTags' . $id . '">
				<div class="form-group col-sm-12">
					<div class="pt-3">
						<span class="text-danger" id="kt_tagify_custom_suggestions' . $id . '">
					</span>
				</div>
			</div>
		</div>
	</div>
	</p>
	</div>
	</div>';
	
	echo $html;
}

function AdminGetClonePostsInfo( $siteId, $isPage )
{
	global $Admin;
	
	$db = db();
	
	$html 	= '';
	$data	= array();

	$sites = $db->from( null,
	"SELECT id, title, enable_multilang, enable_multiblog
	FROM `" . DB_PREFIX . "sites`
	ORDER BY title ASC"
	)->all();
	
	//This is to avoid any output errors, normally this can not be null
	if ( $sites )
	{
		foreach( $sites as $site )
		{
			$siteId = $site['id'];
			
			$data[$siteId] = array(
				'id' 		=> $siteId,
				'title'		=> StripContent( $site['title'] ),
				'multiLang'	=> ( ( $site['enable_multilang'] == 'true' ) ? true : false ),
				'multiBlog'	=> ( ( $site['enable_multiblog'] == 'true' ) ? true : false ),
				'langs'		=> array()
			);
			
			$langs = $db->from( null,
			"SELECT id, title
			FROM `" . DB_PREFIX . "languages`
			WHERE (id_site = " . $siteId . ") AND (status = 'active')
			ORDER BY title ASC"
			)->all();
			
			if ( $langs )
			{
				foreach( $langs as $lang )
				{
					$langId = $lang['id'];
					
					$data[$siteId]['langs'][$langId] = array(
						'id' 	=> $langId,
						'title'	=> StripContent( $lang['title'] ),
						'blogs'	=> array(),
						'cats'	=> array()
					);
					
					//Query: blogs
					$blogs = $db->from( null, "
					SELECT id_blog, name
					FROM `" . DB_PREFIX . "blogs`
					WHERE (id_lang = " . $langId . " OR id_lang = 0) AND (id_site = " . $siteId . ")
					ORDER BY name ASC"
					)->all();
					
					if ( $blogs )
					{
						foreach( $blogs as $blog )
						{
							$blogId = $blog['id_blog'];
							
							$data[$siteId]['langs'][$langId]['blogs'][$blogId] = array
							(
								'id' 		=> $blogId,
								'title'		=> StripContent( $blog['name'] ),
								'cats'	=> array(),
							);

							//Query: categories (blogs)
							$cats = $db->from( null, "
							SELECT id, name
							FROM `" . DB_PREFIX . "categories`
							WHERE (id_parent = 0) AND (id_lang = " . $langId . ") AND (id_blog = " . $blogId . ")
							ORDER BY name ASC"
							)->all();
							
							if ( $cats )
							{
								foreach( $cats as $cat )
								{
									$data[$siteId]['langs'][$langId]['blogs'][$blogId]['cats'][$cat['id']] = array
									(
										'id' 		=> $cat['id'],
										'title'		=> StripContent( $cat['name'] ),
										'childs'	=> array(),
									);
									
									//Query: child categories (blogs)
									$childs = $db->from( null, "
									SELECT id, name
									FROM `" . DB_PREFIX . "categories`
									WHERE (id_parent = " . $cat['id'] . ") AND (id_lang = " . $langId . ") AND (id_blog = " . $blogId . ")
									ORDER BY name ASC"
									)->all();
									
									if ( $childs )
									{
										foreach( $childs as $child )
										{
											$data[$siteId]['langs'][$langId]['blogs'][$blogId]['cats'][$cat['id']]['childs'][] = array(
												'id' 		=> $child['id'],
												'title'		=> StripContent( $child['name'] )
											);
										}
									}
								}
							}
						}
					}
					
					//Query: categories (no blogs)
					$cats = $db->from( null, "
					SELECT id, name
					FROM `" . DB_PREFIX . "categories`
					WHERE (id_parent = 0) AND (id_lang = " . $langId . ") AND (id_blog = 0)
					ORDER BY name ASC"
					)->all();
					
					if ( $cats )
					{
						foreach( $cats as $cat )
						{
							$data[$siteId]['langs'][$langId]['cats'][$cat['id']] = array(
							
								'id' 		=> $cat['id'],
								'title'		=> StripContent( $cat['name'] ),
								'childs'	=> array(),
							);
							
							//Query: child categories (no blogs)
							$childs = $db->from( null, "
							SELECT id, name
							FROM `" . DB_PREFIX . "categories`
							WHERE (id_parent = " . $cat['id'] . ") AND (id_lang = " . $langId . ") AND (id_blog = 0)
							ORDER BY name ASC"
							)->all();
							
							if ( $childs )
							{
								foreach( $childs as $child )
								{
									$data[$siteId]['langs'][$langId]['cats'][$cat['id']]['childs'][] = array(
										'id' 		=> $child['id'],
										'title'		=> StripContent( $child['name'] )
									);
								}
							}
						}
					}
				}
			}
		}
	}

	$html .= '<option value="0">' . __( 'choose' ) . '...</option>';
	
	foreach( $data as $site )
	{
		if ( MULTISITE )
		{
			$html .= '<optgroup label="' . __( 'site' ) . ': ' . StripContent( $site['title'] ) . '">';
		}
		
		foreach( $site['langs'] as $lang )
		{
			if ( $site['multiLang'] && ( count( $site['langs'] ) > 1 ) )
			{
				$html .= '<optgroup label="— ' . $lang['title'] . '">';
			}
			
			if ( $site['multiBlog'] && !empty( $lang['blogs'] ) )
			{
				foreach( $lang['blogs'] as $blog )
				{
					if ( empty( $blog['cats'] ) )
					{
						continue;
					}
					
					$html .= '<optgroup label="—— ' . __( 'blog' ) . ': ' . $blog['title'] . '">';
					
					if ( !$isPage )
					{
						$html .= '<option value="-1" data-id="' . $blog['id'] . '" data-lang="' . $lang['id'] . '">' . __( 'no-category-post-to-a-page' ) . '</option>';
					}

					else
					{
						$html .= '<option value="-1" data-id="' . $blog['id'] . '" data-lang="' . $lang['id'] . '">' . __( 'no-category-page' ) . '</option>';
					}
					
					foreach( $blog['cats'] as $cat )
					{
						$html .= '<option value="' . $cat['id'] . '" data-id="' . $blog['id'] . '" data-lang="' . $lang['id'] . '">' . $cat['title'] . '</option>';
						
						if ( empty( $cat['childs'] ) )
						{
							foreach( $cat['childs'] as $child )
							{
								$html .= '<option value="' . $child['id'] . '" data-id="' . $blog['id'] . '" data-lang="' . $lang['id'] . '">— ' . $child['title'] . '</option>';
							}
						}
					}
					
					$html .= '</optgroup>';
				}
			}
			
			if ( !empty( $lang['cats'] ) )
			{
				if ( $site['multiBlog'] && !empty( $lang['blogs'] ) )
				{
					$html .= '<optgroup label="——— ' . __( 'orphaned-categories' ) . '">';
				}
				else
				{
					$html .= '<optgroup label="—— ' . __( 'categories' ) . '">';
				}
				
				if ( !$isPage )
				{
					$html .= '<option value="-1" data-id="0" data-lang="' . $lang['id'] . '">' . __( 'no-category-post-to-a-page' ) . '</option>';
				}

				else
				{
					$html .= '<option value="-1" data-id="0" data-lang="' . $lang['id'] . '">' . __( 'no-category-page' ) . '</option>';
				}
					
				foreach( $lang['cats'] as $cat )
				{
					$html .= '<option value="' . $cat['id'] . '" data-id="0" data-lang="' . $lang['id'] . '">' . $cat['title'] . '</option>';
						
					if ( empty( $cat['childs'] ) )
					{
						foreach( $cat['childs'] as $child )
						{
							$html .= '<option value="' . $child['id'] . '" data-id="0" data-lang="' . $lang['id'] . '">— ' . $child['title'] . '</option>';
						}
					}
				}
				
				if ( $site['multiBlog'] )
				{
					$html .= '</optgroup>';
				}
			}
			
			if ( $site['multiLang'] )
			{
				$html .= '</optgroup>';
			}
		}
		
		if ( MULTISITE )
		{
			$html .= '</optgroup>';
		}
	}
	
	return $html;
}

function AdminMovePost( $blogId, $siteId, $parentId )
{
	global $Admin;
	
	$html = '';
	
	if ( !MULTISITE && !$Admin->MultiBlog() )
		return $html;
	
	if ( MULTISITE && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-sites' ) ) )
	{
		$sites = $Admin->Sites();
	}
	
	else
	{
		$sites = null;
	}
	
	$html .= '
	<!-- Move Post -->
	<div class="card shadow-sm mb-4">
		<div class="card-header">
			<h4 class="card-heading">' . __( 'move-post' ) . '</h4>
		</div>
		
		<div class="card-body">
			<div class="form-group">
				<select id="movePostSelection" class="form-control shadow-none" style="width: 100%; height:36px;" name="move-post" aria-label="Move select">
					<option value="0" selected>' . __( 'choose' ) . '...</option>';
					
		if ( MULTISITE )
		{
			$html .= '<optgroup label="' . __( 'sites' ) . '">';

			if ( ( $Admin->GetSite() != SITE_ID ) && ( $siteId != SITE_ID ) )
			{
				$html .= '<option value="site" data-id="' . SITE_ID . '">' . $Admin->DefaultSiteName() . '</option>';
			}

			if ( !empty( $sites ) )
			{
				foreach ( $sites as $singeSite )
				{
					if ( $siteId == $singeSite['id'] )
						continue;

						$html .= '<option value="site" data-id="' . $singeSite['id'] . '">' . $singeSite['title'] . '</option>';
				}
			}

			$html .= '</optgroup>';
		}

		if ( $Admin->MultiBlog() )
		{
			$Blogs = $Admin->Settings()::BlogsFullArrayById();

			if ( !empty( $Blogs ) )
			{
				$html .= '<optgroup label="' . __( 'blogs' ) . '">';
							
				foreach( $Blogs as $bId => $bData )
				{
					if ( ( $blogId > 0 ) && ( $blogId == $bData['id_blog'] ) )
						continue;
							
					$html .= '<option value="blog" data-id="' . $bData['id_blog'] . '">' . $bData['name'] . '</option>';
				}

				$html .= '</optgroup>';
			}
		}
		
		$html .= '		
				</select>
			</div>';
			
		$html .= '
		<input type="hidden" name="movePostType" id="movePostTypeInput" value="0">
		<input type="hidden" name="movePostId" id="movePostIdInput" value="0">
		
		<div id="loaderShow" class="form-group d-none">
			<label>&nbsp;</label>
			<img class="form-check" src="' . HTML_ADMIN_PATH_THEME . 'assets/img/ajax-loader.gif">
		</div>
		
		<div id="blogDiv" class="form-group d-none">
			<select id="movePostBlogSelection" class="form-control shadow-none" style="width: 100%; height:36px;" name="move-post-blog"></select>
		</div>
		<div id="siteDiv" class="form-group d-none">
			<select id="movePostSiteSelection" class="form-control shadow-none" style="width: 100%; height:36px;" name="move-post-site"></select>
		</div>';
		
		if ( empty( $parentId ) )
		{
			$html .= '		
			<div class="form-check d-none" id="moveChildsInput">
				<input class="form-check-input" type="checkbox" name="movePostChildsSelection" value="1">
				<label for="moveChildsInput" class="form-check-label">' . __( 'move-child-posts' ) . '</label>
				<small class="form-text text-muted">' . __( 'move-child-posts-tip' ) . '</small>
			</div>';
		}
		
		$html .= '
		</div>
	</div>';
	
	echo $html;
}

function BuildCategoriesTable( $cat, $showAll = false, $showAllSites = false, $langs = null, $isChild = false, $echo = true )
{
	global $Admin;
	
	$tools = '<a href="' . $cat['url'] . '" target="_blank" class="action-icon" title="' . __( 'view' ) . '"> <i class="bi bi-eye"></i></a>

    <a href="' . $Admin->GetUrl( 'edit-category' . PS . 'id' . PS . $cat['id'] ) . '" class="action-icon" title="' . __( 'edit' ) . '"><i class="bi bi-pencil"></i></a>';
	
	if ( !$cat['isDefault'] )
	{

		$tools .= '<a href="' . $Admin->GetUrl( 'delete-category' . PS . 'id' . PS . $cat['id'] ) . ' " id="deleteCategory" title="' . __( 'delete' ) . '" class="action-icon" role="button" onclick="return confirm_alert()"><i class="bi bi-trash"></i></a>';
	}

	$html = '
	<tr id="cat-row' . $cat['id'] . '">
		<td class="d-none d-xl-table-cell">';
		
	if ( !$cat['isDefault'] )
	{
		$html .= '
		<label class="customcheckbox">
			<input type="checkbox" class="listCheckbox" name="categories[]" value="' . $cat['id'] . '" />
            <span class="checkmark"></span>
            </label>';
	}
	
	$html .= '
        </td>
        
		<td class="dt-body-center elements-list"><a href="' . $Admin->GetUrl( 'edit-category' . PS . 'id' . PS . $cat['id'] ) . '" class="text-reset fw-bolder">' . ( $isChild ? '¦&nbsp;&nbsp;&nbsp;&nbsp;' : '' ) . $cat['name'] . '</a><div class="d-none d-lg-block"><span class="intools">' . $tools . '</span></div></td>

		<td class="text-center d-none d-xl-table-cell" style="max-width: 50px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">' . $cat['descr'] . '</td>
			
		<td class="text-center">' . $cat['slug'] . '</td>';
				
		if ( $Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) && ( $Admin->GetBlog() == 0 ) && $showAll )
		{
			$html .= '
			<td class="pt-3 text-center d-none d-xl-table-cell">' . ( ( !empty( $cat['bn'] ) && !empty( $cat['bn'] ) ) ? $post['bn'] : '-' ) . '</td>';
		}
	
		if ( $showAllSites )
		{
			$siteLink = ( ( $cat['siteId'] == SITE_ID ) ? $cat['siteName'] : '<a href="' . ADMIN_URI . 'posts/?site=' . $cat['siteId'] . '">' . $cat['siteName'] . '</a>' );
			
			$html .= '
			<td class="pt-3 text-center d-none d-xl-table-cell">' . $siteLink . '</td>';
		}
			
		if ( $Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) && !empty( $langs ) )
		{
			foreach( $langs as $lId => $lData )
			{
				$icon = '<i class="fa fa-edit"></i>';
				$title = sprintf( __( 'edit-s-translation' ), $lData['title'] );

				$editUri = '#'; //We should find something below...

				if ( $cat['langId'] == $lData['id'] )
					$editUri = $Admin->GetUrl( 'edit-category' . PS . 'id' . PS . $cat['id'] );

				elseif ( !empty( $cat['trans'] ) && isset( $cat['trans'][$lId] ) )
					$editUri = $Admin->GetUrl( 'edit-category' . PS . 'id' . PS . $cat['trans'][$lId]['id'] );

				//Translations can only be added to parent category, so we must check that we have its ID
				elseif ( !empty( $cat['trans'] )  && isset( $cat['trans'][$Admin->DefaultLang()['code']] ) )
				{
					$icon = '<i class="fa fa-plus-circle"></i>';
					$title = sprintf( __( 'add-s-translation' ), $lData['title'] );
									
					$editUri = ADMIN_URI . 'add-cat-translation' . PS . 'id' . PS . $cat['trans'][$Admin->DefaultLang()['code']]['id'] . PS . '?';

					$editUri .= ( $Admin->IsDefaultSite() ? '' : 'site=' . $Admin->GetSite() . ';' );

					$editUri .= 'lang=' . $lData['id'];
				}

				elseif ( ( empty( $cat['trans'] ) && ( $cat['transParent'] == 0 ) ) ||  ( !empty( $cat['trans'] ) && !isset( $cat['trans'][$Admin->DefaultLang()['code']] ) ) )
				{
					$icon = '<i class="fa fa-plus-circle"></i>';
					$title = sprintf( __( 'add-s-translation' ), $lData['title'] );
								
					$editUri = ADMIN_URI . 'add-cat-translation' . PS . 'id' . PS . $cat['id'] . PS . '?';
								
					$editUri .= ( $Admin->IsDefaultSite() ? '' : 'site=' . $Admin->GetSite() . ';' );

					$editUri .= 'lang=' . $lData['id'];								
				}
				
				$html .= '
				<td class="pt-3 text-center d-none d-xl-table-cell"><a title="' . $title . '" href="' . $editUri . '">' . $icon . '</a></td>';
			
				unset( $lId, $lData );
			}
		}
		
		$html .= '<td class="text-center">';
		
		if ( !$isChild )
		{
			$html .= '<input type="radio"' . ( !$cat['isDefault'] ? ' title="' . __( 'select-as-default' ) . '"' : '' ) . ' name="default_cat" value="' . $cat['id'] . '" ' . ( $cat['isDefault'] ? 'checked' : '' ) . '><span style="display: none;">' . ( $cat['isDefault'] ? 'default' : '' ) . '</span>';
		}
		
		$html .= '</td>';
		
		if ( ( $Admin->GetBlog() > 0 ) && ( $cat['blogId'] > 0 ) )
		{
			$html .= '
			<td class="text-center"><input type="checkbox" name="hide_blog[' . $cat['id'] . ']" value="1" ' . ( !empty( $cat['hiddenBlogPage'] ) ? 'checked' : '' ) . '></span></td>';
		}
		
		$html .= '
		<td class="text-center"><input type="checkbox" name="hide_front[' . $cat['id'] . ']" value="1" ' . ( $cat['hiddenFrontPage'] ? 'checked' : '' ) . '></span><input type="hidden" name="cats_array[' . $cat['id'] . ']" value=""></td>';
		
		$html .= '
		<td class="text-center d-none d-xl-table-cell">' . $cat['items'] . '</td>';
				
		$html .= '
		<td class="table-action d-xl-none">
			' . $tools . '
		</td>
    </tr>';
	
	if ( !$echo )
		return $html;
	
	echo $html;
}

//Build the HTML for Table preview
function BuildTablePreviewHtml( $data, $echo = false )
{
	if ( empty( $data ) )
		return null;
	
	$html = '<div class="row">';
	
	foreach( $data as $elmnt )
	{
		$html .= '
		<div class="col">
			<a href="#table-item-' . $elmnt['id'] . '">' . $elmnt['name'] . '</a>
		</div>';
	}
	
	$html .= '</div>';
	
	if ( !$echo )
		return $html;
	
	echo $html;
}

//Build Table Element(s) Html
function BuildTableElementHtml( $data, $type = 'header', $echo = false )
{
	global $Admin;
	
	require ( ARRAYS_ROOT . 'forms-arrays.php');
	
	if ( empty( $data ) )
		return null;
	
	$html = '';
	
	$array = ( ( $type == 'header' ) ? $genericTablesHeaderArray : $genericTablesArray );
	
	$attrs = AdminGetAllAttributes( $Admin->GetSite() );
	
	$currencies = Currencies( $Admin->GetSite(), false );
	
	$defLang = $Admin->DefaultLangId();
	
	$curLang = $Admin->GetLang();

	foreach( $data as $dat )
	{
		$formId 	= $dat['elementId'];

		if ( !isset( $array[$formId] ) )
			continue;
		
		$arr   	  = $array[$formId]['data'];
		$styleArr = ( isset( $array[$formId]['style'] ) && !empty( $array[$formId]['style'] ) ? $array[$formId]['style'] : null );

		$storedData = $dat['data'];
		$styleData	= $dat['style'];
		
		$id 		= $dat['id'];
		$column 	= $dat['columnId'];
		
		$html .= '
			<div data-id="' . $id . '" id="element-item-' . $id . '" class="card collapsed-card">
				<div class="card-header bg-light">
					<h3 class="card-title">
						' . __( $formId ) . '
					</h3>
					<div class="card-tools">
						<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-plus"></i>
						</button>
						<button type="button" id="close" data-id="' . $id . '" class="btn btn-tool remElementButton">
							<i class="fas fa-times"></i>
						</button>
					</div>
				</div>
				
				<div class="card-body">
				
					<ul class="nav nav-tabs" id="el-' . $id . '-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="el-' . $id . '-data-tab" data-toggle="pill" href="#el-' . $id . '-data" role="tab" aria-controls="el-' . $id . '-data" aria-selected="true">' . __( 'data' ) . '</a>
						</li>
						
						<li class="nav-item">
							<a class="nav-link" id="el-' . $id . '-style-tab" data-toggle="pill" href="#el-' . $id . '-style" role="tab" aria-controls="el-' . $id . '-style" aria-selected="false">' . __( 'style-for-element' ) . '</a>
						</li>
					</ul>
					
					<div class="tab-content" id="el-' . $id . '-tabContent">
					
						<div class="tab-pane fade show active" id="el-' . $id . '-data" role="tabpanel" aria-labelledby="el-' . $id . '-data-tab">';
						
		if ( !empty( $arr ) )
		{
			foreach( $arr as $id_ => $val )
			{
				$value = ( ( !empty( $storedData ) && isset( $storedData[$id_] ) ) ? $storedData[$id_] : null );
				
				$uniq = $id_ . '-' . GenerateStrongRandomKey( 6 );
	
				$html .= '
				<div class="form-' . ( ( $val['type'] == 'checkbox' ) ? 'check' : 'group row' ) . '">';
				
				if ( $val['type'] != 'checkbox' ) 
				{
					$html .= '<label for="' . $uniq . '" class="col-sm-2 col-form-label">' . $val['label'] . '</label>';
				}
							
				$html .= '<div class="col-sm-10">';

				if ( $val['type'] == 'text' )
				{
					$elemName = 'element[' . $column . '][' . $id . '][' . $id_ . ']';
					
					$args = array(
						'label' 		=> $val['label'],
						'id' 			=> $uniq,
						'name' 			=> $elemName,
						'value' 		=> $value,
						'placeholder' 	=> ( ( isset( $val['placeholder'] ) && !empty( $val['placeholder'] ) ) ? $val['placeholder'] : null ),
						'required' 		=> ( ( isset( $val['required'] ) && !empty( $val['required'] ) ) ? true : false ),
						'disabled' 		=> ( ( isset( $val['disabled'] ) && !empty( $val['disabled'] ) ) ? true : false ),
						'class' 		=> ( ( isset( $val['colorpicker'] ) && $val['colorpicker'] ) ? ' color-picker' : null ),
					);
				
					$args['type'] = 'text';
				
					$html .= FormInput( $args, false );
				}
				
				if ( $val['type'] == 'currency' )
				{
					$elemName = 'element[' . $column . '][' . $id . '][' . $id_ . ']';
					
					$html .= '<select name="' . $elemName . '" class="form-control" id="' . $uniq . '">';

					if ( !empty( $currencies ) )
					{
						foreach( $currencies as $curr )
						{
							$name = $curr['name'];

							$name .= ' [<em>' . $curr['symbol'] . '</em>]';
							
							$html .= '<option value="' . $curr['id'] . '" ' . ( ( !empty( $value ) && ( $curr['id'] == $value ) ) ? 'selected' : '' ) . '>' . $name . '</option>';
						}
					}
						
					$html .= '</select>';
				}
				
				if ( $val['type'] == 'attribute' )
				{
					$elemName = 'element[' . $column . '][' . $id . '][' . $id_ . ']';
					
					$html .= '<select name="' . $elemName . '" class="form-control" id="' . $uniq . '">';

					if ( !empty( $attrs ) )
					{
						foreach( $attrs as $attr )
						{
							$trans = Json( $attr['trans_data'] );
							
							$name = $attr['name'];
							
							$name = ( ( !empty( $trans ) && !$Admin->IsDefaultLang() && !empty( $trans['lang-' . $curLang]['value'] ) ) ? $trans['lang-' . $curLang]['value'] : $name );
							
							$name = StripContent( $name );
							
							$name .= ( ( $attr['lan'] != $defLang ) ? ' [<em>' . $attr['lt'] . '</em>]' : '' );
							
							$html .= '<option value="' . $attr['id'] . '" ' . ( ( !empty( $value ) && ( $attr['id'] == $value ) ) ? 'selected' : '' ) . '>' . $name . ' [<em>' . $attr['gn'] . '</em>]</option>';
						}
					}
						
					$html .= '</select>';
				}
				
				if ( ( $val['type'] == 'item-group' ) && !empty( $val['items'] ) )
				{
					$html .= '<div class="row">';
						
					foreach( $val['items'] as $item_id => $item )
					{
						$elemName = 'element[' . $column . '][' . $id . '][' . $item_id . ']';
						
						//Reset this value
						$value = ( ( !empty( $storedData ) && isset( $storedData[$item_id] ) ) ? $storedData[$item_id] : null );
						
						$args_ = array(
								'label' 		=> __( $item['name'] ),
								'id' 			=> $item_id,
								'name' 			=> $elemName,
								'value' 		=> $value,
								'placeholder' 	=> ( ( isset( $item['placeholder'] ) && !empty( $item['placeholder'] ) ) ? $item['placeholder'] : null )
						);
				
						$html .= '<div class="col">';
						$html .= FormInput( $args_, false );
						$html .= '</div>';
					}

					$html .= '</div>';
				}
				
				if ( $val['type'] == 'num' )
				{
					$elemName = 'element[' . $column . '][' . $id . '][' . $id_ . ']';
					
					$html .= '<input value="' . ( $value ? $value : 0 ) . '" id="' . $uniq . '" type="number" class="form-control-border border-width-2" name="' . $elemName . '" ' . ( ( isset( $val['step'] ) && $val['step'] ) ? ' step="' . $val['step'] . '"' : ' step="any"' ) . ( isset( $val['min'] ) ? ' min="' . $val['min'] . '"' : '' ) . ( isset( $val['max'] ) ? ' max="' . $val['max'] . '"' : '' ) . '>';
				}
					
				if ( $val['type'] == 'checkbox' )
				{
					$elemName = 'element[' . $column . '][' . $id . '][' . $id_ . ']';
					
					$html .= '<input type="checkbox" name="' . $elemName . '" class="form-check-input" value="1" id="' . $uniq . '"' . ( $value ? ' checked' : '' ) . '>';
					$html .= '<label class="form-check-label" for="' . $id_ . '">' . $val['label'] . '</label>';
				}
					
				if ( $val['type'] == 'select' )
				{
					$elemName = 'element[' . $column . '][' . $id . '][' . $id_ . ']';
					
					$html .= '<select name="' . $elemName . '" class="form-control" id="' . $uniq . '">';
						
					if ( isset( $val['firstNull'] ) && $val['firstNull'] )
					{
						$html .= '<option value="">' . __( 'choose' ) . '...</option>';
					}
					
					if ( !empty( $val['options'] ) )
					{
						foreach( $val['options'] as $id___ => $option )
						{
							$html .= '<option value="' . $id___ . '" ' . ( ( !empty( $value ) && ( $id___ == $value ) ) ? 'selected' : '' ) . '>' . $option['name'] . '</option>';
						}
					}
						
					$html .= '</select>';
				}

				if ( isset( $val['tip'] ) && !empty( $val['tip'] ) )
				{
					$html .= '<small id="' . $uniq . '" class="form-text text-muted">' . $val['tip'] . '</small>';
				}

				$html .= '
					</div>
				</div>';
			}
		}
		
		$html .= '
		</div>
		
		<div class="tab-pane fade" id="el-' . $id . '-style" role="tabpanel" aria-labelledby="el-' . $id . '-style-tab">';
		
		if ( !empty( $styleArr ) )
		{
			$html .= BuildTableDesingHtml( $id, $styleData, $styleArr );
		}

		$html .= '
		</div>
		
		</div>';
		
		$html .= '
			</div>
		</div>';
	}

	if ( !$echo )
		return $html;

	echo $html;
}

//Build Table Desing HTML
function BuildTableDesingHtml( $elId, $data, $source = 'header', $echo = false )
{
	require ( ARRAYS_ROOT . 'forms-arrays.php');
	
	$el_name = null;
	
	if ( is_string( $source ) )
	{
		$arr 	 = ( ( $source == 'header' ) ? $genericTablesHeaderDesingArray : $genericTablesCellDesingArray );
		
		$el_name = $source;
	}
	
	elseif ( is_array( $source ) && !empty( $source ) )
	{
		$arr 	 = $source;
		$el_name = 'style';
	}
	
	else
	{
		$arr = null;
	}

	$html = '';

	if ( !empty( $arr ) )
	{
		foreach( $arr as $id_ => $val )
		{
			$value = ( ( !empty( $data ) && isset( $data[$id_] ) ) ? $data[$id_] : null );

			$uniq = $id_;

			$html .= '
			<div class="form-' . ( ( $val['type'] == 'checkbox' ) ? 'check' : 'group row' ) . '">';
			
			if ( $val['type'] != 'checkbox' ) 
			{
				$html .= '<label for="' . $id_ . '" class="col-sm-2 col-form-label">' . $val['title'] . '</label>';
			}
			
			$html .= '<div class="col-sm-10">';
			
			if ( $val['type'] == 'text' )
			{
				$elemName = $el_name . '[' . $elId . '][' . $id_ . ']';
				
				$args = array(
					'label' 		=> $val['title'],
					'id' 			=> $id_,
					'name' 			=> $elemName,
					'value' 		=> $value,
					'placeholder' 	=> ( ( isset( $val['placeholder'] ) && !empty( $val['placeholder'] ) ) ? $val['placeholder'] : null ),
					'required' 		=> ( ( isset( $val['required'] ) && !empty( $val['required'] ) ) ? true : false ),
					'disabled' 		=> ( ( isset( $val['disabled'] ) && !empty( $val['disabled'] ) ) ? true : false ),
					'class' 		=> ( ( isset( $val['colorpicker'] ) && $val['colorpicker'] ) ? ' color-picker' : null ),
				);
			
				$args['type'] = 'text';
				
				$html .= FormInput( $args, false );
			}
			
			if ( $val['type'] == 'item-group' )
			{
				if ( !empty( $val['items'] ) )
				{
					$html .= '<div class="row">';
					
					foreach( $val['items'] as $item_id => $item )
					{
						$elemName = $el_name . '[' . $elId . '][' . $item_id . ']';
						
						//Reset this value
						$value = ( ( !empty( $data ) && isset( $data[$item_id] ) ) ? $data[$item_id] : null );
						
						$html .= '<div class="col">';
						
						if ( $item['type'] == 'text' )
						{
							$args_ = array(
								'label' 		=> ( !empty( $item['name'] ) ? __( $item['name'] ) : null ),
								'id' 			=> $item_id,
								'name' 			=> $elemName,
								'value' 		=> $value,
								'placeholder' 	=> ( ( isset( $item['placeholder'] ) && !empty( $item['placeholder'] ) ) ? $item['placeholder'] : null ),
								'required' 		=> ( ( isset( $item['required'] ) && !empty( $item['required'] ) ) ? true : false ),
								'disabled' 		=> ( ( isset( $item['disabled'] ) && !empty( $item['disabled'] ) ) ? true : false ),
								'class' 		=> ( ( isset( $item['colorpicker'] ) && $item['colorpicker'] ) ? ' color-picker' : null ),
							);

							$html .= FormInput( $args_, false );
						}
						
						elseif ( $item['type'] == 'select' )
						{
							$html .= '<select name="' . $elemName . '" class="form-control" id="' . $uniq . '">';
					
							if ( isset( $val['firstNull'] ) && $val['firstNull'] )
							{
								$html .= '<option value="">' . __( 'choose' ) . '...</option>';
							}
							
							if ( !empty( $item['options'] ) )
							{
								foreach( $item['options'] as $id___ => $option )
								{
									$html .= '<option value="' . $id___ . '" ' . ( ( !empty( $value ) && ( $id___ == $value ) ) ? 'selected' : '' ) . '>' . ( is_numeric( $option['name'] ) ? $option['name'] : __( $option['name'] ) ) . '</option>';
								}
							}

							$html .= '</select>';
						}
						
						$html .= '</div>';
					}
					
					$html .= '</div>';
				}
			}
			
			if ( $val['type'] == 'num' )
			{
				$elemName = $el_name . '[' . $elId . '][' . $id_ . ']';
				
				$html .= '<input value="' . ( $value ? $value : 0 ) . '" id="' . $uniq . '" type="number" class="form-control-border border-width-2" name="' . $elemName . '" ' . ( ( isset( $val['step'] ) && $val['step'] ) ? ' step="' . $val['step'] . '"' : ' step="any"' ) . ( isset( $val['min'] ) ? ' min="' . $val['min'] . '"' : '' ) . ( isset( $val['max'] ) ? ' max="' . $val['max'] . '"' : '' ) . '>';
			}
				
			if ( $val['type'] == 'checkbox' )
			{
				$elemName = $el_name . '[' . $elId . '][' . $id_ . ']';
				
				$html .= '<input type="checkbox" name="' . $elemName . '" class="form-check-input" value="1" id="' . $uniq . '"' . ( $value ? ' checked' : '' ) . '>';
				$html .= '<label class="form-check-label" for="' . $id_ . '">' . $val['label'] . '</label>';
			}
				
			if ( $val['type'] == 'select' )
			{
				$elemName = $el_name . '[' . $elId . '][' . $id_ . ']';
				
				$html .= '<select name="' . $elemName . '" class="form-control" id="' . $uniq . '">';
					
				if ( isset( $val['firstNull'] ) && $val['firstNull'] )
				{
					$html .= '<option value="">' . __( 'choose' ) . '...</option>';
				}
				
				if ( !empty( $val['options'] ) )
				{
					foreach( $val['options'] as $id___ => $option )
					{
						$html .= '<option value="' . $id___ . '" ' . ( ( !empty( $value ) && ( $id___ == $value ) ) ? 'selected' : '' ) . '>' . ( is_numeric( $option['name'] ) ? $option['name'] : __( $option['name'] ) ) . '</option>';
					}
				}
					
				$html .= '</select>';
			}
			
			if ( isset( $val['tip'] ) && !empty( $val['tip'] ) )
			{
				$html .= '<small id="' . $uniq . '" class="form-text text-muted">' . $val['tip'] . '</small>';
			}
			
			$html .= '
				</div>
			</div>';
		}
	}
		
	if ( !$echo )
		return $html;

	echo $html;
}

function BuildFormElementHtml( $data, $formId, $id = null, $columnId = null, $type = null, $echo = false )
{
	global $Admin;
	
	require ( ARRAYS_ROOT . 'forms-arrays.php');
	
	$array = ( !$type ? $genericFormsArray : ( ( $type == 'header' ) ? $genericTablesHeaderArray : $genericTablesArray ) );

	if ( !isset( $array[$formId] ) )
		return null;
	
	$arr = $array[$formId]['data'];
	
	$attrs = AdminGetAllAttributes( $Admin->GetSite() );
	
	$currencies = Currencies( $Admin->GetSite(), false );
	
	$defLang = $Admin->DefaultLangId();
	
	$curLang = $Admin->GetLang();

	$html = '';
	
	if ( !empty( $arr ) )
	{
		foreach( $arr as $id_ => $val )
		{
			$value = ( ( !empty( $data ) && isset( $data[$id_] ) ) ? $data[$id_] : null );
			
			$uniq = $id_ . '-' . GenerateStrongRandomKey( 6 );
			
			$elemName = 'element';
			
			$elemName .= ( $columnId ? '[' . $columnId . ']' : '' );
			
			$elemName .= '[' . $id . '][' . $id_ . ']';
			
			$args = array(
				'label' 		=> $val['label'],
				'id' 			=> $uniq,
				'name' 			=> $elemName,
				'value' 		=> $value,
				'placeholder' 	=> ( ( isset( $val['placeholder'] ) && !empty( $val['placeholder'] ) ) ? $val['placeholder'] : null ),
				'required' 		=> ( ( isset( $val['required'] ) && !empty( $val['required'] ) ) ? true : false ),
				'disabled' 		=> ( ( isset( $val['disabled'] ) && !empty( $val['disabled'] ) ) ? true : false ),
				'class' 		=> ( ( isset( $val['colorpicker'] ) && $val['colorpicker'] ) ? ' color-picker' : null ),
			);

			$html .= '
			<div class="form-' . ( ( $val['type'] == 'checkbox' ) ? 'check' : 'group row' ) . '">';
			
			if ( $val['type'] != 'checkbox' ) 
			{
				$html .= '<label for="' . $uniq . '" class="col-sm-2 col-form-label">' . $val['label'] . '</label>';
			}
				
			$html .= '<div class="col-sm-10">';

			if ( $val['type'] == 'text' )
			{
				$args['type'] = 'text';
				
				$html .= FormInput( $args, false );
			}
			
			if ( $val['type'] == 'num' )
			{
				$html .= '<input value="' . ( $value ? $value : 0 ) . '" id="' . $uniq . '" type="number" class="form-control-border border-width-2" name="' . $elemName . '" ' . ( ( isset( $val['step'] ) && $val['step'] ) ? ' step="' . $val['step'] . '"' : ' step="any"' ) . ( isset( $val['min'] ) ? ' min="' . $val['min'] . '"' : '' ) . ( isset( $val['max'] ) ? ' max="' . $val['max'] . '"' : '' ) . '>';
			}
			
			if ( $val['type'] == 'currency' )
			{
				$html .= '<select name="' . $elemName . '" class="form-control" id="' . $uniq . '">';

				if ( !empty( $currencies ) )
				{
					foreach( $currencies as $curr )
					{
						$name = $curr['name'];

						$name .= ' [<em>' . $curr['symbol'] . '</em>]';
							
						$html .= '<option value="' . $curr['id'] . '">' . $name . '</option>';
					}
				}

				$html .= '</select>';
			}
				
			if ( $val['type'] == 'attribute' )
			{
				$html .= '<select name="' . $elemName . '" class="form-control" id="' . $uniq . '">';

				if ( !empty( $attrs ) )
				{
					foreach( $attrs as $attr )
					{
						$trans = Json( $attr['trans_data'] );
							
						$name = $attr['name'];
							
						$name = ( ( !empty( $trans ) && !$Admin->IsDefaultLang() && !empty( $trans['lang-' . $curLang]['value'] ) ) ? $trans['lang-' . $curLang]['value'] : $name );
							
						$name = StripContent( $name );
							
						$name .= ( ( $attr['lan'] != $defLang ) ? ' [<em>' . $attr['lt'] . '</em>]' : '' );
							
						$html .= '<option value="' . $attr['id'] . '" ' . ( ( !empty( $value ) && ( $attr['id'] == $value ) ) ? 'selected' : '' ) . '>' . $name . ' [<em>' . $attr['gn'] . '</em>]</option>';
					}
				}
						
				$html .= '</select>';
			}
				
			if ( $val['type'] == 'checkbox' )
			{
				$html .= '<input type="checkbox" name="' . $elemName . '" class="form-check-input" value="1" id="' . $uniq . '"' . ( $value ? ' checked' : '' ) . '>';
				$html .= '<label class="form-check-label" for="' . $id_ . '">' . $val['label'] . '</label>';
			}
				
			if ( $val['type'] == 'select' )
			{
				$html .= '<select name="' . $elemName . '" class="form-control" id="' . $uniq . '">';
					
				if ( isset( $val['firstNull'] ) && $val['firstNull'] )
				{
					$html .= '<option value="">' . __( 'choose' ) . '...</option>';
				}
				
				if ( !empty( $val['options'] ) )
				{
					foreach( $val['options'] as $id___ => $option )
					{
						$html .= '<option value="' . $id___ . '" ' . ( ( !empty( $value ) && ( $id___ == $value ) ) ? 'selected' : '' ) . '>' . $option['name'] . '</option>';
					}
				}
					
				$html .= '</select>';
			}

			if ( isset( $val['tip'] ) && !empty( $val['tip'] ) )
			{
				$html .= '<small id="' . $uniq . '" class="form-text text-muted">' . $val['tip'] . '</small>';
			}

			$html .= '
				</div>
			</div>';
		}
	}
		
	if ( !$echo )
		return $html;

	echo $html;
}

function BuildLogNavHtml( $echo = true )
{
	global $Admin;
	
	if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'view-logs' ) && !IsAllowedTo( 'manage-comments' ) )
		return null;
	
	$data = $Admin->GetLogCounts();
	
	if ( !$echo )
		return $data;
	
	$html = '
	<li class="nav-item dropdown d-none d-md-block" id="logButton">
		<a class="nav-link" data-toggle="dropdown" href="#">
			<i class="far fa-bell"></i>';
			
	if ( $data['totalNotes'] > 0 )
		$html .= '
			<span id="logBadge" class="badge badge-warning navbar-badge">' . $data['totalNotes'] . '</span>';
	
	$html .= '
		</a>';

	$html .= '	
		<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
			<span class="dropdown-item dropdown-header">' . $data['totalNotes'] . ' ' . __( 'notifications' ) . '</span>';
			
			/*
			
			<div class="dropdown-divider"></div>
			<a href="#" class="dropdown-item">
				<i class="fas fa-envelope mr-2"></i> 4 new messages
				<span class="float-right text-muted text-sm">3 mins</span>
			</a>

			<div class="dropdown-divider"></div>
			<a href="#" class="dropdown-item">
				<i class="fas fa-users mr-2"></i> 8 friend requests
				<span class="float-right text-muted text-sm">12 hours</span>
			</a>
			*/
			
			if ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-comments' ) ) && !empty( $data['comments'] ) )
			{
				$html .= '
				<div class="dropdown-divider"></div>
					<a href="' . $Admin->GetUrl( 'comments' ) . '" class="dropdown-item">
					<i class="fas fa-comments mr-2"></i> ' . ( ( $data['comments']['num'] > 0 ) ? $data['comments']['num'] . ' ' . __( 'new' ) : 0 ) . ' ' . __( 'comments' ) . '
				</a>';
			}
			
			if ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'view-logs' ) ) && !empty( $data['logs'] ) )
			{
				$html .= '
				<div class="dropdown-divider"></div>
					<a href="' . $Admin->GetUrl( 'logs' ) . '" class="dropdown-item">
					<i class="fas fa-file mr-2"></i> ' . ( ( $data['logs']['num'] > 0 ) ? $data['logs']['num'] . ' ' . __( 'new' ) : 0 ) . ' ' . __( 'logs' ) . '
				</a>';
			}

			//<div class="dropdown-divider"></div>
			//<a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
		$html .= '
		</div>
	</li>';

	echo $html;
}

function BuildMenuHtml( $items, $echo = true )
{
	$html = '
	<ol class="menu-list sortable">' . PHP_EOL;

	if ( !empty( $items ) )
	{
		foreach( $items as $id => $item )
		{
			if ( !isset( $item['childs'] ) || empty( $item['childs'] ) )
			{
				$html .= BuildMenuLi( $item['itemId'], $item['url'], $item['label'], $item['type'], __( $item['type'] ), $item['title'], $item['newTab'] );
			}
			else
			{
				$childs = PHP_EOL . '<ol class="submenu-list">' . PHP_EOL;

				foreach( $item['childs'] as $_id => $child )
				{
					if ( !isset( $child['childs'] ) || empty( $child['childs'] ) )
					{
						$childs .= BuildMenuLi( $child['itemId'], $child['url'], $child['label'], $child['type'], __( $child['type'] ), $child['title'], $child['newTab'] );
					}

					else
					{
						$_childs = PHP_EOL . '<ol class="submenu-list">' . PHP_EOL;

						foreach( $child['childs'] as $__id => $_child )
						{
							$_childs .= BuildMenuLi( $_child['itemId'], $_child['url'], $_child['label'], $_child['type'], __( $_child['type'] ), $_child['title'], $_child['newTab'] );
						}

						$_childs .= PHP_EOL . '</ol>' . PHP_EOL;

						$childs .= BuildMenuLi( $child['itemId'], $child['url'], $child['label'], $child['type'], __( $child['type'] ), $child['title'], $child['newTab'], $_childs );
					}
				}

				$childs .= PHP_EOL . '</ol>' . PHP_EOL;

				$html .= BuildMenuLi( $item['itemId'], $item['url'], $item['label'], $item['type'], __( $item['type'] ), $item['title'], $item['newTab'], $childs );
			}
		}
	}
	
	$html .= PHP_EOL . '</ol>' . PHP_EOL;
	
	if ( $echo )
		echo $html;
	else
		return $html;
}

function BuildMenuLi( $id, $url, $label, $group, $type, $attr = '', $tab = false, $childs = null )
{
	$html = '
	<li data-id="' . $group . '" id="menuItem_' . $id . '">
		<div id="item">
			<span class="menu-item-bar">
				<span class="handle ui-sortable-handle">
					<i class="fas fa-ellipsis-v"></i>
					<i class="fas fa-ellipsis-v"></i>
				</span> ' . $label . ' <span>[' . $type . ']</span>

				<a href="#collapse' . $id . '" class="pull-right" data-toggle="collapse" aria-expanded="true">
					<div class="tools"><i class="fas fa-edit"></i></div>
				</a>
			</span>
		
			<div class="collapse" id="collapse' . $id . '">
				<div class="input-box">
					<div class="form-group">
						<label>' . __( 'navigation-label') . '</label>
						<input type="text" id="navLabel" name="nav-label" value="' . $label . '" class="form-control">
					</div>

					<div class="form-group">
						<label>' . __( 'title-attribute') . '</label>
						<input type="text" id="titleAttr" name="title-attr" value="' . $attr . '" class="form-control">
					</div>';
					
	if ( $group == 'custom' )
	{
		$html .= '
					<div class="form-group">
						<label>' . __( 'url') . '</label>
						<input type="text" id="navUrl" name="url" value="' . $url . '" class="form-control">
					</div>';
	}
	else
	{
		$html .= '
					<input type="hidden" id="navUrl" name="url" value="' . $url . '">';
	}

	$html .= '
					<div class="form-check">
						<input type="checkbox" name="target" value="1" class="form-check-input" id="blankTarget"' . ( $tab ? ' checked' : '' ) . '>
						<label class="form-check-label" for="blankTarget">' . __( 'open-link-in-new-tab') . '</label>
					</div>

					<div class="btn-group">
						<!--<button id="saveMenuItem" data-id="' . $id . '" class="btn btn-sm btn-primary mr-1">' . __( 'save' ) . '</button>-->
						
						<button id="deleteMenuItem" data-id="' . $id . '" class="btn item-rm btn-sm btn-danger mr-1">' . __( 'remove' ) . '</button>
					</div>
				</div>
			</div>';
		
	if ( !empty( $childs ) )
	{
		$html .= $childs;
	}
	else
		$html .= '<ol class="submenu-list"></ol>';
	
	$html .= '
		</div>
	</li>';
	
	return $html;
}

/*
function AdminWidgetHtml( $widget )
{
	global $Admin;
	
	require ( ARRAYS_ROOT . 'generic-arrays.php');
	$aGroup = ( !empty( $widget['groups'] ) ? Json( $widget['groups'] ) : null );
	
	$html = '';
	
	$html .= '
	<form class="tab-content" id="form" method="post" action="" autocomplete="off">
		<div class="form-row">
			<div class="form-group col-md-9">';
			
			$html .= '
				<div class="form-group">
					<label for="widgetName">' . __( 'name' ) . '</label>
					<input type="text" class="form-control" name="widgetName" id="widgetName" value="' . htmlspecialchars( $widget['title'] ) . '">
					<small id="widgetNameHelp" class="form-text text-muted">' . __( 'widget-name-tip' ) . '</small>
				</div>';

				$html .= '
				<div class="form-group">
					<label for="widgetType">' . __( 'widget-type' ) . '</label>
					<select class="form-select" id="widgetType" name="widgetType">';
					
				foreach( $widgetTypes as $w => $t )
				{
					$html .= '
						<option value="' . $t['name'] . '" ' . ( ( $t['name'] == $widget['type'] ) ? 'selected' : '' ) . '>' . $t['title'] . '</option>';
				}
				
				$html .= '
					</select>
					
					<small id="widgetTypeHelp" class="form-text text-muted">' . __( 'widget-type-tip' ) . '</small>
				</div>';
				/*
				$html .= '				
				<div class="form-group">
					<label for="widgetType">' . __( 'theme-position' ) . '</label>';
					
					if ( empty( ThemeValue( 'widget-position' ) ) )
					{
						$html .= '<p>' . __( 'your-theme-does-not-natively-support-widgets' ) . '</p>';
					}
					else
					{
						$html .= '
						<select class="form-select" id="widgetThemePos" name="widgetThemePos">';
						
						$pos = ( isset( ThemeValue( 'widget-position' )['0'] ) ? ThemeValue( 'widget-position' )['0'] : ThemeValue( 'widget-position' ) );
						
						foreach( $pos as $k => $w )
						{
							$html .= '
							<option value="' . $k . '" ' . ( ( $k == $widget['theme_pos'] ) ? 'selected' : '' ) . '>' . $w['name'] . '</option>';
						}
						
						$html .= '
						</select>';
					}
					
			$html .= '
			</div>';* /

			if ( $widget['type'] == 'built-in' )
			{
				$html .= '
				<div class="form-group">
					<label for="built-in">' . __( 'built-in-widgets' ) . '</label>
					<select class="form-select" id="built-in" name="built-in">
						<option value="">...</option>';
						
						foreach( $builtInWidgets as $w => $t )
						{
							$html .= '
							<option value="' . $t['name'] . '" ' . ( ( $t['name'] == $widget['build_in'] ) ? 'selected' : '' ) . '>' . $t['title'] . '</option>';
						}
					
					$html .= '
					</select>
				</div>';

				if ( ( $widget['build_in'] == 'latest-posts' ) || ( $widget['build_in'] == 'latest-comments' ) )
				{
					$html .= '
					<div class="form-group">
						<label for="widgetCode">' . __( 'number-of-items-to-show' ) . '</label>
						<input class="form-control" value="' . $widget['num'] . '" type="number" name="num" step="any" min="1" max="10">
					</div>';
				}

				if ( ( $widget['build_in'] == 'categories-list' ) || ( $widget['build_in'] == 'tags-list' ) || ( $widget['build_in'] == 'languages-list' ) )
				{
					$html .= '
					<div class="form-group">
						<label for="widgetdropDown">' . ( 'show-drop-down-list' ) . '</label>
						<input type="checkbox" name="dropDown" value="true" ' . ( $widget['show_dropdown_list'] ? 'checked' : '' ) . '>
					</div>';
				}

				if ( ( $widget['build_in'] == 'categories-list' ) || ( $widget['build_in'] == 'tags-list' ) )
				{
					$html .= '
					<div class="form-group">
						<label for="showPostNum">' . __( 'show-number-of-posts' ) . '</label>
						<input type="checkbox" name="showPostNum" value="true" ' . ( $widget['show_num_posts'] ? 'checked' : '' ) . '>
					</div>';
				}
								
			}
			elseif ( $widget['type'] == 'ad' )
			{
				$ads = GetAdminAds( 'sidebar' );
				
				$html .= '
				<div class="form-group">
					<label for="widgetAds">' . __( 'ads' ) . '</label>
					<select class="form-select" id="widgetAd" name="widgetAd">
						<option value="0">...</option>';
						
						if ( !empty( $ads ) )
						{
							foreach( $ads as $ad )
							{
								$html .= '
								<option value="' . $ad['id'] . '" ' . ( ( $ad['id'] == $widget['id_ad'] ) ? 'selected' : '' ) . '>' . $ad['title'] . '</option>';
							}
							
						}
						
					$html .= '
					</select>
					<small id="widgetAdHelp" class="form-text text-muted">' . sprintf( __( 'widget-choose-ad-tip' ), $Admin->GetUrl( 'tools' ) ) . '</small>
				</div>';
							
			}
			
			else
			{
				if ( $widget['type'] == 'php' )
				{
					$html .= '
					<div class="form-group">
						<label for="functionName">' . __( 'function-name' ) . '</label>
						<input type="text" class="form-control" name="functionName" id="functionName" value="' . htmlspecialchars( $widget['function_name'] ) . '">
						<small id="functionNameHelp" class="form-text text-muted">' . __( 'function-name-tip' ) . '</small>
					</div>';
				}
				
				$html .= '
				<div class="form-group">
					<label for="widgetCode">' . __( 'widget-code-text' ) . '</label>
					<textarea class="form-control" id="widgetCode" name="widgetCode" rows="3">' . htmlspecialchars( html_entity_decode( $widget['data'] ) ) . '</textarea>
					<small id="widgetCodeHelp" class="form-text text-muted">' . __( 'widget-code-tip' ) . '</small>
				</div>';
			}
			
			$html .= '
			<h4>' . __( 'widget-visibility' ) . '</h4>
			<div class="form-group">
				<label for="inputFrontpagePage">' . __( 'membergroups' ) . '</label>
				<select  name="membergroups[]" class="form-control select2 form-select shadow-none mt-3" multiple id="slcAmp" >';
				
				$groups = AdminGroups( $Admin->GetSite(), false );
				
				if ( !empty( $groups ) )
				{
					foreach( $groups as $group )
					{
						$html .= '
						<option  value="' . $group['id_group'] . '" ' . ( ( !empty( $aGroup ) && in_array( $group['id_group'], $aGroup ) ) ? 'selected' : '' ) . '>' . $group['group_name'] . '</option>';
					}
				}
				$html .= '
				</select>
				
				<small id="membergroupsHelp" class="form-text text-muted">' . __( 'select-widget-membergroup-tip' ) . '</small>
			</div>';
			
			$html .= '
			<div class="form-group">
				<label for="widgetVisibility1">' . __( 'show-if' ) . '</label>
				<select class="form-select" id="widgetVisibility1" name="widgetVisibilityShow">
					<option value="">...</option>';

					foreach( $widgetVisibilityOptions as $w => $t )
					{
						$html .= '
						<option value="' . $t['name'] . '" ' . ( ( $t['name'] == $widget['enable_on'] ) ? 'selected' : '' ) . '>' . $t['title'] . '</option>';
					}
					
				$html .= '
				</select>
				<small id="widgetVisibility1Help" class="form-text text-muted">' . __( 'widget-visibility-show-tip' ) . '</small>
			</div>';
			/*
			$html .= '
			<div class="form-group">
								<label for="widgetVisibility2"><?php echo $L['hide-if'] ?></label>
								<select class="form-select" id="widgetVisibility2" name="widgetVisibilityHide">
									<option value="">...</option>
									<?php foreach( $widgetVisibilityOptions as $w => $t ) : ?>
										<option value="<?php echo $t['name'] ?>" <?php echo ( ( $t['name'] == $Widget['exclude_from'] ) ? 'selected' : '' ) ?>><?php echo $t['title'] ?></option>
									<?php endforeach ?>
								</select>
								<small id="widgetVisibility2Help" class="form-text text-muted"><?php echo $L['widget-visibility-hide-tip'] ?></small>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="disable" <?php echo ( ( $Widget['disabled'] == 1 ) ? 'checked' : '' ) ?> />
								<label class="form-check-label" for="disableCheckBox">
									<?php echo $L['disable'] ?>
								</label>
								<small id="disableCheckBox" class="form-text text-muted"><?php echo $L['disable-widget-tip'] ?></small>
							</div>
		
							<hr />
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
								<label class="form-check-label" for="deleteCheckBox">
									<?php echo $L['delete'] ?>
								</label>
								<small id="deleteCheckBox" class="form-text text-muted"><?php echo $L['delete-widget-tip'] ?></small>
							</div>
							
							<input type="hidden" name="_token" value="<?php echo generate_token( 'edit_widget_' . $Widget['id'] ) ?>">
							
							<div class="align-middle">
								<div class="float-left mt-1">
									<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
									<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'widgets' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
								</div>
							</div>
						
						</div>
					</div>
				</form>* /
				
	return $html;
}
*/

#####################################################
#
# Admin Dashboard function
#
#####################################################
function AdminDashboard( $pos = 'left' )
{
	global $Admin;

	include ( ARRAYS_ROOT . 'admin-arrays.php');
	
	$dashBoardWidgets = $Admin->DashBoardWidgets();
	
	$userData = $Admin->UserDashData();
	
	if ( isset( $userData['widgets'] ) && !empty( $userData['widgets'] ) )
	{
		$leftData = $rightData = array();
		
		foreach( $userData['widgets'] as $poz => $a )
		{
			foreach( $a as $w )
			{
				if ( isset( $dashBoardWidgets[$w] ) )
				{
					if ( $poz == 'left' )
					{
						$leftData[$w] = $dashBoardWidgets[$w];
					}
						
					else
					{
						$rightData[$w] = $dashBoardWidgets[$w];
					}
				}
			}
		}
	}
	if ( isset( $userData['widgets'] ) && empty( $userData['widgets'] ) )
	{
		$leftData = $rightData = array();
	}

	if ( $pos == 'left' )
	{
		$boxData = $leftData;
	}
	
	else
	{
		$boxData = $rightData;
	}
	
	if ( empty( $boxData ) )
		return;
	
	foreach( $boxData as $id => $data )
	{
		if ( is_callable( $data['function'] ) )
			call_user_func( $data['function'] );
	}
}

#####################################################
#
# Admin Dashboard Widgets function
#
#####################################################
function AdminDashboardWidgets()
{
	global $Admin;
	
	$usrData = $Admin->UserDashData();
	$dashBoardWidgets = $Admin->DashBoardWidgets();

	$buttons = null;
	$hadData = ( isset( $usrData['widgets'] ) ? true : false );
	
	if ( !empty( $usrData ) )
	{
		$left = ( isset( $usrData['widgets']['left'] ) ? $usrData['widgets']['left'] : array() );
		$right = ( isset( $usrData['widgets']['right'] ) ? $usrData['widgets']['right'] : array() );
		
		$usrData = array_merge( $left, $right );
	}

	$modalBody = '
	<form class="tab-content" id="form" method="post" action="" autocomplete="off">';
	
	if ( !empty( $dashBoardWidgets ) )
	{
		foreach( $dashBoardWidgets as $id => $widget )
		{
			if ( isset( $widget['disable'] ) && $widget['disable'] )
			{
				continue;
			}
			
			if ( isset( $widget['allow'] ) && is_array( $widget['allow'] ) && !empty( $widget['allow'] ) )
			{
				if ( ( count( $widget['allow'] ) == 1 ) && !IsAllowedTo( $widget['allow'] ) )
				{
					continue;
				}
				
				elseif ( count( $widget['allow'] ) > 1 )
				{
					$allowed = false;
					
					foreach( $widget['allow'] as $allow )
					{
						if ( IsAllowedTo( $allow ) )
						{
							$allowed = true;
						}
					}
					
					if ( !$allowed )
					{
						continue;
					}
				}
			}
			
			$modalBody .= '
			<div class="form-group">
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" value="1" name="widgets[' . $id . ']" id="' . $id . '" ' . ( in_array( $id, $usrData ) ? 'checked' : ( ( empty( $usrData ) && !$hadData ) ? 'checked' : '' ) ) . '>
					<label class="custom-control-label" for="' . $id . '">' . $widget['title'] . '</label>
				</div>
			</div>';
		}
	}

	$modalBody .= '
		<div class="form-group">
			<button type="submit" class="btn btn-primary float-left">' . __( 'save' ) . '</button>
			<button type="button" class="btn btn-default float-right" data-dismiss="modal">' . __( 'cancel' ) . '</button>
		</div>
	</form>';
	
	$modal = array(
		'title' => __( 'manage-widgets' ),
		'id' => 'manageWidgets',
		'size' => 'sm',
		'body' => $modalBody,
		'buttons' => null,
		'fade' => true,
		'loader' => false,
	);
	
	return $modal;
}

#####################################################
#
# Admin Stats Chart function
#
#####################################################
function AdminStatsChart()
{
	global $Admin, $filters;

	if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'view-stats' ) ) || !$Admin->Settings()::IsTrue( 'enable_stats' ) )
		return;

	$x_max = days_in_month( $filters['mo'], $filters['yr'] );

	$visits = $hits = $v = $h = array();

	for( $d = 1; $d <= $x_max; $d++ )
	{
		$visits[$d] = $hits[$d] = 0;
	}
	
	$loaded_data = load_data( $filters );
	
	if ( empty( $loaded_data ) )
		return;

	foreach( $loaded_data['visits']['date'] as $ts => $data )
	{
		$dn = intval( substr( $ts, -2 ) );
		$visits[ $dn ] += $data['visits'];
		$hits[ $dn ] += $data['hits'];
	}
		
	$per = __( 'day' );
	
	$vtitle = htmlspecialchars( __( 'visits' ) . '/' . $per );
	$htitle = htmlspecialchars( __( 'hits' ) . '/' . $per );
	
	$days = array_keys( $visits );
	
	foreach( $visits as $x => $y )
	{
		$v[] = $y;
		$h[] = $hits[$x];
	}

	$html = '
	<script>
	var visitChartCanvas = document.getElementById(\'visits-stats\').getContext(\'2d\');

	var visitChartData =
	{
		labels: [' . implode( ',', $days ) . '],
		datasets:[{
			backgroundColor: \'rgba(60,141,188,0.9)\',
			borderColor: \'rgb(13,110,253)\',
			label: \'' . $htitle . '\',
			data:[' . implode( ',', $h ) . ']
		},
		{
			backgroundColor: \'rgba(60,141,188,0.8)\',
			borderColor: \'rgb(61,139,253)\',
			label: \'' . $vtitle . '\',
			data:[' . implode( ',', $v ) . ']
		}]
	}

	var visitChartOptions={
		maintainAspectRatio:false,responsive:true,legend:{display:false},scales: {
		yAxes: [{
			ticks: {
				beginAtZero: true,
				stepSize: 1
			}
		}]
	}}
	var visitChart = new Chart(visitChartCanvas,{type:\'bar\',data:visitChartData,options:visitChartOptions})
	</script>';
	
	if ( isset( $loaded_data['visits']['browser'] ) && !empty( $loaded_data['visits']['browser'] ) )
	{
		$data = data_percent( 'browser', $loaded_data );
		
		if ( !empty( $data ) )
		{
			$browsers = $colors = $hits = array();
			
			foreach( $data as $id => $browser )
			{
				$browsers[] = $browser['label'];
				$colors[] = $browser['color'];
				$hits[] = $browser['hits'];
			}
			
			/*$arr = array(
				'labels' => $browsers,
				
				'datasets' => array(
					'data' => $hits,
					'backgroundColor' => $colors
				)
			);*/
			
			$html .= '
			<script>
				var browserChartCanvas=$(\'#browseChart\').get(0).getContext(\'2d\')
				var pieData={labels:[\'' . implode( '\',\'', $browsers ) . '\'],datasets:[{data:[' . implode( ',', $hits ) . '],backgroundColor:[\'' . implode( '\',\'', $colors ) . '\']}]}
				var pieOptions={legend:{display:false}}
				var pieChart=new Chart(browserChartCanvas,{type:\'doughnut\',data:pieData,options:pieOptions})
			</script>';
			
			$html .= '
			<script>
				var data = "<ul class=\"chart-legend clearfix\">";';
				
				foreach( $data as $id => $browser )
				{
					$html .= '
					data += "<li><i class=\"far fa-circle\" style=\"color: ' . $browser['color'] . '\"></i> ' . $browser['label'] . '</li>";';
				}
				
				$html .= '
				data += "</ul>";
				
				$(\'#browseInfo\').append(data);
			</script>';
			
		}
	}

	echo $html;
}

#####################################################
#
# Admin Stats function
#
#####################################################
function AdminStats()
{
	global $Admin;
	
	if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'view-stats' ) ) || !$Admin->Settings()::IsTrue( 'enable_stats' ) )
		return;

	$cardArgs = array();

	$cardArgs['body-class'] = 'p-0';
	$cardArgs['header'] = '<i class="fa fa-chart-area"></i> ' . __( 'stats' );
	$cardArgs['header-class'] = 'border-transparent';
	
	$cardArgs['ids'] = 'id="sort" data-id="stats"';
	
	$ul = '<ul class="list-style-none clearfix">';
	
	$cardArgs['tools'] = '
	<div class="btn-group">
		<ul class="nav nav-pills ml-auto">
			<li class="nav-item">
				<a class="nav-link active" href="#visits-chart" data-toggle="tab">' . __( 'stats' ) . '</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#info-chart" data-toggle="tab">' . __( 'browser-info' ) . '</a>
			</li>
		</ul>
	</div>
	<!--
	<button type="button" id="minimize" data-id="stats" class="btn btn-tool" data-card-widget="collapse">
		<i class="fas fa-minus"></i>
	</button>
	-->
	<button type="button" id="close" data-id="stats" class="btn btn-tool">
		<i class="fas fa-times"></i>
	</button>';

	$ul .= '
	<div class="tab-content p-0">
		<div class="chart tab-pane active" id="visits-chart" style="position: relative; height: 300px;">
			<div class="position-relative mb-4">
				<canvas id="visits-stats" height="300" style="height: 300px;"></canvas>
			</div>
		</div>
		<div class="chart tab-pane" id="info-chart" style="position: relative; height: 300px;">
			<div class="row">
				<div class="col-md-8">
					<div class="chart-responsive">
						<canvas id="browseChart" height="150"></canvas>
					</div>
				</div>

				<div id="browseInfo" class="col-md-4">
				</div>
			</div>
		</div>
	</div>';
	
	$cardArgs['body'] = $ul;

	$html = BootstrapCard( $cardArgs, false );

	echo $html;

	unset( $posts, $html, $ul, $cardArgs );
}

#####################################################
#
# Top Posts function
#
#####################################################
function TopDashboardPosts()
{
	global $Admin;
	
	if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'view-stats' ) )
		return;
	
	$cardArgs = $tableArgs = array();
	
	$tableArgs['responsive'] = true;
	$tableArgs['headData'] = $tableArgs['bodyData'] = array();

	$cardArgs['body-class'] = 'p-0';
	$cardArgs['ids'] = 'id="sort" data-id="top-posts"';
	$cardArgs['header'] = '<i class="fa fa-cubes"></i> ' . __( 'top-posts' );
	$cardArgs['header-class'] = 'border-transparent';
	
	$cardArgs['tools'] = '
	<div class="btn-group">
		<button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
		<i class="fas fa-clock"></i>
		</button>
		<div class="dropdown-menu dropdown-menu-right" role="menu">
			<a href="#" id="topPostsStats" value="today" class="dropdown-item">' . __( 'today' ) . '</a>
			<a href="#" id="topPostsStats" value="yesterday" class="dropdown-item">' . __( 'yesterday' ) . '</a>
			<a href="#" id="topPostsStats" value="week" class="dropdown-item">' . __( 'this-week' ) . '</a>
			<a href="#" id="topPostsStats" value="7days" class="dropdown-item">' . __( 'last-7-days' ) . '</a>
			<a href="#" id="topPostsStats" value="30month" class="dropdown-item">' . __( 'last-30-days' ) . '</a>
			<a href="#" id="topPostsStats" value="year" class="dropdown-item">' . __( 'this-year' ) . '</a>
		</div>
	</div>
	
	<!--
	<button type="button" id="minimize" data-id="top-posts" class="btn btn-tool" data-card-widget="collapse">
		<i class="fas fa-minus"></i>
	</button>
	-->
	
	<button type="button" id="close" data-id="top-posts" class="btn btn-tool">
		<i class="fas fa-times"></i>
	</button>';
	
	$tableArgs['headData'][] = array( 'title' => __( 'title' ) );
	$tableArgs['headData'][] = array( 'title' => __( 'date' ) );
	$tableArgs['headData'][] = array( 'title' => __( 'views' ) );
	
	$cacheFile = CacheFileName( 'admin-dash-top-posts-userid_' . $Admin->UserID(), null, $Admin->GetLang(), $Admin->GetBlog(), null, 5, null, $Admin->GetSite() );

	//Get the data from the cache, if is valid
	if ( ValidOtherCache( $cacheFile, 1800 ) )
	{
		$posts = ReadCache( $cacheFile );
	}
	
	//Get the data and save it to the cache, if needed...
	else
	{
		$db = db();
		
		$q = "(p.id_site = " . $Admin->GetSite() . ") AND (p.id_lang = " . $Admin->GetLang() . ") AND (p.id_blog = " . $Admin->GetBlog() . ")" . ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) ? " AND (p.id_member = " . $Admin->UserID() . ")" : "" ) . " AND (p.post_type = 'post') AND (p.post_status = 'published')";

		$query = PostsDefaultQuery( $q, 5, 'p.views DESC', 'p.id_post' );

		//Query: posts
		$tmp = $db->from( null, $query )->all();
		
		$posts = array();
		
		if ( !empty( $tmp ) )
		{
			$i = 0;
				
			foreach ( $tmp as $p )
			{				
				$posts[$i] = BuildPostVars( $p );
					
				$i++;
			}
			
			unset( $tmp );
			
			WriteOtherCacheFile( $posts, $cacheFile );
		}
	}

	if ( !empty( $posts ) )
	{
		foreach ( $posts as $post )
		{
			$tableArgs['bodyData'][$post['id']] = array();
			
			$tableArgs['bodyData'][$post['id']]['td'][] = array( 'data' => '<a href="' . $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $post['id'] ) . '">' . $post['title'] . '</a>' );
			
			$tableArgs['bodyData'][$post['id']]['td'][] = array( 'data' => $post['added']['time'] );

			$tableArgs['bodyData'][$post['id']]['td'][] = array( 'data' => $post['views'] );
		}
	}
	else
	{
		$data = '
		<div class="alert alert-warning" role="alert">
            ' . __( 'nothing-found' ) . '
        </div>';
		
		$tableArgs['bodyData'][0]['td'][] = array( 'data' => $data );
	}
	
	$cardArgs['body'] = BootstrapTable( $tableArgs, false );
	
	$html = BootstrapCard( $cardArgs, false );
	
	echo $html;
	
	unset( $posts, $html, $cardArgs, $tableArgs );
}

#####################################################
#
# Latest Comments function
#
#####################################################
function LatestDashboardComments()
{
	global $Admin;
	
	if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-comments' ) && !IsAllowedTo( 'manage-own-comments' ) )
		return;
	
	$cardArgs = array();
	
	$showAll = ( $Admin->IsDefaultSite() ? $Admin->Settings()::IsTrue( 'parent_site_shows_everything' ) : false );

	$cardArgs['body-class'] = 'p-0';
	$cardArgs['header'] = '<i class="fa fa-comments"></i> ' . __( 'latest-comments' );
	$cardArgs['header-class'] = 'border-transparent';
	
	$cardArgs['ids'] = 'id="sort" data-id="latest-comments"';
	
	$cardArgs['tools'] = '
	<!--
	<button type="button" id="minimize" data-id="latest-comments" class="btn btn-tool" data-card-widget="collapse">
		<i class="fas fa-minus"></i>
	</button>
	-->
	<button type="button" id="close" data-id="latest-comments" class="btn btn-tool">
		<i class="fas fa-times"></i>
	</button>';
	
	$data = '
	<div class="col-auto mb-4">
		<ul class="list-group list-group-flush list-timeline-activity">';
		
	$cacheFile = CacheFileName( 'admin-dash-comments-userid_' . $Admin->UserID(), null, $Admin->GetLang(), $Admin->GetBlog(), null, 5, null, $Admin->GetSite() );

	//Get the data from the cache, if is valid
	if ( ValidOtherCache( $cacheFile, 1800 ) )
	{
		$comments = ReadCache( $cacheFile );
	}
	
	//Get the data and save it to the cache, if needed...
	else
	{
		$db = db();
		
		$query = "SELECT co.id, co.name, co.url as cu, co.email, co.id_site as cosite, co.id_blog, co.comment, co.added_time, co.status, 
		co.id_parent, co.ip, p.sef AS sef, p.id_post, p.id_lang, p.post_type, p.title AS tl, b.sef as blog_sef, b.name as blog_name, COALESCE(u.real_name, u.user_name) as user_name, u.image_data, la.code as ls, la.title as lt, la.locale as ll, la.flagicon, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, s.enable_multisite as multisite, s.title as sna, ld.id as dlid, ld.code as dlc, ld.title as dlt, ld.locale as dll, lc.date_format, lc.time_format, cnf.value as hide_lang
		FROM `" . DB_PREFIX . "comments` AS co
		INNER JOIN `" . DB_PREFIX . "sites` 		as s ON s.id = co.id_site
		INNER JOIN `" . DB_PREFIX . "languages` 	as la ON la.id = co.id_lang
		INNER JOIN `" . DB_PREFIX . "languages_config` as lc ON lc.id_lang = co.id_lang
		INNER JOIN `" . DB_PREFIX . "languages`  	as ld ON ld.id_site = co.id_site AND ld.is_default = 1
		INNER JOIN `" . DB_PREFIX . "config` 		as cnf ON cnf.id_site = co.id_site AND cnf.variable = 'hide_default_lang_slug'
		LEFT JOIN  `" . DB_PREFIX . "blogs` 		as b ON b.id_blog = co.id_blog
		LEFT JOIN  `" . DB_PREFIX . POSTS . "` 		as p ON p.id_post = co.id_post
		LEFT JOIN  `" . DB_PREFIX . USERS . "` 		as u ON u.id_member = co.user_id
		WHERE (co.status = 'approved' OR co.status = 'pending') AND (co.id_lang = " . $Admin->GetLang() . ")" . ( !$showAll ? " AND (co.id_site = " . $Admin->GetSite() . ") AND (co.id_blog = " . $Admin->GetBlog() . ")" : "" ) . ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-comments' ) ) ? " AND (co.user_id = " . $Admin->UserID() . ")" : "" ) . "
		ORDER BY co.added_time DESC LIMIT 5";

		//Query: comments
		$tmp = $db->from( null, $query )->all();
		
		$comments = $imageData = array();
		
		if ( !empty( $tmp ) )
		{
			foreach( $tmp as $c )
			{
				$image = 'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=';
				
				if ( !empty( $c['image_data'] ) )
				{
					$imageData = Json( $c['image_data'] );
					
					if ( !empty( $imageData ) && isset( $imageData['default'] ) )
					{
						$image = $imageData['default']['imageUrl'];
					}
				}
				
				$comments[$c['id']]['id'] = $c['id'];
				$comments[$c['id']]['status'] = $c['status'];
				$comments[$c['id']]['parentId'] = $c['id_parent'];
				$comments[$c['id']]['ip'] = $c['ip'];
				$comments[$c['id']]['imageData'] = $imageData;
				$comments[$c['id']]['imageUrl'] = $image;
				$comments[$c['id']]['url'] = $c['cu'];
				$comments[$c['id']]['siteName'] = $c['sna'];
				$comments[$c['id']]['blogName'] = $c['blog_name'];
				$comments[$c['id']]['blogId'] = $c['id_blog'];
				$comments[$c['id']]['siteId'] = $c['cosite'];
				$comments[$c['id']]['email'] = $c['email'];
				$comments[$c['id']]['postId'] = $c['id_post'];
				$comments[$c['id']]['postTitle'] = $c['tl'];
				$comments[$c['id']]['time'] = postDate( $c['added_time'], false );
				$comments[$c['id']]['niceTime'] = niceTime( $c['added_time'] );
				$comments[$c['id']]['timeRaw'] = $c['added_time'];
				$comments[$c['id']]['name'] = ( !empty( $c['user_name'] ) ? $c['user_name'] : $c['name'] );
				$comments[$c['id']]['rTime'] = date ( 'r', $c['added_time'] );
				$comments[$c['id']]['timeC'] = postDate ( $c['added_time'], true );
				$comments[$c['id']]['comment'] = CreatePostContent( $c['comment'], null, false );
				$comments[$c['id']]['postUrl'] = BuildPostUrl( $c, $c['ls'], $c['url'] ) . '#comment-' . $c['id'];

				unset( $c );
			}
	
			WriteOtherCacheFile( $comments, $cacheFile );
		}
	}

	if ( !empty( $comments ) )
	{
		foreach ( $comments as $comm )
		{
			$data .= '
			<li class="list-group-item px-0 pt-0 border-0 mb-2">
                <div class="row">
                    <div class="col-auto">
                        <i class="fas fa-user"></i>
                    </div>
	
                    <div class="col-8 ps-0">
                        <h5 class="mb-0 h6">' . $comm['name'] . '</h4>
                        <p class="mb-1">' . generateDescr ( $comm['comment'], 500 ) . '</p>
                        <span class="fs-6 text-muted">' . $comm['niceTime'] . '</span>';
						
						if ( ( $comm['siteId'] != SITE_ID ) && $showAll )
						{
							$data .= '<span class="fs-6 float-right">[<span class="text-lightblue text-muted"><a href="' . ADMIN_URI . '?site=' . $comm['siteId'] . '">' . $comm['siteName'] . '</a></span>]</span> ';
						}
						
						if ( ( $comm['blogId'] > 0 ) && ( $Admin->GetBlog() == 0 ) && $showAll )
						{
							$data .= '<span class="fs-6 float-right">[<span class="text-info text-muted"><a href="' . $Admin->GetUrl( 'blog', $comm['blogId'], true ) . '">' . $comm['blogName'] . '</a></span>]</span> ';
						}

					$data .= '
                    </div>
					
					<div class="col-auto">
						<span class="dropdown dropstart">
							<a class="text-muted text-decoration-none" href="#" role="button" id="latest-comments" data-toggle="dropdown"  data-offset="-20,20" aria-expanded="false">
								<i class="bi bi-three-dots-vertical"></i>
							</a>
							
							<span class="dropdown-menu" aria-labelledby="latest-comments">
								<span class="dropdown-header">' . __( 'actions' ) . '</span>';
								
								if ( $comm['status'] == 'pending' )
								{
									$data .= '
										<a class="dropdown-item" target="_blank" href="' . $Admin->GetUrl( 'approve-comment' . PS . 'id' . PS . $comm['id'] ) . '"><i class="fa fa-desktop dropdown-item-icon text-secondary me-2"></i> ' . __( 'approve' ) . '</a>';
								}
								else
								{
									$data .= '
										<a class="dropdown-item" target="_blank" href="' . $Admin->GetUrl( 'unapprove-comment' . PS . 'id' . PS . $comm['id'] ) . '"><i class="fa fa-desktop dropdown-item-icon text-secondary me-2"></i> ' . __( 'unapprove' ) . '</a>';
								}
								
								$data .= '
									<a class="dropdown-item" href="' . $Admin->GetUrl( 'edit-comment' . PS . 'id' . PS . $comm['id'] ) . '"><i class="fa fa-edit dropdown-icon text-secondary me-2"></i> ' . __( 'edit' ) . '</a>
									
									<a class="dropdown-item" id="deleteComment" onclick="return confirm_alert2()" href="' . $Admin->GetUrl( 'delete-comment' . PS . 'id' . PS . $comm['id'] ) . '"><i class="fa fa-trash dropdown-icon text-danger me-2"></i> ' . __( 'delete' ) . '</a>
								</span>
							</span>
						</span>
					</div>
                </div>
            </li>';
		}
	}
	else
	{
		$data .= '
		<div class="alert alert-warning" role="alert">
            ' . __( 'nothing-found' ) . '
        </div>';
	}
	
	$data .= '</ul>
	</div>';
	
	$cardArgs['body'] = $data;
	
	$html = BootstrapCard( $cardArgs, false );
	
	echo $html;
	
	unset( $comments, $html, $cardArgs, $data );
	
}

#####################################################
#
# Latest News Updates function
#
#####################################################
function CreatePostDashboard()
{
	global $Admin;
	
	if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'create-new-posts' ) )
		return;
	
	$db = db();
	
	$cardArgs = array();
		
	$cardArgs['body-class'] = 'p-0';
	$cardArgs['header'] = '<i class="fa fa-file"></i> ' . __( 'quick-draft' );
	$cardArgs['header-class'] = 'border-transparent';
	
	$cardArgs['ids'] = 'id="sort" data-id="quick-draft"';

	$cardArgs['tools'] = '
	<!--
	<button type="button" id="minimize" data-id="quick-draft" class="btn btn-tool" data-card-widget="collapse">
		<i class="fas fa-minus"></i>
	</button>
	-->
	<button type="button" id="close" data-id="quick-draft"  class="btn btn-tool">
		<i class="fas fa-times"></i>
	</button>';
	
	$data = '
	<div class="row">';
	
	$data .= '
		<div class="col-md-12">
			<form id="draftForm" method="post" action="" role="form">
				<div class="messages"></div>
				<div class="card-body border-top">
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label for="title" class="col-sm-2 control-label col-form-label">' . __( 'title' ) . '</label>
								<input type="text" name="title" class="draftInfo form-control" id="title" required>
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="card-body border-top">
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<textarea id="post" name="post" rows="4" placeholder="' . __( 'whats-on-your-mind' ) . '" class="draftInfo form-control border-0"></textarea>
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
				</div>

				<div id="post-status"></div>

				<div class="border-top">
					<div class="card-body">
						<input type="submit" class="btn btn-success btn-send" value="' . __( 'submit' ) . '">
					</div>
				</div>

				<input type="hidden" name="_token" value="' . generate_token( 'add_draft' ) . '">
				
				<input type="hidden" id="lang_id" name="lang_id" value="' . $Admin->GetLang() . '">
				<input type="hidden" id="site_id" name="site_id" value="' . $Admin->GetSite() . '">
				<input type="hidden" id="blog_id" name="blog_id" value="' . $Admin->GetBlog() . '">
				<input type="hidden" id="user_id" name="user_id" value="' . $Admin->UserID() . '">
			</form>
		</div>
	';
	
	$data .= '
	<div class="card-body border-top">
		<div class="row">
			<div class="col-md-12">
				<h4>' . __( 'your-recent-drafts' ) . '</h4>';
				
	$cacheFile = CacheFileName( 'admin-dash-draft-posts-userid_' . $Admin->UserID(), null, $Admin->GetLang(), $Admin->GetBlog(), null, 5, null, $Admin->GetSite() );

	//Get the data from the cache, if is valid
	if ( ValidOtherCache( $cacheFile, 1800 ) )
	{
		$posts = ReadCache( $cacheFile );
	}
	
	//Get the data and save it to the cache, if needed...
	else
	{
		$db = db();
		
		$q = "(p.id_site = " . $Admin->GetSite() . ") AND (p.id_lang = " . $Admin->GetLang() . ") AND (p.id_blog = " . $Admin->GetBlog() . ")" . ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) ? " AND (p.id_member = " . $Admin->UserID() . ")" : "" ) . " AND (p.post_type = 'post') AND (p.post_status = 'draft')";

		$query = PostsDefaultQuery( $q, 5, 'p.added_time DESC', 'p.id_post' );

		//Query: posts
		$tmp = $db->from( null, $query )->all();
		
		$posts = array();
		
		if ( !empty( $tmp ) )
		{
			$i = 0;
				
			foreach ( $tmp as $p )
			{				
				$posts[$i] = BuildPostVars( $p );
					
				$i++;
			}
			
			unset( $tmp );
			
			WriteOtherCacheFile( $posts, $cacheFile );
		}
	}

	if ( !empty( $posts ) )
	{
		$data .= '
		<ul class="chart-legend clearfix">';
			
		foreach ( $posts as $post )
		{
			$data .= '
				<li class="">
					<a href="' . $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $post['id'] ) . '">' . ( empty( $post['title'] ) ? __( 'empty-title' ) : $post['title'] ) . '</a>
					<span class="badge bg-secondary float-right">' . $post['added']['time'] . '</span>
				</li>';
				
			
			unset( $post );
		}
		
		$data .= '
		</ul>';
		
		unset( $posts );
	}
	else
	{
		$data .= '
		<div class="alert alert-warning" role="alert">
            ' . __( 'nothing-found' ) . '
        </div>';
	}
	
	$data .= '</div>
			</div>
		</div>
	</div>';
	
	$cardArgs['body'] = $data;

	$html = BootstrapCard( $cardArgs, false );
	
	echo $html;
	
	unset( $html, $cardArgs );
}

#####################################################
#
# Latest News Updates function
#
#####################################################
function LatestNewsUpdates()
{
	global $Admin;
	
	$posts = GetFeed( $Admin->newsUri );
	
	$cardArgs = array();
		
	$cardArgs['body-class'] = 'p-0';
	$cardArgs['header'] = '<i class="fa fa-rss"></i> ' . __( 'latest-news-and-releases' );
	$cardArgs['header-class'] = 'border-transparent';
	
	$cardArgs['ids'] = 'id="sort" data-id="latest-news-and-releases"';
	
	$ul = '<ul class="list-style-none clearfix">';
	
	$cardArgs['tools'] = '
	<!--
	<button type="button" id="minimize" data-id="latest-news-and-releases" class="btn btn-tool" data-card-widget="collapse">
		<i class="fas fa-minus"></i>
	</button>
	-->
	<button type="button" id="close" data-id="latest-news-and-releases" class="btn btn-tool">
		<i class="fas fa-times"></i>
	</button>';
	
	if ( !empty( $posts ) )
	{
		foreach( $posts as $item )
		{
			$ul .= '
			<li class="d-flex no-block card-body">
                <i class="fa fa-check-circle w-30px mt-1"></i>
                <div>
                    <a href="' . $item['url'] . '" target="_blank" class="mb-0 font-medium p-0">
						' . $item['title'] . '
					</a>
                    <span class="text-muted">' . $item['descr'] . '</span>
                </div>
				<div class="ms-auto">
                    <div class="tetx-right">
                        <span class="text-muted font-16">' . postDate( $item['dateUnix'] ) . '</span>
                    </div>
				</div>
            </li>';
		}
	}

	$ul .= '</ul>';
	
	$cardArgs['body'] = $ul;

	$html = BootstrapCard( $cardArgs, false );
	
	echo $html;
	
	unset( $posts, $html, $ul, $cardArgs );
}

#####################################################
#
# Page Builder function
#
#####################################################
function AdminPageBuilder( $data = null, $echo = true )
{
	include ( ARRAYS_ROOT . 'forms-arrays.php');
	
	$html = '
	<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title">
			' . __( 'page-builder' ) . '
		</h3>
		
		<div class="card-tools" id="addRawTools">
			<button title="' . __( 'add-row' ) . '" type="button" id="addRow" class="btn btn-tool">
				<i class="fas fa-plus"></i> ' . __( 'add-row' ) . '
			</button>
			
			<button title="' . __( 'expand-contract-all' ) . '" type="button" id="expandAll" class="btn btn-tool">
				<i class="fas fa-compress"></i> ' . __( 'expand-contract-all' ) . '
			</button>
		</div>
	</div>

	<div class="card-body" id="pageBuilder">  
		<section id="formBuilder" class="connectedSortable">';
		/*
		if ( !empty( $Form['elements'] ) ) :

			foreach( $Form['elements'] as $elmnt ) : ?>

			<div data-id="<?php echo $elmnt['id'] ?>" id="table-item-<?php echo $elmnt['id'] ?>" class="card multi-collapse">
				<div class="card-header bg-light">
					<h3 class="card-title">
						<span id="elemntTitle<?php echo $elmnt['id'] ?>"><?php echo $elmnt['name'] ?></span>

						<div id="columnTitleDiv<?php echo $elmnt['id'] ?>" class="btn-group d-none">
							<input placeholder="<?php echo __( 'column-name' ) ?>" class="form-control" type="text" id="elemntTitleInput<?php echo $elmnt['id'] ?>" value="<?php echo $elmnt['name'] ?>" />
							<button type="button" id="cancelTitle<?php echo $elmnt['id'] ?>" data-id="<?php echo $elmnt['id'] ?>" class="btn btn-tool cancelTitleButton"><i class="fa fa-times"></i></button>
							<button type="button" id="saveTitle<?php echo $elmnt['id'] ?>" data-id="<?php echo $elmnt['id'] ?>" class="btn btn-tool saveTitleButton"><i class="fa fa-check"></i></button>
						</div>
						<button type="button" id="changeTitle<?php echo $elmnt['id'] ?>" data-id="<?php echo $elmnt['id'] ?>" class="btn btn-tool changeTitleButton">
							<i class="fas fa-edit"></i>
						</button>
					</h3>

					<div class="card-tools">
						<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-minus"></i>
						</button>

						<button type="button" id="close" data-id="<?php echo $elmnt['id'] ?>" class="btn btn-tool remColumnButton">
							<i class="fas fa-times"></i>
						</button>
					</div>
				</div>
				
				<!-- Head -->
				<div class="card-body">
				
					<ul class="nav nav-tabs" id="tabs-header-<?php echo $elmnt['id'] ?>-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="tab-header-<?php echo $elmnt['id'] ?>-head-tab" data-toggle="pill" href="#tab-header-<?php echo $elmnt['id'] ?>-head" role="tab" aria-controls="tab-header-<?php echo $elmnt['id'] ?>-head" aria-selected="true"><?php echo __( 'heading' ) ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tab-header-<?php echo $elmnt['id'] ?>-design-tab" data-toggle="pill" href="#tab-header-<?php echo $elmnt['id'] ?>-design" role="tab" aria-controls="tab-header-<?php echo $elmnt['id'] ?>-design" aria-selected="false"><?php echo __( 'design' ) ?></a>
						</li>
					</ul>

					<div class="card-body">
						<div class="tab-content" id="tabs-header-<?php echo $elmnt['id'] ?>-tabContent">
						
							<div class="tab-pane fade show active" parent="<?php echo $elmnt['id'] ?>" id="tab-header-<?php echo $elmnt['id'] ?>-head" role="tabpanel" aria-labelledby="tab-header-<?php echo $elmnt['id'] ?>-tab">

								<section id="contentHeaderBuilder<?php echo $elmnt['id'] ?>" class="connectedSortable2">
									<?php if ( !empty( $elmnt['elements']['header'] ) ) : ?>
										<?php BuildTableElementHtml( $elmnt['elements']['header'], 'header', true ) ?>
									<?php endif ?>
								</section>
								
								<button title="<?php echo __( 'add-element' ) ?>" data-id="<?php echo $elmnt['id'] ?>" type="button" class="btn btn-tool addColumnHeadElement">
									<i class="fas fa-plus"></i> <?php echo __( 'add-element' ) ?>
								</button>
							</div>
							<div class="tab-pane fade" id="tab-header-<?php echo $elmnt['id'] ?>-design" role="tabpanel" aria-labelledby="tab-<?php echo $elmnt['id'] ?>-design-tab">
								<?php BuildTableDesingHtml( $elmnt['id'], ( isset( $elmnt['data']['header'] ) ? $elmnt['data']['header'] : null ), 'header', true ) ?>
							</div>
						</div>
					</div>

				</div>
				
				<!-- Cell -->
				<div class="card-body">
				
					<ul class="nav nav-tabs" id="tabs-<?php echo $elmnt['id'] ?>-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="tab-<?php echo $elmnt['id'] ?>-cell-tab" data-toggle="pill" href="#tab-<?php echo $elmnt['id'] ?>-cell" role="tab" aria-controls="tab-<?php echo $elmnt['id'] ?>-cell" aria-selected="true"><?php echo __( 'cell-template' ) ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tab-<?php echo $elmnt['id'] ?>-design-tab" data-toggle="pill" href="#tab-<?php echo $elmnt['id'] ?>-design" role="tab" aria-controls="tab-<?php echo $elmnt['id'] ?>-design" aria-selected="false"><?php echo __( 'design' ) ?></a>
						</li>
					</ul>

					<div class="card-body">
						<div class="tab-content" id="tabs-<?php echo $elmnt['id'] ?>-tabContent">
							<div class="tab-pane fade show active" parent="<?php echo $elmnt['id'] ?>" id="tab-<?php echo $elmnt['id'] ?>-cell" role="tabpanel" aria-labelledby="tab-<?php echo $elmnt['id'] ?>-cell-tab">

								<section id="contentCellBuilder<?php echo $elmnt['id'] ?>" class="connectedSortable2">
									<?php if ( !empty( $elmnt['elements']['cell'] ) ) : ?>
										<?php BuildTableElementHtml( $elmnt['elements']['cell'], 'cell', true ) ?>
									<?php endif ?>
								</section>
								
								<button title="<?php echo __( 'add-element' ) ?>" data-id="<?php echo $elmnt['id'] ?>" type="button" id="cell" class="btn btn-tool addColumnCellElement">
									<i class="fas fa-plus"></i> <?php echo __( 'add-element' ) ?>
								</button>
							</div>
							<div class="tab-pane fade" id="tab-<?php echo $elmnt['id'] ?>-design" role="tabpanel" aria-labelledby="tab-<?php echo $elmnt['id'] ?>-design-tab">
								<?php BuildTableDesingHtml( $elmnt['id'], ( isset( $elmnt['data']['cell'] ) ? $elmnt['data']['cell'] : null ), 'cell', true ) ?>
							</div>
						</div>
					</div>

				</div>
			</div>
			<?php endforeach ?>
		<?php endif ?>*/
		
		$html .= '
		</section>
	</div>
</div>';

	if ( $echo )
		echo $html;
	else
		return $html;
}

#####################################################
#
# Latest Posts function
#
#####################################################
function LatestDashboardPosts()
{
	global $Admin;
	
	if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'manage-own-posts' ) )
		return;
	
	$cardArgs = $tableArgs = array();
	
	$tableArgs['responsive'] = true;
	$tableArgs['headData'] = $tableArgs['bodyData'] = array();
	
	$cardArgs['ids'] = 'id="sort" data-id="latest-posts"';

	$cardArgs['body-class'] = 'p-0';
	$cardArgs['header'] = '<i class="fa fa-copy"></i> ' . __( 'latest-posts' );
	$cardArgs['header-class'] = 'border-transparent';
	
	$cardArgs['tools'] = '
	<!--
	<button type="button" id="minimize" data-id="latest-posts" class="btn btn-tool" data-card-widget="collapse">
		<i class="fas fa-minus"></i>
	</button>
	-->
	<button type="button" id="close" data-id="latest-posts" class="btn btn-tool">
		<i class="fas fa-times"></i>
	</button>';
	
	$tableArgs['headData'][] = array( 'title' => __( 'title' ) );
	$tableArgs['headData'][] = array( 'title' => __( 'date' ) );
	$tableArgs['headData'][] = array( 'title' => '' );
	
	$cacheFile = CacheFileName( 'admin-dash-posts-userid_' . $Admin->UserID(), null, $Admin->GetLang(), $Admin->GetBlog(), null, 5, null, $Admin->GetSite() );

	//Get the data from the cache, if is valid
	if ( ValidOtherCache( $cacheFile, 1800 ) )
	{
		$posts = ReadCache( $cacheFile );
	}
	
	//Get the data and save it to the cache, if needed...
	else
	{
		$db = db();
		
		$q = "(p.id_site = " . $Admin->GetSite() . ") AND (p.id_lang = " . $Admin->GetLang() . ") AND (p.id_blog = " . $Admin->GetBlog() . ")" . ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) ? " AND (p.id_member = " . $Admin->UserID() . ")" : "" ) . " AND (p.post_type = 'post') AND (p.post_status = 'published')";

		$query = PostsDefaultQuery( $q, 5, 'p.added_time DESC', 'p.id_post' );

		//Query: posts
		$tmp = $db->from( null, $query )->all();
		
		$posts = array();
		
		if ( !empty( $tmp ) )
		{
			$i = 0;
				
			foreach ( $tmp as $p )
			{				
				$posts[$i] = BuildPostVars( $p );
					
				$i++;
			}
			
			unset( $tmp );
			
			WriteOtherCacheFile( $posts, $cacheFile );
		}
	}

	if ( !empty( $posts ) )
	{
		foreach ( $posts as $post )
		{
			$tableArgs['bodyData'][$post['id']] = array();
			
			$tableArgs['bodyData'][$post['id']]['td'][] = array( 'data' => '<a href="' . $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $post['id'] ) . '">' . $post['title'] . '</a>' );
			
			$tableArgs['bodyData'][$post['id']]['td'][] = array( 'data' => $post['added']['time'] );
			
			$data = '
				<span class="dropdown dropstart">
                    <a class="text-muted text-decoration-none" href="#" role="button" id="latest-posts" data-toggle="dropdown"  data-offset="-20,20" aria-expanded="false">
						<i class="bi bi-three-dots-vertical"></i>
                    </a>
                    
					<span class="dropdown-menu" aria-labelledby="latest-posts">
                        <span class="dropdown-header">' . __( 'actions' ) . '</span>
                            <a class="dropdown-item" target="_blank" href="' . $post['postUrl'] . '"><i class="fa fa-desktop dropdown-item-icon text-secondary"></i> ' . __( 'view' ) . '</a>
							
							<a class="dropdown-item" href="' . $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $post['id'] ) . '"><i class="fa fa-edit dropdown-item-icon text-secondary"></i> ' . __( 'edit' ) . '</a>
							
							<a class="dropdown-item" id="deletePost" onclick="return confirm_alert2()" href="' . $Admin->GetUrl( 'delete-post' . PS . 'id' . PS . $post['id'] ) . '"><i class="fa fa-trash dropdown-item-icon text-danger"></i> ' . __( 'delete' ) . '</a>
                        </span>
                    </span>
				</span>';

			$tableArgs['bodyData'][$post['id']]['td'][] = array( 'data' => $data );
		}
	}
	else
	{
		$data = '
		<div class="alert alert-warning" role="alert">
            ' . __( 'nothing-found' ) . '
        </div>';
		
		$tableArgs['bodyData'][0]['td'][] = array( 'data' => $data );
	}
	
	$cardArgs['body'] = BootstrapTable( $tableArgs, false );
	
	$html = BootstrapCard( $cardArgs, false );
	
	echo $html;
	
	unset( $posts, $html, $cardArgs, $tableArgs );
}

#####################################################
#
# Latest Dashboard Logs function
#
#####################################################
function LatestDashboardLogs()
{
	global $Admin;
	
	if ( !IsAllowedTo( 'admin-site' ) )
		return;
	
	$cardArgs = array();	

	$cardArgs['body-class'] = 'p-0';
	//$cardArgs['card-class'] = 'collapsed-card';
	$cardArgs['header'] = '<i class="fa fa-bell"></i> ' . __( 'logs' );
	$cardArgs['header-class'] = 'border-transparent';
	
	$cardArgs['ids'] = 'id="sort" data-id="latest-logs"';
	
	$ul = '<ol class="activity-feed mb-0 ps-2">';
	
	$cardArgs['tools'] = '
	<!--
	<button type="button" id="minimize" data-id="latest-logs" class="btn btn-tool" data-card-widget="collapse">
		<i class="fas fa-minus"></i>
	</button>
	-->
	<button type="button" id="close" data-id="latest-logs" class="btn btn-tool">
		<i class="fas fa-times"></i>
	</button>';
	
	$showAll = ( $Admin->IsDefaultSite() ? $Admin->Settings()::IsTrue( 'parent_site_shows_everything' ) : false );
	
	$cacheFile = CacheFileName( 'admin-dash-logs-userid_' . $Admin->UserID(), null, $Admin->GetLang(), null, null, 5, null, $Admin->GetSite() );

	//Get the data from the cache, if is valid
	if ( ValidOtherCache( $cacheFile, 1800 ) )
	{
		$logs = ReadCache( $cacheFile );
	}
	
	//Get the data and save it to the cache, if needed...
	else
	{
		$db = db();
		
		$query = "
		SELECT lo.user_id, lo.title, lo.id_site, lo.descr, lo.added_time, lo.ip, lo.type, s.url, s.title as sna, COALESCE(u.real_name, u.user_name, NULL) as user_name
		FROM `" . DB_PREFIX . "logs` AS lo
		INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = lo.id_site
		INNER JOIN `" . DB_PREFIX . USERS . "` as u ON u.id_member = lo.user_id
		" . ( $showAll ? " WHERE (lo.id_site = " . $Admin->GetSite() . ")" : "" ) . "
		ORDER BY lo.added_time DESC LIMIT 5";

		//Query: logs
		$logs = $db->from( null, $query )->all();

		if ( !empty( $logs ) )
		{
			WriteOtherCacheFile( $logs, $cacheFile );
		}
	}
	
	if ( !empty( $logs ) )
	{
		foreach( $logs as $log )
		{
			$ul .= '
			<li class="feed-item">';
			
			$ul .= '<div class="feed-item-list">';
			
			$ul .= '<p class="text-muted mb-1 font-size-13">';
			
			if ( ( $log['id_site'] != SITE_ID ) && $showAll )
			{
				$ul .= '[<span class="text-lightblue"><a href="' . ADMIN_URI . '?site=' . $log['id_site'] . '">' . $log['sna'] . '</a>' . '</span>] ';
			}

			$ul .= $log['title'] . ' <small class="d-inline-block ms-1">' . postDate( $log['added_time'], false ) . '</small> [<span class="text-danger">' . $log['type'] . '</span>]' . ( !empty( $log['descr'] ) ? ' <a href="#" type="button" data-toggle="tooltip" data-placement="right" data-html="true" title="' . $log['descr'] . '"><i class="bi bi-info-circle"></i></a>' : '' ) . '</p>';

			if ( !empty( $log['ip'] ) )
			{
				$ul .= '<p class="text-muted mb-1">IP: <span class="text-primary">' . $log['ip'] . '</p>';
			}

			$ul .= '</div>';

			$ul .= '
			</li>';
		}
	}

	$ul .= '</ol>';
	
	$cardArgs['body'] = $ul;

	$html = BootstrapCard( $cardArgs, false );
	
	echo $html;
	
	unset( $posts, $html, $ul, $cardArgs );
	
}

#####################################################
#
# At A Glance function
#
#####################################################
function AtAGlanse()
{
	global $Admin;
	
	$counts = $Admin->Counts();
	
	$cardArgs = $tableArgs = array();
	
	$tableArgs['responsive'] = true;
	$tableArgs['headData'] = $tableArgs['bodyData'] = array();

	$cardArgs['body-class'] = 'p-0';
	$cardArgs['header'] = '<i class="fa fa-eye"></i> ' . __( 'at-a-glance' );
	$cardArgs['header-class'] = 'border-transparent';
	
	$cardArgs['ids'] = 'id="sort" data-id="at-a-glance"';
	
	$cardArgs['tools'] = '
	<!--
	<button type="button" id="minimize" data-id="at-a-glance" class="btn btn-tool" data-card-widget="collapse">
		<i class="fas fa-minus"></i>
	</button>
	data-card-widget="remove"
	-->
	<button type="button" id="close" data-id="at-a-glance" class="btn btn-tool">
		<i class="fas fa-times"></i>
	</button>';
	
	$tableArgs['headData'][] = array( 'title' => '' );
	$tableArgs['headData'][] = array( 'title' => '' );
	
	if ( IsAllowedTo( 'admin-site' ) )
	{
		$load = $Admin->GetServerLoad();
		
		$load = ( !is_null( $load ) ? $load . "%" : '' );
		
		$tableArgs['bodyData']['server-php'] = array();
		$tableArgs['bodyData']['server-load'] = array();
		$tableArgs['bodyData']['server-mysql'] = array();
			
		$tableArgs['bodyData']['server-php']['td'][] = array( 'data' => '<i class="fa fa-inbox w-30px mt-1"></i>
				' . __( 'php-version' ) );
			
		$tableArgs['bodyData']['server-php']['td'][] = array( 'data' => '<span class="badge badge-info float-right">' . phpversion() . '</span>' );
		
		$tableArgs['bodyData']['server-load']['td'][] = array( 'data' => '<i class="fa fa-inbox w-30px mt-1"></i>
				' . __( 'server-load' ) );
			
		$tableArgs['bodyData']['server-load']['td'][] = array( 'data' => '<span class="badge badge-info float-right">' . $load . '</span>' );
		
		$tableArgs['bodyData']['server-mysql']['td'][] = array( 'data' => '<hr />' );
			
		$tableArgs['bodyData']['server-mysql']['td'][] = array( 'data' => '<span class="float-right"></span>' );
	}
	
	if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) )
	{
		$tableArgs['bodyData']['posts'] = array();
		
		$tableArgs['bodyData']['pages'] = array();
			
		$tableArgs['bodyData']['posts']['td'][] = array( 'data' => '<i class="far fa-copy w-30px mt-1"></i>
				' . __( 'posts' ) . '' );
			
		$tableArgs['bodyData']['posts']['td'][] = array( 'data' => '<a href="' . $Admin->GetUrl( 'posts' ) . '"><span class="badge badge-info float-right">' . $counts['postsCount'] . '</span></a>' );
		
		$tableArgs['bodyData']['pages']['td'][] = array( 'data' => '<i class="far fa-copy w-30px mt-1"></i>
				' . __( 'pages' ) . '' );
			
		$tableArgs['bodyData']['pages']['td'][] = array( 'data' => '<a href="' . $Admin->GetUrl( 'pages' ) . '"><span class="badge badge-info float-right">' . $counts['pagesCount'] . '</span></a>' );
	}
	
	if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-comments' ) )
	{
		$tableArgs['bodyData']['comments'] = array();
			
		$tableArgs['bodyData']['comments']['td'][] = array( 'data' => '<i class="far fa-comments w-30px mt-1"></i>
				' . __( 'comments' ) . '' );
			
		$tableArgs['bodyData']['comments']['td'][] = array( 'data' => '<a href="' . $Admin->GetUrl( 'comments' ) . '"><span class="badge badge-info float-right">' . $counts['commentsCount'] . '</span></a>' );
	}
	
	if ( $Admin->IsDefaultSite() && $Admin->MultiLang() && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-languages' ) ) )
	{
		$tableArgs['bodyData']['langs'] = array();
			
		$tableArgs['bodyData']['langs']['td'][] = array( 'data' => '<i class="far fa-flag w-30px mt-1"></i>
				' . __( 'langs' ) . '' );
			
		$tableArgs['bodyData']['langs']['td'][] = array( 'data' => '<a href="' . $Admin->GetUrl( 'langs' ) . '"><span class="badge badge-info float-right">' . $counts['langsCount'] . '</span></a>' );
	}
	
	if ( $Admin->IsDefaultSite() && $Admin->MultiBlog() && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-blogs' ) ) )
	{
		$tableArgs['bodyData']['blogs'] = array();
			
		$tableArgs['bodyData']['blogs']['td'][] = array( 'data' => '<i class="fas fa-boxes w-30px mt-1"></i>
				' . __( 'blogs' ) . '' );
			
		$tableArgs['bodyData']['blogs']['td'][] = array( 'data' => '<a href="' . $Admin->GetUrl( 'blogs' ) . '"><span class="badge badge-info float-right">' . $counts['blogsCount'] . '</span></a>' );
	}

	$cardArgs['body'] = BootstrapTable( $tableArgs, false );

	$html = BootstrapCard( $cardArgs, false );
	
	echo $html;
	
	unset( $counts, $html, $ul, $cardArgs );
}