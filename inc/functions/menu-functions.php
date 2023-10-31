<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Menu function
#
#####################################################
function Menu( $param = array(), $menuId = null, $echo = true )
{
	$pages 	= $customTypes = $currCat = $carTag = $curBlog = $curCustom = $items = null;
	$b 		= $c = $p = $cl = 0;

	$currentLang 	= CurrentLang();
	$defaultLang 	= Settings::LangData(); //Set the default language
	$code 			= $currentLang['lang']['code'];
	$defaultCode 	= $defaultLang['lang']['code'];
	$langId 		= $currentLang['lang']['id'];
	$defaultLangId 	= $defaultLang['lang']['id'];
	$canAdminSite 	= IsAllowedTo( 'admin-site' );
	$UserGroup 		= UserGroup();
	$blogId 		= ( ( MULTIBLOG && Router::GetVariable( 'isBlog' ) ) ? Theme::GetData( 'blogId' ) : null );
	$postId 		= ( ( Router::WhereAmI() == 'post' ) ? Theme::GetData( 'postId' ) : null );
	$isCat 			= Router::GetVariable( 'isCat' );
	$isPost			= ( ( Router::WhereAmI() == 'post' ) && !Router::GetVariable( 'isStaticHomePage' ) );
	$i 				= 1;
	$nav_menu 		= '';
	$auto 			= ( Settings::IsTrue( 'enable_auto_menu' ) ? true : false );
	$dt 			= Settings::Themes();
	$themeId 		= Settings::ActiveTheme();

	//Set the settings for the current lang
	$autoSettings = ( ( isset( $dt[$themeId] ) && isset( $dt[$themeId]['auto-menu'][$code] ) && !empty( $dt[$themeId]['auto-menu'][$code] ) ) 
		? $dt[$themeId]['auto-menu'][$code] : null );
	
	//If the language is not the default and the settings are empty for this language, get the settings for the default language
	if ( empty( $autoSettings ) && ( $defaultLang['lang']['id'] != $currentLang['lang']['id'] ) )
	{
		$autoSettings = ( ( isset( $dt[$themeId] ) && isset( $dt[$themeId]['auto-menu'][$defaultCode] ) && !empty( $dt[$themeId]['auto-menu'][$defaultCode] ) ) ? $dt[$themeId]['auto-menu'][$defaultCode] : null );
	}
	
	//We don't need these values anymore
	unset( $dt, $themeId, $defaultLang, $currentLang );
	
	//Stop here if we don't want a menu
	if ( $auto && isset( $autoSettings['disable_menu'] ) && $autoSettings['disable_menu'] )
		return;

	if ( Router::GetVariable( 'isCustomType' ) )
	{
		//global $CustomType;
		
		//$curCustom = ( !empty( $CustomType ) ? $CustomType['id'] : null );
	}

	//Default Args
	$args = array(
		//Auto Menu settings
		'pages_more_title'      	=> ( isset( $autoSettings['pages_more_title'] ) ? $autoSettings['pages_more_title'] : __( 'more' ) ), //What to show as title for the dropdown item, works only for the pages in auto menu
		'links_more_title'      	=> ( isset( $autoSettings['links_more_title'] ) ? $autoSettings['links_more_title'] : __( 'more' ) ), //What to show as title for the dropdown item, works only for the custom links in auto menu
		'categories_button_title'   => ( isset( $autoSettings['categories_button_title'] ) ? $autoSettings['categories_button_title'] : __( 'categories' ) ), //What to show as title for the dropdown item, works only for the pages in auto menu
		'show_home'           		=> ( isset( $autoSettings['show_home'] ) ? $autoSettings['show_home'] : true ), //Choose to show the home button, works only with auto menu
		'show_blogs'           	 	=> ( isset( $autoSettings['show_blogs'] ) ? $autoSettings['show_blogs'] : false ), //works only with auto menu
		'show_blog_cats'           	=> ( isset( $autoSettings['show_blog_cats'] ) ? $autoSettings['show_blog_cats'] : true ), //Choose to show the blog categories, works only with auto menu
		'show_pages'           	 	=> ( isset( $autoSettings['show_pages'] ) ? $autoSettings['show_pages'] : false ),//works only with auto menu
		'show_pages_as_childs'      => ( isset( $autoSettings['show_pages_as_childs'] ) ? $autoSettings['show_pages_as_childs'] : true ), //Choose to show the pages under a "More" item (pages_more_title), works only with auto menu
		'show_custom_types'         =>  ( isset( $autoSettings['show_custom_types'] ) ? $autoSettings['show_custom_types'] : false ),//works only with auto menu
		'show_categories'           => ( isset( $autoSettings['show_categories'] ) ? $autoSettings['show_categories'] : false ), //works only with auto menu
		'show_child_custom_types' 	=> ( isset( $autoSettings['show_child_custom_types'] ) ? $autoSettings['show_child_custom_types'] : false ), //Choose to show the childs under a main "Custom Post Type" item, works only with auto menu
		'show_categories_as_childs' => ( isset( $autoSettings['show_categories_as_childs'] ) ? $autoSettings['show_categories_as_childs'] : true ), //Choose to show the pages under a "Categories" item (categories_button_title), works only with auto menu
		'show_child_categories'     => ( isset( $autoSettings['show_child_categories'] ) ? $autoSettings['show_child_categories'] : true ), //Choose to show the child categories, works only with auto menu
		'only_current_blog_pages'	=> ( isset( $autoSettings['only_current_blog_pages'] ) ? $autoSettings['only_current_blog_pages'] : true ), //Choose to show only the pages of the current blog, or every page despite the blog
		'hide_empty_categories'     => ( isset( $autoSettings['hide_empty_categories'] ) ? $autoSettings['hide_empty_categories'] : true ), //Choose to skip empty categories
		'limit_blog_categories'          => ( isset( $autoSettings['limit_blog_categories'] ) ? (int) $autoSettings['limit_blog_categories'] : 0 ), //0 to show all blog categories, works only with auto menu
		'show_links_as_childs'      	=> ( isset( $autoSettings['show_links_as_childs'] ) ? $autoSettings['show_links_as_childs'] : false ), //Choose to show the links under a "More" item (links_more_title), works only with auto menu
		'custom_links'      	=> ( isset( $autoSettings['custom_links'] ) ? $autoSettings['custom_links'] : null ), //The custom links array, works only with auto menu
		
		//Custom menu settings
		'menu_position'        		 => 'primary',
		
		//Generic menu settings
		'show_children'        		 => true, //This will hide the child links, if the theme doesn't support it
		
		//Menu html values
		'container'      		 	=> 'nav',
		'container_class'		 	=> 'menu-main',
		'container_id'    		 	=> 'menu',
		'container_aria_label' 	 	=> 'Main Navigation',
		'container_role' 	 		=> '',
		'menu_class'      			=> '',
		'menu_id'        		 	=> '',
		'full_depth'           		=> true, //Choose to show the full depth of items (categories and sub menu )
		'items_wrap'      		 	=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
		'childs_wrap'      		 	=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
		'single_item_wrap'      	=> '<li id="%s" class="%s">%s</li>', //<li></li>
		'single_child_item_wrap'    => '<li id="%s" class="%s">%s</li>', //<li></li>
		'link_wrap'      		 	=> '', // for <a href=""></a>
		'child_link_wrap'      		=> '', // for <a href=""></a>
		'main_link_class'   	  	=> '',
		'child_link_class'   	  	=> '',
		'dropdown_toggle_class'		=> '', //If this is not empty, the link will be replaced with a "#"
		'dropdown_toggle_extra'		=> '', //eg: role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
		'submenu_class'   	  	 	=> 'sub-menu',
		'home_class'   	  		 	=> 'menu-item-home',
		'has_children_class' 	 	=> 'menu-item-has-children',
		'current_class'  		 	=> 'current-menu-item',
		'current_parent_class'   	=> 'current-menu-parent',
		'item_class'   	  	 	 	=> 'menu-item',
		'current_item_link_class'  	=> '',
		'link_class'   	  	 		=> '',
		'before'    		 		=> '', //before <container> tag
		'after'    		 		 	=> '', //after <container> tag
		'before_links'    		 	=> '', //before main <ul> tag
		'after_links'      		 	=> '', //after main <ul> tag
		'before_link'    		 	=> '', //before <a href > tag
		'after_link'      		 	=> '', //after <a href > tag
		'before_childs'      	 	=> '', //before <ul> tag
		'after_childs'      		=> '', //after <ul> tag
		'before_child_link'      	=> '', //before <a href > tag
		'after_child_link'      	=> '', //after <a href > tag
		'before_child_title'      	=> '', //before name
		'after_child_title'      	=> '', //after name
	);
	
	if ( !empty( $param ) ) 
	{
		$args = array_merge( $args, $param );
	}

	$args = (object) $args;
	
	if ( $auto )
	{
		//Get the data for auto menu
		$data = AutoMenuCats();
	}
	else
	{
		$data = array();
		
		if ( !empty( $args->menu_position ) )
		{
			$cacheFile = CacheFileName( 'menu-position_' . $args->menu_position );
			
			//Get the data from the cache, if is valid
			if ( ValidOtherCache( $cacheFile ) )
			{
				$data = ReadCache( $cacheFile );
			}
			
			//Get the data and save it to the cache, if needed...
			else
			{
				$db = db();
				
				$itm = $db->from( 
				null, 
				"SELECT id_menu
				FROM `" . DB_PREFIX . "menus`
				WHERE (id_site = " . SITE_ID . ") AND (id_lang = " . $langId . ( ( $langId != $defaultLangId ) ? " OR id_lang = " . $defaultLangId : "" ) . ") AND (location = '" . $args->menu_position . "')"
				)->single();
				
				if ( !empty( $itm ) )
				{
					$itms = $db->from( 
					null, 
					"SELECT *
					FROM `" . DB_PREFIX . "menu_items`
					WHERE (id_menu = " . $itm['id_menu'] . ") AND (id_parent = 0)
					ORDER BY item_order ASC"
					)->all();
					
					if ( !empty( $itms ) )
					{
						foreach( $itms as $item )
						{
							$data[$item['id']] = array(
								'name' 		=> $item['name'],
								'label' 	=> $item['label'],
								'url' 		=> $item['url'],
								'new_tab' 	=> $item['new_tab'],
								'type' 		=> $item['type'],
								'childs'	=> array()
							);
							
							$itms_ = $db->from( 
							null, 
							"SELECT *
							FROM `" . DB_PREFIX . "menu_items`
							WHERE (id_parent = " . $item['id_item'] . ")
							ORDER BY item_order ASC"
							)->all();
							
							if ( !empty( $itms_ ) )
							{
								foreach( $itms_ as $itm_ )
								{
									$data[$item['id']]['childs'][$itm_['id']] = array(
										'name' 		=> $itm_['name'],
										'label' 	=> $itm_['label'],
										'url' 		=> $itm_['url'],
										'new_tab' 	=> $itm_['new_tab'],
										'type' 		=> $itm_['type']
									);
								}
							}
						}
					}
					WriteOtherCacheFile( $data, $cacheFile );
				}
			}
		}
	}
	
	if ( empty( $data ) )
		return null;
	
	if ( $auto && !empty( $args->show_custom_types ) )
	{
		$customTypes = GetMenuCustomTypes( SITE_ID, $args->show_child_custom_types, $code );
	}

	$warpId = ( !empty( $args->menu_id ) ? $args->menu_id : ( $menuId ? 'menu-' . $menuId : '' ) );
	
	$warpClass = ( !empty( $args->menu_class ) ? $args->menu_class : '' );

	if ( !empty( $args->before ) )
		$nav_menu .= $args->before;
	
	if ( !empty( $args->container ) )
	{
		$nav_menu .= '<' . $args->container;
	
		$nav_menu .= ( !empty( $args->container_id ) ? ' id="' . $args->container_id . '"' : '' );

		$nav_menu .= ( !empty( $args->container_class ) ? ' class="' . $args->container_class . '"' : ' class="menu-' . ( $menuId ? $menuId . '-' : '' ) . 'container' );
		
		$nav_menu .= ( !empty( $args->container_role ) ? ' role="' . $args->container_role . '"' : '' );
		
		$nav_menu .= ( ( !empty( $args->container ) && !empty( $args->container_aria_label ) ) ? ' aria-label="' . $args->container_aria_label . '"' : '' );

		$nav_menu .= '>';
	}
	
	if ( !empty( $args->before_links ) )
		$nav_menu .= $args->before_links;

	//Home
	if ( $auto && $args->show_home )
	{
		$chId 		= 'menu-item-' . $i;
		$chClass    = ( ( !empty( $args->current_class ) && ( Router::WhereAmI() == 'home' ) ) ? ' ' . $args->current_class : '' );
		$chClass   .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );
		$chClass   .= ( ( !empty( $args->home_class ) && ( Router::WhereAmI() == 'home' ) ) ? ' ' . $args->home_class : '' );

		$chName = '';
			
		if ( !empty( $args->before_link ) )
			$chName .= $args->before_link;
		
		$chName .= MenuLinkItem( $args, __( 'home' ), Router::GetVariable( 'siteRealUrl' ), false, false, ( Router::WhereAmI() == 'home' ) );

		if ( !empty( $args->after_link ) )
			$chName .= $args->after_link;
		
		$items .= MenuSingleItem( $args, $chId, $chClass, $chName );
		
		$i++;
	}
	
	if ( !empty( $data ) )
	{
		if ( !$auto )
		{
			foreach( $data as $id => $item )
			{
				$chName 	= '';
				$chId 		= 'menu-item-' . $id;
				$isCurrent 	= false;
				
				$chClass    = ( ( !empty( $item['childs'] ) && !empty( $args->has_children_class ) && $args->show_children ) ? ' ' . $args->has_children_class : '' );

				$chClass   .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $id : '' );

				if ( !empty( $args->before_link ) )
				{
					$chName .= $args->before_link;
				}
				
				$link = $item['url'];

				if ( !empty( $args->dropdown_toggle_class ) )
				{
					$link = '#';
				}
				
				$hasChildren = ( !empty( $item['childs'] && $args->show_children ) ? true : false );
				
				$name = ( !empty( $item['name'] ) ? $item['name'] : $item['label'] );

				$chName .= MenuLinkItem( $args, $name, $link, false, $hasChildren, $isCurrent );

				if ( !empty( $item['childs'] ) && $args->show_children )
				{
					$childs = '';
							
					if ( !empty( $args->before_childs ) )
					{
						$childs .= $args->before_childs;
					}

					foreach( $item['childs'] as $c_id => $c_item )
					{
						$cId 		= 'menu-item-' . $c_id;

						$isCurrent 	= false;

						$cClass  	= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );
							
						$cName = '';

						if ( !empty( $args->before_child_link ) )
						{
							$cName .= $args->before_child_link;
						}
						
						$name = ( !empty( $c_item['name'] ) ? $c_item['name'] : $c_item['label'] );
								
						$cName .= MenuLinkItem( $args, $name, $c_item['url'], true, false, $isCurrent );

						if ( !empty( $args->after_child_link ) )
						{
							$cName .= $args->after_child_link;
						}

						$childs .= MenuSingleItem( $args, $cId, $cClass, $cName, true );
								
						if ( !empty( $args->after_childs ) )
						{
							$childs .= $args->after_childs;
						}
					}

					$wId = 'menu-item-' . $id;

					$chName 	.= sprintf( $args->childs_wrap, $wId, $args->submenu_class, $childs );
							
					if ( !empty( $args->after_link ) )
					{
						$chName .= $args->after_link;
					}
				}

				$items .= MenuSingleItem( $args, $chId, $chClass, $chName );
			}
		}
		
		if ( $auto && isset( $data[$code] ) )
		{
			$data = $data[$code];

			if ( MULTIBLOG )
			{
				if ( !empty( $args->show_blogs ) )
				{
					foreach( $data as $bSef => $bRow )
					{
						if ( $bSef == 'orphanCats' )
							continue;
						
						if ( !in_array( $bRow['blogId'], $args->show_blogs ) )
							continue;
						
						//Don't show it if the usergroup is not allowed
						if ( !$canAdminSite && !empty( $bRow['groups'] ) && !in_array( $UserGroup, $bRow['groups'] ) )
							continue;

						$i++;

						//Create a new variable for categories in blogs
						//so we can limit each blog's categories
						$cb = 0;
						
						$blogName = $bRow['blogName'];

						//Check if we have a translation for this name
						if ( isset( $bRow['trans'] ) && !empty( $bRow['trans'] ) && isset( $bRow['trans'][$code] ) )
						{
							$blogName = html_entity_decode( $bRow['trans'][$code]['name'] );
						}

						$chName = '';
						
						$chId 		= 'menu-item-' . $i;
						
						$isCurrent = ( ( $blogId && ( $bSef != 'orphanCats' ) && !empty( $args->current_item_link_class ) && ( $blogId == $bRow['blogId'] ) ) ? true : false );
						
						$isCurrent = ( ( $isPost && !empty( $args->current_item_link_class ) && !empty( Theme::GetData( 'blogId' ) ) && ( Theme::GetData( 'blogId' ) == $bRow['blogId'] ) ) ? true : $isCurrent );
						
						$chClass    = ( ( $blogId && ( $bSef != 'orphanCats' ) && !empty( $args->current_class ) && ( $blogId == $bRow['blogId'] ) ) ? ' ' . $args->current_class : '' );
						
						$chClass   .= ( ( $isPost && !empty( $args->current_class ) && !empty( Theme::GetData( 'blogId' ) ) && ( Theme::GetData( 'blogId' ) == $bRow['blogId'] ) ) ? ' ' . $args->current_class : '' );
	
						$chClass   .= ( ( !empty( $bRow['cats'] ) && !empty( $args->has_children_class ) ) ? ' ' . $args->has_children_class : '' );
						
						$chClass  .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );
	
						if ( $isCat && !empty( Theme::GetData( 'blogId' ) ) && ( Theme::GetData( 'blogId' ) == $bRow['blogId'] ) )
						{
							$chClass  .= ( !empty( $args->current_class ) ? ' ' . $args->current_class : '' );
							
							$chClass  .= ( !empty( $args->current_parent_class ) ? ' ' . $args->current_parent_class : '' );
							
							$isCurrent = ( !empty( $args->current_item_link_class ) ? true : false );
						}

						if ( !empty( $args->before_link ) )
							$chName .= $args->before_link;
						
						$bLink = Router::GetVariable( 'siteRealUrl' ) . $bRow['blogSef'] . PS;
						
						if ( !empty( $args->dropdown_toggle_class ) )
							$bLink = '#';
						
						$hasChildren = ( ( empty( $bRow['cats'] ) || !$args->show_children ) ? false : true );

						$chName .= MenuLinkItem( $args, $blogName, $bLink, false, $hasChildren, $isCurrent );

						if ( !empty( $bRow['cats'] ) && $args->show_blog_cats && $args->show_children )
						{
							$childs = '';
							
							$i++;
							
							if ( !empty( $args->before_childs ) )
								$childs .= $args->before_childs;
							
							foreach( $bRow['cats'] as $cSef => $cRow )
							{
								if ( $args->hide_empty_categories && ( ( $cRow['items'] == 0 ) || empty( $cRow['items'] ) ) )
								{
									continue;
								}
								
								if ( ( $args->limit_blog_categories > 0 ) && ( $cb == $args->limit_blog_categories ) )
								{
									break;
								}
								
								//Don't show it if the usergroup is not allowed
								if ( !$canAdminSite && !empty( $cRow['groups'] ) && !in_array( $UserGroup, $cRow['groups'] ) )
									continue;
								
								$cId 		= 'menu-item-' . $i;
								
								$isCurrent = ( ( $isCat && !empty( Theme::GetData( 'categoryId' ) ) && ( Theme::GetData( 'categoryId' ) == $cRow['id'] ) && $args->current_item_link_class ) ? true : false );
							
								$cClass 	= ( ( $isCat && !empty( Theme::GetData( 'categoryId' ) ) && ( Theme::GetData( 'categoryId' ) == $cRow['id'] ) && $args->current_class ) ? ' ' . $args->current_class : '' );

								$cClass 	.= ( ( $args->show_child_categories && !empty( $cRow['childs'] ) && $args->has_children_class ) ? ' ' . $args->has_children_class : '' );
								
								$cClass  	.= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );
							
								$cName = '';
								
								if ( !empty( $args->before_child_link ) )
									$cName .= $args->before_child_link;
								
								$cName .= MenuLinkItem( $args, $cRow['name'], $cRow['url'], true, false, $isCurrent );

								if ( !empty( $args->after_child_link ) )
									$cName .= $args->after_child_link;

								$childs .= MenuSingleItem( $args, $cId, $cClass, $cName, true );
								
								if ( !empty( $args->after_childs ) )
									$childs .= $args->after_childs;
								
								$i++;
								
								//Increase the categories' counter
								$cb++;
							}

							$wId 	 	 = 'menu-item-' . $i;

							$chName 	.= sprintf( $args->childs_wrap, $wId, $args->submenu_class, $childs );
							
							if ( !empty( $args->after_link ) )
								$chName .= $args->after_link;
						}

						$items .= MenuSingleItem( $args, $chId, $chClass, $chName );

						//Increase the blog's counter
						$b++;
					}
				}
				
				//Show the categories if we don't want to show blogs
				//This is needed because we have multiblogs here but maybe we don't want the blogs in the list
				if ( !empty( $args->show_categories ) )
				{
					$catsData = ( isset( $data['orphanCats'] ) ? $data['orphanCats']['cats'] : null );
					
					if ( !empty( $catsData ) )
					{
						$chName = $childs = '';
						
						//Check if we want categories as "childs"
						if ( $args->show_categories_as_childs && $args->show_children )
						{
							$i++;
							
							//Let's build the "Categories" button
							$chId 	   = 'menu-item-' . $i;

							$chClass    = ( !empty( $args->has_children_class ) ? ' ' . $args->has_children_class : '' );
							$chClass   .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );

							if ( !empty( $args->before_link ) )
								$chName .= $args->before_link;
								
							$chName .= MenuLinkItem( $args, $args->categories_button_title, '#', false, true );

							if ( !empty( $args->before_childs ) )
								$childs .= $args->before_childs;
							
							foreach( $catsData as $cSef => $cRow )
							{
								if ( $args->hide_empty_categories && ( ( $cRow['items'] == 0 ) || empty( $cRow['items'] ) ) )
								{
									continue;
								}
								
								if ( !in_array( $cRow['id'], $args->show_categories ) )
									continue;
						
								$i++;
								
								$cId 		 = 'menu-item-' . $i;

								$cClass 	 = ( ( $isCat && !empty( Theme::GetData( 'categoryId' ) ) && ( Theme::GetData( 'categoryId' ) == $cRow['id'] ) && $args->current_class ) ? ' ' . $args->current_class : '' );
				
								$cClass  	.= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );

								$cName = '';

								if ( !empty( $args->before_child_link ) )
									$cName .= $args->before_child_link;

								$cName .= MenuLinkItem( $args, $cRow['name'], $cRow['url'], true );

								if ( !empty( $args->after_child_link ) )
									$cName .= $args->after_child_link;

								$childs .= MenuSingleItem( $args, $cId, $cClass, $cName, true );
								
								//Increase the categories counter
								$c++;
							}
							
							$wId 	 	 = 'menu-item-' . $i;

							$chName 	.= sprintf( $args->childs_wrap, $wId, $args->submenu_class, $childs );
							
							if ( !empty( $args->after_link ) )
								$chName .= $args->after_link;
						}
						
						else
						{
							foreach( $catsData as $cSef => $cRow )
							{
								if ( $args->hide_empty_categories && ( ( $cRow['items'] == 0 ) || empty( $cRow['items'] ) ) )
								{
									continue;
								}
								
								if ( !in_array( $cRow['id'], $args->show_categories ) )
									continue;

								$i++;
								
								$chName = '';
									
								$chId 		= 'menu-item-' . $i;
								
								$chClass    = ( ( $isPost && !empty( $args->current_class ) && !empty( Theme::GetData( 'categoryId' ) ) && ( Theme::GetData( 'categoryId' ) == $cRow['id'] ) ) ? ' ' . $args->current_class : '' );

								$chClass    .= ( ( $args->show_child_categories && $args->show_children && !empty( $cRow['childs'] ) && !empty( $args->has_children_class ) ) ? ' ' . $args->has_children_class : '' );

								$chClass    .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );

								if ( !empty( $args->before_link ) )
									$chName .= $args->before_link;
									
								$chName .= MenuLinkItem( $args, $cRow['name'], $cRow['url'] );

								if ( !empty( $cRow['childs'] ) && $args->show_child_categories && $args->show_children )
								{
									$childs = '';
								
									if ( !empty( $args->before_childs ) )
										$childs .= $args->before_childs;
										
									foreach( $cRow['childs'] as $chSef => $chRow )
									{
										if ( $args->hide_empty_categories && ( ( $chRow['items'] == 0 ) || empty( $chRow['items'] ) ) )
										{
											continue;
										}
										
										if ( !in_array( $chRow['id'], $args->show_categories ) )
											continue;
								
										$i++;
											
										$cId 		= 'menu-item-' . $i;

										$cClass 	 = ( ( $isCat && !empty( Theme::GetData( 'categoryId' ) ) && ( Theme::GetData( 'categoryId' ) == $chRow['id'] ) && $args->current_class ) ? ' ' . $args->current_class : '' );
											
										$cName 		= '';
											
										if ( !empty( $args->before_child_link ) )
											$cName .= $args->before_child_link;
											
										$cName .= MenuLinkItem( $args, $chRow['name'], $chRow['url'], true );

										if ( !empty( $args->after_child_link ) )
											$cName .= $args->after_child_link;

										$childs .= MenuSingleItem( $args, $cId, $cClass, $cName, true );
											
										$i++;
									}
										
									if ( !empty( $args->after_childs ) )
										$childs .= $args->after_childs;

									$wId 	 	= 'menu-item-' . $i;
										
									$chName 	.= sprintf( $args->childs_wrap, $wId, $args->submenu_class, $childs );
								}
							
								if ( !empty( $args->after_link ) )
									$chName .= $args->after_link;
							
								//Increase the categories counter
								$c++;
								
								$items .= MenuSingleItem( $args, $chId, $chClass, $chName );
							}
						}

						//$items .= MenuSingleItem( $args, $chId, $chClass, $chName );
					}
					
					unset( $catsData );
				}
			}

			//No multiblog, continue with the classic categories model
			else
			{
				if ( !empty( $args->show_categories ) )
				{
					//We want categories as "childs"
					if ( $args->show_categories_as_childs && $args->show_children )
					{
						$i++;
						
						//Let's build the "Categories" button
						$cId 	   = 'menu-item-' . $i;
						//$cClass  = ( !empty( $args->main_link_class ) ? $args->main_link_class : '' );
						$cClass    = ( ( !empty( $args->has_children_class ) && $args->show_children ) ? ' ' . $args->has_children_class : '' );
						$cClass   .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );
						
						$cName = $childs = '';
						
						if ( !empty( $args->before_link ) )
							$cName .= $args->before_link;
						
						$cName .= MenuLinkItem( $args, $args->categories_button_title, '#' );
						
						if ( !empty( $args->before_childs ) )
							$childs .= $args->before_childs;
						
						foreach( $data as $cSef => $cRow )
						{
							if ( $args->hide_empty_categories && ( ( $cRow['items'] == 0 ) || empty( $cRow['items'] ) ) )
							{
								continue;
							}
							
							if ( !in_array( $cRow['id'], $args->show_categories ) )
								continue;

							$i++;

							$cId 		 = 'menu-item-' . $i;

							//$cClass 	 = ( !empty( $args->child_link_class ) ? $args->child_link_class : '' );

							$cClass 	 = ( ( $isCat && !empty( Theme::GetData( 'categoryId' ) ) && ( Theme::GetData( 'categoryId' ) == $cRow['id'] ) && $args->current_class ) ? ' ' . $args->current_class : '' );

							$cClass  	.= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );

							$cName = '';

							if ( !empty( $args->before_child_link ) )
								$cName .= $args->before_child_link;

							$cName .= MenuLinkItem( $args, $cRow['name'], $cRow['url'], true );

							if ( !empty( $args->after_child_link ) )
								$cName .= $args->after_child_link;

							$childs .= MenuSingleItem( $args, $cId, $cClass, $cName );

							//Increase the categories counter
							$c++;
						}
						
						if ( !empty( $args->after_childs ) )
							$childs .= $args->after_childs;

						$wId 	 	= 'menu-item-' . $i;
						
						$chName 	.= sprintf( $args->childs_wrap, $wId, $args->submenu_class, $childs );
						
						if ( !empty( $args->after_link ) )
							$chName .= $args->after_link;

						$items .= MenuSingleItem( $args, $chId, $chClass, $chName );
					}
					
					else
					{
						foreach( $data as $cSef => $cRow )
						{
							if ( $args->hide_empty_categories && ( ( $cRow['items'] == 0 ) || empty( $cRow['items'] ) ) )
							{
								continue;
							}
							
							if ( !in_array( $cRow['id'], $args->show_categories ) )
								continue;
							
							$i++;
							
							$chName = '';
							
							$chId = 'menu-item-' . $i;
							
							//$chClass    = ( !empty( $args->main_link_class ) ? $args->main_link_class : '' );

							$chClass    = ( ( $isPost && !empty( $args->current_class ) && !empty( Theme::GetData( 'categoryId' ) ) && ( Theme::GetData( 'categoryId' ) == $cRow['id'] ) ) ? ' ' . $args->current_class : '' );
			
							$chClass   .= ( ( $args->show_child_categories && $args->show_children && !empty( $cRow['cats'] ) && !empty( $args->has_children_class ) ) ? ' ' . $args->has_children_class : '' );
								
							$chClass   .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );
							
							$chClass   .= ( ( $isCat && !empty( Theme::GetData( 'categoryId' ) ) && ( Theme::GetData( 'categoryId' ) == $cRow['id'] ) && $args->current_class ) ? ' ' . $args->current_class : '' );

							if ( !empty( $args->before_link ) )
								$chName .= $args->before_link;
							
							$chName .= MenuLinkItem( $args, $cRow['name'], $cRow['url'] );

							if ( !empty( $cRow['childs'] ) && $args->show_child_categories && $args->show_children )
							{
								$childs = '';
								
								$i++;

								if ( !empty( $args->before_childs ) )
									$childs .= $args->before_childs;
									
								foreach( $cRow['childs'] as $chSef => $chRow )
								{
									if ( $args->hide_empty_categories && ( ( $chRow['items'] == 0 ) || empty( $chRow['items'] ) ) )
									{
										continue;
									}
									
									if ( !in_array( $chRow['id'], $args->show_categories ) )
										continue;
							
									$i++;
									
									$cId 		 = 'menu-item-' . $i;
								
									//$cClass 	 = ( !empty( $args->child_link_class ) ? $args->child_link_class : '' );

									$cClass 	 = ( ( $isCat && !empty( Theme::GetData( 'categoryId' ) ) && ( Theme::GetData( 'categoryId' ) == $chRow['id'] ) && $args->current_class ) ? ' ' . $args->current_class : '' );

									$cClass  	.= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );
								
									$cName = '';
									
									if ( !empty( $args->before_child_link ) )
										$cName .= $args->before_child_link;
									
									$cName .= MenuLinkItem( $args, $chRow['name'], $chRow['url'], true );

									if ( !empty( $args->after_child_link ) )
										$cName .= $args->after_child_link;
									
									$childs .= MenuSingleItem( $args, $cId, $cClass, $cName );
								}
								
								if ( !empty( $args->after_childs ) )
									$childs .= $args->after_childs;

								$wId 	 	= 'menu-item-' . $i;
								
								$chName 	.= sprintf( $args->childs_wrap, $wId, $args->submenu_class, $childs );
							}
							
							if ( !empty( $args->after_link ) )
								$chName .= $args->after_link;

							$items .= MenuSingleItem( $args, $chId, $chClass, $chName );
							
							//Increase the categories counter
							$c++;
						}
					}
				}
			}
		}
	}
	
	if ( $auto && !empty( $customTypes ) )
	{
		foreach( $customTypes as $cusId => $ctp )
		{
			if ( !in_array( $cusId, $args->show_custom_types ) )
				continue;

			$i++;

			$chId       = 'menu-item-' . $i;
			
			$chClass   = '';

			$chClass   .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );

			$chName = '';

			if ( !empty( $args->before_link ) )
				$chName .= $args->before_link;

			$chName .= MenuLinkItem( $args, $ctp['title'], $ctp['url'], true );
					
			if ( !empty( $args->after_link ) )
				$chName .= $args->after_link;

			$items .= MenuSingleItem( $args, $chId, $chClass, $chName, true );
		}
	}
	
	if ( $auto && !empty( $args->show_pages ) )
	{
		$pages = AutoMenuPages();
		
		if ( !empty( $pages ) )
		{
			if ( $args->show_pages_as_childs && $args->show_children )
			{
				$i++;
				
				$cId 	    = 'menu-item-' . $i;
				//$cClass   = ( !empty( $args->main_link_class ) ? $args->main_link_class : '' );
				$cClass     = ( !empty( $args->has_children_class ) ? ' ' . $args->has_children_class : '' );
				$cClass    .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );

				$cName = $childs = '';
			
				if ( !empty( $args->before_link ) )
					$cName .= $args->before_link;
		
				$cName .= MenuLinkItem( $args, $args->pages_more_title, '#', false, true );

				if ( !empty( $args->before_childs ) )
					$childs .= $args->before_childs;
				
				foreach( $pages as $page )
				{
					$i++;

					if ( !in_array( $page['id'], $args->show_pages ) )
						continue;
					
					$chId       = 'menu-item-' . $i;
					//$chClass  = ( !empty( $args->child_link_class ) ? $args->child_link_class : '' );
					
					$chClass    = ( ( $isPost && !empty( $args->current_class ) && !empty( Theme::GetData( 'postId' ) ) && ( Theme::GetData( 'postId' ) == $page['id'] ) ) ? ' ' . $args->current_class : '' );

					$chClass   .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );
					
					$chName = '';
					
					if ( !empty( $args->before_child_link ) )
						$chName .= $args->before_child_link;

					$chName .= MenuLinkItem( $args, $page['title'], $page['postURL'], true );

					if ( !empty( $args->after_child_link ) )
						$chName .= $args->after_child_link;

					$childs .= MenuSingleItem( $args, $chId, $chClass, $chName, true );
					
					//Increase the pages counter
					$p++;
				}				

				if ( !empty( $args->after_childs ) )
					$childs .= $args->after_childs;
				
				$i++;

				$wId 	 	 = 'menu-item-' . $i;
				
				$cName 		.= sprintf( $args->childs_wrap, $wId, $args->submenu_class, $childs );
	
				if ( !empty( $args->after_link ) )
					$cName .= $args->after_link;

				$items .= MenuSingleItem( $args, $cId, $cClass, $cName );
			}
			
			else
			{
				foreach( $pages as $page )
				{
					if ( !in_array( $page['id'], $args->show_pages ) )
						continue;

					$i++;
					
					$chId       = 'menu-item-' . $i;
					//$chClass  = ( !empty( $args->child_link_class ) ? $args->child_link_class : '' );
					
					$chClass    = ( ( $isPost && !empty( $args->current_class ) && !empty( Theme::GetData( 'postId' ) ) && ( Theme::GetData( 'postId' ) == $page['id'] ) ) ? ' ' . $args->current_class : '' );
					
					$chClass   .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );
					
					$chName = '';
					
					if ( !empty( $args->before_link ) )
						$chName .= $args->before_link;
					
					$chName .= MenuLinkItem( $args, $page['title'], $page['postUrl'], true );
					
					if ( !empty( $args->after_link ) )
						$chName .= $args->after_link;

					$items .= MenuSingleItem( $args, $chId, $chClass, $chName, true );
					
					//Increase the pages counter
					$p++;
				}
			}
		}
	}

	if ( $auto && !empty( $args->custom_links ) )
	{
		if ( $args->show_links_as_childs && $args->show_children )
		{
			$i++;
				
			$cId 	    = 'menu-item-' . $i;
			//$cClass   = ( !empty( $args->main_link_class ) ? $args->main_link_class : '' );
			$cClass     = ( !empty( $args->has_children_class ) ? ' ' . $args->has_children_class : '' );
			$cClass    .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );

			$cName = $childs = '';
			
			if ( !empty( $args->before_link ) )
				$cName .= $args->before_link;
		
			$cName .= MenuLinkItem( $args, $args->links_more_title, '#', false, true );

			if ( !empty( $args->before_childs ) )
				$childs .= $args->before_childs;
				
			foreach( $args->custom_links as $link )
			{
				$i++;

				$chId       = 'menu-item-' . $i;

				$chClass    = '';

				$chClass   .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );

				$chName = '';

				if ( !empty( $args->before_child_link ) )
					$chName .= $args->before_child_link;

				$chName .= MenuLinkItem( $args, $link['title'], $link['url'], true, false, false, $link['target'] );

				if ( !empty( $args->after_child_link ) )
					$chName .= $args->after_child_link;

				$childs .= MenuSingleItem( $args, $chId, $chClass, $chName, true );
					
				//Increase the pages counter
				$cl++;
			}

			if ( !empty( $args->after_childs ) )
				$childs .= $args->after_childs;
				
			$i++;

			$wId 	 	 = 'menu-item-' . $i;
				
			$cName 		.= sprintf( $args->childs_wrap, $wId, $args->submenu_class, $childs );
	
			if ( !empty( $args->after_link ) )
				$cName .= $args->after_link;

			$items .= MenuSingleItem( $args, $cId, $cClass, $cName );
		}
			
		else
		{
			foreach( $args->custom_links as $link )
			{
				$i++;
					
				$chId       = 'menu-item-' . $i;

				$chClass    = '';
					
				$chClass   .= ( !empty( $args->item_class ) ? ' ' . $args->item_class . ' ' . $args->item_class . '-' . $i : '' );
					
				$chName = '';
					
				if ( !empty( $args->before_link ) )
					$chName .= $args->before_link;
					
				$chName .= MenuLinkItem( $args, $link['title'], $link['url'], true, false, false, $link['target'] );

				if ( !empty( $args->after_link ) )
					$chName .= $args->after_link;

				$items .= MenuSingleItem( $args, $chId, $chClass, $chName, true );

				//Increase the custom links counter
				$cl++;
			}
		}
	}

	$nav_menu .= sprintf( $args->items_wrap, $warpId, $warpClass, $items );
	
	unset( $pages, $items, $data );
	
	if ( !empty( $args->after_links ) )
		$nav_menu .= $args->after_links;
	
	if ( !empty( $args->container ) )
		$nav_menu .= '</' . $args->container . '>';
	
	if ( !empty( $args->after ) ) 
		$nav_menu .= $args->after;
	
	$nav_menu .= PHP_EOL;

	if ( $echo )
	{
		echo $nav_menu;
		unset( $nav_menu );
	}

	else
		return $nav_menu;
}

