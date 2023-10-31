<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Advanced Settings Form
#
#####################################################

$L = $this->lang;
$settings = $this->adminSettings::Get();

include ( ARRAYS_ROOT . 'generic-arrays.php');

$site = $this->adminSettings::Site();

$share = ( ( isset( $site['share_data'] ) && !empty( $site['share_data'] ) ) ? Json( $site['share_data'] ) : null );

$rssSettings = Json( $settings['rss_settings'] );

$cdnSettings = Json( $settings['cdn_data'] );

$apiKeys = Json( $settings['api_keys'] );

$imageKitKey = ( isset( $apiKeys['imagekit'] ) ? $apiKeys['imagekit'] : array() );

$youtubeKey = ( isset( $apiKeys['youtube'] ) ? $apiKeys['youtube'] : null );

$mapsKey = ( isset( $apiKeys['gmaps'] ) ? $apiKeys['gmaps'] : null );

$disqusKey = ( isset( $apiKeys['disqus'] ) ? $apiKeys['disqus'] : null );

$keys = ( isset( $apiKeys[$this->LangKey()]['blog-' . $this->blogID] ) ? $apiKeys[$this->LangKey()]['blog-' . $this->blogID] : array() );

if ( empty( $rssSettings ) )
{
	$rssSettings = array();
	
	$rssSettings['lang-' . $this->GetLang()]['blog-' . $this->blogID]['data'] = array();
	
}
else
{
	if ( isset( $rssSettings['lang-' . $this->GetLang()]['blog-' . $this->blogID] ) )
		$rssSettings = $rssSettings['lang-' . $this->GetLang()]['blog-' . $this->blogID]['data'];
}

$rssButtons = array(
	'author-link' => array( 'title' => $L['author-link'], 'var' => '{{author-link}} ' ),
	'author-name' => array( 'title' => $L['author-name'], 'var' => '{{author-name}} ' ),
	'post-link' => array( 'title' => $L['post-link'], 'var' => '{{post-link}} ' ),
	'post-title' => array( 'title' => $L['post-title'], 'var' => '{{post-title}} ' ),
	'post-date' => array( 'title' => $L['post-date'], 'var' => '{{post-date}} ' ),
	'site-link' => array( 'title' => $L['site-link'], 'var' => '{{site-link}} ' ),
	'site-title' => array( 'title' => $L['site-title'], 'var' => '{{site-title}} ' ),
	'site-slogan' => array( 'title' => $L['site-slogan'], 'var' => '{{site-slogan}} ' ),
	'site-description' => array( 'title' => $L['site-description'], 'var' => '{{site-description}} ' ),
);

$regData = $wwwData = $favData = $reviewsData = $notifyData = $groupsAllowed = array();

//Group Settings
$groups = AdminGroups( $this->GetSite(), false );

foreach( $groups as $key => $row )
{
	//Admins can view everything, so don't bother with this group
	if ( $row['id_group'] == 1 )
		continue;
	
	$groupsAllowed[$row['id_group']] = array( 'name' => $row['id_group'], 'title'=> $row['group_name'], 'disabled' => false, 'data' => array() );
}

$currentType = $settings['parent_type'];

$blogTypes = $hosts = array();

foreach ( $siteHosts as $key => $row )
{
	$hosts[$key] = array( 'name' => $key, 'title'=> $row['title'] , 'disabled' => false, 'data' => array() );
}

foreach( $blogTypesArray as $typeId => $blogType ) 
{
	$checked = false;
	
	if ( empty( $currentType ) && ( $typeId == 'normal' ) )
		$checked = true;
	
	elseif ( !empty( $currentType ) && ( $currentType == $typeId ) )
		$checked = true;
	
	$blogTypes[$typeId] = array( 'name' => $typeId, 'value' => $typeId, 'title'=> $blogType['title'], 'disabled' => false, 'checked' => $checked );
}

//Fill the arrays
$regData['immediate'] = array( 'name' => 'immediate', 'title'=> $L['immediate-registration'], 'disabled' => false, 'data' => array() );
$regData['email'] = array( 'name' => 'email', 'title'=> $L['email-activation'], 'disabled' => false, 'data' => array() );
$regData['admin'] = array( 'name' => 'admin', 'title'=> $L['admin-approval'], 'disabled' => false, 'data' => array() );

