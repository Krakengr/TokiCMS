<?php defined('TOKICMS') or die('Hacking attempt...');

class RobotTxtSettings extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-seo' ) ) || !$Admin->Settings()::IsTrue( 'enable_seo' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'robots-txt-settings' ) )
			return;
		
		//Get the needed POST values
		$settings = $_POST['settings'];
		$robots = $_POST['robots'];
	
		$settingsArray = array(
				'enable_robots_txt' => ( isset( $settings['enable_robots_txt'] ) ? "true" : "false" )
		);
		
		//We are going to create a new array, so there is no need to get the settings from the DB
		$r = array(
			'add_your_sitemap_in_the_robots_file' => ( isset( $robots['add_your_sitemap_in_the_robots_file'] ) ? true : false ),
			'add_ads_in_the_robots_file' => ( isset( $robots['add_ads_in_the_robots_file'] ) ? true : false ),
			'add_app_ads_in_the_robots_file' => ( isset( $robots['add_app_ads_in_the_robots_file'] ) ? true : false ),
			'add_stop_crawling_in_the_robots_file' => ( isset( $robots['add_stop_crawling_in_the_robots_file'] ) ? true : false ),
			'add_spam_backlink_blocker_in_the_robots_file' => ( isset( $robots['add_spam_backlink_blocker_in_the_robots_file'] ) ? true : false ),
			'add_bad_bot_blocker_in_the_robots_file' => ( isset( $robots['add_bad_bot_blocker_in_the_robots_file'] ) ? true : false ),
			'add_backlink_protector_in_the_robots_file' => ( isset( $robots['add_backlink_protector_in_the_robots_file'] ) ? true : false ),
			'crawl_delay' => ( isset( $robots['crawl_delay'] ) ? (int) $robots['crawl_delay'] : 0 ),
			'show_google_bot_in_robots' => ( isset( $robots['show_google_bot_in_robots'] ) ? Sanitize ( $robots['show_google_bot_in_robots'], false ) : 'disable' ),
			'show_google_images_in_robots' => ( isset( $robots['show_google_images_in_robots'] ) ? Sanitize ( $robots['show_google_images_in_robots'], false ) : 'disable' ),
			'show_media_partners_in_robots' => ( isset( $robots['show_media_partners_in_robots'] ) ? Sanitize ( $robots['show_media_partners_in_robots'], false ) : 'disable' ),
			'show_google_adsbot_in_robots' => ( isset( $robots['show_google_adsbot_in_robots'] ) ? Sanitize ( $robots['show_google_adsbot_in_robots'], false ) : 'disable' ),
			'show_google_mobile_in_robots' => ( isset( $robots['show_google_mobile_in_robots'] ) ? Sanitize ( $robots['show_google_mobile_in_robots'], false ) : 'disable' ),
			'show_bing_bot_in_robots' => ( isset( $robots['show_bing_bot_in_robots'] ) ? Sanitize ( $robots['show_bing_bot_in_robots'], false ) : 'disable' ),
			'show_msn_bot_in_robots' => ( isset( $robots['show_msn_bot_in_robots'] ) ? Sanitize ( $robots['show_msn_bot_in_robots'], false ) : 'disable' ),
			'show_msn_bot_media_in_robots' => ( isset( $robots['show_msn_bot_media_in_robots'] ) ? Sanitize ( $robots['show_msn_bot_media_in_robots'], false ) : 'disable' ),
			'show_apple_bot_in_robots' => ( isset( $robots['show_apple_bot_in_robots'] ) ? Sanitize ( $robots['show_apple_bot_in_robots'], false ) : 'disable' ),
			'show_yandex_bot_in_robots' => ( isset( $robots['show_yandex_bot_in_robots'] ) ? Sanitize ( $robots['show_yandex_bot_in_robots'], false ) : 'disable' ),
			'show_yandex_images_in_robots' => ( isset( $robots['show_yandex_images_in_robots'] ) ? Sanitize ( $robots['show_yandex_images_in_robots'], false ) : 'disable' ),
			'show_yahoo_search_in_robots' => ( isset( $robots['show_yahoo_search_in_robots'] ) ? Sanitize ( $robots['show_yahoo_search_in_robots'], false ) : 'disable' ),
			'show_duckduckgo_search_in_robots' => ( isset( $robots['show_duckduckgo_search_in_robots'] ) ? Sanitize ( $robots['show_duckduckgo_search_in_robots'], false ) : 'disable' ),
			'show_qwant_in_robots' => ( isset( $robots['show_qwant_in_robots'] ) ? Sanitize ( $robots['show_qwant_in_robots'], false ) : 'disable' ),
			'show_chinese_search_engines_in_robots' => ( isset( $robots['show_chinese_search_engines_in_robots'] ) ? Sanitize ( $robots['show_chinese_search_engines_in_robots'], false ) : 'disable' ),
			'show_facebook_in_robots' => ( isset( $robots['show_facebook_in_robots'] ) ? Sanitize ( $robots['show_facebook_in_robots'], false ) : 'disable' ),'show_twitter_in_robots' => ( isset( $robots['show_twitter_in_robots'] ) ? Sanitize ( $robots['show_twitter_in_robots'], false ) : 'disable' ),
			'show_linkedin_in_robots' => ( isset( $robots['show_linkedin_in_robots'] ) ? Sanitize ( $robots['show_linkedin_in_robots'], false ) : 'disable' ),
			'show_pinterest_in_robots' => ( isset( $robots['show_pinterest_in_robots'] ) ? Sanitize ( $robots['show_pinterest_in_robots'], false ) : 'disable' ),
			'personal_code' => ( isset( $robots['personal_code'] ) ? Sanitize ( $robots['personal_code'], false ) : '' )
		);
			
		$settingsArray['robots_data'] = json_encode( $r );
		
		$Admin->UpdateSettings( $settingsArray );
		
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}