#####################################################
#
# Get Custom Post Types For the Menu function
#
#####################################################
function GetMenuCustomTypes( $siteId, $get_childs = false, $langId = null )
{
	$cacheFile = CacheFileName( 'custom_post_types_menu-', null, $langId, null, null, null, null, $siteId );
	
	$CurrentLang = CurrentLang();
	
	$lang 		 = $CurrentLang['lang']['code'];

	if ( ValidCache( $cacheFile ) )
	{
		$data = readCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		//Get the default language
		$defaultCode = Settings::LangData()['lang']['code'];
	
		$data = array();
		
		//Query: types
		$cus = $db->from( null, "
		SELECT id, sef, title, description, trans_data
		FROM `" . DB_PREFIX . "post_types`
		WHERE (id_site = " . $siteId . ") AND (id_parent = 0)" 
		)->all();

		if( !$cus ) 
			return null;
		
		foreach( $cus as $cu )
		{
			$url = SITE_URL;
			
			if ( MULTILANG && !empty( $lang ) && ( !Settings::IsTrue( 'hide_default_lang_slug' ) || ( Settings::IsTrue( 'hide_default_lang_slug' ) && ( $lang != $defaultCode ) ) )
			)
			{
				$url .= $lang . PS;
			}
			
			$url .= $cu['sef'] . PS;
				
			$data[$cu['id']] = array(
				'title' 		=> StripContent( $cu['title'] ),
				'description' 	=> StripContent( $cu['description'] ),
				'sef'			=> $cu['sef'],
				'url'			=> $url,
				'childs'		=> array()
			);
			
			if ( $lang )
			{
				$trans = Json( $cu['trans_data'] );
				
				if ( !empty( $trans ) && ( $lang != $defaultCode ) && isset( $trans[$lang] ) )
				{
					$data[$cu['id']]['title'] 		 = StripContent( $trans[$lang]['title'] );
					$data[$cu['id']]['description']  = StripContent( $trans[$lang]['description'] );
				}
			}
			
			if ( $get_childs )
			{
				$cuc = $db->from( null, "
				SELECT id, sef, title, description, trans_data
				FROM `" . DB_PREFIX . "post_types`
				WHERE (id_parent = " . $cu['id'] . ")"
				)->all();

				if( $cuc )
				{
					foreach( $cuc as $cc )
					{
						$data[$cu['id']]['childs'][$cc['id']] = array(
							'title' 		=> StripContent( $cc['title'] ),
							'description' 	=> StripContent( $cc['description'] ),
							'sef'			=> $cc['sef'],
							'url'			=> $url . $cc['sef'] . PS
						);
						
						if ( $lang )
						{
							$trans = Json( $cc['trans_data'] );
							
							if ( !empty( $trans ) && ( $lang != $defaultCode ) && isset( $trans[$lang] ) )
							{
								$data[$cu['id']]['childs'][$cc['id']]['title'] 		  = StripContent( $trans[$lang]['title'] );
								$data[$cu['id']]['childs'][$cc['id']]['description']  = StripContent( $trans[$lang]['description'] );
							}
						}
					}
				}
			}
		}

		WriteCacheFile( $data, $cacheFile );
	}
	
	return $data;
}

