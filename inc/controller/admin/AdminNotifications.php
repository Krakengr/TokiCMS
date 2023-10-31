<?php defined('TOKICMS') or die('Hacking attempt...');

class AdminNotifications extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		if ( !IsAllowedTo( 'admin-site' ) )
		{
			$this->router->SetNotFound();
			$this->view();
			return;
		}

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		$Admin = $this->getVariable( 'Admin' );
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'notifications' ) )
			return;
		
		//Get the needed POST values
		$settings = $_POST['settings'];

		$postFilter = ( ( $settings['posts_filter'] == '' ) ? '/post/' : Sanitize( LastTrailCheck( $settings['posts_filter'] ), false ) );
		
		//Add a slash in the beginning
		$postFilter = FirstTrailCheck( $postFilter );
		
		$categoriesFilter = ( ( ( $settings['categories_filter'] == '' ) || ( $settings['categories_filter'] == '/' ) ) ? '/category/' : Sanitize( LastTrailCheck( $settings['categories_filter'] ), falsε  ) );
		
		$categoriesFilter = FirstTrailCheck( $categoriesFilter );
		
		$tagsFilter = ( ( ( $settings['tags_filter'] == '' ) || ( $settings['tags_filter'] == '/' ) ) ? '/tag/' : Sanitize( LastTrailCheck( $settings['tags_filter'] ), false ) );
		
		$tagsFilter = FirstTrailCheck( $tagsFilter );
		
		$settingsArray = array(
				'enable_rss' => ( isset( $settings['enable_rss'] ) ? "true" : "false" ),
				'enable_auto_menu' => ( isset( $settings['enable_auto_menu'] ) ? "true" : "false" ),
				'load_jquery_cdn' => ( isset( $settings['load_jquery_cdn'] ) ? "true" : "false" ),
				'show_full_post_rss' => ( isset( $settings['show_full_post_rss'] ) ? "true" : "false" ),
				'rss_limit' => (int) $settings['rss_limit'],
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
				'images_html' => Sanitize ( $settings['images_html'], false ),
				'images_root' => Sanitize ( $settings['images_root'], false ),
				'allowed_extensions' => Sanitize ( $settings['allowed_extensions'], false )
			);
		
		$Admin->UpdateSettings( $settingsArray );
		
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}