$wwwData['false'] = array( 'name' => 'false', 'title'=> $L['disabled'], 'disabled' => false, 'data' => array() );
$wwwData['to-non-www'] = array( 'name' => 'to-non-www', 'title'=> $L['to-non-www'], 'disabled' => false, 'data' => array() );
$wwwData['to-www'] = array( 'name' => 'to-www', 'title'=> $L['to-www'], 'disabled' => false, 'data' => array() );

$favData['everywhere'] = $reviewsData['everywhere'] = $notifyData['everywhere'] = array( 'name' => 'everywhere', 'title'=> $L['everywhere'], 'data' => array() );
$favData['everywhere']['data'][] = array( 'name' => 'everywhere', 'title'=> sprintf( $L['display-s-in-every-post'], $L['add-to-favorites-button'] ), 'disabled' => false, 'data' => array() );
$reviewsData['everywhere']['data'][] = array( 'name' => 'everywhere', 'title'=> sprintf( $L['display-s-in-every-post'], $L['reviews'] ), 'disabled' => false, 'data' => array() );
$notifyData['everywhere']['data'][] = array( 'name' => 'everywhere', 'title'=> sprintf( $L['display-s-in-every-post'], $L['post-update-notifications'] ), 'disabled' => false, 'data' => array() );

if ( $this->MultiBlog() )
{
	$blogs = $this->adminSettings::BlogsFullArray();
	
	if ( !empty( $blogs ) )
	{
		$favData['blogs'] = array( 'name' => $L['blogs'], 'data' => array() );
		$reviewsData['blogs'] = array( 'name' => $L['blogs'], 'data' => array() );
		$notifyData['blogs'] = array( 'name' => $L['blogs'], 'data' => array() );
		
		foreach ( $blogs as $k => $blog )
		{
			$favData['blogs']['data'][] = array( 'name' => 'blog::' . $blog['id_blog'], 'title'=> sprintf( $L['show-in-s-posts'], $blog['name'] ), 'disabled' => false, 'data' => array() );
			$reviewsData['blogs']['data'][] = array( 'name' => 'blog::' . $blog['id_blog'], 'title'=> sprintf( $L['show-in-s-posts'], $blog['name'] ), 'disabled' => false, 'data' => array() );
			$notifyData['blogs']['data'][] = array( 'name' => 'blog::' . $blog['id_blog'], 'title'=> sprintf( $L['show-in-s-posts'], $blog['name'] ), 'disabled' => false, 'data' => array() );
		}
	}
}

if ( $this->MultiLang() )
{
	$langs = $this->adminSettings::AllLangs();

	if ( !empty( $langs ) )
	{
		$favData['langs'] = array( 'name' => $L['langs'], 'data' => array() );
		$reviewsData['langs'] = array( 'name' => $L['langs'], 'data' => array() );
		$notifyData['langs'] = array( 'name' => $L['langs'], 'data' => array() );
		
		foreach ( $langs as $k => $lang )
		{
			$favData['langs']['data'][] = array( 'name' => 'lang::' . $lang['lang']['id'], 'title'=> sprintf( $L['show-in-s-posts'], $lang['lang']['title'] ), 'disabled' => false, 'data' => array() );
			$reviewsData['langs']['data'][] = array( 'name' => 'lang::' . $lang['lang']['id'], 'title'=> sprintf( $L['show-in-s-posts'], $lang['lang']['title'] ), 'disabled' => false, 'data' => array() );
			$notifyData['langs']['data'][] = array( 'name' => 'lang::' . $lang['lang']['id'], 'title'=> sprintf( $L['show-in-s-posts'], $lang['lang']['title'] ), 'disabled' => false, 'data' => array() );
		}
	}
}