#####################################################
#
# Single Item for Menu function
#
#####################################################
function MenuSingleItem( $args, $id, $class, $name, $child = false )
{
	if ( $child )
	{
		if ( !empty( $args->single_child_item_wrap ) )
		{
			return sprintf( $args->single_child_item_wrap, $id, $class, $name ) . PHP_EOL;
		}

		else
		{
			return $name . PHP_EOL;
		}
	}
	
	else
	{
		if ( !empty( $args->single_item_wrap ) )
		{
			return sprintf( $args->single_item_wrap, $id, $class, $name ) . PHP_EOL;
		}
		else
		{
			return $name . PHP_EOL;
		}
	}
}

#####################################################
#
# Single Link Item for Menu function
#
#####################################################
function MenuLinkItem( $args, $name, $url, $child = false, $hasChildren = false, $current = false, $target = null )
{
	$class   = ( !$child && !empty( $args->main_link_class ) ? ' ' . $args->main_link_class : '' );
	
	$class  .= ( $child && !empty( $args->child_link_class ) ? ' ' . $args->child_link_class : '' );
	
	$class  .= ( ( !$child && $hasChildren && !empty( $args->dropdown_toggle_class ) ) ? ' ' . $args->dropdown_toggle_class : '' );
	
	$class  .= ( ( $current && !empty( $args->current_item_link_class ) ) ? ' ' . $args->current_item_link_class : '' );

	if ( $child )
	{
		if ( !empty( $args->child_link_wrap ) )
		{
			$n = ( !empty( $class ) ? 'class="' . $class . '" ' : '' ) . 'title="' . htmlspecialchars( $name ) . '" href="' . $url . '"';

			return sprintf( $args->child_link_wrap, $n, $name );
		}

		else
		{
			return '<a ' . ( !empty( $class ) ? 'class="' . $class . '" ' : '' ) . ( !empty( $target ) ? ' target="_' . $target . '"' : '' ) . 'href="' . $url . '" title="' . htmlspecialchars( $name ) . '">' . $name . '</a>';
		}
	}
	
	else
	{
		if ( $args->link_wrap )
		{
			$n  = ( !empty( $class ) ? 'class="' . $class . '" ' : '' ) . 'title="' . htmlspecialchars( $name ) . '" href="' . $url . '"';
			
			$n .= ( ( $hasChildren && !empty( $args->dropdown_toggle_extra ) ) ? ' ' . $args->dropdown_toggle_extra : '' );

			return sprintf( $args->link_wrap, $n, $name );
		}
		
		else
		{
			$n  = '<a ' . ( !empty( $class ) ? 'class="' . $class . '" ' : '' ) . ( !empty( $target ) ? ' target="_' . $target . '"' : '' ) . 'href="' . $url . '" title="' . htmlspecialchars( $name ) . '"';
			
			$n .= ( ( $hasChildren && !empty( $args->dropdown_toggle_extra ) ) ? ' ' . $args->dropdown_toggle_extra : '' );
			
			$n .= '>' . $name . '</a>';
			
			return $n;
		}
	}
}

