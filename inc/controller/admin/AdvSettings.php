<?php defined('TOKICMS') or die('Hacking attempt...');

class AdvSettings extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( !IsAllowedTo( 'admin-site' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'advanced-settings' ) . ' | ' . $Admin->SiteName() );
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'advanced-settings' ) )
			return;
		
		//Get the needed POST values
		$settings = $_POST['settings'];

		$postFilter = ( ( $settings['posts_filter'] == '' ) ? '/post/' : Sanitize( LastTrailCheck( $settings['posts_filter'] ), false ) );

		$categoriesFilter = ( ( ( $settings['categories_filter'] == '' ) || ( $settings['categories_filter'] == '/' ) ) ? '/category/' : Sanitize( LastTrailCheck( $settings['categories_filter'] ), false  ) );
		
		$tagsFilter = ( ( ( $settings['tags_filter'] == '' ) || ( $settings['tags_filter'] == '/' ) ) ? '/tag/' : Sanitize( LastTrailCheck( $settings['tags_filter'] ), false ) );
		
		//Add a slash in the beginning ot the tag
		$postFilter = FirstTrailCheck( $postFilter );
		$categoriesFilter = FirstTrailCheck( $categoriesFilter );
		$tagsFilter = FirstTrailCheck( $tagsFilter );
		
		$reviewsPermissions = $notificationsPermissions = $favoritePermissions = array();
			
		if ( !empty( $settings['allow_reviews_group'] ) )
			$reviewsPermissions = $settings['allow_reviews_group'];
		
		if ( !empty( $settings['allow_notifications_group'] ) )
			$notificationsPermissions = $settings['allow_notifications_group'];
		
		if ( !empty( $settings['allow_favorite_posts_group'] ) )
			$favoritePermissions = $settings['allow_favorite_posts_group'];
		
		$rss = ( ( !empty( $_POST['rss'] ) && is_array( $_POST['rss'] ) ) ? $_POST['rss'] : array() );
		
		$apiSettings = Json( $Admin->Settings()::Get()['api_keys'] );
		
		$rssSettings = Json( $Admin->Settings()::Get()['rss_settings'] );
		
		$cdnSettings = Json( $Admin->Settings()::Get()['cdn_data'] );
		
		$rssSettings['lang-' . $Admin->GetLang()]['blog-' . $Admin->GetBlog()]['data'] = $rss;
		
		$rssSettings = json_encode( $rssSettings, JSON_UNESCAPED_UNICODE );
		
		$reviewsPermissions = json_encode( $reviewsPermissions );
		$notificationsPermissions = json_encode( $notificationsPermissions );
		$favoritePermissions = json_encode( $favoritePermissions );
		
		$images_html = ( ( isset( $settings['images_html'] ) && !empty( $settings['images_html'] ) ) ? Sanitize ( LastTrailCheck( $settings['images_html'] ), false ) : '' );
		
		$images_root = ( ( isset( $settings['images_root'] ) && !empty( $settings['images_html'] ) ) ? Sanitize ( LastTrailCheck( $settings['images_root'] ), false ) : '' );
		
		$statsData = Json( Settings::Get()['stats_data'] );
		
		if ( isset( $_POST['cdn'] ) )
		{
			$cdn = array(
				'enable_imagekit' => ( isset( $_POST['cdn']['enable_imagekit'] ) ? true : false ),
			);
		}
		
		else
			$cdn = $cdnSettings;
		
		if ( isset( $_POST['stats'] ) )
		{
			$stats = array(
				'log_full_user_agent_string' => ( isset( $_POST['stats']['log_full_user_agent_string'] ) ? true : false ),
				'log_visits_from_robots' => ( isset( $_POST['stats']['log_visits_from_robots'] ) ? true : false ),
				'ignore_ips' => ( isset( $_POST['stats']['ignore_ips'] ) ? Sanitize ( $_POST['stats']['ignore_ips'], false ) : null ),
				'aggregate_data' => ( isset( $_POST['stats']['aggregate_data'] ) ? Sanitize ( $_POST['stats']['aggregate_data'], false ) : 0 ),
				'last_aggregated' => ( isset( $statsData['last_aggregated'] ) ? $statsData['last_aggregated'] : null )
			);
		}
		
		else
			$stats = $statsData;
		
		if ( isset( $_POST['imagekit'] ) )
		{
			$apiSettings['imagekit'] = array(
				'id' => ( isset( $_POST['imagekit']['id'] ) ? Sanitize ( $_POST['imagekit']['id'], false ) : null ),
				
				'url' => ( !empty( $_POST['imagekit']['url'] ) ? Sanitize( LastTrailCheck( $_POST['imagekit']['url'] ), false ) : null ),
				
				'public_key' => ( !empty( $_POST['imagekit']['public_key'] ) ? Sanitize ( $_POST['imagekit']['public_key'], false ) : null ),
				
				'private_key' => ( !empty( $_POST['imagekit']['private_key'] ) ? Sanitize ( $_POST['imagekit']['private_key'], false ) : null )
			);
		}
		
		else
			$apiSettings['imagekit'] = array();
		
		$apiSettings['youtube'] = ( isset( $settings['youtube_api_key'] ) ? Sanitize ( $settings['youtube_api_key'], false ) : null );
		
		$apiSettings['gmaps'] = ( isset( $settings['maps_api_key'] ) ? Sanitize ( $settings['maps_api_key'], false ) : null );
		
		$apiSettings['disqus'] = array(
			'public_key' => ( !empty( $settings['disqus_public_key'] ) ? Sanitize ( $settings['disqus_public_key'], false ) : null ),

			'secret_key' => ( !empty( $settings['disqus_secret_key'] ) ? Sanitize ( $settings['disqus_secret_key'], false ) : null )
		);
		
		$settingsArray = array(
				'enable_rss' => ( isset( $settings['enable_rss'] ) ? "true" : "false" ),
				'enable_auto_menu' => ( isset( $settings['enable_auto_menu'] ) ? "true" : "false" ),
				'disable_user_login' => ( isset( $settings['disable_user_login'] ) ? "true" : "false" ),
				'load_jquery_cdn' => ( isset( $settings['load_jquery_cdn'] ) ? "true" : "false" ),
				'enable_debug_mode' => ( isset( $settings['enable_debug_mode'] ) ? "true" : "false" ),
				'parent_site_shows_everything' => ( isset( $settings['parent_site_shows_everything'] ) ? "true" : "false" ),
				'enable_new_content_time_limit' => ( isset( $settings['enable_new_content_time_limit'] ) ? "true" : "false" ),
				
				'allow_favorite_posts' => ( isset( $settings['allow_favorite_posts'] ) ? "true" : "false" ),
				'allow_favorite_posts_in' => Sanitize ( $settings['allow_favorite_posts_in'], false ),
				'allow_favorite_posts_group' => $favoritePermissions,
				
				'allow_full_search' => ( isset( $settings['allow_full_search'] ) ? "true" : "false" ),
				'enable_stats' => ( isset( $settings['enable_stats'] ) ? "true" : "false" ),
				'stats_data' => json_encode( $stats, JSON_UNESCAPED_UNICODE ),
				
				'enable_reviews' => ( isset( $settings['enable_reviews'] ) ? "true" : "false" ),
				'reviews_allowed_in' => Sanitize ( $settings['reviews_allowed_in'], false ),
				'allow_reviews_group' => $reviewsPermissions,
				
				'allow_post_notifications' => ( isset( $settings['allow_post_notifications'] ) ? "true" : "false" ),
				'show_subscribers_num' => ( isset( $settings['show_subscribers_num'] ) ? "true" : "false" ),
				'allow_post_notifications_in' => Sanitize ( $settings['allow_post_notifications_in'], false ),
				'allow_notifications_group' => $notificationsPermissions,
				
				'rss_settings' => $rssSettings,
				
				'api_keys' => json_encode( $apiSettings, JSON_UNESCAPED_UNICODE ),
				
				'cdn_data' => json_encode( $cdn, JSON_UNESCAPED_UNICODE ),
				
				'parent_type' => ( isset( $_POST['parent-type'] ) ? Sanitize( $_POST['parent-type'], false ) : "normal" ),
				'disable_author_archives' => ( isset( $settings['disable_author_archives'] ) ? "true" : "false" ),
				'enable_html5_video_player' => ( isset( $settings['enable_html5_video_player'] ) ? "true" : "false" ),
				
				'posts_filter' => $postFilter,
				'categories_filter' => $categoriesFilter,
				'tags_filter' => $tagsFilter,
				'search_engine_disallow' => ( isset( $settings['search_engine_disallow'] ) ? "true" : "false" ),
				'display_pagination_home' => ( isset( $settings['display_pagination_home'] ) ? "true" : "false" ),
				'enable_registrations' => ( isset( $settings['enable_registrations'] ) ? "true" : "false" ),
				'send_welcome_email_new_reg' => ( isset( $settings['send_welcome_email_new_reg'] ) ? "true" : "false" ),
				'require_accept_reg_agreement' => ( isset( $settings['require_accept_reg_agreement'] ) ? "true" : "false" ),
				'require_accept_privacy_policy' => ( isset( $settings['require_accept_privacy_policy'] ) ? "true" : "false" ),
				'registration_method' => Sanitize ( $settings['registration_method'], false ),
				'force_https' => ( isset( $settings['force_https'] ) ? "true" : "false" ),
				'redirect_www' => ( isset( $settings['redirect_www'] ) ? Sanitize ( $settings['redirect_www'], false ) : "false" ),
				'show_admin_bar' => ( isset( $settings['show_admin_bar'] ) ? "true" : "false" ),
				
				'website_email' => ( Validate( $settings['website_email'] ) ? Sanitize ( $settings['website_email'], false ) : '' ),
				
				'contact_email' => ( Validate( $settings['contact_email'] ) ? Sanitize ( $settings['contact_email'], false ) : '' ),
				
				'images_html' => $images_html,
				'images_root' => $images_root,
				'allowed_extensions' => Sanitize ( $settings['allowed_extensions'], false )
		);
		
		$redirUrl = $Admin->GetUrl( null, null, true );
		
		$site = $Admin->Settings()::Site();
		
		$share = ( ( isset( $site['share_data'] ) && !empty( $site['share_data'] ) ) ? Json( $site['share_data'] ) : array() );

		$hosted = Json( $Admin->Settings()::Site()['hosted'] );
			
		$hosted[$Admin->LangKey()]['blog-' . $Admin->GetBlog()] = ( isset( $settings['site_hosted'] ) ? Sanitize ( $settings['site_hosted'], false ) : 'self' );
		
		if ( $settings['site_hosted'] != 'self' )
		{
			$redirUrl = $Admin->GetUrl( 'settings'  );
		}

		//If this is a child site, we have a few settings to check
		if ( !$Admin->IsDefaultSite() )
		{
			$share['sync_uploads'] = ( isset( $settings['sync_uploads'] ) ? true : false );
		}
		
		$dbarr = array(
			"share_data" 	=> json_encode( $share ),
			"hosted" 		=> json_encode( $hosted )
        );

		$this->db->update( 'sites' )->where( 'id', $Admin->GetSite() )->set( $dbarr );
		
		$Admin->UpdateSettings( $settingsArray );
		
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $redirUrl );
		exit;
	}
}