$form = array
(
	'url-filters' => array
	(
		'title' => $L['url-filters'],
		'data' => array(
			'site-rss' => array(
				'title' => null, 'data' => array
				(
					'posts-filters'=>array('label'=>$L['posts'], 'type'=>'text', 'name' => 'settings[posts_filter]', 'value' => $settings['posts_filter'], 'tip'=> $this->adminSettings::Site()['url'] . ltrim( $settings['posts_filter'], '/') ),
					'categories-filters'=>array('label'=>$L['categories'], 'type'=>'text', 'name' => 'settings[categories_filter]', 'value' => $settings['categories_filter'], 'tip'=> $this->adminSettings::Site()['url'] . ltrim( $settings['categories_filter'], '/') ),
					'tags-filters'=>array('label'=>$L['tags'], 'type'=>'text', 'name' => 'settings[tags_filter]', 'value' => $settings['tags_filter'], 'tip'=> $this->adminSettings::Site()['url'] . ltrim( $settings['tags_filter'], '/') )
				)
			)
		)
	),
	
	'images-dirs' => array
	(
		'title' => $L['image-settings'],
		'data' => array(
			
			'images-dirs' => array(
				'title' => null, 'data' => array
				(
					'sync-uploads'=>array('label'=>$L['synchronise-uploads'], 'type'=>'checkbox', 'name' => 'settings[sync_uploads]', 'value' => ( isset( $share['sync_uploads'] ) ? $share['sync_uploads'] : null ), 'tip'=>$L['synchronise-uploads-tip'], 'hide' => ( !$this->isChildSite ? true : false ) ),
					'images-url'=>array('label'=>$L['images-url'], 'type'=>'text', 'name' => 'settings[images_html]', 'value' => $settings['images_html'], 'tip'=>$L['images-url-tip'], 'hide' => ( $this->isChildSite ? true : false ) ),
					'images-root'=>array('label'=>$L['images-root'], 'type'=>'text', 'name' => 'settings[images_root]', 'value' => $settings['images_root'], 'tip'=>$L['images-root-tip'], 'hide' => ( $this->isChildSite ? true : false ) )
				)
			)
		)
	),
	
	'rss-settings' => array
	(
		'title' => $L['rss-settings'],
		'data' => array(
			'site-rss' => array(
				'title' => $L['rss-feed'], 'data' => array
				(
					'enable-rss'=>array('label'=>$L['enable-rss'], 'name' => 'settings[enable_rss]', 'type'=>'checkbox', 'value'=>$settings['enable_rss'], 'tip'=>$L['rss-tip'] ),
					
					'full-post'=>array('label'=>$L['rss-full-post'], 'name' => 'rss[show_full_post]', 'type'=>'checkbox', 'value'=>( ( isset( $rssSettings['show_full_post'] ) && $rssSettings['show_full_post'] ) ? true : false ), 'tip'=>$L['rss-full-post-tip'] ),
					
					'num-posts'=>array('label'=>$L['rss-items'], 'name' => 'rss[post_limit]', 'type'=>'num', 'value'=>( isset( $rssSettings['post_limit'] ) ? $rssSettings['post_limit'] : 5 ), 'tip'=>$L['number-of-rss-items-to-show'], 'min'=>'1', 'max'=>'20'),
				)
			),
			
			'rss-settings' => array( 
				'title' => $L['rss-settings'], 'tip' =>$L['rss-settings-tip'], 'data' => array
				(
					'rss-header'=>array('label'=>$L['rss-header'], 'type'=>'textarea', 'name' => 'rss[header_code]', 'value' => ( isset( $rssSettings['header_code'] ) ? $rssSettings['header_code'] : null ), 'tip'=>null, 'buttons' =>  $rssButtons ),
					
					'rss-footer'=>array('label'=>$L['rss-footer'], 'type'=>'textarea', 'name' => 'rss[footer_code]', 'value' => ( isset( $rssSettings['footer_code'] ) ? $rssSettings['footer_code'] : 'This post {{post-link}} was written for {{site-link}}' ), 'tip'=>null, 'buttons' =>  $rssButtons )
				)
			)
		)
	),
	
	'api-keys' => array
	(
		'title' => $L['api-keys'],
		'data' => array(
			'generic-keys' => array(
				'title' => null, 'data' => array
				(
					'youtube-api'=>array('label'=>$L['youtube-api'], 'type'=>'text', 'name' => 'settings[youtube_api_key]', 'value' => $youtubeKey, 'tip'=>$L['youtube-api-tip']),
					
					//'maps-api'=>array('label'=>$L['google-maps-api'], 'type'=>'text', 'name' => 'settings[maps_api_key]', 'value' => $mapsKey, 'tip'=>$L['google-maps-api-tip']),
					
					'hr'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					//disqus keys
					'disqus-public-key'=>array('label'=>$L['disqus-public-key'], 'type'=>'text', 'name' => 'settings[disqus_public_key]', 'placeholder' => 'Public Key', 'value' => ( !empty( $disqusKey['public_key'] ) ? $disqusKey['public_key'] : '' ), 'tip'=>null, 'hide' => ( empty( $this->currentLang['settings']['disqus_shortname'] ) ? true : false ) ),
					
					'disqus-secret-key'=>array('label'=>$L['disqus-secret-key'], 'type'=>'text', 'name' => 'settings[disqus_secret_key]', 'placeholder' => 'Secret Key', 'value' => ( !empty( $disqusKey['secret_key'] ) ? $disqusKey['secret_key'] : '' ), 'tip'=>$L['disqus-api-tip'], 'hide' => ( empty( $this->currentLang['settings']['disqus_shortname'] ) ? true : false ) ),
					
					'hr1'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false, 'hide' => ( empty( $this->currentLang['settings']['disqus_shortname'] ) ? true : false ) ),
				)
			),
			
			'image-kit' => array(
				'title' => $L['imagekit-settings'], 'tip' => null, 'data' => array
				(
					'enable-imagekit'=>array('label'=>$L['enable-imagekit-cdn'], 'name' => 'cdn[enable_imagekit]', 'type'=>'checkbox', 'value'=>( isset( $cdnSettings['enable_imagekit'] ) ? $cdnSettings['enable_imagekit'] : null ), 'tip'=>$L['enable-imagekit-cdn-tip'] ),
					'imagekit-id'=>array('label'=>$L['imagekit-id'], 'type'=>'text', 'name' => 'imagekit[id]', 'value' => ( isset( $imageKitKey['id'] ) ? $imageKitKey['id'] : null ), 'tip'=> null, 'hide' => false ),
					'imagekit-url'=>array('label'=>$L['url-endpoint'], 'type'=>'text', 'name' => 'imagekit[url]', 'value' => ( isset( $imageKitKey['url'] ) ? $imageKitKey['url'] : null ), 'tip'=> null, 'hide' => false ),
					'public-key'=>array('label'=>$L['public-key'], 'type'=>'text', 'name' => 'imagekit[public_key]', 'value' => ( isset( $imageKitKey['public_key'] ) ? $imageKitKey['public_key'] : null ), 'tip'=> null, 'hide' => false ),
					'private-key'=>array('label'=>$L['private-key'], 'type'=>'text', 'name' => 'imagekit[private_key]', 'value' => ( isset( $imageKitKey['private_key'] ) ? $imageKitKey['private_key'] : null ), 'tip'=> null, 'hide' => false )
				)
			),
		)
	),
	
	'misc' => array
	(
		'title' => $L['miscellaneous'],
		'data' => array(
			
			'user-registration' => array(
				'title' => $L['user-registration'], 'data' => array
				(
					'enable-registrations'=>array('label'=>$L['enable-registration'], 'name' => 'settings[enable_registrations]', 'type'=>'checkbox', 'value'=>$settings['enable_registrations'], 'tip'=>$L['enable-registration-tip'] ),
					
					'registration-method'=>array('label'=>$L['method-of-registration-employed-for-new-members'], 'type'=>'select', 'name' => 'settings[registration_method]', 'value'=>$settings['registration_method'], 'tip'=>$L['method-of-registration-employed-for-new-members-tip'], 'firstNull' => false, 'data' => $regData ),
					
					'send-welcome-email'=>array('label'=>$L['send-welcome-email-to-new-members'], 'name' => 'settings[send_welcome_email_new_reg]', 'type'=>'checkbox', 'value'=>$settings['send_welcome_email_new_reg'], 'tip'=>$L['send-welcome-email-to-new-members-tip'] ),
					
					'accept-the-registration-agreement'=>array('label'=>$L['require-new-members-to-accept-the-registration-agreement'], 'name' => 'settings[require_accept_reg_agreement]', 'type'=>'checkbox', 'value'=>$settings['require_accept_reg_agreement'], 'tip'=>sprintf( $L['require-new-members-to-accept-the-agreement-tip'], $this->GetUrl( 'privacy' ) ) ),
					
					'accept-the-privacy-policy'=>array('label'=>$L['require-new-members-to-accept-the-privacy-policy'], 'name' => 'settings[require_accept_privacy_policy]', 'type'=>'checkbox', 'value'=>$settings['require_accept_privacy_policy'], 'tip'=> sprintf( $L['require-new-members-to-accept-the-agreement-tip'], $this->GetUrl( 'privacy' ) ) ),
					
					'hr'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false )
				)
			),
			
			
			
			'reviews' => array(
				'title' => $L['reviews'], 'data' => array
				(
					'enable-reviews'=>array('label'=>$L['enable-reviews'], 'type'=>'checkbox', 'name' => 'settings[enable_reviews]', 'value' => $settings['enable_reviews'], 'tip'=>$L['enable-reviews-tip'], 'disabled' => false ),
					
					'display-reviews'=>array('label'=> sprintf( $L['enable-s-in'], $L['reviews'] ), 'type'=>'select-group', 'name' => 'settings[reviews_allowed_in]', 'value'=>$settings['reviews_allowed_in'], 'tip'=>$L['allowing-extra-data-tip'], 'firstNull' => false, 'data' => $reviewsData ),
					
					'membergroups-allowed'=>array('label'=>$L['membergroups-allowed'], 'name' => 'settings[allow_reviews_group][]', 'type'=>'select', 'value'=>Json( $settings['allow_reviews_group'] ), 'tip'=>null, 'firstNull' => false, 'data' => $groupsAllowed, 'id' => 'slcReviews', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
						'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
					
					'hr'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false )
				)
			),
			
			'favorite-posts' => array(
				'title' => $L['favorite-posts'], 'data' => array
				(
					'allow-favorite-posts'=>array('label'=>$L['allow-users-to-add-favorite-posts'], 'type'=>'checkbox', 'name' => 'settings[allow_favorite_posts]', 'value' => $settings['allow_favorite_posts'], 'tip'=>$L['allow-users-to-add-favorite-posts-tip'], 'disabled' => false ),
					
					'display-favorite'=>array('label'=>sprintf( $L['enable-s-in'], $L['favorite-posts'] ), 'type'=>'select-group', 'name' => 'settings[allow_favorite_posts_in]', 'value'=>$settings['allow_favorite_posts_in'], 'tip'=>$L['allowing-extra-data-tip'], 'firstNull' => false, 'data' => $favData ),
					
					'membergroups-allowed'=>array('label'=>$L['membergroups-allowed'], 'name' => 'settings[allow_favorite_posts_group][]', 'type'=>'select', 'value'=>Json( $settings['allow_favorite_posts_group'] ), 'tip'=>null, 'firstNull' => false, 'data' => $groupsAllowed, 'id' => 'slcFavorite', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
						'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
						
					'hr'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false )
				)
			),
			
			'post-notifications' => array(
				'title' => $L['post-update-notifications'], 'data' => array
				(
					'post-update-notifications'=>array('label'=>$L['enable-post-update-notifications'], 'type'=>'checkbox', 'name' => 'settings[allow_post_notifications]', 'value' => $settings['allow_post_notifications'], 'tip'=>$L['enable-post-update-notifications-tip'], 'disabled' => false ),
					
					'show-subs'=>array('label'=>$L['show-subscribers-count'], 'type'=>'checkbox', 'name' => 'settings[show_subscribers_num]', 'value' => $settings['show_subscribers_num'], 'tip'=>$L['show-subscribers-count-tip'], 'disabled' => false ),
					
					'display-update-notifications'=>array('label'=>sprintf( $L['enable-s-in'], $L['post-update-notifications'] ), 'type'=>'select-group', 'name' => 'settings[allow_post_notifications_in]', 'value'=>$settings['allow_post_notifications_in'], 'tip'=>$L['allowing-extra-data-tip'], 'firstNull' => false, 'data' => $notifyData ),
					
					'membergroups-allowed'=>array('label'=>$L['membergroups-allowed'], 'name' => 'settings[allow_notifications_group][]', 'type'=>'select', 'value'=>Json( $settings['allow_notifications_group'] ), 'tip'=>null, 'firstNull' => false, 'data' => $groupsAllowed, 'id' => 'slcNotifications', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
						'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
				
					'hr'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false )
				)
			)
		)
	),
	
	'generic' => array
	(
		'title' => $L['site-settings'],
		'data' => array(
			'site-settings' => array(
				'title' => null, 'data' => array
				(
					'auto-menu'=>array('label'=>$L['auto-menu'], 'name' => 'settings[enable_auto_menu]', 'type'=>'checkbox', 'value'=>$settings['enable_auto_menu'], 'tip'=>$L['auto-menu-tip'] ),
					
					'disable-login'=>array('label'=>$L['disable-user-login'], 'name' => 'settings[disable_user_login]', 'type'=>'checkbox', 'value'=>$settings['disable_user_login'], 'tip'=>$L['disable-user-login-tip'], 'hide' => ( !$this->isChildSite ? true : false ) ),
					
					'parent-site-shows-everything'=>array('label'=>( !$this->isChildSite ? $L['parent-site-shows-everything'] : $L['main-site-shows-everything'] ), 'name' => 'settings[parent_site_shows_everything]', 'type'=>'checkbox', 'value'=>$settings['parent_site_shows_everything'], 'tip'=>( !$this->isChildSite ? $L['parent-site-shows-everything-tip'] : $L['main-site-shows-everything-tip'] ), 'hide' => false ),//( $this->isChildSite ? true : false ) ),
					
					'load-jquery-cdn'=>array('label'=>$L['load-jquery-from-cdn'], 'name' => 'settings[load_jquery_cdn]', 'type'=>'checkbox', 'value'=>$settings['load_jquery_cdn'], 'tip'=>$L['load-jquery-from-cdn-tip'] ),
					
					'enable-video-player'=>array('label'=>$L['enable-html5-video-player'], 'type'=>'checkbox', 'name' => 'settings[enable_html5_video_player]', 'value' => $settings['enable_html5_video_player'], 'tip'=>$L['enable-html5-video-player-tip'], 'disabled' => false ),
					
					'enable-new-content-time-limit'=>array('label'=>$L['enable-new-content-time-limit'], 'name' => 'settings[enable_new_content_time_limit]', 'type'=>'checkbox', 'value'=>$settings['enable_new_content_time_limit'], 'tip'=>$L['enable-new-content-time-limit-tip'] ),
					
					'enable-stats'=>array('label'=>$L['enable-stats'], 'name' => 'settings[enable_stats]', 'type'=>'checkbox', 'value'=>$settings['enable_stats'], 'tip'=>$L['enable-stats-tip'] ),
					
					'enable-debug-mode'=>array('label'=>$L['enable-debug-mode'], 'name' => 'settings[enable_debug_mode]', 'type'=>'checkbox', 'value'=>$settings['enable_debug_mode'], 'tip'=>$L['enable-debug-mode-tip'] ),
					
					'disable-author-archive-pages'=>array('label'=>$L['disable-author-archive-pages'], 'name' => 'settings[disable_author_archives]', 'type'=>'checkbox', 'value'=>$settings['disable_author_archives'], 'tip'=>$L['disable-author-archive-pages-tip'] ),
					
					'display-pagination-home'=>array('label'=>$L['display-pagination-home'], 'type'=>'checkbox', 'name' => 'settings[display_pagination_home]', 'value' => $settings['display_pagination_home'], 'tip'=>$L['display-pagination-home-tip']),
				
					'search-engine-visibility'=>array('label'=>$L['search-engine-visibility'], 'type'=>'checkbox', 'name' => 'settings[search_engine_disallow]', 'value' => $settings['search_engine_disallow'], 'tip'=>$L['search-engine-visibility-tip']),
					
					'force-https'=>array('label'=>$L['force-https'], 'type'=>'checkbox', 'name' => 'settings[force_https]', 'value' => $settings['force_https'], 'tip'=>$L['force-https-tip']),
					
					'redirect-www'=>array('label'=>$L['redirect-www'], 'type'=>'select', 'name' => 'settings[redirect_www]', 'value'=>$settings['redirect_www'], 'tip'=>$L['redirect-www-tip'], 'firstNull' => false, 'data' => $wwwData ),
					
					'full-search-posts'=>array( 'label'=>$L['search-content-child-sites'], 'type'=>'checkbox', 'name' => 'settings[allow_full_search]', 'value'=>$settings['allow_full_search'], 'tip'=>$L['search-content-child-sites-tip'], 'hide' => ( $this->isChildSite ? true : false ) ),
					
					'show-admin-bar'=>array('label'=>$L['show-admin-bar'], 'type'=>'checkbox', 'name' => 'settings[show_admin_bar]', 'value' => $settings['show_admin_bar'], 'tip'=>$L['show-admin-bar-tip']),
					
					'sender-email-address'=>array('label'=>$L['sender-email'], 'type'=>'text', 'name' => 'settings[website_email]', 'value' => $settings['website_email'], 'tip'=>$L['emails-will-be-sent-from-this-address']),
					
					'contact-email-address'=>array('label'=>$L['contact-email-address'], 'type'=>'text', 'name' => 'settings[contact_email]', 'value' => $settings['contact_email'], 'tip'=>$L['contact-email-address-tip']),
					
					'allowed-extensions'=>array('label'=>$L['allowed-extensions'], 'type'=>'text', 'name' => 'settings[allowed_extensions]', 'value' => $settings['allowed_extensions'], 'tip'=>$L['allowed-extensions-tip']),
					
					'parent-type'=>array('label'=>$L['select-parent-type'], 'type'=>'radio', 'name' => 'parent_site_type', 'value'=>$currentType, 'tip'=>$L['select-parent-type-tip'], 'data' => $blogTypes ),
					
					'hosts'=>array('label'=>$L['site-is'], 'type'=>'select', 'name' => 'settings[site_hosted]', 'value'=>$this->siteHost, 'tip'=>$L['site-is-hosted-tip'], 'firstNull' => false, 'data' => $hosts ),
				)
			)
		)
	)
	
	/*
	'user-registration' => array
	(
		'title' => $L['user-registration'],
		'data' => array(
			'site-rss' => array(
				'title' => null, 'data' => array
				(
					'enable-registrations'=>array('label'=>$L['enable-registration'], 'name' => 'settings[enable_registrations]', 'type'=>'checkbox', 'value'=>$settings['enable_registrations'], 'tip'=>$L['enable-registration-tip'] ),
					
					'registration-method'=>array('label'=>$L['method-of-registration-employed-for-new-members'], 'type'=>'select', 'name' => 'settings[registration_method]', 'value'=>$settings['registration_method'], 'tip'=>$L['method-of-registration-employed-for-new-members-tip'], 'firstNull' => false, 'data' => $regData ),
					
					'send-welcome-email'=>array('label'=>$L['send-welcome-email-to-new-members'], 'name' => 'settings[send_welcome_email_new_reg]', 'type'=>'checkbox', 'value'=>$settings['send_welcome_email_new_reg'], 'tip'=>$L['send-welcome-email-to-new-members-tip'] ),
					
					'accept-the-registration-agreement'=>array('label'=>$L['require-new-members-to-accept-the-registration-agreement'], 'name' => 'settings[require_accept_reg_agreement]', 'type'=>'checkbox', 'value'=>$settings['require_accept_reg_agreement'], 'tip'=>sprintf( $L['require-new-members-to-accept-the-agreement-tip'], $this->GetUrl( 'privacy' ) ) ),
					
					'accept-the-privacy-policy'=>array('label'=>$L['require-new-members-to-accept-the-privacy-policy'], 'name' => 'settings[require_accept_privacy_policy]', 'type'=>'checkbox', 'value'=>$settings['require_accept_privacy_policy'], 'tip'=> sprintf( $L['require-new-members-to-accept-the-agreement-tip'], $this->GetUrl( 'privacy' ) ) )
				)
			)
		)
	),
	
	'reviews' => array
	(
		'title' => $L['reviews'],
		'data' => array(
			'site-rss' => array(
				'title' => null, 'data' => array
				(
					'enable-reviews'=>array('label'=>$L['enable-reviews'], 'type'=>'checkbox', 'name' => 'settings[enable_reviews]', 'value' => $settings['enable_reviews'], 'tip'=>$L['enable-reviews-tip'], 'disabled' => false ),
					
					'display-reviews'=>array('label'=> sprintf( $L['enable-s-in'], $L['reviews'] ), 'type'=>'select-group', 'name' => 'settings[reviews_allowed_in]', 'value'=>$settings['reviews_allowed_in'], 'tip'=>$L['allowing-extra-data-tip'], 'firstNull' => false, 'data' => $reviewsData ),
					
					'membergroups-allowed'=>array('label'=>$L['membergroups-allowed'], 'name' => 'settings[allow_reviews_group][]', 'type'=>'select', 'value'=>Json( $settings['allow_reviews_group'] ), 'tip'=>null, 'firstNull' => false, 'data' => $groupsAllowed, 'id' => 'slcReviews', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
						'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
				)
			)
		)
	),
	
	'favorite-posts' => array
	(
		'title' => $L['favorite-posts'],
		'data' => array(
			'site-rss' => array(
				'title' => null, 'data' => array
				(
					'allow-favorite-posts'=>array('label'=>$L['allow-users-to-add-favorite-posts'], 'type'=>'checkbox', 'name' => 'settings[allow_favorite_posts]', 'value' => $settings['allow_favorite_posts'], 'tip'=>$L['allow-users-to-add-favorite-posts-tip'], 'disabled' => false ),
					
					'display-favorite'=>array('label'=>sprintf( $L['enable-s-in'], $L['favorite-posts'] ), 'type'=>'select-group', 'name' => 'settings[allow_favorite_posts_in]', 'value'=>$settings['allow_favorite_posts_in'], 'tip'=>$L['allowing-extra-data-tip'], 'firstNull' => false, 'data' => $favData ),
					
					'membergroups-allowed'=>array('label'=>$L['membergroups-allowed'], 'name' => 'settings[allow_favorite_posts_group][]', 'type'=>'select', 'value'=>Json( $settings['allow_favorite_posts_group'] ), 'tip'=>null, 'firstNull' => false, 'data' => $groupsAllowed, 'id' => 'slcFavorite', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
						'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
				)
			)
		)
	),
	
	'post-notifications' => array
	(
		'title' => $L['post-update-notifications'],
		'data' => array(
			'site-rss' => array(
				'title' => null, 'data' => array
				(
					'post-update-notifications'=>array('label'=>$L['enable-post-update-notifications'], 'type'=>'checkbox', 'name' => 'settings[allow_post_notifications]', 'value' => $settings['allow_post_notifications'], 'tip'=>$L['enable-post-update-notifications-tip'], 'disabled' => false ),
					
					'show-subs'=>array('label'=>$L['show-subscribers-count'], 'type'=>'checkbox', 'name' => 'settings[show_subscribers_num]', 'value' => $settings['show_subscribers_num'], 'tip'=>$L['show-subscribers-count-tip'], 'disabled' => false ),
					
					'display-update-notifications'=>array('label'=>sprintf( $L['enable-s-in'], $L['post-update-notifications'] ), 'type'=>'select-group', 'name' => 'settings[allow_post_notifications_in]', 'value'=>$settings['allow_post_notifications_in'], 'tip'=>$L['allowing-extra-data-tip'], 'firstNull' => false, 'data' => $notifyData ),
					
					'membergroups-allowed'=>array('label'=>$L['membergroups-allowed'], 'name' => 'settings[allow_notifications_group][]', 'type'=>'select', 'value'=>Json( $settings['allow_notifications_group'] ), 'tip'=>null, 'firstNull' => false, 'data' => $groupsAllowed, 'id' => 'slcNotifications', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
						'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
				)
			)
		)
	),*/
);