#####################################################
#
# Get Pages for Auto Menu function
#
#####################################################
function AutoMenuPages()
{
	$CurrentLang = CurrentLang();
	
	$blogId 	 = ( ( MULTIBLOG && Router::GetVariable( 'isBlog' ) ) ? Theme::GetData( 'blogId' ) : 0 );

	$code 		 = $CurrentLang['lang']['code'];
	
	$cacheFile 	 = CacheFileName( 'auto-menu-pages', null, $CurrentLang['lang']['id'], $blogId, null, null, $code );

	if ( ValidCache( $cacheFile ) )
	{
		$pages = readCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		$pages = array();
		
		$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $CurrentLang['lang']['id'] . ") AND (p.id_blog = " . $blogId . ") AND (p.post_type = 'page') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)";
			
		$query = PostsDefaultQuery( $q, null, 'p.added_time DESC', 'p.id_post' );

		//Query: pages
		$tmp = $db->from( null, $query )->all();

		if ( empty( $tmp ) )
		{
			return null;
		}

		foreach( $tmp as $p )
		{
			$pages[] = BuildPostVars( $p );
		}

		WriteCacheFile( $pages, $cacheFile );
	}

	return $pages;
}


#####################################################
#
# Get Categories for Auto Menu function
#
#####################################################
function AutoMenuCats( $siteId = SITE_ID, $cache = true )
{
	$CurrentLang = CurrentLang();
	
	$blogId = 0;
	
	if ( Router::GetVariable( 'isBlog' ) && !empty( $Blog ) )
	{
		$blogId = $Blog['id_blog'];
	}
	
	$cacheFile = CacheFileName( 'auto-menu-categories' . ( MULTIBLOG ? '-multiblog' : '' ), null, $CurrentLang['lang']['id'], $blogId, null, null, null, $siteId );
	
	if ( $cache && ValidCache( $cacheFile ) )
	{
		$categories = readCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		if ( !MULTIBLOG )
		{
			$categories = array();

			//Get the languages
			$langList = Settings::AllLangs();

			foreach( $langList as $ll => $lang )
			{
				$query = "SELECT ca.*, la.code as ls, la.locale as lc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_category = ca.id AND p.id_lang = ca.id_lang AND p.post_status = 'published') as items
				FROM `" . DB_PREFIX . "categories` AS ca
				INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = ca.id_lang
				WHERE (ca.id_parent = 0) AND (ca.id_lang = " . $lang['lang']['id'] . ") AND (ca.id_blog = 0) ORDER BY ca.name ASC";
	
				//Query: cats
				$cats = $db->from( null, $query )->all();

				if ( $cats )
				{
					foreach ( $cats as $cat )
					{
						//We need these strings as null
						$cat['bs'] = null;
						$cat['bn'] = null;
							
						$catUrl = BuildCategoryUrl( $cat, $ll );
							
						$categories[$lang['lang']['code']][$cat['sef']] = array(
									'id' => $cat['id'],
									'items' => $cat['items'],
									'hide_front' => $cat['hide_front'],
									'groups' => ( !empty( $cat['groups_data'] ) ? Json( $cat['groups_data'] ) : null ),
									'name' => stripslashes( $cat['name'] ),
									'description' => stripslashes( $cat['descr'] ),
									'url' => $catUrl,
									'childs' => array()
						);
							
						//Get the subcategories, if any
						$subs = $db->from( null, "
							SELECT ca.*, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_sub_category = ca.id AND p.id_lang = ca.id_lang AND p.post_status = 'published') as items
							FROM `" . DB_PREFIX . "categories` AS ca
							WHERE (ca.id_parent = " . $cat['id'] . ") ORDER BY ca.name ASC" 
						)->all();

						if ( $subs )
						{
							foreach ( $subs as $sub )
							{
								$categories[$lang['lang']['code']][$cat['sef']]['childs'][$sub['sef']] = array(
										'id' => $sub['id'],
										'items' => $sub['items'],
										'hide_front' => $sub['hide_front'],
										'groups' => ( !empty( $sub['groups_data'] ) ? Json( $sub['groups_data'] ) : null ),
										'name' => stripslashes( $sub['name'] ),
										'description' => stripslashes( $sub['descr'] ),
										'url' => $catUrl . $sub['sef'] . PS
								);
							}
						}
					}
				}
			}
		}
		
		//Multiblog?
		else
		{
			//Get the languages
			$langList = Settings::AllLangs();
			
			$categories = array();
		
			//Get the cats, but don't cache 'em
			$noBlogCats = GetCats( $siteId, false );
		
			foreach( $langList as $ll => $lang )
			{
				//Now get the blogs
				$query = "SELECT id_blog, sef, name, groups_data, trans_data FROM `" . DB_PREFIX . "blogs` 
				WHERE (id_lang = " . $lang['lang']['id'] . " OR id_lang = 0) AND (disabled = 0) AND (id_site = " . $siteId . ") ORDER BY name ASC";
	
				//Query: blogs
				$blogs = $db->from( null, $query )->all();
				
				if ( !empty( $blogs ) )
				{
					foreach( $blogs as $blog )
					{
						$query = "SELECT ca.*, la.code as ls, la.locale as lc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_category = ca.id AND p.id_lang = ca.id_lang AND p.post_status = 'published') as items FROM `" . DB_PREFIX . "categories` AS ca
						INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = ca.id_lang
						WHERE (ca.id_parent = 0) AND (ca.id_lang = " . $lang['lang']['id'] . ") AND (ca.id_blog = " . $blog['id_blog'] . ") ORDER BY ca.name ASC";
		
						//Query: cats
						$cats = $db->from( null, $query )->all();

						$categories[$ll][$blog['sef']] = array(
									'blogName' => $blog['name'],
									'blogSef' => $blog['sef'],
									'blogId' => $blog['id_blog'],
									'groups' => ( !empty( $blog['groups_data'] ) ? Json( $blog['groups_data'] ) : null ),
									'trans' => ( !empty( $blog['trans_data'] ) ? Json( $blog['trans_data'] ) : null ),
									'cats' => array()
						
						);

						if ( $cats )
						{
							foreach ( $cats as $cat )
							{
								$cat['bs'] = $blog['sef'];
								$cat['bn'] = $blog['name'];
								
								$catUrl = BuildCategoryUrl( $cat, $ll );

								$categories[$ll][$blog['sef']]['cats'][$cat['sef']] = array(
											'id' => $cat['id'],
											'hide_front' => $cat['hide_front'],
											'name' => stripslashes( $cat['name'] ),
											'items' => $cat['items'],
											'description' => stripslashes( $cat['descr'] ),
											'url' => $catUrl,
											'childs' => array()
								);
									
								//Get the subcategories, if any	
								//Query: subcats
								$subs = $db->from( null, "
								SELECT ca.*, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_sub_category = ca.id AND p.id_lang = ca.id_lang AND p.post_status = 'published') as items
								FROM `" . DB_PREFIX . "categories` AS ca
								WHERE (ca.id_parent = " . $cat['id'] . ") ORDER BY ca.name ASC" 
								)->all();

								if ( $subs )
								{
									foreach ( $subs as $sub )
									{
										$categories[$ll][$blog['sef']]['cats'][$cat['sef']]['childs'][$sub['sef']] = array(
												'id' => $sub['id'],
												'hide_front' => $sub['hide_front'],
												'items' => $sub['items'],
												'name' => stripslashes( $sub['name'] ),
												'description' => stripslashes( $sub['descr'] ),
												'url' => $catUrl . $sub['sef'] . PS
										);
									}
								}
							}
						}
					}
				}
				
				//We also need the orphan categories
				if ( !empty( $noBlogCats ) && isset( $noBlogCats[$ll] ) )
				{
					$categories[$ll]['orphanCats'] = array(
												'blogName' => __( 'categories' ),
												'blogSef' => null,
												'blogId' => 0,
												'groups' => null,
												'cats' => array()
					);
					
					foreach( $noBlogCats[$ll] as $oSef => $oRow )
					{
						if ( empty( $oSef ) )
							continue;
						
						$categories[$ll]['orphanCats']['cats'][$oSef] = array(
											'id' => $oRow['id'],
											'name' => stripslashes( $oRow['name'] ),
											'description' => stripslashes( $oRow['description'] ),
											'url' => $oRow['url'],
											'items' => $oRow['items'],
											'childs' => array()
						);
						
						if ( !empty( $oRow['childs'] ) )
						{
							foreach( $oRow['childs'] as $ocSef => $ocRow )
							{
								$categories[$ll]['orphanCats']['cats'][$oSef]['childs'][$ocSef] = array(
											'id' => $ocRow['id'],
											'name' => stripslashes( $ocRow['name'] ),
											'items' => $ocRow['items'],
											'description' => stripslashes( $ocRow['description'] ),
											'url' => $ocRow['url']
								);
							}
						}
					}
				}
			}

		}
		
		if ( $cache && !empty( $categories ) )
		{
			WriteCacheFile( $categories, $cacheFile );
		}
	}
	
	return $categories;
}

