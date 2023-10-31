<?php defined('TOKICMS') or die('Hacking attempt...');

class AutoMenuSettings extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) || !$Admin->Settings()::IsTrue( 'enable_auto_menu' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'auto-menu-settings' ) . ' | ' . $Admin->SiteName() );
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		// Check if the token is correct
		if ( !verify_token( 'auto-menu-settings' ) )
			return;
		
		//Get the needed POST values
		$settings = $_POST['menu'];
		
		$themeId = $Admin->ActiveTheme();

		$dt = $Admin->SiteThemes();

		$code = $Admin->LangCode();
		
		$links = array();
		
		if ( !empty( $_POST['custom_link'] ) )
		{
			foreach ( $_POST['custom_link'] as $link )
			{
				if ( !Validate( $link['url'], 'url' ) )
				{
					continue;
				}
				
				$links[] = array(
					'url' 		=> $link['url'],
					'title' 	=> Sanitize ( $link['title'], false ),
					'target' 	=> Sanitize ( $link['target'], false )
				);
			}
		}
		
		$arr = array(
			'disable_menu' => ( isset( $settings['disable_menu'] ) ? true : false ),
			'show_home' => ( isset( $settings['show_home'] ) ? true : false ),
			'show_login_logout' => ( isset( $settings['show_login_logout'] ) ? true : false ),
			'show_blogs' => ( ( $Admin->MultiBlog() && isset( $settings['show_blogs'] ) && is_array( $settings['show_blogs'] ) ) ? array_values( $settings['show_blogs'] ) : array() ),
			'show_blog_cats' => ( $Admin->MultiBlog() && isset( $settings['show_blog_cats'] ) ? true : false ),
			'show_pages' => ( ( isset( $settings['show_pages'] ) && is_array( $settings['show_pages'] ) ) ? array_values( $settings['show_pages'] ) : array() ),
			'show_pages_as_childs' => ( isset( $settings['show_pages_as_childs'] ) ? true : false ),
			'pages_more_title' => ( isset( $settings['pages_more_title'] ) ? Sanitize ( $settings['pages_more_title'], false ) : null ),
			'show_custom_types' => ( ( isset( $settings['show_custom_types'] ) && is_array( $settings['show_custom_types'] ) ) ? array_values( $settings['show_custom_types'] ) : array() ),
			'show_child_custom_types' => ( isset( $settings['show_child_custom_types'] ) ? true : false ),
			'show_categories' => ( ( isset( $settings['show_categories'] ) && is_array( $settings['show_categories'] ) ) ? array_values( $settings['show_categories'] ) : array() ),
			'show_categories_as_childs' => ( isset( $settings['show_categories_as_childs'] ) ? true : false ),
			'categories_button_title' => ( isset( $settings['categories_button_title'] ) ? Sanitize ( $settings['categories_button_title'], false ) : null ),
			'show_child_categories' => ( isset( $settings['show_child_categories'] ) ? true : false ),
			'only_current_blog_pages' => ( $Admin->MultiBlog() && isset( $settings['only_current_blog_pages'] ) ? true : false ),
			'hide_empty_categories' => ( isset( $settings['hide_empty_categories'] ) ? true : false ),
			'limit_blog_categories' => ( $Admin->MultiBlog() && isset( $settings['limit_blog_categories'] ) ? (int) $settings['limit_blog_categories'] : 0 ),
			
			//Custom links
			'custom_links' 			=> $links,
			'show_links_as_childs' 	=> ( isset( $settings['show_links_as_childs'] ) ? true : false ),
			'links_more_title' 		=> ( isset( $settings['links_more_title'] ) ? Sanitize ( $settings['links_more_title'], false ) : null )
		);
		
		$dt[$themeId]['auto-menu'][$code] = $arr;

		$settingsArray = array(
			'themes_data' => json_encode( $dt, JSON_UNESCAPED_UNICODE )
		);
		
		$Admin->UpdateSettings( $settingsArray );

		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}