if ( $this->adminSettings::IsTrue( 'enable_stats' ) )
{
	$stats = Json( $settings['stats_data'] );
	
	$aggData = array();
	
	$aggData['0'] = array( 'name' => '0', 'title'=> $L['never-aggregate-data'], 'disabled' => false, 'data' => array() );
	$aggData['3'] = array( 'name' => '3', 'title'=> sprintf( $L['x-months'], 3 ), 'disabled' => false, 'data' => array() );
	$aggData['6'] = array( 'name' => '6', 'title'=> sprintf( $L['x-months'], 6 ), 'disabled' => false, 'data' => array() );
	$aggData['9'] = array( 'name' => '9', 'title'=> sprintf( $L['x-months'], 9 ), 'disabled' => false, 'data' => array() );
	$aggData['12'] = array( 'name' => '12', 'title'=> sprintf( $L['x-months'], 12 ), 'disabled' => false, 'data' => array() );

	$form['stats'] = array
	(
		'title' => $L['stats'],
		'data' => array(
			'site-rss' => array(
				'title' => null, 'data' => array
				(
					'log-full-user-agent-string'=>array('label'=>$L['log-full-user-agent-string'], 'type'=>'checkbox', 'name' => 'stats[log_full_user_agent_string]', 'value' => ( isset( $stats['log_full_user_agent_string'] ) ? $stats['log_full_user_agent_string'] : null ), 'tip'=>$L['log-full-user-agent-string-tip'], 'hide' => false ),
					
					'log-visits-from-robots'=>array('label'=>$L['log-visits-from-robots'], 'type'=>'checkbox', 'name' => 'stats[log_visits_from_robots]', 'value' => ( isset( $stats['log_visits_from_robots'] ) ? $stats['log_visits_from_robots'] : null ), 'tip'=>null, 'hide' => false ),
					
					'ignore-ips'=>array('label'=>$L['ignore-these-ip-addresses'], 'type'=>'textarea', 'name' => 'stats[ignore_ips]', 'value' => ( isset( $stats['ignore_ips'] ) ? $stats['ignore_ips'] : '' ), 'tip'=>$L['ignore-these-ip-addresses-tip'] ),
					
					'aggregate-data-after'=>array('label'=>$L['aggregate-data-after'], 'type'=>'select', 'name' => 'stats[aggregate_data]', 'value'=>( isset( $stats['aggregate_data'] ) ? $stats['aggregate_data'] : null ), 'tip'=>$L['aggregate-data-after-tip'], 'firstNull' => false, 'data' => $aggData )
				)
			)
		)
	);
}