/*
#####################################################
#
# Social Menu function
#
#####################################################
function SocialMenu( $param = array(), $echo = true )
{
	$CurrentLang = CurrentLang();
	
	$code = $CurrentLang['lang']['code'];
	
	$socialData = $CurrentLang['data']['social'];
	
	if ( empty( $socialData ) )
		return ( $echo ? '' : null );
	
	$args = array(
			'container'      		 => ( ( !empty( $param ) && ( isset( $param['container'] ) || is_null( $param['container'] ) ) ) ? $param['container'] : 'div' ),
			
			'container_class'		 => ( ( !empty( $param ) && isset( $param['container_class'] ) ) ? $param['container_class'] : '' ),
			'container_id'    		 => ( ( !empty( $param ) && isset( $param['container_id'] ) ) ? $param['container_id'] : '' ),
			'menu_class'      		 => ( ( !empty( $param ) && isset( $param['menu_class'] ) ) ? $param['menu_class'] : 'social-menu' ),
			'menu_id'        		 => ( ( !empty( $param ) && isset( $param['menu_id'] ) ) ? $param['menu_id'] : '' ),
			'before'          		 => ( ( !empty( $param ) && isset( $param['before'] ) ) ? $param['before'] : '' ),
			'after'          		 => ( ( !empty( $param ) && isset( $param['after'] ) ) ? $param['after'] : '' ),
			
			'add_before_links'     	 => ( ( !empty( $param ) && isset( $param['add_before_links'] ) ) ? $param['add_before_links'] : null ),
			
			'add_before_name'     	 => ( ( !empty( $param ) && isset( $param['add_before_name'] ) ) ? $param['add_before_name'] : null ),
			
			'add_after_links'     	 => ( ( !empty( $param ) && isset( $param['add_after_links'] ) ) ? $param['add_after_links'] : null ),
			
			'add_after_name'     	 => ( ( !empty( $param ) && ( isset( $param['add_after_name'] ) ) ) ? $param['add_after_name'] : null ),
			
			'items_wrap'      		 => ( ( !empty( $param ) && isset( $param['items_wrap'] ) ) ? $param['items_wrap'] : '<ul id="%s" class="%s">%s</ul>' ),
			
			'links_wrap'     		 => ( ( !empty( $param ) && isset( $param['links_wrap'] ) ) ? $param['links_wrap'] : '<li id="%s" class="%s">%s</li>' ),
			
			'items_class'   	  	 => ( ( !empty( $param ) && isset( $param['items_class'] ) ) ? $param['items_class'] : '' )
	);
	
	$nav_menu = '';
	$items = '';
	
	$i = 0;
	
	if ( !empty( $args['container'] ) ) 
		$nav_menu .= '<' . $args['container'] . ( !empty( $args['container_id'] ) ? ' id="' . $args['container_id'] . '"' : '' ) . ' class="' . $args['container_class'] . '">';
	
	if ( !empty( $args['before'] ) )
		$nav_menu .= $args['before'];
	
	$itemId = ( !empty( $args['menu_id'] ) ? $args['menu_id'] : '' );
	$itemClass = $args['menu_class'];
	
	foreach ( $socialData as $id => $social )
	{
		if ( empty( $social ) )
			continue;
		
		$i++;
		
		$chId      = 'menu-item-' . $i;
		$chClass   = ( !empty( $args['items_class'] ) ? $args['items_class'] . ' menu-item-' . $i : '' );
	
		$chName = '';
		
		if ( !empty( $args['add_before_links'] ) )
			$chName .= $args['add_before_links'];
	
		$chName .= '<a target="_blank" href="' . $social . '">';
		
		if ( !empty( $args['add_before_name'] ) )
			$chName .= $args['add_before_name'];

		$chName .= ucfirst( $id );

		if ( !empty( $args['add_after_name'] ) )
			$chName .= $args['add_after_name'];

		$chName .= '</a>';
		
		if ( !empty( $args['add_after_links'] ) )
			$chName .= $args['add_after_links'];
	
		$items .= sprintf( $args['links_wrap'], $chId, $chClass, $chName ) . PHP_EOL;
	}
	
	$nav_menu .= sprintf( $args['items_wrap'], $itemId, $itemClass, $items );
	
	if ( !empty( $args['after'] ) )
		$nav_menu .= $args['after'];
	
	if ( !empty( $args['container'] ) ) 
		$nav_menu .= '</' . $args['container'] . '>';
	
	unset( $socialData );

	if ( $echo )
	{
		echo $nav_menu;
		unset( $nav_menu );
	}

	else
		return $nav_menu;
}
*/
