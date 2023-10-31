<?php defined('TOKICMS') or die('Hacking attempt...');

class SeoSettings extends Controller {
	
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
		
		if ( !verify_token( 'seo-settings' ) )
			return;

		//Get the needed POST values
		$settings = $_POST['settings'];
		
		//We are going to create a new array, so there is no need to get the settings from the DB
		$r = array(
				'title_seperator' => Sanitize ( $settings['title_seperator'], false ),
				'nofollow_tag_archive' => ( isset( $settings['nofollow_tag_archive'] ) ? true : false ),
				'open_external_links_new_tab' => ( isset( $settings['open_external_links_new_tab'] ) ? true : false ),
				'nofollow_external_links' => ( isset( $settings['nofollow_external_links'] ) ? true : false ),
				'enable_open_graph' => ( isset( $settings['enable_open_graph'] ) ? true : false ),
				'enable_schema_markup' => ( isset( $settings['enable_schema_markup'] ) ? true : false ),
				'add_alt_on_images' => ( isset( $settings['add_alt_on_images'] ) ? true : false ),
				'facebook_profile' => Sanitize ( $settings['facebook_profile'], false ),
				
				//Title formats
				'homepage_title_format' => Sanitize ( $settings['homepage_title_format'], false ),
				'pages_title_format' => Sanitize ( $settings['pages_title_format'], false ),
				'posts_title_format' => Sanitize ( $settings['posts_title_format'], false ),
				'blogs_title_format' => Sanitize ( $settings['blogs_title_format'], false ),
				'categories_title_format' => Sanitize ( $settings['categories_title_format'], false ),
				'authors_title_format' => Sanitize ( $settings['authors_title_format'], false ),
				'tags_title_format' => Sanitize ( $settings['tags_title_format'], false ),
				'search_title_format' => Sanitize ( $settings['search_title_format'], false ),
				
				//Meta formats
				'homepage_meta_format' => Sanitize ( $settings['homepage_meta_format'], false ),
				'pages_meta_format' => Sanitize ( $settings['pages_meta_format'], false ),
				'posts_meta_format' => Sanitize ( $settings['posts_meta_format'], false ),
				'blogs_meta_format' => Sanitize ( $settings['blogs_meta_format'], false ),
				'categories_meta_format' => Sanitize ( $settings['categories_meta_format'], false ),
				'authors_meta_format' => Sanitize ( $settings['authors_meta_format'], false ),
				'tags_meta_format' => Sanitize ( $settings['tags_meta_format'], false ),
				'search_meta_format' => Sanitize ( $settings['search_meta_format'], false ),
				
				'remove_short_words' => ( isset( $settings['remove_short_words'] ) ? (int) $settings['remove_short_words'] : 0 ),
				'threads_title_format' => ( isset( $settings['threads_title_format'] ) ? Sanitize ( $settings['threads_title_format'], false ) : '' ),
				'products_title_format' => ( isset( $settings['products_title_format'] ) ? Sanitize ( $settings['products_title_format'], false ) : '' ),
				'show_pages_search' => ( isset( $settings['show_pages_search'] ) ? "true" : "false" ),
				'show_custom_post_types_search' => ( isset( $settings['show_custom_post_types_search'] ) ? "true" : "false" ),
				'show_blogs_search' => ( isset( $settings['show_blogs_search'] ) ? "true" : "false" ),
				'show_posts_search' => ( isset( $settings['show_posts_search'] ) ? "true" : "false" ),
				'show_tags_search' => ( isset( $settings['show_tags_search'] ) ? "true" : "false" ),
				'show_categories_search' => ( isset( $settings['show_categories_search'] ) ? "true" : "false" ),
				'show_products_search' => ( isset( $settings['show_products_search'] ) ? "true" : "false" ),
				'show_threads_search' => ( isset( $settings['show_threads_search'] ) ? "true" : "false" ),
				'show_authors_search' => ( isset( $settings['show_authors_search'] ) ? "true" : "false" ),
				'google_site_verification' => ( isset( $settings['google_site_verification'] ) ? Sanitize ( $settings['google_site_verification'], false ) : '' ),
				'msvalidate' => ( isset( $settings['msvalidate'] ) ? Sanitize ( $settings['msvalidate'], false ) : '' ),
				'yandex_verification' => ( isset( $settings['yandex_verification'] ) ? Sanitize ( $settings['yandex_verification'], false ) : '' ),
				'tracking-codes' => array(
					'google_analytics_four' => ( isset( $settings['google_analytics_four'] ) ? Sanitize ( $settings['google_analytics_four'], false ) : '' ),
					'google_analytics_ua' => ( isset( $settings['google_analytics_ua'] ) ? Sanitize ( $settings['google_analytics_ua'], false ) : '' ),
					'facebook_pixel_id' => ( isset( $settings['facebook_pixel_id'] ) ? Sanitize ( $settings['facebook_pixel_id'], false ) : '' ),
					'google_tag_manager_id' => ( isset( $settings['google_tag_manager_id'] ) ? Sanitize ( $settings['google_tag_manager_id'], false ) : '' )
				),
		);
		
		$settingsArray['seo_data'] = json_encode( $r );

		unset( $r );
		
		$Admin->UpdateSettings( $settingsArray );
		
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		$Admin->UpdateSitemaps();
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}